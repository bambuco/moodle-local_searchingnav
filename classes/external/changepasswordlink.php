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
 * This class contains the changepasswordlink webservice functions.
 *
 * @package    local_searchingnav
 * @copyright  2024 David Herney @ BambuCo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_searchingnav\external;

use external_api;
use external_function_parameters;
use external_value;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/login/lib.php');

/**
 * Service implementation.
 *
 * @copyright   2024 David Herney - cirano
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class changepasswordlink extends external_api {

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new \external_function_parameters(
            [
                'userid' => new \external_value(PARAM_INT, 'The user id', VALUE_REQUIRED),
            ]
        );
    }

    /**
     * Returns the transaction information about the buy.
     *
     * @param int $userid
     * @return string
     */
    public static function execute(int $userid): ?string {
        global $PAGE, $DB, $CFG;

        if (!isloggedin() || isguestuser()) {
            require_login(null, false);
        }

        $syscontext = \context_system::instance();
        $PAGE->set_context($syscontext);

        self::validate_parameters(self::execute_parameters(), [
            'userid' => $userid,
        ]);

        $user = $DB->get_record('user', ['id' => $userid], '*', MUST_EXIST);

        if (isguestuser($user) || is_siteadmin($user)) {
            throw new \moodle_exception('invalidusertochangepassword', 'local_searchingnav');
        }

        // Create a new auth object acording to the user's auth method.
        $authplugin = get_auth_plugin($user->auth);

        if ($authplugin->can_change_password()) {
            $url = $authplugin->change_password_url();

            if ($url) {
                return $url;
            }

            // The account the requesting user claims to be is entitled to change their password.
            // Next, check if they have an existing password reset in progress.
            $resetinprogress = $DB->get_record('user_password_resets', ['userid' => $userid]);
            if (empty($resetinprogress)) {
                // Completely new reset request - common case.
                $resetrecord = core_login_generate_password_reset($user);
            } else if ($resetinprogress->timerequested < (time() - $CFG->pwresettime)) {
                // Preexisting, but expired request - delete old record & create new one.
                // Uncommon case - expired requests are cleaned up by cron.
                $DB->delete_records('user_password_resets', ['id' => $resetinprogress->id]);
                $resetrecord = core_login_generate_password_reset($user);
            } else if (empty($resetinprogress->timererequested)) {
                // Preexisting, valid request. This is the first time user has re-requested the reset.
                // Re-sending the same email once can actually help in certain circumstances
                // eg by reducing the delay caused by greylisting.
                $resetinprogress->timererequested = time();
                $DB->update_record('user_password_resets', $resetinprogress);
                $resetrecord = $resetinprogress;
            } else {
                // Preexisting, valid request. User has already re-requested email.
                $resetrecord = $resetinprogress;
            }

            $url = $CFG->wwwroot;

            if ($CFG->registerauth == 'customized') {
                $url .= '/auth/customized/forgot_password.php';
            } else {
                $url .= '/login/forgot_password.php';
            }
            $url .= '?token=' . $resetrecord->token;

            return $url;
        }

        return null;
    }

    /**
     * Returns description of method result value.
     *
     * @return external_value
     */
    public static function execute_returns(): external_value {
        return new \external_value(PARAM_TEXT, 'The URL to change the user password.', VALUE_OPTIONAL, null, NULL_ALLOWED);
    }
}
