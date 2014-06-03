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
 * Manage attcontrol sessions
 *
 * @package    mod_attcontrol
 * @copyright  2013 José Luis Antúnez<jantunez@xtec.cat>
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__).'/../../config.php');
require_once(dirname(__FILE__).'/locallib.php');

$pageparams = new att_manage_page_params();

$id                         = required_param('id', PARAM_INT);
$pageparams->action     	= optional_param('action', null, PARAM_INT);
$from                       = optional_param('from', null, PARAM_ALPHANUMEXT);
$pageparams->view           = optional_param('view', null, PARAM_INT);
$pageparams->curdate        = optional_param('curdate', null, PARAM_INT);

$cm             = get_coursemodule_from_id('attcontrol', $id, 0, false, MUST_EXIST);
$course         = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$att            = $DB->get_record('attcontrol', array('id' => $cm->instance), '*', MUST_EXIST);

require_login($course, true, $cm);

$pageparams->init($cm);
$att = new attcontrol($att, $cm, $course, $PAGE->context, $pageparams);
if (!$att->perm->can_manage() && !$att->perm->can_take() && !$att->perm->can_change()) {
    redirect($att->url_view());
}

// If teacher is coming from block, then check for a session exists for today.
if ($from === 'block') {
    $sessions = $att->get_today_sessions();
    $size = count($sessions);
    if ($size == 1) {
        $sess = reset($sessions);
        $nottaken = !$sess->lasttaken && has_capability('mod/attcontrol:takeattcontrols', $PAGE->context);
        $canchange = $sess->lasttaken && has_capability('mod/attcontrol:changeattcontrols', $PAGE->context);
        if ($nottaken || $canchange) {
            redirect($att->url_take(array('sessionid' => $sess->id, 'grouptype' => $sess->groupid)));
        }
    } else if ($size > 1) {
        $att->curdate = $today;
        // Temporarily set $view for single access to page from block.
        $att->view = ATT_VIEW_DAYS;
    }
}

$PAGE->set_url($att->url_manage());
$PAGE->set_title($course->shortname. ": ".$att->name);
$PAGE->set_heading($course->fullname);
$PAGE->set_cacheable(true);
$PAGE->set_button($OUTPUT->update_module_button($cm->id, 'attcontrol'));
$PAGE->navbar->add($att->name);


switch ($pageparams->action) {
     case att_manage_page_params::ACTION_CHANGE_PERPAGE:
     	$att->set_attcontrol_perpage(optional_param('perpage', 10, PARAM_INT));     	
     	break; 
}

$output = $PAGE->get_renderer('mod_attcontrol');
$tabs = new attcontrol_tabs($att, attcontrol_tabs::TAB_SESSIONS);
$filtercontrols = new attcontrol_filter_controls($att);
$sesstable = new attcontrol_manage_data($att);

// Output starts here.

echo $output->header();
echo $output->heading(get_string('attendancecontrol', 'attcontrol'));
echo $output->render($tabs);
echo $output->render($filtercontrols);
echo $output->render($sesstable);

echo $output->footer();

