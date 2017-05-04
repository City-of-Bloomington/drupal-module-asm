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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ASMController extends ControllerBase
{
    const PROXY_CACHE = '/tmp/asm';

    public function animals()
    {
        return [
            '#theme'   => 'asm_animals',
            '#animals' => ASMGateway::animals(),
            '#asm_url' => ASMGateway::getUrl(),      // Set in module configuration
            '#proxy'   => ASMGateway::enableProxy()  // True or False, based on configuration
        ];
    }

    public function animal($animal_id)
    {
        return [
            '#theme'   => 'asm_animal',
            '#animal'  => ASMGateway::animal($animal_id),
            '#asm_url' => ASMGateway::getUrl(),      // Set in module configuration
            '#proxy'   => ASMGateway::enableProxy()  // True or False, based on configuration
        ];
    }

    public function image($animal_id)
    {
        $animal_id = (int)$animal_id;
        $cacheFile = self::PROXY_CACHE."/$animal_id";

        if (!is_file($cacheFile)) {
            if (!is_dir(self::PROXY_CACHE)) {
                  mkdir(self::PROXY_CACHE);
            }

            $url = ASMGateway::getUrl()."/service?method=animal_image&animalid=$animal_id";
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
