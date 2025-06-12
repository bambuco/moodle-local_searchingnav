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
 * Upgrade code for the local_searchingnav plugin.
 *
 * @package    local
 * @subpackage searchingnav
 * @copyright  2025 David Herney @ BambuCo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
function xmldb_local_searchingnav_upgrade($oldversion = 0) {
    global $CFG, $DB;

    require_once($CFG->libdir . '/db/upgradelib.php'); // Core Upgrade-related functions.

    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.

    if ($oldversion < 2023081602) {

        // Changing precision of field resourcetype.
        $table = new xmldb_table('local_searchingnav');
        $field = new xmldb_field('resourcetype', XMLDB_TYPE_CHAR, '511', null, XMLDB_NOTNULL);

        // Launch change of precision for field.
        $dbman->change_field_precision($table, $field);

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2023081602, 'local', 'searchingnav');
    }

    return true;
}
