<?php
/**
 * @copyright 2017-2020 City of Bloomington, Indiana
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
    const MODE_LOST   = 'lost';
    const MODE_FOUND  = 'found';

    public function title(int $animal_id)
    {
        $animal = ASMGateway::adoptable_animal($animal_id);
        return !empty($animal['ANIMALNAME']) ? $animal['ANIMALNAME'] : '';
    }

    private static function filterForSpecies(string $species)
    {
        $fields     = null;
        if ($species != 'All') {
            $fields = [ASMGateway::SPECIESNAME => $species];
        }
        return $fields;
    }

    public function adoptable_animals(string $species)
    {
        return [
            '#theme'   => 'asm_adoptable_animals',
            '#animals' => ASMGateway::adoptable_animals(self::filterForSpecies($species)),
            '#asm_url' => ASMGateway::getUrl(),      // Set in module configuration
            '#proxy'   => ASMGateway::enableProxy()  // True or False, based on configuration
        ];
    }

    public function adoptable_animal(int $animal_id)
    {
        return [
            '#theme'   => 'asm_adoptable_animal',
            '#animal'  => ASMGateway::adoptable_animal($animal_id),
            '#asm_url' => ASMGateway::getUrl(),      // Set in module configuration
            '#proxy'   => ASMGateway::enableProxy()  // True or False, based on configuration
        ];
    }

    public function found_animal(int $lfid)
    {
        return [
            '#theme'   => 'asm_lf_animal',
            '#animal'  => ASMGateway::lf_animal(ASMGateway::MODE_FOUND, $lfid),
            '#asm_url' => ASMGateway::getUrl(),
            '#proxy'   => ASMGateway::enableProxy()
        ];
    }

    public function found_animals(string $species)
    {
        return [
            '#theme'   => 'asm_lf_animals',
            '#animals' => ASMGateway::lf_animals(ASMGateway::MODE_FOUND, self::filterForSpecies($species)),
            '#asm_url' => ASMGateway::getUrl(),
            '#proxy'   => ASMGateway::enableProxy(),
            '#title'   => 'Found Animals',
            '#mode'    => ASMGateway::MODE_FOUND
        ];
    }

    public function lost_animal(int $lfid)
    {
        return [
            '#theme'   => 'asm_lf_animal',
            '#animal'  => ASMGateway::lf_animal(ASMGateway::MODE_LOST, $lfid),
            '#asm_url' => ASMGateway::getUrl(),
            '#proxy'   => ASMGateway::enableProxy()
        ];
    }

    public function lost_animals(string $species)
    {
        return [
            '#theme'   => 'asm_lf_animals',
            '#animals' => ASMGateway::lf_animals(ASMGateway::MODE_LOST, self::filterForSpecies($species)),
            '#asm_url' => ASMGateway::getUrl(),
            '#proxy'   => ASMGateway::enableProxy(),
            '#title'   => 'Lost Animals',
            '#mode'    => ASMGateway::MODE_LOST
        ];
    }

    private function proxyImage(string $url, string $uniqid)
    {
        $cacheDir  = \Drupal::config('asm.settings')->get('asm_cache');
        $cacheFile = "$cacheDir/$uniqid";

        if (!is_file($cacheFile)) {
            if (!is_dir($cacheDir)) {
                  mkdir($cacheDir);
            }
            try {
                $client = \Drupal::httpClient();
                $client->request('GET', $url, ['sink'=>$cacheFile]);
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

    public function image(int $animal_id, int $imagenum)
    {
        $uniqid = "$animal_id-$imagenum";

        $url = ASMGateway::getUrl()."/service?method=animal_image&animalid=$animal_id";
        if ($imagenum > 1) { $url.="&seq=$imagenum"; }

        return $this->proxyImage($url, $uniqid);
    }

    public function media(int $media_id)
    {
        return $this->proxyImage(
            ASMGateway::getUrl()."/service?method=dbfs_image&title=$media_id.jpg",
            "media-$media_id"
        );
    }
}
