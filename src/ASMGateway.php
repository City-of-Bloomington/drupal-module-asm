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
declare (strict_types=1);
namespace Drupal\asm;

class ASMGateway
{
    const SPECIESNAME   = 'SPECIESNAME';
    const SPECIES_CAT   = 'Cat';
    const SPECIES_DOG   = 'Dog';
    const SPECIES_OTHER = 'Other';

    const MODE_LOST     = 'lost';
    const MODE_FOUND    = 'found';

    const DATE_BIRTH    = 'DATEOFBIRTH';
    const AGE           = 'ANIMALAGE';

    public static function getUrl()      { return \Drupal::config('asm.settings')->get('asm_url'); }
    public static function enableProxy() { return \Drupal::config('asm.settings')->get('asm_proxy') ? true : false; }

    /**
     * @param  string $url
     * @return array        The JSON data
     */
    private static function doJsonQuery($url)
    {
        $client = \Drupal::httpClient();
        $response = $client->get($url);
        return json_decode($response->getBody()->__toString(), true);
    }

    private static function filter(array &$results, array $fields)
    {
        foreach ($results as $i=>$row) {
            foreach ($fields as $k=>$v) {
                switch ($k) {
                    case self::SPECIESNAME:
                        switch ($v) {
                            case self::SPECIES_CAT:
                            case self::SPECIES_DOG:
                                if ($row[$k] != $v) { unset($results[$i]); }
                            break;

                            case self::SPECIES_OTHER:
                                if ($row[$k]==self::SPECIES_CAT || $row[$k]==self::SPECIES_DOG) { unset($results[$i]); }
                            break;
                        }
                    break;
                }
            }
        }
    }

    /**
     * @param  array  $fields An array of key,values to filter results
     * @return array          The JSON data from the response
     */
    public static function adoptable_animals(array $fields=null)
    {
        $config = \Drupal::config('asm.settings');
        $ASM    = $config->get('asm_url');
        $url    = $ASM.'/service?'.http_build_query([
            'method'   => 'json_adoptable_animals',
            'username' => $config->get('asm_user'),
            'password' => $config->get('asm_pass')
        ], '', '&');
        $results = self::doJsonQuery($url);
        if ($fields && $results) {
            self::filter($results, $fields);
        }
        return $results;
    }

    /**
     * @param  int   $animal_id The Animal ID
     * @return array            The JSON data from the response
     */
    public static function adoptable_animal(int $animal_id)
    {
        static $cache = [];

        if (empty($cache[$animal_id]))  {
            $config = \Drupal::config('asm.settings');
            $ASM    = $config->get('asm_url');
            $url    = $ASM.'/service?'.http_build_query([
                'method'   => 'json_adoptable_animal',
                'animalid' => $animal_id,
                'username' => $config->get('asm_user'),
                'password' => $config->get('asm_pass')
            ], '', '&');
            $json = self::doJsonQuery($url);
            $cache[$animal_id] = isset($json[0]) ? $json[0] : [];
        }
        return $cache[$animal_id];
    }

    /**
     * @param  string $mode  'lost' || 'found'
     * @return array
     */
    public static function lf_animals(string $mode, array $fields=null)
    {
        $config = \Drupal::config('asm.settings');
        $ASM    = $config->get('asm_url');
        $url    = $ASM.'/service?'.http_build_query([
            'method'   => $mode == self::MODE_LOST ? 'json_lost_animals' : 'json_found_animals',
            'username' => $config->get('asm_user'),
            'password' => $config->get('asm_pass')
        ], '', '&');
        $results = self::doJsonQuery($url);
        if ($fields && $results) {
            self::filter($results, $fields);
        }
        return $results;
    }

    /**
     * @param  int   $lfid The Lost & Found ID for a found animal
     * @return array       The JSON data array from response
     */
    public static function lf_animal(string $mode, int $lfid=null)
    {
        $animals = self::lf_animals($mode);
        foreach ($animals as $a) {
            if ($a['LFID'] == $lfid) {
                return $a;
            }
        }
    }

    /**
     * @return array       The JSON data array from response
     */
    public static function held_animals(): ?array
    {
        $config = \Drupal::config('asm.settings');
        $ASM    = $config->get('asm_url');
        $url    = $ASM.'/service?'.http_build_query([
            'method'   => 'json_held_animals',
            'username' => $config->get('asm_user'),
            'password' => $config->get('asm_pass')
        ], '', '&');
        $results = self::doJsonQuery($url);
        return $results;
    }
}
