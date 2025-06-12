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

use block_completion_progress\defaults;

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
     * List of modules that are considered activities.
     *
     * @return array
     */
    public const ACTIVITIES = [
        'mod_assign',
        'mod_data',
        'mod_feedback',
        'mod_forum',
        'mod_lesson',
        'mod_quiz',
        'mod_scorm',
        'mod_workshop',
    ];

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

        // If the resource is not an activity, it is understood that it is always available.
        if (!in_array($info[0], self::ACTIVITIES)) {
            return true;
        }

        $opendate = 0;
        $closedate = 0;
        switch ($info[0]) {
            case 'mod_assign':
                $instance = $DB->get_record('assign', ['id' => $resource->get('itemid')]);
                $closedate = $instance->duedate;
            break;
            case 'mod_data':
                $instance = $DB->get_record('data', ['id' => $resource->get('itemid')]);
                $opendate = $instance->timeavailablefrom;
                $closedate = $instance->timeavailableto;
            break;
            case 'mod_feedback':
                $instance = $DB->get_record('feedback', ['id' => $resource->get('itemid')]);
                $opendate = $instance->timeopen;
                $closedate = $instance->timeclose;
            break;
            case 'mod_forum':
                $instance = $DB->get_record('forum', ['id' => $resource->get('itemid')]);
                $closedate = $instance->duedate;
            break;
            case 'mod_lesson':
                $instance = $DB->get_record('lesson', ['id' => $resource->get('itemid')]);
                $opendate = $instance->available;
                $closedate = $instance->deadline;
            break;
            case 'mod_quiz':
                $instance = $DB->get_record('quiz', ['id' => $resource->get('itemid')]);
                $closedate = $instance->timeclose;
            break;
            case 'mod_scorm':
                $instance = $DB->get_record('scorm', ['id' => $resource->get('itemid')]);
                $opendate = $instance->timeopen;
                $closedate = $instance->timeclose;
            break;
            case 'mod_workshop':
                $instance = $DB->get_record('workshop', ['id' => $resource->get('itemid')]);
                $opendate = $instance->submissionstart;
                $closedate = $instance->submissionend;
            break;
        }

        $opendate = (int) $opendate;
        $closedate = (int) $closedate;

        if ($opendate == 0 && $closedate == 0) {
            return true;
        }

        $closedate = $closedate == 0 ? PHP_INT_MAX : $closedate;

        if ($startdate == 0 || $enddate == 0 || $startdate == $enddate) {

            // If only one of the dates is specified or if both dates are the same,
            // it is understood that these are the activities available at that specific time.
            $checkdate = $startdate == $enddate ? $startdate : ($startdate + $enddate);
            return $checkdate <= $closedate && $checkdate >= $opendate;
        }

        return $opendate <= $enddate && $closedate >= $startdate;

    }
}
