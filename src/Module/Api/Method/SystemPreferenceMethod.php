<?php
/*
 * vim:set softtabstop=4 shiftwidth=4 expandtab:
 *
 * LICENSE: GNU Affero General Public License, version 3 (AGPL-3.0-or-later)
 * Copyright 2001 - 2020 Ampache.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 */

declare(strict_types=0);

namespace Ampache\Module\Api\Method;

use Ampache\Model\Preference;
use Ampache\Model\User;
use Ampache\Module\Api\Api;
use Ampache\Module\Api\Xml_Data;
use Ampache\Module\System\Session;

final class SystemPreferenceMethod
{
    /**
     * system_preference
     * MINIMUM_API_VERSION=430000
     *
     * Get your system preferences by name
     *
     * @param array $input
     * filter = (string) Preference name e.g ('notify_email', 'ajax_load')
     * action = (integer) $artist_id, $album_id, $song_id)
     * @return boolean
     */
    public static function system_preference($input)
    {
        if (!Api::check_parameter($input, array('filter'), 'system_preference')) {
            return false;
        }
        $user = User::get_from_username(Session::username($input['auth']));
        if (!Api::check_access('interface', 100, $user->id)) {
            return false;
        }
        $pref_name  = (string) $input['filter'];
        $preference = Preference::get($pref_name, -1);
        if (empty($preference)) {
            Api::message('error', 'not found: ' . $pref_name, '404', $input['api_format']);

            return false;
        }
        $output_array = array('preferences' => $preference);
        switch ($input['api_format']) {
            case 'json':
                echo json_encode($output_array, JSON_PRETTY_PRINT);
                break;
            default:
                echo XML_Data::object_array($output_array['preferences'], 'preferences', 'pref');
        }
        Session::extend($input['auth']);

        return true;
    }
}
