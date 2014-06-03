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
 * Web service for mod attcontrol
 * 
 * @package mod_attcontrol
 * @subpackage db
 * @since Moodle 2.4
 * @copyright 2014 José Luis Antúnez Reales
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$services = array (
		'mod_attcontrolservice' => array (
				'functions' => array (
                        'mod_attcontrol_get_user',
						'mod_attcontrol_get_sessions' ,
                        'mod_attcontrol_get_session_take' ,
                        'mod_attcontrol_get_attendance_statuses',
                        'mod_attcontrol_save_session_take',
                        'mod_attcontrol_get_attcourses',
                        'mod_attcontrol_save_new_session',
                        'mod_attcontrol_save_edit_session',
                        'mod_attcontrol_delete_session',
                        'mod_attcontrol_get_students',
                        'mod_attcontrol_get_reports',
                        'mod_attcontrol_get_reportsummary',
                        'mod_attcontrol_save_report_data',
				),
				'requiredcapability' => '',
				'restrictedusers' => 0,
				'enabled' => 1,
				'shortname' => 'mod_attcontrolservice',
				'downloadfiles' => 1,
				'uploadfiles' => 1,
				'requiredcapability' => ''
		 )
);

$functions = array (
        'mod_attcontrol_get_user' => array (
                'classname' => 'mod_attcontrol_external',
                'methodname' => 'get_user',
                'classpath' => 'mod/attcontrol/externallib.php',
                'description' => 'Get the personal information of the logged user.',
                'type' => 'read'
        ),
		'mod_attcontrol_get_sessions' => array (
				'classname' => 'mod_attcontrol_external',
				'methodname' => 'get_sessions',
				'classpath' => 'mod/attcontrol/externallib.php',
				'description' => 'Get the users sessions between two dates.',
                'type' => 'read'
		),
        'mod_attcontrol_get_session_take' => array (
            'classname' => 'mod_attcontrol_external',
            'methodname' => 'get_session_take',
            'classpath' => 'mod/attcontrol/externallib.php',
            'description' => 'Get the information for doing the attendance take for a session.',
            'type' => 'read'
        ),
        'mod_attcontrol_get_attendance_statuses' => array (
            'classname' => 'mod_attcontrol_external',
            'methodname' => 'get_attendance_statuses',
            'classpath' => 'mod/attcontrol/externallib.php',
            'description' => 'Get the status for this attendance instance.',
            'type' => 'read'
        ),
        'mod_attcontrol_save_session_take' => array (
            'classname' => 'mod_attcontrol_external',
            'methodname' => 'save_session_take',
            'classpath' => 'mod/attcontrol/externallib.php',
            'description' => 'Saves the information after doing the attendance take for a session.',
            'type' => 'write'
        ),
        'mod_attcontrol_get_attcourses' => array (
                'classname' => 'mod_attcontrol_external',
                'methodname' => 'get_attendance_attcourses',
                'classpath' => 'mod/attcontrol/externallib.php',
                'description' => 'Get the course and the attendance instances for a user.',
                'type' => 'read'
        ),
        'mod_attcontrol_save_new_session' => array (
                'classname' => 'mod_attcontrol_external',
                'methodname' => 'save_new_session',
                'classpath' => 'mod/attcontrol/externallib.php',
                'description' => 'Saves a new generated session.',
                'type' => 'write'
        ),
        'mod_attcontrol_save_edit_session' => array (
                'classname' => 'mod_attcontrol_external',
                'methodname' => 'save_edit_session',
                'classpath' => 'mod/attcontrol/externallib.php',
                'description' => 'Saves an edited session.',
                'type' => 'write'
        ),
        'mod_attcontrol_delete_session' => array (
                'classname' => 'mod_attcontrol_external',
                'methodname' => 'delete_session',
                'classpath' => 'mod/attcontrol/externallib.php',
                'description' => 'Deletes an existing session.',
                'type' => 'write'
        ),
        'mod_attcontrol_get_students' => array (
                'classname' => 'mod_attcontrol_external',
                'methodname' => 'get_students',
                'classpath' => 'mod/attcontrol/externallib.php',
                'description' => 'Gets all the students of the attcontrols acoording to the filters.',
                'type' => 'read'
        ),
        'mod_attcontrol_get_reports' => array (
                'classname' => 'mod_attcontrol_external',
                'methodname' => 'get_reports',
                'classpath' => 'mod/attcontrol/externallib.php',
                'description' => 'Gets the report, according to the parameters specified.',
                'type' => 'read'
        ),
        'mod_attcontrol_get_reportsummary' => array (
                'classname' => 'mod_attcontrol_external',
                'methodname' => 'get_reportsummary',
                'classpath' => 'mod/attcontrol/externallib.php',
                'description' => 'Gets the summary for the report, according to the parameters specified.',
                'type' => 'read'
        ),
        'mod_attcontrol_save_report_data' => array (
            'classname' => 'mod_attcontrol_external',
            'methodname' => 'save_report_data',
            'classpath' => 'mod/attcontrol/externallib.php',
            'description' => 'Saves the information after doing the attendance take for a session.',
            'type' => 'write'
        ),
);