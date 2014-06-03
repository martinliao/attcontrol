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
 * attcontrol report
 *
 * @package    mod_attcontrol
 * @copyright  2013 José Luis Antúnez <jantunez@xtec.cat>
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__).'/../../config.php');
require_once(dirname(__FILE__).'/locallib.php');

$pageparams = new att_report_page_params();

$id                     = required_param('id', PARAM_INT);
$from                   = optional_param('from', null, PARAM_ACTION);
$pageparams->view       = optional_param('view', null, PARAM_INT);
$pageparams->curdate    = optional_param('curdate', null, PARAM_INT);
$pageparams->group      = optional_param('group', null, PARAM_INT);
$pageparams->sort       = optional_param('sort', null, PARAM_INT);
$pageparams->action        = optional_param('action', null, PARAM_INT);

$cm             = get_coursemodule_from_id('attcontrol', $id, 0, false, MUST_EXIST);
$course         = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$att            = $DB->get_record('attcontrol', array('id' => $cm->instance), '*', MUST_EXIST);

require_login($course, true, $cm);

$pageparams->init($cm);
$att = new attcontrol($att, $cm, $course, $PAGE->context, $pageparams);

$att->perm->require_view_reports_capability();

$PAGE->set_url($att->url_report());
$PAGE->set_pagelayout('report');
$PAGE->set_title($course->shortname. ": ".$att->name.' - '.get_string('report', 'attcontrol'));
$PAGE->set_heading($course->fullname);
$PAGE->set_cacheable(true);
$PAGE->set_button($OUTPUT->update_module_button($cm->id, 'attcontrol'));
$PAGE->navbar->add(get_string('report', 'attcontrol'));

switch ($pageparams->action) {
	case att_report_page_params::ACTION_CHANGE_PERPAGE:
		$att->set_attcontrol_perpage(optional_param('perpage', 10, PARAM_INT));
		break;
}

$output = $PAGE->get_renderer('mod_attcontrol');
$tabs = new attcontrol_tabs($att, attcontrol_tabs::TAB_REPORT);
$filtercontrols = new attcontrol_filter_controls($att);
$reportdata = new attcontrol_report_data($att);

add_to_log($course->id, 'attcontrol', 'report viewed', '/mod/attcontrol/report.php?id='.$id, '', $cm->id);

// Output starts here.

echo $output->header();
echo $output->heading(get_string('attendancecontrol', 'attcontrol'));
echo $output->render($tabs);
echo $output->render($filtercontrols);
echo $output->render($reportdata);

echo $output->footer();

