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

defined('MOODLE_INTERNAL') || die();

$plugin->version  = 2021050701;  // The current module version (Date: YYYYMMDDXX)
$plugin->requires = 2019111806;  // Requires this Moodle version (moodle 2.9.x)
$plugin->component = 'local_searchingnav';
$plugin->dependencies = array();
$plugin->release  = '0.0.1';       // Human-friendly version name.
//MATURITY_ALPHA, MATURITY_BETA, MATURITY_RC, MATURITY_STABLE
$plugin->maturity = MATURITY_ALPHA;
