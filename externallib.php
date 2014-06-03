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

/**
 * External assign API
 *
 * @package    mod_attcontrol
 * @since      Moodle 2.4
 * @copyright  2014 José Luis Antúnez
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;



require_once("$CFG->libdir/externallib.php");

/**
 * Class containing all functions of the AttControl Webservice API.
 * @copyright 2014 José Luis Antúnez
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_attcontrol_external extends external_api {

    /**
     * Constant for the No groups value
     */
    const NOGROUPS = 0;
    /**
     * Constant for the Separate groups value
     */
    const SEPARATEGROUPS = 1;
    /**
     * Constant for the Visible groups value
     */
    const VISIBLEGROUPS = 2;
    /**
     * Constant for the common session.
     */
    const COMMONSESSION = -10;
    /**
     * Infinite number, for calculating minimums.
     */
    const BIGNUMBER = 999999999999999999999;
    /**
     * Constant value for indicating all courses.
     */
    const ALL = -1;


    /**
     * Parameters for the get_user external function
     * @return external_function_parameters with the validated parameters for this function
     */
    public static function get_user_parameters() {
        return new external_function_parameters(
            array(
            )
        );
    }

    /**
     * Parameters for the get_sesions external function
     * @return external_function_parameters with the validated parameters for this function
     */
    public static function get_sessions_parameters() {
        return new external_function_parameters(
            array(
                'inidate' => new external_value(PARAM_TEXT, 'Initial date in YYYY-MM-DD format'),
                'enddate' => new external_value(PARAM_TEXT, 'End date in YYYY-MM-DD format')
            )
        );
    }


    /**
     * Parameters for the get_session_take external function
     * @return external_function_parameters with the validated parameters for this function
     */
    public static function get_session_take_parameters() {
        return new external_function_parameters(
            array(
                'sessionid' => new external_value(PARAM_INT, 'Session id')
            )
        );
    }


    /**
     * Parameters for the get_attendance_statuses external function
     * @return external_function_parameters with the validated parameters for this function
     */
    public static function get_attendance_statuses_parameters() {
        return new external_function_parameters(
            array()
        );
    }

    /**
     * Parameters for the get_attendance_attcourses external function
     * @return external_function_parameters with the validated parameters for this function
     */
    public static function get_attendance_attcourses_parameters() {
        return new external_function_parameters(
            array()
        );
    }

    /**
     * Parameters for the save_session_take external function
     * @return external_function_parameters with the validated parameters for this function
     */
    public static function save_session_take_parameters() {
        return new external_function_parameters(
            array(
                'sessionid' => new external_value(PARAM_INT, 'Session id'),
                'takedata' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'userid'            => new external_value(PARAM_INT, 'User id.'),
                                'statusid'            => new external_value(PARAM_INT, 'Status id.'),
                                'remarks'           => new external_value(PARAM_RAW, 'Remarks text')
                            )
                        )
                    )
            )
        );
    }


    /**
     * Parameters for the save_report_data external function
     * @return external_function_parameters with the validated parameters for this function
     */
    public static function save_report_data_parameters() {
        return new external_function_parameters(
            array(
                'reportdata' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'atlid'            => new external_value(PARAM_INT, 'Attendance log id.'),
                                'statusid'            => new external_value(PARAM_INT, 'Status id.'),
                                'remarks'           => new external_value(PARAM_RAW, 'Remarks text')
                            )
                        )
                    )
            )
        );
    }


    /**
     * Parameters for the save_new_session external function
     * @return external_function_parameters with the validated parameters for this function
     */
    public static function save_new_session_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'Course id'),
                'attid' => new external_value(PARAM_INT, 'AttControl id'),
                'group' => new external_value(PARAM_TEXT, 'Group id, if assigned', false),
                'timestamp' => new external_value(PARAM_TEXT, 'Session timestamp'),
                'duration' => new external_value(PARAM_TEXT, 'Session duration'),
                'recurrence' => new external_value(PARAM_TEXT, 'Session recurrence', false),
                'description' => new external_value(PARAM_TEXT, 'Session description', false),
            )
        );
    }

    /**
     * Parameters for the save_edit_session external function
     * @return external_function_parameters with the validated parameters for this function
     */
    public static function save_edit_session_parameters() {
        return new external_function_parameters(
            array(
                'sessionid' => new external_value(PARAM_INT, 'Session id'),
                'timestamp' => new external_value(PARAM_TEXT, 'Session TimeStamp'),
                'duration' => new external_value(PARAM_INT, 'Session duration'),
                'description' => new external_value(PARAM_TEXT, 'Session description', false)
            )
        );
    }

    /**
     * Parameters for the delete_session external function
     * @return external_function_parameters with the validated parameters for this function
     */
    public static function delete_session_parameters() {
        return new external_function_parameters(
            array(
                'sessionid' => new external_value(PARAM_INT, 'Session id')
            )
        );
    }


    /**
     * Parameters for the get_students external function
     * @return external_function_parameters with the validated parameters for this function
     */
    public static function get_students_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'Course id'),
                'attcontrolid' => new external_value(PARAM_INT, 'AttControl id'),
                'groupid' => new external_value(PARAM_INT, 'Group id')
            )
        );
    }

    /**
     * Parameters for the get_reports external function
     * @return external_function_parameters with the validated parameters for this function
     */
    public static function get_reports_parameters() {
        return new external_function_parameters(
            array(
                'studentid' => new external_value(PARAM_INT, 'Student id', false),
                'startdate' => new external_value(PARAM_TEXT, 'Start date in YYYY-MM-DD format'),
                'enddate' => new external_value(PARAM_TEXT, 'End date in YYYY-MM-DD format'),
                'courses' => new external_multiple_structure(new external_value(PARAM_INT, 'Course id'), 'List of course ids', VALUE_DEFAULT, array(), NULL_ALLOWED),
                'attcontrols' => new external_multiple_structure(new external_value(PARAM_INT, 'AttControl id'), 'List of attcontrol ids', VALUE_DEFAULT, array(), NULL_ALLOWED),
                'groups' => new external_multiple_structure(new external_value(PARAM_INT, 'Group id'), 'List of group ids', VALUE_DEFAULT, array(), NULL_ALLOWED),

            )
        );
    }

    /**
     * Parameters for the get_reportsummary external function
     * @return external_function_parameters with the validated parameters for this function
     */
    public static function get_reportsummary_parameters() {
        return new external_function_parameters(
            array(
                'studentid' => new external_value(PARAM_INT, 'Student id', false),
                'startdate' => new external_value(PARAM_TEXT, 'Start date in YYYY-MM-DD format'),
                'enddate' => new external_value(PARAM_TEXT, 'End date in YYYY-MM-DD format'),
                'courses' => new external_multiple_structure(new external_value(PARAM_INT, 'Course id'), 'List of course ids', VALUE_DEFAULT, array(), NULL_ALLOWED),
                'attcontrols' => new external_multiple_structure(new external_value(PARAM_INT, 'AttControl id'), 'List of attcontrol ids', VALUE_DEFAULT, array(), NULL_ALLOWED),
                'groups' => new external_multiple_structure(new external_value(PARAM_INT, 'Group id'), 'List of group ids', VALUE_DEFAULT, array(), NULL_ALLOWED),

            )
        );
    }

    /**
     * Gets the current user full information, including the access to attcontrol/atthome.
     * @return array containing the information of the current user, and its access to atthcontrol and atthome instances.
     */
    public static function get_user() {
        global $DB, $USER, $CFG;

        require_once("$CFG->libdir/accesslib.php");

        $context = context_system::instance();
        self::validate_context($context);

        $sql = "SELECT u.id, u.firstname, u.lastname, u.email, f.contextid pic1, u.picture pic2, u.username
                FROM {user} u
                LEFT JOIN {files} f ON u.picture = f.id
                WHERE u.id = ?";

        $user = $DB->get_record_sql($sql, array($USER->id));

        $user->hasattcontrols = self::count_attcontrols();

        $user->hasatthomes = self::count_atthomes();

        return array($user);
    }

    /**
     * Counts the number of attcontrol instances accessible for the user.
     * @return int number of attcontrols accessible for the user
     */
    private static function count_attcontrols() {
        global $DB, $USER, $CFG;

        require_once("$CFG->libdir/accesslib.php");

        $context = context_system::instance();
        self::validate_context($context);

        $sql = "SELECT COUNT(ac.id) hasattcontrols FROM
                (
                SELECT ac.id
                FROM {role_assignments} a 
                JOIN {role_capabilities} rc ON a.userid = :userid1 AND a.roleid = rc.roleid AND rc.capability = :capability1
                JOIN {context} c ON c.contextlevel = :modulecontext AND a.contextid = c.id
                JOIN {course_modules} cm ON cm.id = c.instanceid
                JOIN {modules} m ON m.name = :modulename AND cm.module = m.id
                JOIN {attcontrol} ac ON ac.id = cm.instance
                
                UNION
                
                SELECT ac.id
                FROM {role_assignments} a 
                JOIN {role_capabilities} rc ON a.userid = :userid2 AND a.roleid = rc.roleid AND rc.capability = :capability2
                JOIN {context} c ON c.contextlevel = :coursecontext AND a.contextid = c.id
                JOIN {attcontrol} ac ON ac.course = c.instanceid
                JOIN {course_modules} cm ON cm.instance = ac.id
                JOIN {modules} m ON m.name = :modulename2 AND cm.module = m.id

                ) ac";

        $qparams = array("userid1" => $USER->id,
            "capability1" => "mod/attcontrol:view",
            "modulecontext" => CONTEXT_MODULE,
            "modulename" => "attcontrol",
            "userid2" => $USER->id,
            "capability2" => "mod/attcontrol:view",
            "coursecontext" => CONTEXT_COURSE,
            "modulename2" => "attcontrol",
        );

        try {
            $attcontrols = $DB->count_records_sql($sql, $qparams);
        }
        catch (Exception $e) {
            $attcontrols = 0;
        }

        return $attcontrols;

    }


    /**
     * Counts the number of atthome instances accessible for this user.
     * @return int number of atthomes accessible for the user
     */
    private static function count_atthomes() {
        global $DB, $USER, $CFG;

        require_once("$CFG->libdir/accesslib.php");


        $context = context_system::instance();
        self::validate_context($context);


        $sql = "SELECT count(ah.id) hasatthomes FROM
                (
                
                SELECT ah.id
                FROM {role_assignments} a 
                JOIN {role_capabilities} rc ON a.userid = :userid1 AND a.roleid = rc.roleid AND rc.capability = :capability1
                JOIN {context} c ON c.contextlevel = :modulecontext AND a.contextid = c.id
                JOIN {course_modules} cm ON cm.id = c.instanceid
                JOIN {modules} m ON m.name = :modulename AND cm.module = m.id
                JOIN {atthome} ah ON ah.id = cm.instance
                
                UNION
                
                SELECT ah.id
                FROM {role_assignments} a 
                JOIN {role_capabilities} rc ON a.userid = :userid2 AND a.roleid = rc.roleid AND rc.capability = :capability2
                JOIN {context} c ON c.contextlevel = :coursecontext AND a.contextid = c.id
                JOIN {atthome} ah ON ah.course = c.instanceid
                
                ) ah";

        $qparams = array("userid1" => $USER->id,
            "capability1" => "mod/atthome:view",
            "modulecontext" => CONTEXT_MODULE,
            "modulename" => "atthome",
            "userid2" => $USER->id,
            "capability2" => "mod/atthome:view",
            "coursecontext" => CONTEXT_COURSE
        );

        try {
            $atthomes = $DB->count_records_sql($sql, $qparams);
        }
        catch (Exception $e) {
            $atthomes = 0;
        }

        return $atthomes;
    }

    /**
     * Gets the attendances statuses available in this configuration of the system.
     * @return array the statuses
     */
    public static function get_attendance_statuses() {
        $config = get_config ('attcontrol');
        $statuses = array ();
        for($i = 1; $i < 9; $i ++) {
            if (trim ( $config->{"status" . $i} )) {
                $status = new stdClass();
                $status->id = $i;
                $status->acronym = $config->{"status" . $i};
                $status->description = $config->{"statusdesc" . $i};

                $statuses [$i] = $status;
            }
        }

        return $statuses;

    }


    /**
     * Gets all the courses associated to the attcontrol instances of this user
     * @return array attcontrols with course information
     */
    public static function get_attendance_attcourses() {
        global $DB, $USER, $CFG;

        require_once("$CFG->libdir/accesslib.php");


        $context = context_system::instance();
        self::validate_context($context);


        $sql = "SELECT ac.id id, ac.course courseid, ac.name acname, co.fullname coursename, co.shortname courseshortname, ac.groupmode groupmode FROM
                (
                SELECT ac.*, cm.groupmode
                FROM {role_assignments} a 
                JOIN {role_capabilities} rc ON a.userid = :userid1 AND a.roleid = rc.roleid AND rc.capability = :capability1
                JOIN {context} c ON c.contextlevel = :modulecontext AND a.contextid = c.id
                JOIN {course_modules} cm ON cm.id = c.instanceid
                JOIN {modules} m ON m.name = :modulename AND cm.module = m.id
                JOIN {attcontrol} ac ON ac.id = cm.instance
                
                UNION
                
                SELECT ac.*, cm.groupmode
                FROM {role_assignments} a 
                JOIN {role_capabilities} rc ON a.userid = :userid2 AND a.roleid = rc.roleid AND rc.capability = :capability2
                JOIN {context} c ON c.contextlevel = :coursecontext AND a.contextid = c.id
                JOIN {attcontrol} ac ON ac.course = c.instanceid
                JOIN {course_modules} cm ON cm.instance = ac.id
                JOIN {modules} m ON m.name = :modulename2 AND cm.module = m.id

                ) ac
                
                JOIN {course} co ON ac.course = co.id
                ORDER BY co.sortorder, ac.name";


        $qparams = array("userid1" => $USER->id,
            "capability1" => "mod/attcontrol:manageattcontrols",
            "modulecontext" => CONTEXT_MODULE,
            "modulename" => "attcontrol",
            "userid2" => $USER->id,
            "capability2" => "mod/attcontrol:manageattcontrols",
            "coursecontext" => CONTEXT_COURSE,
            "modulename2" => "attcontrol",
        );

        $attcontrols = $DB->get_records_sql($sql, $qparams);


        $results = array();
        $lastc = -1;

        foreach ($attcontrols as $atc) {
            if ($atc->courseid != $lastc) {
                $course = array();
                $course['courseid'] = $atc->courseid;
                $course['coursename'] = $atc->coursename;
                $course['courseshortname'] = $atc->courseshortname;
                $course['attcontrols'] = array();
                $results[] = $course;

                $lastc = $atc->courseid;
            }
        }

        $i = 0;
        $groupsql = "SELECT g.id groupid, g.name groupname FROM {groups} g WHERE g.courseid = ?";
        foreach ($attcontrols as $atc) {
            while ($results[$i]['courseid'] != $atc->courseid) $i++;

            $attcontrol = array();
            $attcontrol['acid'] = $atc->id;
            $attcontrol['acname'] = $atc->acname;
            $attcontrol['groupmode'] = $atc->groupmode;
            $attcontrol['groups'] = array();

            if ($atc->groupmode != NOGROUPS) {
                //S'HAN DE BUSCAR ELS GRUPS
                foreach ($DB->get_records_sql($groupsql, array($atc->courseid)) as $g) {
                    $group = array();
                    $group['groupid'] = $g->groupid;
                    $group['groupname'] = $g->groupname;
                    $attcontrol['groups'][] = $group;
                }
            }

            $results[$i]['attcontrols'][] = $attcontrol;
        }

        return $results;

    }

    /**
     * Gets the sessions accessible for this user, between the dates indicated
     * @param $inidate initial date
     * @param $enddate end date
     * @return array the sessions available for this user in between the dates
     */
    public static function get_sessions($inidate, $enddate) {
        global $DB, $USER, $CFG;

        require_once("$CFG->libdir/accesslib.php");


        $context = context_system::instance();
        self::validate_context($context);

        $params = self::validate_parameters(self::get_sessions_parameters(), array('inidate' => $inidate, 'enddate' => $enddate));

        //It is not necessary to work with time offsets as we only indicate a date.
        $inistamp =  strtotime($params["inidate"]);

        $endstamp =  strtotime($params["enddate"]) + 86399;


        $sql = "SELECT acs.id, ac.id cid, ac.course courseid, ac.name, co.fullname coursename, acs.sessdate, acs.lasttaken, acs.description, acs.duration FROM
                (
                
                SELECT ac.*
                FROM {role_assignments} a 
                JOIN {role_capabilities} rc ON a.userid = :userid1 AND a.roleid = rc.roleid AND rc.capability = :capability1
                JOIN {context} c ON c.contextlevel = :modulecontext AND a.contextid = c.id
                JOIN {course_modules} cm ON cm.id = c.instanceid
                JOIN {modules} m ON m.name = :modulename AND cm.module = m.id
                JOIN {attcontrol} ac ON ac.id = cm.instance
                
                UNION
                
                SELECT ac.*
                FROM {role_assignments} a 
                JOIN {role_capabilities} rc ON a.userid = :userid2 AND a.roleid = rc.roleid AND rc.capability = :capability2
                JOIN {context} c ON c.contextlevel = :coursecontext AND a.contextid = c.id
                JOIN {attcontrol} ac ON ac.course = c.instanceid
                
                ) ac
                
                JOIN {course} co ON ac.course = co.id
                JOIN {attcontrol_sessions} acs ON acs.attcontrolid = ac.id
                WHERE acs.sessdate BETWEEN :inistamp AND :endstamp
                ORDER BY acs.sessdate ASC";

        $qparams = array("userid1" => $USER->id,
            "capability1" => "mod/attcontrol:takeattcontrols",
            "modulecontext" => CONTEXT_MODULE,
            "modulename" => "attcontrol",
            "userid2" => $USER->id,
            "capability2" => "mod/attcontrol:takeattcontrols",
            "coursecontext" => CONTEXT_COURSE,
            "inistamp" => $inistamp,
            "endstamp" => $endstamp,
        );



        $sessionsarray = $DB->get_records_sql($sql, $qparams);

        return $sessionsarray;
    }

    /**
     * Gets the information for taking the indicated session.
     * @param $sessionid session id
     * @return array the complete information of the session, for taking its attendance.
     */
    public static function get_session_take($sessionid) {
        global $DB, $USER, $CFG, $OUTPUT;

        require_once("$CFG->libdir/accesslib.php");


        $validparams = self::validate_parameters(self::get_session_take_parameters(), array('sessionid' => $sessionid));

        //Get this session's information.
        $sql = "SELECT cm.id, ats.id as asid, ats.attcontrolid, ats.groupid, ats.sessdate
                FROM {modules} m
                JOIN {course_modules} cm ON m.name = 'attcontrol' AND m.id = cm.module
                JOIN {attcontrol_sessions} ats ON ats.id = :sessionid AND cm.instance = ats.attcontrolid";

        $params = array("sessionid" => $validparams["sessionid"]);

        $sessioninfo = $DB->get_records_sql ( $sql, $params );


        //If no session with information, return as there's an error in the parameters.
        if (count($sessioninfo) == 0) throw new invalid_parameter_exception('invalid_sessionid');

        //Get this session's information and generate the context.
        $sessioninfo = reset($sessioninfo);

        $context = context_module::instance($sessioninfo->id);

        self::validate_context($context);

        //If this is a group session, get only the users for this group
        if ($sessioninfo->groupid > self::NOGROUPS) {
            list ( $esql, $params ) = get_enrolled_sql ( $context, 'mod/attcontrol:canbelisted', $sessioninfo->groupid);
        }
        else {
            list ( $esql, $params ) = get_enrolled_sql ( $context, 'mod/attcontrol:canbelisted' );
        }

        $sql = "SELECT u.id, u.firstname, u.lastname, u.email, f.contextid pic1, u.picture pic2, u.username, atl.statusid status, atr.remarks
                FROM {user} u
                LEFT JOIN {files} f ON u.picture = f.id
                LEFT JOIN ($esql) eu ON eu.id=u.id
                LEFT JOIN {attcontrol_log} atl ON sessionid = :sessionid AND atl.studentid = u.id
                LEFT JOIN {attcontrol_remarks} atr ON atl.id = atr.id
                WHERE u.deleted = 0 AND eu.id=u.id
                ORDER BY u.lastname, u.firstname";

        $qparams = array("sessionid" => $validparams["sessionid"]);

        $resultsarray = $DB->get_records_sql ( $sql, array_merge($params, $qparams) );



        return $resultsarray;

    }

    /**
     * Saves the attcontrol data for this session
     * @param $sessionid session id
     * @param $takedata take attendance data
     */
    public static function save_session_take($sessionid, $takedata) {
        global $DB, $USER, $CFG, $OUTPUT;

        require_once("$CFG->libdir/accesslib.php");

        $validparams = self::validate_parameters(self::save_session_take_parameters(), array('sessionid' => $sessionid, 'takedata' => $takedata));

        foreach ($validparams["takedata"] as $td) {
            self::save_session_take_user_data($validparams["sessionid"], $td);
        }
    }

    /**
     * Saves the the attendance data for a session and a particular user
     * @param $sessionid session id
     * @param $userdata user information for this attendance take
     */
    private static function save_session_take_user_data($sessionid, $userdata) {
        global $DB, $USER, $CFG, $OUTPUT;

        $log = new stdClass();

        $log->sessionid = $sessionid;
        $log->studentid = $userdata["userid"];
        $log->statusid = $userdata["statusid"];
        $log->timetaken = time();
        $log->takenby = $USER->id;

        $existinglog = $DB->get_record_sql('SELECT * FROM {attcontrol_log} WHERE sessionid = ? AND studentid = ?',
            array($log->sessionid, $log->studentid));

        $updateditems = 0;
        $updatedremarks = 0;

        if ($existinglog === false) {
            //INSERT INFORMATION
            $log->id = $DB->insert_record("attcontrol_log", $log);
            $updateditems++;
        }
        else {
            $log->id = $existinglog->id;
            if ($existinglog->statusid != $log->statusid) {
                //UPDATE INFORMATION
                $DB->update_record("attcontrol_log", $log);
                $updateditems++;
            }
        }

        $existingremarks = $DB->get_record_sql('SELECT * FROM {attcontrol_remarks} WHERE id = ?', array($log->id));

        $remarks = new stdClass();
        $remarks->remarks = $userdata["remarks"];
        $remarks->id = $log->id;

        if ($existingremarks === false) {
            if (trim($userdata["remarks"])) {
                $DB->execute("INSERT INTO {attcontrol_remarks} (id, remarks) VALUES (?,?)", array($remarks->id, $remarks->remarks));
                $updatedremarks++;
            }
        }
        else {
            if (count(trim($userdata["remarks"]))>0) {
                $DB->update_record("attcontrol_remarks", $remarks);
            }
            else {
                $DB->delete_records("attcontrol_remarks", "id = ?", array($remarks->id));
            }

            $updatedremarks++;
        }

        if ($updateditems > 0 || $updatedremarks > 0) {
            //UPDATE SESSION TAKING DATA
            $session = new stdClass();
            $session->id = $sessionid;
            $session->lasttaken = time();
            $session->lasttakenby = $USER->id;

            $DB->update_record("attcontrol_sessions", $session);
        }
    }


    /**
     * Saves the modified report data
     * @param $reportdata the report data to be saved
     */
    public static function save_report_data($reportdata) {
        global $DB, $USER, $CFG, $OUTPUT;

        require_once("$CFG->libdir/accesslib.php");

        $validparams = self::validate_parameters(self::save_report_data_parameters(), array('reportdata' => $reportdata));

        foreach ($validparams["reportdata"] as $rd) {
            self::save_report_user_data($rd);
        }
    }


    /**
     * Saves the changes in a particular report
     * @param $rd particular report data
     */
    private static function save_report_user_data($rd) {
        global $DB, $USER, $CFG, $OUTPUT;

        //Save attendance take

        $log = new stdClass();

        $log->id = $rd["atlid"];
        $log->statusid = $rd["statusid"];
        $log->timetaken = time();
        $log->takenby = $USER->id;


        $DB->update_record("attcontrol_log", $log);
        $updatedlogs ++;


        //Save remarks

        $existingremarks = $DB->get_record_sql('SELECT * FROM {attcontrol_remarks} WHERE id = ?', array($log->id));

        $remarks = new stdClass();
        $remarks->remarks = $rd["remarks"];
        $remarks->id = $log->id;

        if ($existingremarks === false) {
            if (trim($rd["remarks"])) {
                $DB->execute("INSERT INTO {attcontrol_remarks} (id, remarks) VALUES (?,?)", array($remarks->id, $remarks->remarks));
                $updatedremarks++;
            }
        }
        else {
            if (count(trim($rd["remarks"]))>0) {
                $DB->update_record("attcontrol_remarks", $remarks);
            }
            else {
                $DB->delete_records("attcontrol_remarks", "id = ?", array($remarks->id));
            }

            $updatedremarks++;
        }

    }


    /**
     * Creates a new session
     * @param $courseid course id
     * @param $attid attcontrol id
     * @param $group group id
     * @param $timestamp timestamp for this session
     * @param $duration duration of the session
     * @param $recurrence recurrence rule of this session
     * @param $description description for it (can be empty)
     */
    public static function save_new_session($courseid, $attid, $group, $timestamp, $duration, $recurrence, $description) {
        global $DB, $USER, $CFG, $OUTPUT;

        require_once("$CFG->libdir/accesslib.php");


        $context = context_system::instance();
        self::validate_context($context);


        $vpar= self::validate_parameters(self::save_new_session_parameters(),
            array('courseid' => $courseid,
                'attid' => $attid,
                'group' => $group,
                'timestamp' => $timestamp,
                'duration' => $duration,
                'recurrence' => $recurrence,
                'description' => $description));

        $vpar['timestamp'] = $vpar['timestamp']/1000;


        //1. Get the module for this $course and $attid.

        $sql = "SELECT cm.id, cm.course, cm.instance, cm.groupmode
                FROM {course_modules} cm
                JOIN {modules} m ON m.name = 'attcontrol' AND m.id = cm.module 
                WHERE cm.course = ? AND cm.instance = ?";

        $coursemodule = $DB->get_record_sql($sql, array($vpar['courseid'], $vpar['attid']));

        if ($coursemodule->course != $vpar['courseid'] || $coursemodule->instance != $vpar['attid']) {
            throw new invalid_parameter_exception('invalid_atcid');
        }

        //2. Check if user is to manage attcontrols in this module context
        $modcontext = context_module::instance($coursemodule->id);
        require_capability('mod/attcontrol:manageattcontrols', $modcontext);

        //Check if this activity accepts groups & this group is one of the allowed in this activity module.
        if ($coursemodule->groupmode != self::NOGROUPS) {
            if ($vpar["group"] == self::COMMONSESSION) {
                $vpar["group"] = 0;
            }
            else {
                //Check group for this activity
                $sql = "SELECT * FROM {groups} g WHERE g.courseid = ? AND g.id = ?";
                $group = $DB->get_record_sql($sql, array($vpar['courseid'], $vpar['group']));

                if ($group->id != $vpar['group']) {
                    throw new invalid_parameter_exception('invalid_groupid');
                }
            }
        }
        else {
            $vpar["group"] = 0;
        }

        $vpar["timemodified"] = time();

        if (trim($vpar["recurrence"])) {

            $recurrencerules = explode(";", $vpar["recurrence"]);
            $interval = 1;
            $count = self::BIGNUMBER;
            $until = self::BIGNUMBER;
            $cando = false;

            foreach ($recurrencerules as $r) {
                $r = explode("=", $r);

                $rule = $r[0];
                $content = $r[1];

                switch ($rule) {
                    case "FREQ":
                        $cando = ($content == "WEEKLY");
                        break;
                    case "BYDAY":
                        $weekdays = self::getWeekDaysArray($content);
                        break;
                    case "UNTIL":
                        $until = strtotime(substr($content, 0, 8));
                        break;
                    case "COUNT":
                        $count = $content;
                        break;
                    case "INTERVAL":
                        $interval = $content;
                        break;
                    case "WKST":
                        $wkst = $content;
                        break;
                }
            }

            if ($cando) {
                $date = $vpar["timestamp"];

                $wkend = (self::getWeekDayNum($wkst) + 6)%7;

                $i = 0;

                while ($i < $count && $date < $until) {
                    $wd = date("w", $date);

                    if ($weekdays[$wd]) {
                        $sql = "INSERT INTO {attcontrol_sessions} (attcontrolid, groupid, sessdate, duration, timemodified, description)
                                VALUES (?, ?, ?, ?, ?, ?)";

                        $params = array($vpar["attid"], $vpar["group"], $date, $vpar["duration"], $vpar["timemodified"], $vpar["description"]);

                        $DB->execute($sql, $params);

                        $i++;
                    }

                    //If this is the end of a week: jump as many weeks as the interval indicates.
                    if ($wd == $wkend) {
                        $date+=(($interval-1)*604800);
                    }

                    //Next day
                    $date+=86400;
                }
            }
            else {
                throw new invalid_parameter_exception('invalid_recurrence');
            }
        }
        else {
            //Instert just a session (no recurrence)

            $sql = "INSERT INTO {attcontrol_sessions} (attcontrolid, groupid, sessdate, duration, timemodified, description)
                    VALUES (?, ?, ?, ?, ?, ?)";
            $params = array($vpar["attid"], $vpar["group"], $vpar["timestamp"], $vpar["duration"], $vpar["timemodified"], $vpar["description"]);

            $DB->execute($sql, $params);
        }
    }


    /**
     * Saves the edited information for a session
     * @param $sessionid session id
     * @param $timestamp new timestamp
     * @param $duration new duration
     * @param $description new description (can be empty)
     */
    public static function save_edit_session($sessionid, $timestamp, $duration, $description) {
        global $DB, $USER, $CFG, $OUTPUT;

        require_once("$CFG->libdir/accesslib.php");


        $context = context_system::instance();
        self::validate_context($context);


        $vpar= self::validate_parameters(self::save_edit_session_parameters(),
            array('sessionid' => $sessionid,
                'timestamp' => $timestamp,
                'duration' => $duration,
                'description' => $description));

        $vpar['timestamp'] = $vpar['timestamp']/1000;


        //1. Validate permissions for this attendance module
        $sql = "SELECT cm.id, s.id sessionid
                FROM {attcontrol_sessions} s
                JOIN {attcontrol} ac ON s.id = ? AND s.attcontrolid = ac.id
                JOIN {course_modules} cm ON cm.course = ac.course AND cm.instance = ac.id
                JOIN {modules} m ON m.name = 'attcontrol' AND m.id = cm.module";

        $coursemodule = $DB->get_record_sql($sql, array($vpar['sessionid']));

        if ($coursemodule->sessionid != $vpar['sessionid']) {
            throw new invalid_parameter_exception('invalid_atcid');
        }

        //2. Check if user is to manage attcontrols in this module context
        $modcontext = context_module::instance($coursemodule->id);
        require_capability('mod/attcontrol:manageattcontrols', $modcontext);

        $now = time();

        $DB->execute("UPDATE {attcontrol_sessions} SET sessdate = ?, duration = ?, timemodified = ?, description = ? WHERE id = ?",
            array( $vpar["timestamp"], $vpar["duration"], $now, $vpar["description"], $vpar["sessionid"]));
    }


    /**
     * Deletes a session
     * @param $sessid session id
     */
    public static function delete_session($sessid) {
        global $DB, $USER, $CFG, $OUTPUT;

        require_once("$CFG->libdir/accesslib.php");

        $context = context_system::instance();
        self::validate_context($context);

        $vpar= self::validate_parameters(self::delete_session_parameters(),
            array('sessionid' => $sessid));


        // Validate permissions for this attendance module
        $sql = "SELECT cm.id, s.id sessionid
                FROM {attcontrol_sessions} s
                JOIN {attcontrol} ac ON s.id = ? AND s.attcontrolid = ac.id
                JOIN {course_modules} cm ON cm.course = ac.course AND cm.instance = ac.id
                JOIN {modules} m ON m.name = 'attcontrol' AND m.id = cm.module";

        $coursemodule = $DB->get_record_sql($sql, array($vpar['sessionid']));

        if ($coursemodule->sessionid != $vpar['sessionid']) {
            throw new invalid_parameter_exception('invalid_atcid');
        }

        // Check if user is to manage attcontrols in this module context
        $modcontext = context_module::instance($coursemodule->id);
        require_capability('mod/attcontrol:manageattcontrols', $modcontext);


        $DB->execute("DELETE FROM {attcontrol_sessions} WHERE id = ?", array($vpar["sessionid"]));

        $DB->execute("DELETE FROM {attcontrol_log} WHERE sessionid = ?", array($vpar["sessionid"]));
    }


    /**
     * Gets the students participating in a certain course, attcontrol and group
     * @param $courseid course id
     * @param $attcontrolid attcontrol id
     * @param $groupid group id
     * @return array full information of the students according to the criteria set
     */
    public static function get_students($courseid, $attcontrolid, $groupid) {
        global $DB, $USER, $CFG, $OUTPUT;

        //1. Validate parameters
        $validparams = self::validate_parameters(self::get_students_parameters(), array('courseid' => $courseid, 'attcontrolid' => $attcontrolid, 'groupid' => $groupid));

        //2. Validate information to get the students
        $attcourses = self::get_attendance_attcourses();

        $course = self::validateCourses($attcourses, $validparams['courseid']);

        if ($course != "") {
            $attcontrol = self::validateAttControls($attcourses, $course, $validparams['attcontrolid']);

            if ($attcontrol != "") {
                $group = self::validateGroups($attcourses, $course, $attcontrol, $validparams['groupid']);

                if ($validparams['groupid'] != -1 && $group == "") throw new invalid_parameter_exception('invalid_group_id');
            }
            else {
                throw new invalid_parameter_exception('invalid_attcontrol_id');
            }
        }
        else {
            throw new invalid_parameter_exception('invalid_course_id');
        }

        //3. Get all the contexts for this attcontrol instances.
        $sql = "SELECT cm.id, ct.path
                FROM {modules} m
                JOIN {course_modules} cm ON m.name = 'attcontrol' AND m.id = cm.module AND cm.instance IN ($attcontrol)
                JOIN {context} ct ON ct.contextlevel = ? AND cm.id = ct.instanceid";

        $coursemodules = $DB->get_records_sql ( $sql, array(CONTEXT_MODULE) );


        $allusers = array();

        foreach ($coursemodules as $cm) {
            $context = context_module::instance($cm->id);

            if ($group > self::NOGROUPS) {
                list ( $esql, $params ) = get_enrolled_sql ( $context, 'mod/attcontrol:canbelisted', $group);
            }
            else {
                list ( $esql, $params ) = get_enrolled_sql ( $context, 'mod/attcontrol:canbelisted' );
            }

            $sql = "SELECT u.id, u.firstname, u.lastname, u.email, f.contextid pic1, u.picture pic2, u.username
                    FROM {user} u
                    LEFT JOIN {files} f ON u.picture = f.id
                    LEFT JOIN ($esql) eu ON eu.id=u.id
                    WHERE u.deleted = 0 AND eu.id=u.id";


            $users = $DB->get_records_sql ($sql, $params );

            foreach ($users as $u) {
                $allusers[$u->id] = $u;
            }
        }

        usort($allusers, "self::usercmp");

        return $allusers;
    }


    /**
     * Gets the reports information according to the parameters indicated
     * @param $studentid student id
     * @param $startdate start date
     * @param $enddate end date
     * @param $courses courses in which filter
     * @param $attcontrols attcontrol instances in which filter
     * @param $groups groups in which filter
     * @return array report according to the filter set
     */
    public static function get_reports($studentid, $startdate, $enddate, $courses, $attcontrols, $groups) {
        global $DB, $USER, $CFG, $OUTPUT;

        $vp = self::validate_parameters(self::get_reportsummary_parameters(),
            array('studentid' => $studentid, 'startdate' => $startdate, 'enddate' => $enddate, 'courses' => $courses, 'attcontrols' => $attcontrols, 'groups' => $groups));

        $cs = implode(",", $vp["courses"]);
        $acs = implode(",", $vp["attcontrols"]);
        $gs = implode(",", $vp["groups"]);


        $inistamp =  strtotime($startdate);

        $endstamp =  strtotime($enddate) + 86399;

        $where = array();

        $where[] = "acs.sessdate >= $inistamp";
        $where[] = "acs.sessdate <= $endstamp";
        if ($studentid != -1) $where[] = "acl.studentid IN ($studentid)";
        if ($acs != "") $where[] = "a.id IN ($acs)";
        if ($cs != "") $where[] = "a.course IN ($cs)";
        if ($gs != "") $where[] = "acs.groupid IN ($gs)";

        $where = "AND " . implode(" AND ", $where);


        $sql = "SELECT acl.id, u.id uid, u.firstname ufirstname, u.lastname ulastname, acs.id sid, acs.sessdate sdate, c.fullname cname, a.name acname, acl.statusid statusid, acr.remarks AS remarks
                FROM {attcontrol} a
                JOIN {course} c ON a.course = c.id
                JOIN {attcontrol_sessions} acs ON a.id = acs.attcontrolid
                JOIN {attcontrol_log} acl ON acs.id = acl.sessionid
                JOIN {user} u ON acl.studentid = u.id
                LEFT JOIN {attcontrol_remarks} acr ON acl.id = acr.id
                WHERE 1 = 1 $where
                ORDER BY u.lastname, u.firstname, acs.sessdate";

        $resultsarray = $DB->get_records_sql ($sql);

        return $resultsarray;
    }


    /**
     * Gets the summary information (counts) of a report, according to the filters set
     * @param $studentid student id
     * @param $startdate start date
     * @param $enddate end date
     * @param $courses courses in which find information
     * @param $attcontrols attcontrol instance in which look for the information
     * @param $groups groups for the filter
     * @return array summary information
     */
    public static function get_reportsummary($studentid, $startdate, $enddate, $courses, $attcontrols, $groups) {
        global $DB, $USER, $CFG, $OUTPUT;

        $vp = self::validate_parameters(self::get_reportsummary_parameters(),
            array('studentid' => $studentid, 'startdate' => $startdate, 'enddate' => $enddate, 'courses' => $courses, 'attcontrols' => $attcontrols, 'groups' => $groups, ));

        $cs = implode(",", $vp["courses"]);
        $acs = implode(",", $vp["attcontrols"]);
        $gs = implode(",", $vp["groups"]);

        $inistamp =  strtotime($startdate);

        $endstamp =  strtotime($enddate) + 86399;

        $where = array();

        $where[] = "acs.sessdate >= $inistamp";
        $where[] = "acs.sessdate <= $endstamp";
        if ($studentid != -1) $where[] = "acl.studentid IN ($studentid)";
        if ($acs != "") $where[] = "a.id IN ($acs)";
        if ($cs != "") $where[] = "a.course IN ($cs)";
        if ($gs != "") $where[] = "acs.groupid IN ($gs)";

        $where = "AND " . implode(" AND ", $where);


        $sql = "SELECT acl.statusid, COUNT(acl.statusid) statuscount
                FROM {attcontrol} a
                JOIN {attcontrol_sessions} acs ON a.id = acs.attcontrolid
                JOIN {attcontrol_log} acl ON acs.id = acl.sessionid
                WHERE 1 = 1 $where
                GROUP BY acl.statusid";

        $resultsarray = $DB->get_records_sql ($sql);


        return $resultsarray;
    }


    /**
     * Validates if certain course is in the list of the available courses for this user.
     * If found, returns the course.
     * If the ALL value is found, returns a list with all the courses available for this user.
     * Else, it returns and empty string.
     * @param $attcourses list of courses
     * @param $courseid course to find
     * @return string list of courses according to the filter indicated
     */
    private static function validateCourses($attcourses, $courseid) {
        $allcourses = array();
        $found = false;

        foreach ($attcourses as $c) {
            $allcourses[] = $c["courseid"];
            if ($c["courseid"] == $courseid) $found = true;
        }

        if ($found) return $courseid;
        else if ($courseid == self::ALL) return implode(",", $allcourses);
        else return "";
    }


    /**
     * Validates if certain attcontrol instance is in the list of the available attcontrols for this user.
     * If found, returns the attcontrol id.
     * If the ALL value is found, returns a list with all the attcontrols available for this user.
     * Else it returns an empty list.
     * @param $attcourses list of courses with the attcontrols associated to them
     * @param $courses list of courses available for the user
     * @param $attcontrolid attcontrol id to find
     * @return string list of attcontrols according to the filter indicated
     */
    private static function validateAttControls($attcourses, $courses, $attcontrolid) {
        $selcourses = explode(",", $courses);

        $allattcontrols = array();
        $found = false;

        foreach ($attcourses as $c) {
            if (array_search($c["courseid"], $selcourses) !== false) {
                foreach($c["attcontrols"] as $ac) {
                    $allattcontrols[] = $ac["acid"];
                    if ($ac["acid"] == $attcontrolid) $found = true;
                }
            }
        }

        if ($found) return $attcontrolid;
        else if ($attcontrolid == self::ALL) return implode(",", $allattcontrols);
        else return "";
    }


    /**
     * Validates if certain group is in the list of the available groups for this user.
     * If found, returns the group id.
     * If the ALL value is found, returns a list with all the groups available for this user.
     * Else it returns an empty list.
     * @param $attcourses list of courses with the attcontrols associated to them
     * @param $courses list of courses available for the user
     * @param $attcontrols list of attcontrol instances available for the user
     * @param $groupid group id to find
     * @return string list of groups according to the filter indicated
     */
    private static function validateGroups($attcourses, $courses, $attcontrols, $groupid) {
        $selcourses = explode(",", $courses);
        $selattcontrols = explode(",", $attcontrols);

        $allgroups = array();
        $found = false;

        foreach ($attcourses as $c) {
            if (array_search($c["courseid"], $selcourses) !== false) {
                foreach($c["attcontrols"] as $ac) {
                    if (array_search($ac["acid"], $selattcontrols) !== false) {
                        foreach($ac["groups"] as $g) {
                            $allgroups[] = $g["groupid"];
                            if ($g["groupid"] == $groupid) $found = true;
                        }
                    }
                }
            }
        }

        if ($groupid == self::ALL) return 0;
        else if ($found) return $groupid;
        else return "";
    }


    /**
     * Gets the weekdays of a recurrence rule
     * @param $rule recurrence rule
     * @return array days indicated in the recurrence rule
     */
    private static function getWeekDaysArray($rule) {
        $rules = explode(",", $rule);

        $rarray = array();

        foreach ($rules as $r) {
            $rarray[self::getWeekDayNum($r)] = true;
        }

        return $rarray;
    }

    /**
     * Gets the number associated to a weekday string code.
     * @param $code weekday string code
     * @return int number associated to it
     */
    private static function getWeekDayNum($code) {
        switch ($code){
            case "SU":
                return 0;
            case "MO":
                return 1;
            case "TU":
                return 2;
            case "WE":
                return 3;
            case "TH":
                return 4;
            case "FR":
                return 5;
            case "SA":
                return 6;
        }

    }

    /**
     * Compares to user instances in order to sort them
     * @param $u1 first user
     * @param $u2 second user
     * @return int result of this comparing
     */
    function usercmp($u1, $u2)
    {
        $ret = strcmp($u1->lastname, $u2->lastname);
        if ($ret != 0) return $ret;
        else return strcmp($u1->firstname, $u2->firstname);
    }


    /**
     * Return structure for the get_user function
     * @return external_multiple_structure return structure as described by the service
     */
    public static function get_user_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'user id'),
                    'username' => new external_value(PARAM_TEXT, 'user name'),
                    'firstname' => new external_value(PARAM_TEXT, 'user\'s first name'),
                    'lastname' => new external_value(PARAM_TEXT, 'user\'s last name'),
                    'pic1' => new external_value(PARAM_INT, 'user image 1st part'),
                    'pic2' => new external_value(PARAM_INT, 'user image 2nd part'),
                    'hasattcontrols' => new external_value(PARAM_INT, 'integer indicating how many attcontrols has the user access to.'),
                    'hasatthomes' => new external_value(PARAM_INT, 'integer indicating how many atthomes has the user access to.'),
                )
            )
        );
    }

    /**
     * Return structure for the get_sessions function
     * @return external_multiple_structure return structure as described by the service
     */
    public static function get_sessions_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'id of the session instance'),
                    'cid' => new external_value(PARAM_INT, 'id of course enrolment instance'),
                    'courseid' => new external_value(PARAM_INT, 'id of course'),
                    'name' => new external_value(PARAM_RAW, 'name of the attendance item'),
                    'coursename' => new external_value(PARAM_RAW, 'name of the course'),
                    'sessdate' => new external_value(PARAM_INT, 'session date and time'),
                    'lasttaken' => new external_value(PARAM_INT, 'session last take date and time'),
                    'description' => new external_value(PARAM_RAW, 'session description'),
                    'duration' => new external_value(PARAM_INT, 'session duration')
                )
            )
        );
    }

    /**
     * Return structure for the get_session_take function
     * @return external_multiple_structure return structure as described by the service
     */
    public static function get_session_take_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'user id'),
                    'username' => new external_value(PARAM_TEXT, 'user name'),
                    'firstname' => new external_value(PARAM_TEXT, 'user\'s first name'),
                    'lastname' => new external_value(PARAM_TEXT, 'user\'s last name'),
                    'status' => new external_value(PARAM_INT, 'attendance status for the user (-1 if not set)'),
                    'remarks' => new external_value(PARAM_TEXT, 'attendance status remarks'),
                    'pic1' => new external_value(PARAM_INT, 'user image 1st part'),
                    'pic2' => new external_value(PARAM_INT, 'user image 2nd part'),
                )
            )
        );

    }

    /**
     * Return structure for the get_attendance_statuses function
     * @return external_multiple_structure return structure as described by the service
     */
    public static function get_attendance_statuses_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'status id'),
                    'acronym' => new external_value(PARAM_TEXT, 'status acronym'),
                    'description' => new external_value(PARAM_TEXT, 'status description')
                )
            )
        );
    }


    /**
     * Return structure for the get_attendance_attcourses function
     * @return external_multiple_structure return structure as described by the service
     */
    public static function get_attendance_attcourses_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'courseid' => new external_value(PARAM_INT, 'course id'),
                    'coursename' => new external_value(PARAM_RAW, 'course full name'),
                    'courseshortname' => new external_value(PARAM_TEXT, 'course short name'),
                    'attcontrols' => new external_multiple_structure(
                            new external_single_structure(
                                array(
                                    'acid' => new external_value(PARAM_INT, 'attcontrol id'),
                                    'acname' => new external_value(PARAM_TEXT, 'attcontrol name'),
                                    'groupmode' => new external_value(PARAM_INT, 'attcontrol group mode'),
                                    'groups' => new external_multiple_structure(
                                            new external_single_structure(
                                                array(
                                                    'groupid' => new external_value(PARAM_INT, 'group id'),
                                                    'groupname' => new external_value(PARAM_TEXT, 'group name'),
                                                ), VALUE_DEFAULT, array()
                                            ), 'list of groups'
                                        )
                                ), VALUE_DEFAULT, array()
                            ), 'list of attcontrols'
                        )
                )
            ));
    }


    /**
     * Return structure for the save_session_take function
     */
    public static function save_session_take_returns() {
    }

    /**
     * Return structure for the save_report_data function
     */
    public static function save_report_data_returns() {
    }

    /**
     * Return structure for the save_new_session function
     */
    public static function save_new_session_returns() {
    }

    /**
     * Return structure for the delete_session function
     */
    public static function delete_session_returns() {
    }

    /**
     * Return structure for the save_edit_session function
     */
    public static function save_edit_session_returns() {
    }


    /**
     * Return structure for the get_students function
     * @return external_multiple_structure return structure as described by the service
     */
    public static function get_students_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'user id'),
                    'username' => new external_value(PARAM_TEXT, 'user name'),
                    'firstname' => new external_value(PARAM_TEXT, 'user\'s first name'),
                    'lastname' => new external_value(PARAM_TEXT, 'user\'s last name'),
                    'pic1' => new external_value(PARAM_INT, 'user image 1st part'),
                    'pic2' => new external_value(PARAM_INT, 'user image 2nd part'),
                )
            )
        );

    }


    /**
     * Return structure for the get_reportsummary function
     * @return external_multiple_structure return structure as described by the service
     */
    public static function get_reportsummary_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'statusid' => new external_value(PARAM_INT, 'status id'),
                    'statuscount' => new external_value(PARAM_INT, 'count of statuses'),
                )
            )
        );

    }

    /**
     * Return structure for the get_reports function
     * @return external_multiple_structure return structure as described by the service
     */
    public static function get_reports_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'attendance log id'),
                    'uid' => new external_value(PARAM_INT, 'attendance log user id'),
                    'ufirstname' => new external_value(PARAM_TEXT, 'user first name'),
                    'ulastname' => new external_value(PARAM_TEXT, 'user last name'),
                    'sid' => new external_value(PARAM_INT, 'session id'),
                    'sdate' => new external_value(PARAM_RAW, 'session date'),
                    'cname' => new external_value(PARAM_TEXT, 'course name'),
                    'acname' => new external_value(PARAM_TEXT, 'attcontrol name'),
                    'statusid' => new external_value(PARAM_INT, 'status id'),
                    'remarks' => new external_value(PARAM_TEXT, 'remarks for this attendance log'),
                )
            )
        );
    }



}
