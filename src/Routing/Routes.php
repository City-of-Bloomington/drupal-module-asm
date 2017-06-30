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

            'asm.animal' => new Route(
                "$base/animals/{animal}",
                [
                    '_controller'     => '\Drupal\asm\Controller\ASMController::animal',
                    '_title_callback' => '\Drupal\asm\Controller\ASMController::title'
                ],
                [
                    '_permission' => 'access content'
                ],
                [
                    'parameters' => [
                        'animal' => ['type' => 'animal_id']
                    ]
                ]
            ),
            'asm.animals' => new Route(
                "$base/animals",
                [
                    '_controller' => '\Drupal\asm\Controller\ASMController::animals',
                    '_title'      => 'Adoptable Animals'
                ],
                [
                    '_permission' => 'access content'
                ]
            )
        ];
    }
}
