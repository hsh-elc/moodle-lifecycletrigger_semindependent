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

namespace tool_lifecycle\trigger;

use tool_lifecycle\processor;
use tool_lifecycle_trigger_semindependent_generator as generator;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lib.php');
require_once(__DIR__ . '/generator/lib.php');

/**
 * Trigger test for semester independent trigger.
 *
 * @package    tool_lifecycle_trigger
 * @category   semindependent
 * @group tool_lifecycle_trigger
 * @copyright  2017 Tobias Reischmann WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_lifecycle_trigger_semindependent_testcase extends \advanced_testcase {

    /**@var processor Instance of the lifecycle processor */
    private $processor;

    private $semindependentcourse;
    private $semcourse;

    public function setUp() {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $this->processor = new processor();

        $this->semindependentcourse = $this->getDataGenerator()->create_course(array('startdate' => 915152400));
        $this->semcourse = $this->getDataGenerator()->create_course(array('startdate' => time()));
    }

    /**
     * Tests if trigger for inclusion of semester independent courses works as expected.
     */
    public function test_include_semester_independent() {

        $this->triggerinstance = generator::create_workflow_with_semindependent(true);

        $recordset = $this->processor->get_course_recordset([$this->triggerinstance], []);
        $foundsem = false;
        $foundsemindep = false;
        foreach ($recordset as $element) {
            if ($this->semcourse->id === $element->id) {
                $foundsem = true;
                break;
            }
            if ($this->semindependentcourse->id === $element->id) {
                $foundsemindep = true;
                break;
            }
        }
        $this->assertFalse($foundsem, 'The semester course should not have been triggered');
        $this->assertTrue($foundsemindep, 'The semester independent course should have been triggered');
    }

    /**
     * Tests if trigger for exclusion of semester independent courses works as expected.
     */
    public function test_exclude_semester_independent() {

        $this->triggerinstance = generator::create_workflow_with_semindependent(false);

        $recordset = $this->processor->get_course_recordset([$this->triggerinstance], []);
        $foundsem = false;
        $foundsemindep = false;
        foreach ($recordset as $element) {
            if ($this->semcourse->id === $element->id) {
                $foundsem = true;
                break;
            }
            if ($this->semindependentcourse->id === $element->id) {
                $foundsemindep = true;
                break;
            }
        }
        $this->assertTrue($foundsem, 'The semester course should not have been triggered');
        $this->assertFalse($foundsemindep, 'The semester independent course should have been triggered');
    }
}