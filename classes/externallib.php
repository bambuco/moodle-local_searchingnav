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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * External Searching nav API
 *
 * @package    local
 * @subpackage searchingnav
 * @copyright  2021 David Herney @ BambuCo
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_searchingnav;

defined('MOODLE_INTERNAL') || die();

require_once $CFG->libdir . '/externallib.php';
require_once $CFG->dirroot . '/local/searchingnav/locallib.php';


/**
 * External WS lib.
 *
 * @copyright 2021 David Herney @ BambuCo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external extends \external_api {

    /**
     * To validade input parameters
     * @return external_function_parameters
     */
    public static function get_search_parameters() {
        return new \external_function_parameters(
              array(
                  'courseid'        => new \external_value(PARAM_INT, 'Course id to search in', VALUE_REQUIRED),
                  'search'            => new \external_value(PARAM_TEXT, 'Text to search', VALUE_DEFAULT, ''),
                  'resourcetype'    => new \external_value(PARAM_TEXT, 'Resource type (mod basic name)', VALUE_DEFAULT, ''),
                  'userid'          => new \external_value(PARAM_INT, 'Filter by user id', VALUE_DEFAULT, 0),
               )
        );
    }


    public static function get_search($courseid, $search, $resourcetype, $userid) {
        global $DB, $USER, $CFG;

        $found = array();
        $responselog = new \stdClass();
        $responselog->length = 0;

        $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

        $indexingenabled = \core_search\manager::is_indexing_enabled();

        if (!$indexingenabled) {
            return $found;
        }

        $searchmanager = \core_search\manager::instance();

        $data = (object)['q' => $search, 'courseids' => [$courseid]];

        if (!empty($resourcetype)) {
            $data->areaids = explode(',', $resourcetype);
        }

        if (!empty($userid)) {
            $data->userids = [$userid];
        }

        $results = $searchmanager->search($data);
        $responselog->length = count($results);

        foreach ($results as $result) {
            $title = $result->get('title');

            $resource = new \stdClass();
            $resource->name = ($title !== '') ? $title : get_string('notitle', 'search');
            $resource->url = (string)$result->get_doc_url();
            $resource->type = $result->get('areaid');
            $found[] = $resource;
        }

        if ($CFG->debugdeveloper) {
            $responselog->resources = $found;
        }

        // Save the current search.
        $data = new \stdClass();
        $data->courseid = $courseid;
        $data->userid = $userid;
        $data->resourcetype = $resourcetype;
        $data->search = $search;
        $data->timecreated = time();
        $data->responselog = json_encode($responselog);
        $DB->insert_record('local_searchingnav', $data);

        return $found;
    }

    /**
     * Validate the return value
     * @return external_single_structure
     */
    public static function get_search_returns() {
        return new \external_multiple_structure(
                    new \external_single_structure(
                        array(
                            'name'      => new \external_value(PARAM_TEXT, 'Resource name'),
                            'url'       => new \external_value(PARAM_TEXT, 'Url to resource'),
                            'type'      => new \external_value(PARAM_TEXT, 'Resource type'),
                        ), 'Resource found information'
                    ), 'List of found resources'
            );
    }


}
