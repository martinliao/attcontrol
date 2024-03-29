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
 * This file contains the forms to add
 *
 * @package   mod_attcontrol
 * @copyright  2013 José Luis Antúnez<jantunez@xtec.cat>
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir.'/formslib.php');

/**
 * class for displaying add form.
 *
 * @copyright  2013 José Luis Antúnez<jantunez@xtec.cat>
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_attcontrol_add_form extends moodleform {

    /**
     * Called to define this moodle form
     *
     * @return void
     */
    public function definition() {

        global $CFG, $USER;
        $mform    =& $this->_form;

        $course        = $this->_customdata['course'];
        $cm            = $this->_customdata['cm'];
        $modcontext    = $this->_customdata['modcontext'];

        $groupmode = groups_get_activity_groupmode($cm);
        switch ($groupmode) {
            case NOGROUPS:
                $mform->addElement('static', 'sessiontypedescription', get_string('sessiontype', 'attcontrol'),
                    get_string('commonsession', 'attcontrol'));
                $mform->addHelpButton('sessiontypedescription', 'sessiontype', 'attcontrol');
                $mform->addElement('hidden', 'sessiontype', attcontrol::SESSION_COMMON);
                $mform->setType('sessiontype', PARAM_INT);
                break;
            case SEPARATEGROUPS:
                $mform->addElement('static', 'sessiontypedescription', get_string('sessiontype', 'attcontrol'),
                    get_string('groupsession', 'attcontrol'));
                $mform->addHelpButton('sessiontypedescription', 'sessiontype', 'attcontrol');
                $mform->addElement('hidden', 'sessiontype', attcontrol::SESSION_GROUP);
                $mform->setType('sessiontype', PARAM_INT);
                break;
            case VISIBLEGROUPS:
                $radio=array();
                $radio[] = &$mform->createElement('radio', 'sessiontype', '',
                    get_string('commonsession', 'attcontrol'), attcontrol::SESSION_COMMON);
                $radio[] = &$mform->createElement('radio', 'sessiontype', '',
                    get_string('groupsession', 'attcontrol'), attcontrol::SESSION_GROUP);
                $mform->addGroup($radio, 'sessiontype', get_string('sessiontype', 'attcontrol'), ' ', false);
                $mform->setType('sessiontype', PARAM_INT);
                $mform->addHelpButton('sessiontype', 'sessiontype', 'attcontrol');
                $mform->setDefault('sessiontype', attcontrol::SESSION_COMMON);
                break;
        }
        if ($groupmode == SEPARATEGROUPS or $groupmode == VISIBLEGROUPS) {
            if ($groupmode == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $modcontext)) {
                $groups = groups_get_all_groups ($course->id, $USER->id);
            } else {
                $groups = groups_get_all_groups($course->id);
            }
            if ($groups) {
                $selectgroups = array();
                foreach ($groups as $group) {
                    $selectgroups[$group->id] = $group->name;
                }
                $select = &$mform->addElement('select', 'groups', get_string('groups', 'group'), $selectgroups);
                $select->setMultiple(true);
                $mform->disabledIf('groups', 'sessiontype', 'neq', attcontrol::SESSION_GROUP);
            } else {
                if ($groupmode == VISIBLEGROUPS) {
                    $mform->updateElementAttr($radio, array('disabled'=>'disabled'));
                    $mform->addElement('static', 'groups', get_string('groups', 'group'),
                    get_string('nogroups', 'attcontrol'));
                }
                if ($groupmode == SEPARATEGROUPS) {
                    $mform->addElement('html', '<p class="nostudent">'.get_string('nogroups', 'attcontrol')."</p>");
                    return;
                }
            }
        }

        $mform->addElement('checkbox', 'addmultiply', '', get_string('createmultiplesessions', 'attcontrol'));
        $mform->addHelpButton('addmultiply', 'createmultiplesessions', 'attcontrol');

        $mform->addElement('date_time_selector', 'sessiondate', get_string('sessiondate', 'attcontrol'));

        for ($i=0; $i<=23; $i++) {
            $hours[$i] = sprintf("%02d", $i);
        }
        for ($i=0; $i<60; $i+=5) {
            $minutes[$i] = sprintf("%02d", $i);
        }
        $durtime = array();
        $durtime[] =& $mform->createElement('select', 'hours', get_string('hour', 'form'), $hours, false, true);
        $durtime[] =& $mform->createElement('select', 'minutes', get_string('minute', 'form'), $minutes, false, true);
        $mform->addGroup($durtime, 'durtime', get_string('duration', 'attcontrol'), array(' '), true);

        $mform->addElement('date_selector', 'sessionenddate', get_string('sessionenddate', 'attcontrol'));
        $mform->disabledIf('sessionenddate', 'addmultiply', 'notchecked');

        $sdays = array();
        if ($CFG->calendar_startwday === '0') { // Week start from sunday.
            $sdays[] =& $mform->createElement('checkbox', 'Sun', '', get_string('sunday', 'calendar'));
        }
        $sdays[] =& $mform->createElement('checkbox', 'Mon', '', get_string('monday', 'calendar'));
        $sdays[] =& $mform->createElement('checkbox', 'Tue', '', get_string('tuesday', 'calendar'));
        $sdays[] =& $mform->createElement('checkbox', 'Wed', '', get_string('wednesday', 'calendar'));
        $sdays[] =& $mform->createElement('checkbox', 'Thu', '', get_string('thursday', 'calendar'));
        $sdays[] =& $mform->createElement('checkbox', 'Fri', '', get_string('friday', 'calendar'));
        $sdays[] =& $mform->createElement('checkbox', 'Sat', '', get_string('saturday', 'calendar'));
        if ($CFG->calendar_startwday !== '0') { // Week start from sunday.
            $sdays[] =& $mform->createElement('checkbox', 'Sun', '', get_string('sunday', 'calendar'));
        }
        $mform->addGroup($sdays, 'sdays', get_string('sessiondays', 'attcontrol'), array('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'), true);
        $mform->disabledIf('sdays', 'addmultiply', 'notchecked');

        $period = array(1=>1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36);
        $periodgroup = array();
        $periodgroup[] =& $mform->createElement('select', 'period', '', $period, false, true);
        $periodgroup[] =& $mform->createElement('static', 'perioddesc', '', get_string('week', 'attcontrol'));
        $mform->addGroup($periodgroup, 'periodgroup', get_string('period', 'attcontrol'), array(' '), false);
        $mform->disabledIf('periodgroup', 'addmultiply', 'notchecked');


        //Enlarged the original textarea to fit the description better.
        $mform->addElement('textarea', 'description', get_string("description", "attcontrol"), 'wrap="virtual" rows="5" cols="100"');

        $submit_string = get_string('addsession', 'attcontrol');
        $this->add_action_buttons(false, $submit_string);
    }

    /**
     * Perform minimal validation on the settings form
     * @param array $data
     * @param array $files
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if ($data['sessiontype'] == attcontrol::SESSION_GROUP and empty($data['groups'])) {
            $errors['groups'] = get_string('errorgroupsnotselected', 'attcontrol');
        }
        return $errors;
    }

}
