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
 * Prints attcontrol info for particular user
 *
 * @package    mod_attcontrol
 * @copyright  2013 José Luis Antúnez<jantunez@xtec.cat>
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__).'/../../config.php');
require_once(dirname(__FILE__).'/locallib.php');

$pageparams = new att_view_page_params();

$id                     = required_param('id', PARAM_INT);
$pageparams->studentid  = optional_param('studentid', null, PARAM_INT);
$pageparams->view       = optional_param('view', null, PARAM_INT);
$pageparams->curdate    = optional_param('curdate', null, PARAM_INT);

$cm             = get_coursemodule_from_id('attcontrol', $id, 0, false, MUST_EXIST);
$course         = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$attcontrol    = $DB->get_record('attcontrol', array('id' => $cm->instance), '*', MUST_EXIST);

require_login($course, true, $cm);

$pageparams->init($cm);
$att = new attcontrol($attcontrol, $cm, $course, $PAGE->context, $pageparams);



// Redirect to appropriate page if can.
if (!$pageparams->studentid) {
    if ($att->perm->can_manage() || $att->perm->can_take() || $att->perm->can_change()) {
        redirect($att->url_manage());
    } else if ($att->perm->can_view_reports()) {
        redirect($att->url_report());
    }
}
$att->perm->require_view_capability();
redirect($att->url_individualreport());