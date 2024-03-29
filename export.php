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
 * Export attcontrol sessions
 *
 * @package   mod_attcontrol
 * @copyright  2013 José Luis Antúnez<jantunez@xtec.cat>
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__).'/../../config.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once(dirname(__FILE__).'/export_form.php');
require_once(dirname(__FILE__).'/renderables.php');
require_once(dirname(__FILE__).'/renderhelpers.php');

$id             = required_param('id', PARAM_INT);

$cm             = get_coursemodule_from_id('attcontrol', $id, 0, false, MUST_EXIST);
$course         = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$att            = $DB->get_record('attcontrol', array('id' => $cm->instance), '*', MUST_EXIST);

require_login($course, true, $cm);

$att = new attcontrol($att, $cm, $course, $PAGE->context);

$att->perm->require_export_capability();

$PAGE->set_url($att->url_export());

//Changed page heading
$PAGE->set_title($course->shortname. ": ".$att->name);
$PAGE->set_heading($course->fullname);

$PAGE->set_cacheable(true);
$PAGE->set_button($OUTPUT->update_module_button($cm->id, 'attcontrol'));
$PAGE->navbar->add(get_string('export', 'quiz'));

$formparams = array('course' => $course, 'cm' => $cm, 'modcontext' => $PAGE->context);
$mform = new mod_attcontrol_export_form($att->url_export(), $formparams);

if ($mform->is_submitted()) {
    $formdata = $mform->get_data();

    $pageparams = new att_page_with_filter_controls();
    $pageparams->init($cm);
    $pageparams->group = $formdata->group;
    $pageparams->set_current_sesstype($formdata->group ? $formdata->group : att_page_with_filter_controls::SESSTYPE_ALL);
    if (isset($formdata->includeallsessions)) {
        if (isset($formdata->includenottaken)) {
            $pageparams->view = ATT_VIEW_ALL;
        } else {
            $pageparams->view = ATT_VIEW_ALLPAST;
            $pageparams->curdate = time();
        }
        $pageparams->init_start_end_date();
    } else {
        $pageparams->startdate = $formdata->sessionstartdate;
        $pageparams->enddate = $formdata->sessionenddate;
    }
    $att->pageparams = $pageparams;

    $reportdata = new attcontrol_report_data($att, true);
    if ($reportdata->users) {
        $filename = clean_filename($course->shortname.'_attcontrols_'.userdate(time(), '%Y%m%d-%H%M'));

        $group = $formdata->group ? $reportdata->groups[$formdata->group] : 0;
        $data = new stdClass;
        $data->tabhead = array();
        $data->course = $att->course->fullname;
        $data->group = $group ? $group->name : get_string('allparticipants');

        if (isset($formdata->ident['id'])) {
            $data->tabhead[] = get_string('studentid', 'attcontrol');
        }
        if (isset($formdata->ident['uname'])) {
            $data->tabhead[] = get_string('username');
        }
        $data->tabhead[] = get_string('lastname');
        $data->tabhead[] = get_string('firstname');

        if (count($reportdata->sessions) > 0) {
            foreach ($reportdata->sessions as $sess) {
                $text = userdate($sess->sessdate, get_string('strftimedmyhm', 'attcontrol'));
                $text .= ' ';
                $text .= $sess->groupid ? $reportdata->groups[$sess->groupid]->name : get_string('commonsession', 'attcontrol');
                $data->tabhead[] = $text;
            }
        } else {
            print_error('sessionsnotfound', 'attcontrol', $att->url_manage());
        }

        //Removed the grade information

        $emptystatuses = array();

        foreach ($reportdata->statuses as $status) {
            $data->tabhead[] = $status->acronym;
            $emptystatuses[$status->id] = 0;
        }

        $i = 0;
        $data->table = array();

        foreach ($reportdata->users as $user) {
            if (isset($formdata->ident['id']))
                $data->table[$i][] = $user->id;

            if (isset($formdata->ident['uname']))
                $data->table[$i][] = $user->username;

            $data->table[$i][] = $user->lastname;
            $data->table[$i][] = $user->firstname;

            $countstatuses = $emptystatuses;


            foreach ($reportdata->sessions as $sess) {
                if (isset($user->sessions[$sess->id])) {

                    $thistatus = $user->sessions[$sess->id]->statusid;
                    $countstatuses[$thistatus]++;

                    $data->table[$i][] = $reportdata->statuses[$thistatus]->acronym;
                }
                else {
                    $data->table[$i][] = " ";
                }
            }

            foreach ($countstatuses as $countstatus) {
                $data->table[$i][] = $countstatus;
            }

            $i++;

        }



        if ($formdata->format === 'text') {
            exporttocsv($data, $filename);
        } else {
            exporttotableed($data, $filename, $formdata->format);
        }
        exit;
    } else {
        print_error('studentsnotfound', 'attcontrol', $att->url_manage());
    }
}

$output = $PAGE->get_renderer('mod_attcontrol');
$tabs = new attcontrol_tabs($att, attcontrol_tabs::TAB_EXPORT);
echo $output->header();
echo $output->heading(get_string('attendancecontrol', 'attcontrol'));
echo $output->render($tabs);

$mform->display();

echo $OUTPUT->footer();


function exporttotableed($data, $filename, $format) {
    global $CFG;

    if ($format === 'excel') {
        require_once("$CFG->libdir/excellib.class.php");
        $filename .= ".xls";
        $workbook = new MoodleExcelWorkbook("-");
    } else {
        require_once("$CFG->libdir/odslib.class.php");
        $filename .= ".ods";
        $workbook = new MoodleODSWorkbook("-");
    }
    // Sending HTTP headers.
    $workbook->send($filename);
    // Creating the first worksheet.
    $myxls = $workbook->add_worksheet('attcontrols');
    // Format types.
    $formatbc = $workbook->add_format();
    $formatbc->set_bold(1);

    $myxls->write(0, 0, get_string('course'), $formatbc);
    $myxls->write(0, 1, $data->course);
    $myxls->write(1, 0, get_string('group'), $formatbc);
    $myxls->write(1, 1, $data->group);

    $i = 3;
    $j = 0;
    foreach ($data->tabhead as $cell) {
        $myxls->write($i, $j++, $cell, $formatbc);
    }
    $i++;
    $j = 0;
    foreach ($data->table as $row) {
        foreach ($row as $cell) {
            $myxls->write($i, $j++, $cell);
        }
        $i++;
        $j = 0;
    }
    $workbook->close();
}

function exporttocsv($data, $filename) {
    //Changed extension to csv.
    $filename .= ".csv";

    header("Content-Type: application/download\n");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Expires: 0");
    header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
    header("Pragma: public");

    echo get_string('course')."\t".$data->course."\n";
    echo get_string('group')."\t".$data->group."\n\n";

    echo implode("\t", $data->tabhead)."\n";
    foreach ($data->table as $row) {
        echo implode("\t", $row)."\n";
    }
}
