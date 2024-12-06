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

namespace local_searchingnav\local;

/**
 * Class controller
 *
 * @package    local
 * @subpackage searchingnav
 * @copyright  2024 David Herney @ BambuCo
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class controller {

    /**
     * Check if the resource is between the dates
     *
     * @param \core_search\document $resource
     * @param int $startdate
     * @param int $enddate
     * @return bool
     */
    public static function check_resourcebetweendates($resource, $startdate, $enddate): bool {
        global $DB;

        $info = explode("-", $resource->get('areaid'));

        switch ($info[0]) {
            case 'mod_assign':
                $cm = get_coursemodule_from_instance('assign', $resource->get('itemid'));
                $context = \context_module::instance($cm->id);
                $instance = new \assign($context, $cm, $resource->get('courseid'));
                if (empty($instance->duedate)) {
                    return true;
                }
                return $instance->duedate >= $startdate && $instance->duedate <= $enddate;
            case 'mod_quiz':
                $cm = get_coursemodule_from_instance('quiz', $resource->get('itemid'));
                $context = \context_module::instance($cm->id);
                $instance = new \quiz($context, $cm, $resource->get('courseid'));
                if (empty($instance->timeclose)) {
                    return true;
                }
                return $instance->timeclose >= $startdate && $instance->timeclose <= $enddate;
            case 'mod_scorm':
                $scorm = $DB->get_record('scorm', ['id' => $resource->get('itemid')]);
                if (empty($scorm->timeclose) && empty($scorm->timeopen)) {
                    return true;
                }

                if (empty($scorm->timeclose)) {
                    return $scorm->timeopen >= $startdate && $scorm->timeopen <= $enddate;
                }

                return $scorm->timeclose >= $startdate && $scorm->timeclose <= $enddate;
            case 'mod_data':
                $instance = $DB->get_record('data', ['id' => $resource->get('itemid')]);
                if (empty($instance->timeavailablefrom) && empty($instance->timeavailableto)) {
                    return true;
                }

                if (empty($instance->timeavailablefrom)) {
                    return $instance->timeavailableto >= $startdate && $instance->timeavailableto <= $enddate;
                }

                return $instance->timeavailablefrom >= $startdate && $instance->timeavailablefrom <= $enddate;
        }

        return true;
    }
}
