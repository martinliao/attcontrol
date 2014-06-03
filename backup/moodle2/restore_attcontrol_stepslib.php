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

// This module is based in the original Attendance Module created by
// Artem Andreev <andreev.artem@gmail.com> - 2011

/**
 * Structure step to restore one attcontrol activity
 *
 * @package    mod_attcontrol
 * @copyright  2013 José Luis Antúnez<jantunez@xtec.cat>
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Define all the restore steps that will be used by the restore_attcontrol_activity_task
 *
 * @copyright  2013 José Luis Antúnez<jantunez@xtec.cat>
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_attcontrol_activity_structure_step extends restore_activity_structure_step {

    /**
     * Define the structure of the restore workflow.
     *
     * @return restore_path_element $structure
     */
    protected function define_structure() {

        $paths = array();

        $userinfo = $this->get_setting_value('userinfo'); // Are we including userinfo?

        // XML interesting paths - non-user data.
        $paths[] = new restore_path_element('attcontrol', '/activity/attcontrol');

        $paths[] = new restore_path_element('attcontrol_session',
                       '/activity/attcontrol/sessions/session');

        // End here if no-user data has been selected.
        if (!$userinfo) {
            return $this->prepare_activity_structure($paths);
        }

        // XML interesting paths - user data.
        $paths[] = new restore_path_element('attcontrol_log',
                       '/activity/attcontrol/sessions/session/logs/log');

        $paths[] = new restore_path_element('attcontrol_remarks',
                       '/activity/attcontrol/sessions/session/logs/log/remarks/remark');

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Process an attcontrol restore.
     *
     * @param object $data The data in object form
     * @return void
     */
    protected function process_attcontrol($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        // Insert the attcontrol record.
        $newitemid = $DB->insert_record('attcontrol', $data);
        // Immediately after inserting "activity" record, call this.
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Process attcontrol session restore
     * @param object $data The data in object form
     * @return void
     */
    protected function process_attcontrol_session($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->attcontrolid = $this->get_new_parentid('attcontrol');
        $data->groupid = $this->get_mappingid('group', $data->groupid);
        $data->sessdate = $this->apply_date_offset($data->sessdate);

        if ($this->get_setting_value('userinfo')) {
            $data->lasttaken = $this->apply_date_offset($data->lasttaken);
        }
        else {
            $data->lasttaken = null;
        }

        $data->lasttakenby = $this->get_mappingid('user', $data->lasttakenby);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $newitemid = $DB->insert_record('attcontrol_sessions', $data);
        $this->set_mapping('attcontrol_session', $oldid, $newitemid, true);
    }

    /**
     * Process attcontrol log restore
     * @param object $data The data in object form
     * @return void
     */
    protected function process_attcontrol_log($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->sessionid = $this->get_mappingid('attcontrol_session', $data->sessionid);
        $data->studentid = $this->get_mappingid('user', $data->studentid);
        $data->timetaken = $this->apply_date_offset($data->timetaken);
        $data->takenby = $this->get_mappingid('user', $data->takenby);

        $newitemid = $DB->insert_record('attcontrol_log', $data);
        

        $this->set_mapping('attcontrol_log', $oldid, $newitemid, true);
    }


    /**
     * Process attcontrol remarks restore
     * @param object $data The data in object form
     * @return void
     */
    protected function process_attcontrol_remarks($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->id = $this->get_mappingid('attcontrol_log', $data->id);

        //SQL insert, as we need to indicate the new id.
        $DB->execute("INSERT INTO {attcontrol_remarks} (id, remarks) VALUES (?,?)", array($data->id, $data->remarks) );
    }

    /**
     * Once the database tables have been fully restored, restore the files
     * @return void
     */
    protected function after_execute() {
        $this->add_related_files('mod_attcontrol', 'session', 'attcontrol_session');
    }
}
