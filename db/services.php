<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * "Searching nav" external functions and service definitions.
 *
 * @package    local
 * @subpackage searchingnav
 * @copyright  2021 David Herney @ BambuCo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$functions = array(
    'local_searchingnav_get_search' => array(
        'classname' => '\local_searchingnav\external',
        'methodname' => 'get_search',
        'classpath' => 'local/searchingnav/classes/externallib.php',
        'description' => 'Get resources according a searching text',
        'type' => 'read',
        'capabilities' => 'local/searchingnav:view'
    )
);

$services = array(
        'Searching nav plugin webservices' => array(
                'functions' => array ('local_searchingnav_get_search'),
                'restrictedusers' => 1, // if 1, the administrator must manually select which user can use this service.
                // (Administration > Plugins > Web services > Manage services > Authorised users)
                'enabled' => 0, // if 0, then token linked to this service won't work
                'shortname' => 'local_searchingnav_ws'
        )
);
