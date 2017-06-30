<?php
/**
 * Upcasts the animal_id in the route to an animal data array
 *
 * @see https://www.drupal.org/docs/8/api/routing-system/parameter-upcasting-in-routes
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
namespace Drupal\asm\ParamConverter;

use Drupal\asm\ASMGateway;
use Drupal\Core\ParamConverter\ParamConverterInterface;
use Symfony\Component\Routing\Route;

class AnimalParamConverter implements ParamConverterInterface
{
    public function convert($value, $definition, $name, array $defaults)
    {
        return ASMGateway::animal($value);
    }

    public function applies($definition, $name, Route $route)
    {
        return (!empty($definition['type']) && $definition['type']=='animal_id');
    }
}
