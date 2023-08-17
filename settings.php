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
 * Plugin settings.
 *
 * @package    local_searchingnav
 * @copyright  2023 David Herney @ BambuCo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    $ADMIN->add('localplugins', new admin_category('local_searchingnav', get_string('pluginname', 'local_searchingnav')));

    $settings = new admin_settingpage('local_searchingnav_settings', get_string('general', 'local_searchingnav'));
    $ADMIN->add('local_searchingnav', $settings);

    // Get site glossaries.
    $glossaries = $DB->get_records_menu('glossary', ['course' => SITEID, 'displayformat' => 'faq'], 'name', 'id, name');

    if ($glossaries) {
        // Glossaries to FAQ answers.
        $name = 'local_searchingnav/faqids';
        $title = get_string('faqids', 'local_searchingnav');
        $help = get_string('faqids_help', 'local_searchingnav');
        $setting = new admin_setting_configmultiselect($name, $title, $help, [], $glossaries);
        $settings->add($setting);
    }

}
