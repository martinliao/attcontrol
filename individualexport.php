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
 * @copyright  2013 José Luis Antúnez <jantunez@xtec.cat>
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__).'/../../config.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once(dirname(__FILE__).'/individualexportform.php');
require_once(dirname(__FILE__).'/renderables.php');

$id             = required_param('id', PARAM_INT);

$cm             = get_coursemodule_from_id('attcontrol', $id, 0, false, MUST_EXIST);
$course         = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$att            = $DB->get_record('attcontrol', array('id' => $cm->instance), '*', MUST_EXIST);



require_login($course, true, $cm);

$att = new attcontrol($att, $cm, $course, $PAGE->context);

$att->perm->require_export_capability();

$PAGE->set_url($att->url_individualexport());
$PAGE->set_title($course->shortname. ": ".$att->name);
$PAGE->set_heading($course->fullname);
$PAGE->set_cacheable(true);
$PAGE->set_button($OUTPUT->update_module_button($cm->id, 'attcontrol'));
$PAGE->navbar->add(get_string('export', 'attcontrol'));

$formparams = array('course' => $course, 'cm' => $cm, 'modcontext' => $PAGE->context);
$mform = new mod_attcontrol_individualexportform($att->url_individualexport(), $formparams);

if ($mform->is_submitted()) {
    $formdata = $mform->get_data();

    $pageparams = new att_page_with_filter_controls();
    $pageparams->init($cm);

    if (isset($formdata->includeallsessions)) {
        if (isset($formdata->includenottaken)) {
            $pageparams->view = ATT_VIEW_ALL;
        } else {
            $pageparams->view = ATT_VIEW_ALLPAST;
            $pageparams->curdate = time();
        }
        $pageparams->init_start_end_date();
    } else {
        $pageparams->startdate = $formdata->startdate;
        $pageparams->enddate = $formdata->enddate;
    }
    $att->pageparams = $pageparams;

    $reportdata = new attcontrol_individual_report_data($att, true);
    if ($reportdata->logs) {

        $filename = clean_filename($course->shortname.'_attcontrol_'.userdate(time(), '%Y%m%d-%H%M'));


        $studentfilter = $formdata->studentid;


        $data = new stdClass;
        $data->tabhead = array();
        $data->course = $att->course->fullname;
        $data->student = $reportdata->studentinfo->lastname.", ".$reportdata->studentinfo->firstname;

        $data->tabhead[] = get_string ( "date", "attcontrol" );
        $data->tabhead[] = get_string ( "time", "attcontrol" );
        $data->tabhead[] = get_string ( "status", "attcontrol" );
        $data->tabhead[] = get_string ( "remarks", "attcontrol" );

        $i = 0;
        $data->table = array();

        foreach ($reportdata->logs as $log) {
            $data->table[$i][] = userdate ( $log->sessdate, get_string ( 'strftimedmy', 'attcontrol' ) );
            $data->table[$i][] = userdate ( $log->sessdate, get_string ( 'strftimehm', 'attcontrol' ) ) . " - " . userdate ( $log->sessdate + $log->duration, get_string ( 'strftimehm', 'attcontrol' ) );
            if ($log->statusid) {
                $data->table[$i][] = $reportdata->statuses[$log->statusid]->description;
            }
            else {
                $data->table[$i][] = "";
            }
            $data->table[$i][] = $log->remarks;

            $i++;
        }

        if ($formdata->format === 'text') {
            exporttocsv($data, $filename);
        } else {
            exporttotableed($data, $filename, $formdata->format);
        }
        exit;
    } else {
        print_error('studentsnotfound', 'attendance', $att->url_individual_export());
    }
}

$output = $PAGE->get_renderer('mod_attcontrol');
$tabs = new attcontrol_tabs($att, attcontrol_tabs::TAB_INDIVIDUALEXPORT);
echo $output->header();
//Changed title
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
    $myxls = $workbook->add_worksheet('Individual Report');
    // Format types.
    $formatbc = $workbook->add_format();
    $formatbc->set_bold(1);

    $myxls->write(0, 0, get_string('course'), $formatbc);
    $myxls->write(0, 1, $data->course);
    $myxls->write(1, 0, get_string('student', 'attcontrol'), $formatbc);
    $myxls->write(1, 1, $data->student);

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
    $filename .= ".csv";

    header("Content-Type: application/download\n");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Expires: 0");
    header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
    header("Pragma: public");
    header("Content-Transfer-Encoding: UTF-8");

    echo get_string('course')."\t".$data->course."\n";
    echo get_string('student', 'attcontrol')."\t".$data->student."\n\n";

    echo implode("\t", $data->tabhead)."\n";

    foreach ($data->table as $row) {
        echo implode("\t", $row)."\n";
    }

}
