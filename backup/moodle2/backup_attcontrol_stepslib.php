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
 * Defines all the backup steps that will be used by {@link backup_attcontrol_activity_task}
 *
 * @package    mod_attcontrol
 * @copyright  2013 José Luis Antúnez<jantunez@xtec.cat>
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Defines the complete attcontrol structure for backup, with file and id annotations
 *
 * @copyright  2013 José Luis Antúnez<jantunez@xtec.cat>
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_attcontrol_activity_structure_step extends backup_activity_structure_step {

    /**
     * Define the structure of the backup workflow.
     *
     * @return restore_path_element $structure
     */
    protected function define_structure() {

        // Are we including userinfo?
        $userinfo = $this->get_setting_value('userinfo');

        // XML nodes declaration - non-user data.
        $attcontrol = new backup_nested_element('attcontrol', array('id'), array(
            'name'));

        $sessions = new backup_nested_element('sessions');
        $session  = new backup_nested_element('session', array('id'), array(
            'groupid', 'sessdate', 'duration', 'lasttaken', 'lasttakenby',
            'timemodified', 'description'));

        // XML nodes declaration - user data.
        $logs = new backup_nested_element('logs');
        $log  = new backup_nested_element('log', array('id'), array(
            'sessionid', 'studentid', 'statusid', 'lasttaken', 'timetaken', 'takenby'));

        $remarks = new backup_nested_element('remarks');
        $remark  = new backup_nested_element('remark', array('id'), array('remarks'));

        // Build the tree in the order needed for restore.
        $attcontrol->add_child($sessions);
        $sessions->add_child($session);

        $session->add_child($logs);
        $logs->add_child($log);

        $log->add_child($remarks);
        $remarks->add_child($remark);

        // Data sources - non-user data.

        $attcontrol->set_source_table('attcontrol', array('id' => backup::VAR_ACTIVITYID));

        $session->set_source_table('attcontrol_sessions', array('attcontrolid' => backup::VAR_PARENTID));

        // Data sources - user related data.
        if ($userinfo) {
            $log->set_source_table('attcontrol_log', array('sessionid' => backup::VAR_PARENTID));

            $remark->set_source_table('attcontrol_remarks', array('id' => backup::VAR_PARENTID));
        }

        // Id annotations.
        $session->annotate_ids('user', 'lasttakenby');
        $session->annotate_ids('group', 'groupid');
        $log->annotate_ids('user', 'studentid');
        $log->annotate_ids('user', 'takenby');

        // File annotations.
        $session->annotate_files('mod_attcontrol', 'session', 'id');

        // Return the root element (workshop), wrapped into standard activity structure.
        return $this->prepare_activity_structure($attcontrol);
    }
}
