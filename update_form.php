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
 * Update form
 *
 * @package    mod_attcontrol
 * @copyright  2013 José Luis Antúnez<jantunez@xtec.cat>
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once($CFG->libdir.'/formslib.php');

/**
 * class for displaying update form.
 *
 * @copyright  2013 José Luis Antúnez<jantunez@xtec.cat>
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_attcontrol_update_form extends moodleform {

    /**
     * Called to define this moodle form
     *
     * @return void
     */
    public function definition() {

        global $DB;
        $mform    =& $this->_form;

        $course        = $this->_customdata['course'];
        $cm            = $this->_customdata['cm'];
        $modcontext    = $this->_customdata['modcontext'];
        $sessionid     = $this->_customdata['sessionid'];

        if (!$sess = $DB->get_record('attcontrol_sessions', array('id'=> $sessionid) )) {
            error('No such session in this course');
        }
        $dhours = floor($sess->duration / HOURSECS);
        $dmins = floor(($sess->duration - $dhours * HOURSECS) / MINSECS);
        $defopts = array('maxfiles'=>EDITOR_UNLIMITED_FILES, 'noclean'=>true, 'context'=>$modcontext);
        $data = array('sessiondate' => $sess->sessdate,
                'durtime' => array('hours' => $dhours, 'minutes' => $dmins),
                'description' => $sess->description);

        $mform->addElement('header', 'general', get_string('changesession', 'attcontrol'));

        $mform->addElement('static', 'olddate', get_string('olddate', 'attcontrol'),
                           userdate($sess->sessdate, get_string('strftimedmyhm', 'attcontrol')));
        $mform->addElement('date_time_selector', 'sessiondate', get_string('newdate', 'attcontrol'));

        for ($i=0; $i<=23; $i++) {
            $hours[$i] = sprintf("%02d", $i);
        }
        for ($i=0; $i<60; $i+=5) {
            $minutes[$i] = sprintf("%02d", $i);
        }
        $durselect[] =& $mform->createElement('select', 'hours', '', $hours);
        $durselect[] =& $mform->createElement('select', 'minutes', '', $minutes, false, true);
        $mform->addGroup($durselect, 'durtime', get_string('duration', 'attcontrol'), array(' '), true);

        $mform->addElement('textarea', 'description', get_string("description", "attcontrol"), 'wrap="virtual" rows="10" cols="100"');

        $mform->setDefaults($data);

        $submit_string = get_string('update', 'attcontrol');
        $this->add_action_buttons(true, $submit_string);
    }
}
