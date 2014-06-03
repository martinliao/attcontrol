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
 * Export attcontrol sessions forms
 *
 * @package   mod_attcontrol
 * @copyright  2013 José Luis Antúnez<jantunez@xtec.cat>
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir.'/formslib.php');

/**
 * class for displaying export form.
 *
 * @copyright  2013 José Luis Antúnez<jantunez@xtec.cat>
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_attcontrol_export_form extends moodleform {

    /**
     * Called to define this moodle form
     *
     * @return void
     */
    public function definition() {

        global $USER;
        $mform    =& $this->_form;

        $course        = $this->_customdata['course'];
        $cm            = $this->_customdata['cm'];
        $modcontext    = $this->_customdata['modcontext'];

        $groupmode=groups_get_activity_groupmode($cm);
        $groups = groups_get_activity_allowed_groups($cm, $USER->id);
        if ($groupmode == VISIBLEGROUPS or has_capability('moodle/site:accessallgroups', $modcontext)) {
            $grouplist[0] = get_string('allparticipants');
        }
        if ($groups) {
            foreach ($groups as $group) {
                $grouplist[$group->id] = $group->name;
            }
        }
        $mform->addElement('select', 'group', get_string('group'), $grouplist);

        $ident = array();
        $ident[] =& $mform->createElement('checkbox', 'id', '', get_string('studentid', 'attcontrol'));

        $ident[] =& $mform->createElement('checkbox', 'uname', '', get_string('username'));
        $mform->addGroup($ident, 'ident', get_string('identifyby', 'attcontrol'), array('<br />'), true);
        $mform->setDefaults(array('ident[id]' => true, 'ident[uname]' => true));
        $mform->setType('id', PARAM_INT);
        $mform->setType('uname', PARAM_INT);

        $mform->addElement('checkbox', 'includeallsessions', get_string('includeall', 'attcontrol'), get_string('yes'));
        $mform->setDefault('includeallsessions', true);
        $mform->addElement('checkbox', 'includenottaken', get_string('includenottaken', 'attcontrol'), get_string('yes'));
        $mform->setDefault('includenottaken', true);
        $mform->addElement('date_selector', 'sessionstartdate', get_string('startofperiod', 'attcontrol'));
        $mform->setDefault('sessionstartdate', $course->startdate);
        $mform->disabledIf('sessionstartdate', 'includeallsessions', 'checked');
        $mform->addElement('date_selector', 'sessionenddate', get_string('endofperiod', 'attcontrol'));
        $mform->disabledIf('sessionenddate', 'includeallsessions', 'checked');

        $mform->addElement('select', 'format', get_string('format'),
            array('excel' => get_string('downloadexcel', 'attcontrol'),
                'ooo' => get_string('downloadooo', 'attcontrol'),
                'text' => get_string('downloadtext', 'attcontrol')
            ));

        $submit_string = get_string('export', 'attcontrol');
        $this->add_action_buttons(false, $submit_string);

        $mform->addElement('hidden', 'id', $cm->id);
    }
}

