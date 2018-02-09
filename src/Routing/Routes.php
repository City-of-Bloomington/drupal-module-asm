<?php
/**
 * @copyright 2017-2018 City of Bloomington, Indiana
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
namespace Drupal\asm\Routing;

use Symfony\Component\Routing\Route;

class Routes
{
    public function routes()
    {
        $config  = \Drupal::config('asm.settings');
        $base    = $config->get('asm_route');

        if (!$base) { $base = '/asm'; }
        return [
            'asm.image' => new Route(
                "$base/animals/{animal_id}/image/{imagenum}",
                [
                    '_controller' => '\Drupal\asm\Controller\ASMController::image',
                    'imagenum'    => 1
                ],
                [ '_permission' => 'access content' ]
            ),
            'asm.media' => new Route(
                "$base/media/{media_id}",
                ['_controller'  => '\Drupal\asm\Controller\ASMController::media'],
                [
                    '_permission' => 'access content',
                    'media_id'    => '^[0-9]+$'
                ],
                [
                    'parameters' => [
                        'media_id' => ['type' => 'Integer']
                    ]
                ]
            ),

            'asm.adoptable_animal' => new Route(
                "$base/animals/{animal_id}",
                [
                    '_controller'     => '\Drupal\asm\Controller\ASMController::adoptable_animal',
                    '_title_callback' => '\Drupal\asm\Controller\ASMController::title'
                ],
                [
                    '_permission' => 'access content',
                    'animal_id'   => '^[0-9]+$'
                ],
                [
                    'parameters' => [
                        'animal_id' => ['type' => 'Integer']
                    ]
                ]
            ),
            'asm.adoptable_animals' => new Route(
                "$base/animals/{species}",
                [
                    '_controller' => '\Drupal\asm\Controller\ASMController::adoptable_animals',
                    '_title'      => 'Adoptable Animals',
                    'species'     => 'All'
                ],
                [
                    '_permission' => 'access content',
                    'species'     => '^All|Cat|Dog|Other$'
                ]
            ),
            'asm.found_animal' => new Route(
                "$base/found/{lfid}",
                [
                    '_controller' => '\Drupal\asm\Controller\ASMController::found_animal',
                    '_title'      => 'Found Animals'
                ],
                [
                    '_permission' => 'access content',
                    'lfid'        => '^[0-9]+$'
                ],
                [
                    'parameters' => [
                        'lfid' => ['type' => 'Integer']
                    ]
                ]
            ),
            'asm.found_animals' => new Route(
                "$base/found/{species}",
                [
                    '_controller' => '\Drupal\asm\Controller\ASMController::found_animals',
                    '_title'      => 'Found Animals',
                    'species'     => 'All'
                ],
                [
                    '_permission' => 'access content',
                    'species'     => '^All|Cat|Dog|Other$'
                ]
            )
        ];
    }
}
