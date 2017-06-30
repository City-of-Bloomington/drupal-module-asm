<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/old-licenses/gpl-2.0 GNU/GPL2, see LICENSE
 *
 * This file is part of the ASM drupal module.
 *
 * The ASM module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * The ASM module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with the ASM module.  If not, see <https://www.gnu.org/licenses/old-licenses/gpl-2.0/>.
 */
namespace Drupal\asm\Controller;

use Drupal\asm\ASMGateway;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormState;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ASMController extends ControllerBase
{
    const PROXY_CACHE = '/tmp/asm';

    public function title(array $animal_id)
    {
        return !empty($animal_id['ANIMALNAME']) ? $animal_id['ANIMALNAME'] : '';
    }

    public function animals()
    {
        $fields     = null;
        $form_state = new FormState();
        $form_state->setAlwaysProcess(true);
        $form_state->setRebuild(true);

        if (!empty($_GET['species'])) {
            $form_state->set('species', $_GET['species']);
            $fields = [ASMGateway::SPECIESNAME => $_GET['species']];
        }
        $form = \Drupal::formBuilder()->buildForm('Drupal\asm\Form\AnimalSearchForm', $form_state);
        return [
            '#theme'   => 'asm_animals',
            '#animals' => ASMGateway::animals($fields),
            '#asm_url' => ASMGateway::getUrl(),      // Set in module configuration
            '#proxy'   => ASMGateway::enableProxy(), // True or False, based on configuration
            '#form'    => $form
        ];
    }

    /**
     * @param array $animal  The JSON data from ASMGateway::animal()
     * @see https://www.drupal.org/docs/8/api/routing-system/parameter-upcasting-in-routes
     */
    public function animal(array $animal_id)
    {
        return [
            '#theme'   => 'asm_animal',
            '#animal'  => $animal_id,
            '#asm_url' => ASMGateway::getUrl(),      // Set in module configuration
            '#proxy'   => ASMGateway::enableProxy()  // True or False, based on configuration
        ];
    }

    public function image($animal_id, $imagenum)
    {
        $animal_id = (int)$animal_id;
        $imagenum  = (int)$imagenum;
        $cacheFile = self::PROXY_CACHE."/$animal_id-$imagenum";

        if (!is_file($cacheFile)) {
            if (!is_dir(self::PROXY_CACHE)) {
                  mkdir(self::PROXY_CACHE);
            }

            $url = ASMGateway::getUrl()."/service?method=animal_image&animalid=$animal_id";
            if ($imagenum > 1) { $url.="&seq=$imagenum"; }

            try {
                $client = \Drupal::httpClient();
                $response = $client->request('GET', $url, ['sink'=>$cacheFile]);
            }
            catch (\Exception $e) {
                throw new NotFoundHttpException();
            }
        }

        $info = new \finfo(FILEINFO_MIME);
        $headers['Content-Type'  ] = $info->file($cacheFile);
        $headers['Content-Length'] =    filesize($cacheFile);

        return new BinaryFileResponse($cacheFile, 200, $headers);
    }
}
