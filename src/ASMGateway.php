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
namespace Drupal\asm;

use Drupal\Core\Site\Settings;

class ASMGateway
{
    /**
     * @param  string $url
     * @return array        The JSON data
     */
    private static function doJsonQuery($url)
    {
        $client   = \Drupal::httpClient();
        try {
            $response = $client->get($url);
            return json_decode($response->getBody(), true);
        }
        catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @return array The JSON data from the response
     */
    public static function animals()
    {
        $config = \Drupal::config('asm.settings');
        $ASM    = $config->get('asm_url');
        $url    = $ASM.'/service?'.http_build_query([
            'method'   => 'json_adoptable_animals',
            'username' => $config->get('asm_user'),
            'password' => $config->get('asm_pass')
        ]);
        return self::doJsonQuery($url);
    }

    /**
     * @return array The JSON data from the response
     */
    public static function animal()
    {
    }
}
