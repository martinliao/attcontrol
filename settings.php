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
* attcontrol module settings
*
* @package mod_attcontrol
* @copyright 2013 José Luis Antúnez <jantunez@xtec.cat>
* @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    require_once(dirname(__FILE__).'/lib.php');

    //Created a new settings page in order to have the different attenance statuses as global parameters.

    $settings->add(new admin_setting_heading('attcontrol_status1', get_string('status1', 'attcontrol'), ""));

    $settings->add(new admin_setting_configtext('attcontrol/status1', get_string('status1', 'attcontrol'),
                       get_string('configstatus1', 'attcontrol'), "P"));
    $settings->add(new admin_setting_configtext('attcontrol/statusdesc1', get_string('statusdesc1', 'attcontrol'),
                       get_string('configstatusdesc1', 'attcontrol'), "Present"));



    $settings->add(new admin_setting_heading('attcontrol_status2', get_string('status2', 'attcontrol'), ""));

    $settings->add(new admin_setting_configtext('attcontrol/status2', get_string('status2', 'attcontrol'),
                       get_string('configstatus2', 'attcontrol'), "L"));
    $settings->add(new admin_setting_configtext('attcontrol/statusdesc2', get_string('statusdesc2', 'attcontrol'),
                       get_string('configstatusdesc2', 'attcontrol'), "Late"));



    $settings->add(new admin_setting_heading('attcontrol_status3', get_string('status3', 'attcontrol'), ""));

    $settings->add(new admin_setting_configtext('attcontrol/status3', get_string('status3', 'attcontrol'),
                       get_string('configstatus3', 'attcontrol'), "A"));
    $settings->add(new admin_setting_configtext('attcontrol/statusdesc3', get_string('statusdesc3', 'attcontrol'),
                       get_string('configstatusdesc3', 'attcontrol'), "Absent"));


    $settings->add(new admin_setting_heading('attcontrol_status4', get_string('status4', 'attcontrol'), ""));

    $settings->add(new admin_setting_configtext('attcontrol/status4', get_string('status4', 'attcontrol'),
                       get_string('configstatus4', 'attcontrol'), "E"));
    $settings->add(new admin_setting_configtext('attcontrol/statusdesc4', get_string('statusdesc4', 'attcontrol'),
                       get_string('configstatusdesc4', 'attcontrol'), "Excused"));


    $settings->add(new admin_setting_heading('attcontrol_status5', get_string('status5', 'attcontrol'), ""));

    $settings->add(new admin_setting_configtext('attcontrol/status5', get_string('status5', 'attcontrol'),
                       get_string('configstatus5', 'attcontrol'), ""));
    $settings->add(new admin_setting_configtext('attcontrol/statusdesc5', get_string('statusdesc5', 'attcontrol'),
                       get_string('configstatusdesc5', 'attcontrol'), ""));


    $settings->add(new admin_setting_heading('attcontrol_status6', get_string('status6', 'attcontrol'), ""));

    $settings->add(new admin_setting_configtext('attcontrol/status6', get_string('status6', 'attcontrol'),
                       get_string('configstatus6', 'attcontrol'), ""));
    $settings->add(new admin_setting_configtext('attcontrol/statusdesc6', get_string('statusdesc6', 'attcontrol'),
                       get_string('configstatusdesc6', 'attcontrol'), ""));


    $settings->add(new admin_setting_heading('attcontrol_status7', get_string('status7', 'attcontrol'), ""));

    $settings->add(new admin_setting_configtext('attcontrol/status7', get_string('status7', 'attcontrol'),
                       get_string('configstatus7', 'attcontrol'), ""));
    $settings->add(new admin_setting_configtext('attcontrol/statusdesc7', get_string('statusdesc7', 'attcontrol'),
                       get_string('configstatusdesc7', 'attcontrol'), ""));


    $settings->add(new admin_setting_heading('attcontrol_status8', get_string('status8', 'attcontrol'), ""));

    $settings->add(new admin_setting_configtext('attcontrol/status8', get_string('status8', 'attcontrol'),
                       get_string('configstatus8', 'attcontrol'), ""));
    $settings->add(new admin_setting_configtext('attcontrol/statusdesc8', get_string('statusdesc8', 'attcontrol'),
                       get_string('configstatusdesc8', 'attcontrol'), ""));


}