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
 * attcontrol module renderable components are defined here
 *
 * @package    mod_attcontrol
 * @copyright  2013 José Luis Antúnez <jantunez@xtec.cat>
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__).'/locallib.php');


/**
 * Represents info about attcontrol tabs.
 *
 * Proxy class for security reasons (renderers must not have access to all attcontrol methods)
 *
 * @copyright  2013 José Luis Antúnez <jantunez@xtec.cat>
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
class attcontrol_tabs implements renderable {
    /**
     * Identifier for the sessions tab
     */
    const TAB_SESSIONS      = 1;
    /**
     * Identifier for the add sessions tab
     */
    const TAB_ADD           = 2;
    /**
     * Identifier for the report tab
     */
    const TAB_REPORT        = 3;
    /**
     * Identifier for the export tab
     */
    const TAB_EXPORT        = 4;
    /**
     * Identifier for the individual report tab
     */
    const TAB_INDIVIDUALREPORT        = 5;
    /**
     * Identifier for the individual report export tab
     */
    const TAB_INDIVIDUALEXPORT        = 6;
    /**
     * @var Identifier of the currently selected tab
     */
    public $currenttab;
    /**
     * Attcontrol instance
     */
    private $att;

    /**
     * Prepare info about sessions for attendance taking into account view parameters.
     *
     * @param attcontrol $att AttControl instance
     * @param $currenttab - one of attcontrol_tabs constants
     */
    public function  __construct(attcontrol $att, $currenttab=null) {
        $this->att = $att;
        $this->currenttab = $currenttab;
    }

    /**
     * Return array of rows where each row is an array of tab objects
     * taking into account permissions of current user
     */
    public function get_tabs() {
        $toprow = array();
        if ($this->att->perm->can_manage() or
            $this->att->perm->can_take() or
            $this->att->perm->can_change()) {
            $toprow[] = new tabobject(self::TAB_SESSIONS, $this->att->url_manage()->out(),
                get_string('sessionlist', 'attcontrol'));
        }

        if ($this->att->perm->can_manage()) {
            $toprow[] = new tabobject(self::TAB_ADD,
                $this->att->url_sessions()->out(true, array('action' => att_sessions_page_params::ACTION_ADD)),
                get_string('addsessions', 'attcontrol'));
        }

        if ($this->att->perm->can_view_reports()) {
            $toprow[] = new tabobject(self::TAB_REPORT, $this->att->url_report()->out(),
                get_string('coursereport', 'attcontrol'));
        }

        if ($this->att->perm->can_export()) {
            $toprow[] = new tabobject(self::TAB_EXPORT, $this->att->url_export()->out(),
                get_string('courseexport', 'attcontrol'));
        }

        if ($this->att->perm->can_view_reports()) {
            $toprow[] = new tabobject(self::TAB_INDIVIDUALREPORT, $this->att->url_individualreport()->out(),
                get_string('individualreport', 'attcontrol'));
        }

        if ($this->att->perm->can_export()) {
            $toprow[] = new tabobject(self::TAB_INDIVIDUALEXPORT, $this->att->url_individualexport()->out(),
                get_string('individualexport', 'attcontrol'));
        }

        return array($toprow);
    }
}


/**
 * Class attcontrol_filter_controls
 */
class attcontrol_filter_controls implements renderable {
    /** @var int current view mode */
    public $pageparams;

    /**
     * @var stdclass
     */
    public $cm;

    /**
     * @var
     */
    public $curdate;

    /**
     * @var
     */
    public $prevcur;
    /**
     * @var
     */
    public $nextcur;
    /**
     * @var Current date, as a string
     */
    public $curdatetxt;

    /**
     * @var URL path for this access.
     */
    private $urlpath;
    /**
     * @var URL parameters indicated in the query
     */
    private $urlparams;

    /**
     * @var attcontrol
     */
    private $att;

    /**
     * @param attcontrol $att
     */
    public function __construct(attcontrol $att) {
        global $PAGE;

        $this->pageparams = $att->pageparams;

        $this->cm = $att->cm;

        $this->initialize_date_parameters($att);

        $this->urlpath = $PAGE->url->out_omit_querystring();
        $params = $att->pageparams->get_significant_params();
        $params['id'] = $att->cm->id;
        $this->urlparams = $params;

        $this->att = $att;
    }

    /**
     * @param attcontrol $att
     */
    protected function initialize_student_parameters (attcontrol &$att) {
        $this->studentfilter = $att->pageparams->studentfilter;
    }

    /**
     * @param attcontrol $att
     */
    protected function initialize_date_parameters (attcontrol &$att) {
        $this->curdate = $att->pageparams->curdate;

        $date = usergetdate($att->pageparams->curdate);
        $mday = $date['mday'];
        $wday = $date['wday'];
        $mon = $date['mon'];
        $year = $date['year'];

        switch ($this->pageparams->view) {
            case ATT_VIEW_DAYS:
                $format = get_string('strftimedm', 'atthome');
                $this->prevcur = make_timestamp($year, $mon, $mday - 1);
                $this->nextcur = make_timestamp($year, $mon, $mday + 1);
                $this->curdatetxt =  userdate($att->pageparams->startdate, $format);
                break;
            case ATT_VIEW_WEEKS:
                $format = get_string('strftimedm', 'atthome');
                $this->prevcur = $att->pageparams->startdate - WEEKSECS;
                $this->nextcur = $att->pageparams->startdate + WEEKSECS;
                $this->curdatetxt = userdate($att->pageparams->startdate, $format).
                    " - ".userdate($att->pageparams->enddate, $format);
                break;
            case ATT_VIEW_MONTHS:
                $format = '%B';
                $this->prevcur = make_timestamp($year, $mon - 1);
                $this->nextcur = make_timestamp($year, $mon + 1);
                $this->curdatetxt = userdate($att->pageparams->startdate, $format);
                break;
        }
    }

    /**
     * @param array $params
     * @return moodle_url
     */
    public function url($params=array()) {
        $params = array_merge($this->urlparams, $params);

        return new moodle_url($this->urlpath, $params);
    }

    /**
     * @return mixed
     */
    public function url_path() {
        return $this->urlpath;
    }

    /**
     * @param array $params
     * @return array
     */
    public function url_params($params=array()) {
        $params = array_merge($this->urlparams, $params);

        return $params;
    }

    /**
     * Gets the group mode for this AttControl instance.
     * @return int the group mode
     */
    public function get_group_mode() {
        return $this->att->get_group_mode();
    }

    /**
     * @return mixed
     */
    public function get_sess_groups_list() {
        return $this->att->pageparams->get_sess_groups_list();
    }

    /**
     * @return mixed
     */
    public function get_current_sesstype() {
        return $this->att->pageparams->get_current_sesstype();
    }

    /**
     * @return mixed
     */
    public function get_students_list() {
        return $this->att->pageparams->get_students_list();
    }

    /**
     * @return mixed
     */
    public function get_current_student() {
        return $this->att->pageparams->get_current_student();
    }
}

/**
 * Class attcontrol_individualreport_filter_controls
 */
class attcontrol_individualreport_filter_controls extends attcontrol_filter_controls {
    /**
     * @param attcontrol $att
     */
    public function __construct(attcontrol $att) {
        parent::__construct($att);

        $this->initialize_date_parameters($att);

        $this->initialize_student_parameters($att);
    }
}

/**
 * Class attcontrol_ownreport_filter_controls
 */
class attcontrol_ownreport_filter_controls extends attcontrol_filter_controls {

    /**
     * @param attcontrol $att
     * @param null $studentid
     */
    public function __construct(attcontrol $att, $studentid = null) {
        parent::__construct($att);

        $this->initialize_date_parameters($att);

        $this->initialize_student_parameters($att);
    }
}


/**
 * Represents info about attcontrol sessions taking into account view parameters.
 *
 * @copyright  2013 José Luis Antúnez <jantunez@xtec.cat>
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class attcontrol_manage_data implements renderable {
    /** @var array of sessions*/
    public $sessions;

    /** @var attcontrol_permissions permission of current user for attcontrol instance*/
    public $perm;

    /**
     * @var
     */
    public $groups;

    /**
     * @var
     */
    public $id;

    /** @var total number of registers according to the conditions*/
    public $total;
    /** @var number of registers shown per page*/
    public $perpage;
    /** @var selected page number*/
    public $page;
    /** @var number of the first register shown*/
    public $offset;


    /** @var attcontrol */
    private $att;
    /**
     * Prepare info about attcontrol sessions taking into account view parameters.
     *
     * @param attcontrol $att instance
     */
    public function __construct(attcontrol $att) {
        $this->perm = $att->perm;

        $this->id = required_param('id', PARAM_INT);

        //Populate pagination parameters
        $this->page = optional_param('page', 0, PARAM_INT);
        $this->perpage = get_user_preferences('attcontrol_perpage', 10);
        $this->offset = $this->page * $this->perpage;
        $this->total = $att->get_filtered_sessions_count($this->offset, $this->perpage);


        $this->sessions = $att->get_filtered_sessions($this->offset, $this->perpage);

        $this->groups = groups_get_all_groups($att->course->id);

        $this->att = $att;
    }


    /**
     * @return mixed
     */
    public function url_manage() {
        return url_helpers::url_manage($this->att);
    }

    /**
     * @param $sessionid
     * @param $grouptype
     * @return mixed
     */
    public function url_take($sessionid, $grouptype) {
        return url_helpers::url_take($this->att, $sessionid, $grouptype);
    }

    /**
     * @param null $sessionid
     * @param null $action
     * @return mixed
     */
    public function url_sessions($sessionid=null, $action=null) {
        return url_helpers::url_sessions($this->att, $sessionid, $action);
    }
}

/**
 * Class attcontrol_take_data
 */
class attcontrol_take_data implements renderable {
    /**
     * @var
     */
    public $users;

    /**
     * @var null
     */
    public $pageparams;
    /**
     * @var attcontrol_permissions
     */
    public $perm;

    /**
     * @var
     */
    public $groupmode;
    /**
     * @var stdclass
     */
    public $cm;

    /**
     * @var array
     */
    public $statuses;

    /**
     * @var
     */
    public $sessioninfo;

    /**
     * @var array
     */
    public $sessionlog;

    /**
     * @var array
     */
    public $sessions4copy;

    /**
     * @var bool
     */
    public $updatemode;

    /**
     * @var
     */
    private $urlpath;
    /**
     * @var
     */
    private $urlparams;
    /**
     * @var attcontrol
     */
    private $att;

    /**
     * @param attcontrol $att
     */
    public function  __construct(attcontrol $att) {
        if ($att->pageparams->grouptype) {
            $this->users = $att->get_users($att->pageparams->grouptype);
        } else {
            $this->users = $att->get_users($att->pageparams->group);
        }

        $this->pageparams = $att->pageparams;
        $this->perm = $att->perm;

        $this->groupmode = $att->get_group_mode();
        $this->cm = $att->cm;

        $this->statuses = $att->get_statuses();

        $this->sessioninfo = $att->get_session_info($att->pageparams->sessionid);
        $this->updatemode = $this->sessioninfo->lasttaken > 0;

        if (isset($att->pageparams->copyfrom)) {
            $this->sessionlog = $att->get_session_log($att->pageparams->copyfrom);
        } else if ($this->updatemode) {
            $this->sessionlog = $att->get_session_log($att->pageparams->sessionid);
        } else {
            $this->sessionlog = array();
        }

        if (!$this->updatemode) {
            $this->sessions4copy = $att->get_today_sessions_for_copy($this->sessioninfo);
        }

        $this->urlpath = $att->url_take()->out_omit_querystring();
        $params = $att->pageparams->get_significant_params();
        $params['id'] = $att->cm->id;
        $this->urlparams = $params;

        $this->att = $att;
    }

    /**
     * @param array $params
     * @param array $excludeparams
     * @return moodle_url
     */
    public function url($params=array(), $excludeparams=array()) {
        $params = array_merge($this->urlparams, $params);

        foreach ($excludeparams as $paramkey) {
            unset($params[$paramkey]);
        }

        return new moodle_url($this->urlpath, $params);
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function url_view($params=array()) {
        return url_helpers::url_view($this->att, $params);
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function url_individualreport($params=array()) {
        return url_helpers::url_individualreport($this->att, $params);
    }

    /**
     * @return mixed
     */
    public function url_path() {
        return $this->urlpath;
    }
}


/**
 * Class attcontrol_report_data
 */
class attcontrol_report_data implements renderable {
    /**
     * @var attcontrol_permissions
     */
    public $perm;
    /**
     * @var null
     */
    public $pageparams;

    /**
     * @var
     */
    public $users;

    /**
     * @var
     */
    public $groups;

    /**
     * @var
     */
    public $sessions;

    /**
     * @var array
     */
    public $statuses;

    /**
     * @var
     */
    public $decimalpoints;

    /**
     * @var array
     */
    public $usersgroups = array();

    /**
     * @var array
     */
    public $sessionslog = array();

    /**
     * @var array
     */
    public $usersstats = array();

    /**
     * @var array
     */
    public $grades = array();

    /**
     * @var array
     */
    public $maxgrades = array();

    /** @var total number of registers according to the conditions*/
    public $total;
    /** @var number of registers shown per page*/
    public $perpage;
    /** @var selected page number*/
    public $page;
    /** @var number of the first register shown*/
    public $offset;

    /**
     * @var attcontrol
     */
    private $att;

    /**
     * @param attcontrol $att
     * @param bool $isexport
     */
    public function  __construct(attcontrol $att, $isexport = false) {
        global $CFG;

        $this->perm = $att->perm;
        $this->pageparams = $att->pageparams;

        //Populate pagination parameters
        if (!$isexport) {
            $this->page = optional_param('page', 0, PARAM_INT);
            $this->perpage = get_user_preferences('attcontrol_perpage', 10);
            $this->offset = $this->page * $this->perpage;
            $this->total = $att->get_students_count($this->pageparams->get_current_sesstype());
        }

        if ($isexport) {
            $this->sessions = $att->get_sessions();
            $this->users = $att->get_course_report();
        }
        else {
            $this->sessions = $att->get_sessions();
            $this->users = $att->get_course_report($this->offset, $this->perpage);
        }

        $this->groups = groups_get_all_groups($att->course->id);

        $this->statuses = $att->get_statuses();

        $this->att = $att;
    }

    /**
     * @param $sessionid
     * @param $grouptype
     * @return mixed
     */
    public function url_take($sessionid, $grouptype) {
        return url_helpers::url_take($this->att, $sessionid, $grouptype);
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function url_view($params=array()) {
        return url_helpers::url_view($this->att, $params);
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function url_individualreport($params=array()) {
        return url_helpers::url_individualreport($this->att, $params);
    }

    /**
     * @param array $params
     * @return moodle_url
     */
    public function url($params=array()) {
        $params = array_merge($params, $this->pageparams->get_significant_params());

        return $this->att->url_report($params);
    }

}


/**
 * Class attcontrol_individual_report_data
 */
class attcontrol_individual_report_data implements renderable {
    /**
     * @var attcontrol_permissions
     */
    public $perm;

    /**
     * @var null
     */
    public $pageparams;

    /**
     * @var
     */
    public $students;

    /**
     * @var
     */
    public $sessions;

    /**
     * @var array
     */
    public $logs;

    /**
     * @var array
     */
    public $statuses;

    /**
     * @var attcontrol
     */
    private $att;

    /**
     * @var int
     */
    public $total;

    /**
     * @var
     */
    public $perpage;

    /**
     * @var
     */
    public $page;

    /**
     * @var
     */
    public $offset;

    /**
     * @var
     */
    public $currentstudent;

    /**
     * @var
     */
    public $studentinfo;

    /**
     * @param attcontrol $att
     * @param bool $isexport
     * @param null $studentid
     */
    public function  __construct(attcontrol $att, $isexport = false, $studentid = null) {
        global $CFG;

        $this->perm = $att->perm;
        $this->pageparams = $att->pageparams;

        if($studentid) {
            $this->pageparams->set_current_student($studentid);
        }

        //Populate pagination parameters
        if (!$isexport) {
            $this->page = optional_param('page', 0, PARAM_INT);
            $this->perpage = get_user_preferences('attcontrol_perpage', 10);
            $this->offset = $this->page * $this->perpage;
        }

        $this->currentstudent = $this->pageparams->get_current_student();

        if ($isexport) {
            $this->logs = $att->get_individual_report($this->currentstudent);
        }
        else {
            $this->logs = $att->get_individual_report($this->currentstudent, $this->offset, $this->perpage);
            $this->total = $att->get_individual_report_count($this->currentstudent);
        }

        if ($this->currentstudent != att_individualreport_page_params::SELSTUDENT_NONE) {
            $this->studentinfo = $att->get_student_info($this->currentstudent);
        }

        $this->statuses = $att->get_statuses();


        $this->att = $att;
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function url_individualreport($params=array()) {
        return url_helpers::url_individualreport($this->att, $params);
    }

    /**
     * @param $sessionid
     * @param $grouptype
     * @return mixed
     */
    public function url_take($sessionid, $grouptype) {

        return url_helpers::url_take($this->att, $sessionid, $grouptype);
    }

    /**
     * @param array $params
     * @return moodle_url
     */
    public function url($params=array()) {
        $params = array_merge($this->pageparams->get_significant_params(), $params);

        return $this->att->url_individualreport($params);
    }
}

/**
 * Class url_helpers
 */
class url_helpers {
    /**
     * @param $att
     * @param $sessionid
     * @param $grouptype
     * @return mixed
     */
    public static function url_take($att, $sessionid, $grouptype) {
        $params = array('sessionid' => $sessionid);
        if (isset($grouptype)) {
            $params['grouptype'] = $grouptype;
        }

        return $att->url_take($params);
    }

    /**
     * Must be called without or with both parameters
     */
    public static function url_sessions($att, $sessionid=null, $action=null) {
        if (isset($sessionid) && isset($action)) {
            $params = array('sessionid' => $sessionid, 'action' => $action);
        } else {
            $params = array();
        }

        return $att->url_sessions($params);
    }

    /**
     * @param $att
     * @param array $params
     * @return mixed
     */
    public static function url_view($att, $params=array()) {
        return $att->url_view($params);
    }

    /**
     * @param $att
     * @return mixed
     */
    public static function url_manage($att) {
        return $att->url_manage();

    }

    /**
     * @param $att
     * @param array $params
     * @return mixed
     */
    public static function url_individualreport($att, $params=array()) {
        return $att->url_individualreport($params);
    }
}
