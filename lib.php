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
 * Interface for the subplugintype trigger
 * It has to be implemented by all subplugins.
 *
 * @package tool_lifecycle_trigger
 * @subpackage semindependent
 * @copyright  2019 Tobias Reischmann WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lifecycle\trigger;

use tool_lifecycle\manager\settings_manager;
use tool_lifecycle\response\trigger_response;
use tool_lifecycle\settings_type;

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/../lib.php');
require_once(__DIR__ . '/../../lib.php');

/**
 * Class which implements the basic methods necessary for a lifecycle trigger subplugin
 * @package tool_lifecycle_trigger
 */
class semindependent extends base_automatic {


    /**
     * Checks the course and returns a repsonse, which tells if the course should be further processed.
     * @param $course object to be processed.
     * @param $triggerid int id of the trigger instance.
     * @return trigger_response
     */
    public function check_course($course, $triggerid) {
        // Everything is already in the sql statement.
        return trigger_response::trigger();
    }

    public function get_course_recordset_where($triggerid) {
        $include = settings_manager::get_settings($triggerid, settings_type::TRIGGER)['include'];
        if ($include) {
            $where = "{course}.startdate <= :semindepdate";
        } else {
            $where = "{course}.startdate > :semindepdate";
        }
        // Date before which a course counts as semester independent. In this case the 1.1.2000.
        $params = array(
            "semindepdate" => 946688400,
        );
        return array($where, $params);
    }

    public function get_subpluginname() {
        return 'semindependent';
    }

    public function instance_settings() {
        return array(
            new instance_setting('include', PARAM_BOOL)
        );
    }

    public function extend_add_instance_form_definition($mform) {
        $mform->addElement('checkbox', 'include', get_string('include', 'lifecycletrigger_semindependent'));
        $mform->addHelpButton('include', 'include', 'lifecycletrigger_semindependent');
    }

    public function extend_add_instance_form_definition_after_data($mform, $settings) {
        if (is_array($settings) && array_key_exists('include', $settings)) {
            $default = $settings['include'];
        } else {
            $default = true;
        }
        $mform->setDefault('include', $default);
    }
}
