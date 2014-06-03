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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

// This module is based in the original Attendance Module created by
// Artem Andreev <andreev.artem@gmail.com> - 2011

/**
 * Local functions and constants for module attcontrol
 *
 * @package mod_attcontrol
 * @copyright 2013 José Luis Antúnez <jantunez@xtec.cat>
 * @copyright 2011 Artem Andreev <andreev.artem@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined ( 'MOODLE_INTERNAL' ) || die ();

global $CFG;
require_once (dirname ( __FILE__ ) . '/renderhelpers.php');

/**
 * Filter value for dayly sessions view.
 */
define ( 'ATT_VIEW_DAYS', 1 );
/**
 * Filter value for weekly sessions view.
 */
define ( 'ATT_VIEW_WEEKS', 2 );
/**
 * Filter value for monthly sessions view.
 */
define ( 'ATT_VIEW_MONTHS', 3 );
/**
 * Filter value for past sessions view.
 */
define ( 'ATT_VIEW_ALLPAST', 4 );
/**
 * Filter value for all sessions view.
 */
define ( 'ATT_VIEW_ALL', 5 );

/**
 * Filter value for sorting by last name
 */
define ( 'ATT_SORT_LASTNAME', 1 );
/**
 * Filter value for sorting by first name
 */
define ( 'ATT_SORT_FIRSTNAME', 2 );





/**
 * Defines the permissions available in the attcontrol context
 */
class attcontrol_permissions {
    /**
     * @var boolean whether the user is allowed to view attcontrol instances or not.
     */
    private $canview;
    /**
     * @var boolean whether the user is allowed to view reports or not.
     */
    private $canviewreports;
    /**
     * @var boolean whether the user is allowed to take attendance or not.
     */
    private $cantake;
    /**
     * @var boolean whether the user is allowed to change an attcontrol instance or not.
     */
    private $canchange;
    /**
     * @var boolean whether the user is allowed to manage sessions or not.
     */
    private $canmanage;
    /**
     * @var boolean whether the user is allowed to export reports or not.
     */
    private $canexport;
    /**
     * @var boolean whether the user can be listed in the attendance taking list or not.
     */
    private $canbelisted;
    /**
     * @var boolean whether the user can access all groups or not.
     */
    private $canaccessallgroups;
    /**
     * @var object defines the course module for this activity
     */
    private $cm;
    /**
     * @var object defines the current moodle context
     */
    private $context;


    /**
     * @param $cm
     * @param $context
     */
    public function __construct($cm, $context) {
		$this->cm = $cm;
		$this->context = $context;
	}

    /**
     * Returns whether the current user can view the attcontrol instance
     * @return bool whether the user can view the attcontrol instance
     */
    public function can_view() {
		if (is_null ( $this->canview )) {
			$this->canview = has_capability ( 'mod/attcontrol:view', $this->context );
		}
		
		return $this->canview;
	}

    /**
     * Require the view capability
     */
    public function require_view_capability() {
		require_capability ( 'mod/attcontrol:view', $this->context );
	}

    /**
     * Returns whether the current user can view the attcontrol reports
     * @return bool whether the user can view the attcontrol reports
     */
    public function can_view_reports() {
		if (is_null ( $this->canviewreports )) {
			$this->canviewreports = has_capability ( 'mod/attcontrol:viewreports', $this->context );
		}
		
		return $this->canviewreports;
	}

    /**
     * Require the view reports capability
     */
    public function require_view_reports_capability() {
		require_capability ( 'mod/attcontrol:viewreports', $this->context );
	}

    /**
     * Returns whether the current user can take attendance in this attcontrol
     * @return bool whether the user can take attendance in this attcontrol
     */
    public function can_take() {
		if (is_null ( $this->cantake )) {
			$this->cantake = has_capability ( 'mod/attcontrol:takeattcontrols', $this->context );
		}
		
		return $this->cantake;
	}

    /**
     * Returns whether the current user can take attendance in this session and attcontrol
     * @param $groupid group id of the session
     * @return bool whether the user can take attendance in this attcontrol, for this group
     */
    public function can_take_session($groupid) {
		if (! $this->can_take ()) {
			return false;
		}
		
		if ($groupid == attcontrol::SESSION_COMMON || $this->can_access_all_groups () || array_key_exists ( $groupid, groups_get_activity_allowed_groups ( $this->cm ) )) {
			return true;
		}
		
		return false;
	}

    /**
     * Returns whether the current user can change this attcontrol
     * @return bool whether the current user can change this attcontrol
     */
    public function can_change() {
		if (is_null ( $this->canchange )) {
			$this->canchange = has_capability ( 'mod/attcontrol:changeattcontrols', $this->context );
		}
		
		return $this->canchange;
	}

    /**
     * Returns whether the current user can manage this attcontrol
     * @return bool whether the current user can manage this attcontrol
     */
    public function can_manage() {
		if (is_null ( $this->canmanage )) {
			$this->canmanage = has_capability ( 'mod/attcontrol:manageattcontrols', $this->context );
		}
		
		return $this->canmanage;
	}

    /**
     * Require the manage capability
     */
    public function require_manage_capability() {
		require_capability ( 'mod/attcontrol:manageattcontrols', $this->context );
	}

    /**
     * Return whether the current user can export reports from this attcontrol
     * @return bool whether the current user can export reports from this attcontrol
     */
    public function can_export() {
		if (is_null ( $this->canexport )) {
			$this->canexport = has_capability ( 'mod/attcontrol:export', $this->context );
		}
		
		return $this->canexport;
	}

    /**
     * Require the export reports capability
     */
    public function require_export_capability() {
		require_capability ( 'mod/attcontrol:export', $this->context );
	}

    /**
     * Returns whether the current user can be listed as a student in this attcontrol
     * @return bool whether the current user can be listed as a student in this attcontrol
     */
    public function can_be_listed() {
		if (is_null ( $this->canbelisted )) {
			$this->canbelisted = has_capability ( 'mod/attcontrol:canbelisted', $this->context, null, false );
		}
		
		return $this->canbelisted;
	}

    /**
     * Returns whether the current user can access all groups in this attcontrol
     * @return bool whether the current user can access all groups in this attcontrol
     */
    public function can_access_all_groups() {
		if (is_null ( $this->canaccessallgroups )) {
			$this->canaccessallgroups = has_capability ( 'moodle/site:accessallgroups', $this->context );
		}
		
		return $this->canaccessallgroups;
	}
}

/**
 * Generic class for all pages with filter controls in attcontrol
 */
class att_page_with_filter_controls {
    /**
     * All groups
     */
    const SELECTOR_NONE = 1;
    /**
     * Group
     */
    const SELECTOR_GROUP = 2;
    /**
     * Session type selector
     */
    const SELECTOR_SESS_TYPE = 3;
    /**
     * Common session
     */
    const SESSTYPE_COMMON = 0;
    /**
     * All sessions
     */
    const SESSTYPE_ALL = - 1;
    /**
     * Session type not set
     */
    const SESSTYPE_NO_VALUE = - 2;
    /**
     * Session type: all
     */
    const SELTYPE_ALL = - 1;

    /**
     * Action save
     */
    const ACTION_SAVE = 1;
    /**
     * Action save the items per page selector
     */
    const ACTION_CHANGE_PERPAGE = -1;
    /**
     * No student selected
     */
    const SELSTUDENT_NONE = - 1;


    public $context;
	
	/**
	 * @var int current view mode
	 */
	public $view;
	
	/**
	 * @var int $view and $curdate specify displayed date range
	 */
	public $curdate;
	
	/**
	 * @var int start date of displayed date range
	 */
	public $startdate;
	
	/**
	 * @var int end date of displayed date range
	 */
	public $enddate;
    /**
     * @var int type of session selected
     */
    public $selectortype = self::SELECTOR_NONE;
    /**
     * @var int date view selected
     */
    protected $defaultview = ATT_VIEW_WEEKS;
    /**
     * @var course module
     */
    private $cm;
    /**
     * @var list of groups for this attcontrol
     */
    private $sessgroupslist;
    /**
     * @var list of students for this page
     */
    private $studentslist;
    /**
     * @var list of session types for this page
     */
    private $sesstype;

    /**
     * Initializes the class
     * @param $cm
     */
    public function init($cm) {
		$this->cm = $cm;
		$this->init_view ();
		$this->init_curdate ();
		$this->init_start_end_date ();
		$this->init_studentfilter();
		
		$this->context = context_module::instance ( $this->cm->id );
	}

    /**
     * Initializes the view
     */
    private function init_view() {
		global $SESSION;
		
		if (isset ( $this->view )) {
			$SESSION->attcurrentattview [$this->cm->course] = $this->view;
		} else if (isset ( $SESSION->attcurrentattview [$this->cm->course] )) {
			$this->view = $SESSION->attcurrentattview [$this->cm->course];
		} else {
			$this->view = $this->defaultview;
		}
	}

    /**
     * Initializes the current date controls
     */
    private function init_curdate() {
		global $SESSION;
		
		if (isset ( $this->curdate )) {
			$SESSION->attcurrentattdate [$this->cm->course] = $this->curdate;
		} else if (isset ( $SESSION->attcurrentattdate [$this->cm->course] )) {
			$this->curdate = $SESSION->attcurrentattdate [$this->cm->course];
		} else {
			$this->curdate = time ();
		}
	}

    /**
     * Initializes the start and end date for the filters selected
     */
    public function init_start_end_date() {
		global $CFG;
		
		// HOURSECS solves issue for weeks view with Daylight saving time and clocks adjusting by one hour backward.
		$date = usergetdate ( $this->curdate + HOURSECS );
		$mday = $date ['mday'];
		$wday = $date ['wday'] - $CFG->calendar_startwday;
		if ($wday < 0) {
			$wday += 7;
		}
		$mon = $date ['mon'];
		$year = $date ['year'];
		
		switch ($this->view) {
			case ATT_VIEW_DAYS :
				$this->startdate = make_timestamp ( $year, $mon, $mday );
				$this->enddate = make_timestamp ( $year, $mon, $mday + 1 );
				break;
			case ATT_VIEW_WEEKS :
				$this->startdate = make_timestamp ( $year, $mon, $mday - $wday );
				$this->enddate = make_timestamp ( $year, $mon, $mday + 7 - $wday ) - 1;
				break;
			case ATT_VIEW_MONTHS :
				$this->startdate = make_timestamp ( $year, $mon );
				$this->enddate = make_timestamp ( $year, $mon + 1 );
				break;
			case ATT_VIEW_ALLPAST :
				$this->startdate = 1;
				$this->enddate = time ();
				break;
			case ATT_VIEW_ALL :
				$this->startdate = 0;
				$this->enddate = 0;
				break;
		}
	}

    /**
     * Initializes the student filter
     */
    private function init_studentfilter() {
		global $SESSION;
	
		$this->studentfilter = optional_param ( 'studentid', null, PARAM_INT );
	
		if (isset ( $this->studentfilter )) {
			$SESSION->attstudentfilter [$this->cm->course] = $this->studentfilter;
		} else if (isset ( $SESSION->attstudentfilter [$this->cm->course] )) {
			$this->studentfilter = $SESSION->attstudentfilter [$this->cm->course];
		} else {
			$SESSION->attstudentfilter [$this->cm->course] = $this->studentfilter;
		}
	
		if ($this->studentfilter == null) {
			$this->studentfilter = att_individualreport_page_params::SELSTUDENT_NONE;
		}
	}

    /**
     * Sets the elemtns for the groups list and the session type list
     */
    private function calc_sessgroupslist_sesstype() {
		global $SESSION;
		
		if (! array_key_exists ( 'attsessiontype', $SESSION )) {
			$SESSION->attsessiontype = array (
					$this->cm->course => self::SESSTYPE_ALL 
			);
		} else if (! array_key_exists ( $this->cm->course, $SESSION->attsessiontype )) {
			$SESSION->attsessiontype [$this->cm->course] = self::SESSTYPE_ALL;
		}
		
		$group = optional_param ( 'group', self::SESSTYPE_NO_VALUE, PARAM_INT );
		if ($this->selectortype == self::SELECTOR_SESS_TYPE) {
			if ($group > self::SESSTYPE_NO_VALUE) {
				$SESSION->attsessiontype [$this->cm->course] = $group;
				if ($group > self::SESSTYPE_ALL) {
					// Set activegroup in $SESSION.
					groups_get_activity_group ( $this->cm, true );
				} else {
					// Reset activegroup in $SESSION.
					unset ( $SESSION->activegroup [$this->cm->course] [VISIBLEGROUPS] [$this->cm->groupingid] );
					unset ( $SESSION->activegroup [$this->cm->course] ['aag'] [$this->cm->groupingid] );
					unset ( $SESSION->activegroup [$this->cm->course] [SEPARATEGROUPS] [$this->cm->groupingid] );
				}
				$this->sesstype = $group;
			} else {
				$this->sesstype = $SESSION->attsessiontype [$this->cm->course];
			}
		} else if ($this->selectortype == self::SELECTOR_GROUP) {
			if ($group == 0) {
				$SESSION->attsessiontype [$this->cm->course] = self::SESSTYPE_ALL;
				$this->sesstype = self::SESSTYPE_ALL;
			} else if ($group > 0) {
				$SESSION->attsessiontype [$this->cm->course] = $group;
				$this->sesstype = $group;
			} else {
				$this->sesstype = $SESSION->attsessiontype [$this->cm->course];
			}
		}
		
		if (is_null ( $this->sessgroupslist )) {
			$this->calc_sessgroupslist ();
		}
		// For example, we set SESSTYPE_ALL but user can access only to limited set of groups.
		if (! array_key_exists ( $this->sesstype, $this->sessgroupslist )) {
			reset ( $this->sessgroupslist );
			$this->sesstype = key ( $this->sessgroupslist );
		}
	}

    /**
     * Sets the elements for the session group list
     */
    private function calc_sessgroupslist() {
		global $USER, $PAGE;
		
		$this->sessgroupslist = array ();
		$groupmode = groups_get_activity_groupmode ( $this->cm );
		if ($groupmode == NOGROUPS) {
			return;
		}
		
		if ($groupmode == VISIBLEGROUPS or has_capability ( 'moodle/site:accessallgroups', $PAGE->context )) {
			$allowedgroups = groups_get_all_groups ( $this->cm->course, 0, $this->cm->groupingid );
		} else {
			$allowedgroups = groups_get_all_groups ( $this->cm->course, $USER->id, $this->cm->groupingid );
		}
		
		if ($allowedgroups) {
			if ($groupmode == VISIBLEGROUPS or has_capability ( 'moodle/site:accessallgroups', $PAGE->context )) {
				$this->sessgroupslist [self::SESSTYPE_ALL] = get_string ( 'all', 'attcontrol' );
			}
			if ($groupmode == VISIBLEGROUPS) {
				$this->sessgroupslist [self::SESSTYPE_COMMON] = get_string ( 'commonsessions', 'attcontrol' );
			}
			foreach ( $allowedgroups as $group ) {
				$this->sessgroupslist [$group->id] = format_string ( $group->name );
			}
		}
	}

    /**
     * Sets the elements for the student list
     */
    private function calc_studentlist() {
		global $DB;
	
		$this->studentslist = array ();
	
		list ( $esql, $params ) = get_enrolled_sql ( $this->context, 'mod/attcontrol:canbelisted' );
	
		$sql = "SELECT u.id, u.firstname, u.lastname FROM {user} u " . "LEFT JOIN ($esql) eu ON eu.id=u.id " . "WHERE u.deleted = 0 AND eu.id=u.id " . "ORDER BY u.lastname ASC, u.firstname ASC ";
	
		$students = $DB->get_records_sql ( $sql, $params );
	
		foreach ( $students as $student ) {
			$this->studentslist [$student->id] = $student->lastname . ", " . $student->firstname;
		}
	}

    /**
     * Gets the session group list
     * @return array session group list
     */
    public function get_sess_groups_list() {
		if (is_null ( $this->sessgroupslist )) {
			$this->calc_sessgroupslist_sesstype ();
		}
		
		return $this->sessgroupslist;
	}

    /**
     * Gets the students list
     * @return array students list
     */
    public function get_students_list() {
		if (is_null ( $this->studentslist )) {
			$this->calc_studentlist();
		}
	
		return $this->studentslist;
	}

    /**
     * Gets the current session type
     * @return int current session type
     */
    public function get_current_sesstype() {
		if (is_null ( $this->sesstype )) {
			$this->calc_sessgroupslist_sesstype ();
		}
		
		return $this->sesstype;
	}

    /**
     * Sets the current session type
     * @param $sesstype int session type
     */
    public function set_current_sesstype($sesstype) {
		$this->sesstype = $sesstype;
	}

    /**
     * Gets the current student
     * @return int current user id
     */
    public function get_current_student() {
		return $this->studentfilter;
	}

    /**
     * Sets the current student id
     * @param $userid int user id
     */
    public function set_current_student($userid) {
		$this->studentfilter = $userid;
	}
}

/**
 * Class for the view page
 */
class att_view_page_params extends att_page_with_filter_controls {
    /**
     * @var int student id
     */
    public $studentid;

    /**
     * Construct this view
     */
    public function __construct() {
		$this->defaultview = ATT_VIEW_MONTHS;
	}

    /**
     * Gets the default parameters for this view
     * @return array default parameters for this view
     */
    public function get_significant_params() {
		$params = array ();
		
		if (isset ( $this->studentid )) {
			$params ['studentid'] = $this->studentid;
		}
		
		return $params;
	}
}

/**
 * Class for the individual report view
 */
class att_individualreport_page_params extends att_page_with_filter_controls {

    /**
     * @var int student id
     */
    public $student;
    /**
     * @var int sort value
     */
    public $sort;

    /**
     * Construct this view
     */
    public function __construct() {
		$this->selectortype = self::SELECTOR_GROUP;
	}

    /**
     * Initialize this view
     * @param $cm course module
     */
    public function init($cm) {
		parent::init ( $cm );
		
		if (! isset ( $this->student )) {
			$this->student = $this->get_current_student ();
		}
	}

    /**
     * Gets the default parameters for this view
     * @return array default parameters for this view
     */
    public function get_significant_params() {
		$params = array ();
		
		if ($this->sort != ATT_SORT_LASTNAME) {
			$params ['sort'] = $this->sort;
		}
		
		return $params;
	}
}

/**
 * Class for the manage view
 */
class att_manage_page_params extends att_page_with_filter_controls {
    /**
     * Action for changing the items per page value
     */
    const ACTION_CHANGE_PERPAGE = - 1;

    /**
     * Construct this view
     */
    public function __construct() {
		$this->selectortype = att_page_with_filter_controls::SELECTOR_SESS_TYPE;
	}

    /**
     * Gets the default parameters for this view
     * @return array default parameters for this view
     */
    public function get_significant_params() {
		return array ();
	}
}

/**
 * Class for the sessions view
 */
class att_sessions_page_params {
    /**
     * Represents the add action in the sessions form
     */
    const ACTION_ADD = 1;
    /**
     * Represents the update action in the sessions form
     */
    const ACTION_UPDATE = 2;
    /**
     * Represents the delete action in the sessions form
     */
    const ACTION_DELETE = 3;
    /**
     * Represents the delete selected items action in the sessions form
     */
    const ACTION_DELETE_SELECTED = 4;
    /**
     * Represents the change duration action in the sessions form
     */
    const ACTION_CHANGE_DURATION = 5;
    /**
     * Represents the change items per page action in the sessions form
     */
    const ACTION_CHANGE_PERPAGE = - 1;
	
	/**
	 * @var int view mode of taking attcontrol page
	 */
	public $action;
}

/**
 * Class for the attendance taking page
 */
class att_take_page_params {
    /**
     * Represents the sorted list display mode
     */
    const SORTED_LIST = 1;
    /**
     * Represents the sorted grid display mode
     */
    const SORTED_GRID = 2;
    /**
     * Defines the default view mode (sorted list)
     */
    const DEFAULT_VIEW_MODE = self::SORTED_LIST;
    /**
     * @var int Session Id
     */
    public $sessionid;
    /**
     * @var int Group type
     */
    public $grouptype;
    /**
     * @var int Selected group
     */
    public $group;
    /**
     * @var int Sort mode
     */
    public $sort;
    /**
     * @var int the session id, if we want to copy it from another one
     */
    public $copyfrom;
	
	/**
	 * @var int view mode of taking attcontrol page
	 */
	public $viewmode;
    /**
     * @var int number of columns in grid mode
     */
    public $gridcols;

    /**
     * Initializes the view.
     */
    public function init() {
		if (! isset ( $this->group )) {
			$this->group = 0;
		}
		if (! isset ( $this->sort )) {
			$this->sort = ATT_SORT_LASTNAME;
		}
		$this->init_view_mode ();
		$this->init_gridcols ();
	}

    /**
     * Initializes the default view mode.
     */
    private function init_view_mode() {
		if (isset ( $this->viewmode )) {
			set_user_preference ( "attcontrol_take_view_mode", $this->viewmode );
		} else {
			$this->viewmode = get_user_preferences ( "attcontrol_take_view_mode", self::DEFAULT_VIEW_MODE );
		}
	}

    /**
     * Initializes the number of columns in the grid mode.
     */
    private function init_gridcols() {
		if (isset ( $this->gridcols )) {
			set_user_preference ( "attcontrol_gridcolumns", $this->gridcols );
		} else {
			$this->gridcols = get_user_preferences ( "attcontrol_gridcolumns", 5 );
		}
	}

    /**
     * Gets the default parameters for the take attendance view
     * @return array Initializes the parameters to display the take attendance page.
     */
    public function get_significant_params() {
		$params = array ();
		
		$params ['sessionid'] = $this->sessionid;
		$params ['grouptype'] = $this->grouptype;
		if ($this->group) {
			$params ['group'] = $this->group;
		}
		if ($this->sort != ATT_SORT_LASTNAME) {
			$params ['sort'] = $this->sort;
		}
		if (isset ( $this->copyfrom )) {
			$params ['copyfrom'] = $this->copyfrom;
		}
		
		return $params;
	}
}

/**
 * Class for the reports page
 */
class att_report_page_params extends att_page_with_filter_controls {
    /**
     * @var selected group
     */
    public $group;
    /**
     * @var sorting type
     */
    public $sort;

    /**
     * Construct this view
     */
    public function __construct() {
		$this->selectortype = self::SELECTOR_GROUP;
	}

    /**
     * Initialize this view
     * @param $cm course module
     */
    public function init($cm) {
		parent::init ( $cm );
		
		if (! isset ( $this->group )) {
			$this->group = $this->get_current_sesstype () > 0 ? $this->get_current_sesstype () : 0;
		}
		if (! isset ( $this->sort )) {
			$this->sort = ATT_SORT_LASTNAME;
		}
	}

    /**
     * Get the default parameters for this view
     * @return array default parameters for this view
     */
    public function get_significant_params() {
		$params = array ();
		
		if ($this->sort != ATT_SORT_LASTNAME) {
			$params ['sort'] = $this->sort;
		}
		
		return $params;
	}
}

/**
 * Main attcontrol class
 */
class attcontrol {
    /**
     * Common session
     */
    const SESSION_COMMON = 0;
    /**
     * Group session
     */
    const SESSION_GROUP = 1;
	
	/**
	 * @var stdclass course module record
	 */
	public $cm;
	
	/**
	 * @var stdclass course record
	 */
	public $course;
	
	/**
	 * @var stdclass context object
	 */
	public $context;
	
	/**
	 * @var int attcontrol instance identifier
	 */
	public $id;
	
	/**
	 * @var string attcontrol activity name
	 */
	public $name;
	
	/**
	 * current page parameters
	 */
	public $pageparams;
	
	/**
	 * @var attcontrol_permissions permission of current user for attcontrol instance
	 */
	public $perm;
    /**
     * @var group mode
     */
    private $groupmode;
    /**
     * @var status list
     */
    private $statuses;
	
	// Array by sessionid.
    /**
     * @var array session information
     */
    private $sessioninfo = array ();
	
	// Arrays by userid.
    /**
     * @var array user taken session count
     */
    private $usertakensesscount = array ();
    /**
     * @var array user statuses
     */
    private $userstatusesstat = array ();
	
	/**
	 * Initializes the attcontrol API instance using the data from DB
	 *
	 * Makes deep copy of all passed records properties. Replaces integer $course attribute
	 * with a full database record (course should not be stored in instances table anyway).
	 *
	 * @param stdClass $dbrecord
	 *        	Attandance instance data from {attcontrol} table
	 * @param stdClass $cm
	 *        	Course module record as returned by {@link get_coursemodule_from_id()}
	 * @param stdClass $course
	 *        	Course record from {course} table
	 * @param stdClass $context
	 *        	The context of the workshop instance
	 */
	public function __construct(stdclass $dbrecord, stdclass $cm, stdclass $course, stdclass $context = null, $pageparams = null) {
		foreach ( $dbrecord as $field => $value ) {
			if (property_exists ( 'attcontrol', $field )) {
				$this->{$field} = $value;
			} else {
				throw new coding_exception ( 'The attcontrol table has a field with no property in the attcontrol class' );
			}
		}
		$this->cm = $cm;
		$this->course = $course;
		if (is_null ( $context )) {
			$this->context = context_module::instance_by_id ( $this->cm->id );
		} else {
			$this->context = $context;
		}
		
		$this->pageparams = $pageparams;
		
		$this->perm = new attcontrol_permissions ( $this->cm, $this->context );
	}

    /**
     * Gets the group mode
     * @return int group mode
     */
    public function get_group_mode() {
		if (is_null ( $this->groupmode )) {
			$this->groupmode = groups_get_activity_groupmode ( $this->cm );
		}
		return $this->groupmode;
	}
	
	/**
	 * Returns current sessions for this attcontrol
	 *
	 * Fetches data from {attcontrol_sessions}
	 *
	 * @return array of records or an empty array
	 */
	public function get_current_sessions() {
		global $DB;
		
		$page = optional_param ( 'page', 0, PARAM_INT );
		$perpage = get_user_preference ( 'attcontrol_perpage', 10 );
		$offset = $page * $perpage;
		
		$today = time (); // Because we compare with database, we don't need to use usertime().
		
		$sql = "SELECT *
                  FROM {attcontrol_sessions}
                 WHERE :time BETWEEN sessdate AND (sessdate + duration)
                   AND attcontrolid = :aid";
		$params = array (
				'time' => $today,
				'aid' => $this->id 
		);
		
		return $DB->get_records_sql ( $sql, $params, $offset, $perpage );
	}

    /**
     * Gets the individual report number of records
     * @param $student selected stuent
     * @return int number of records
     */
    public function get_individual_report_count($student) {
		global $DB;
		
		$count = 0;
		
		//Only working if student is selected.
		if( isset($student) && $student != att_individualreport_page_params::SELSTUDENT_NONE) {
				
			$sql = " SELECT count(*) 
    		FROM {attcontrol_sessions} ats
    		JOIN {attcontrol_log} atl ON ats.attcontrolid = :aid AND atl.sessionid = ats.id
    		WHERE atl.studentid = :studentid ";
		
		
			if ($this->pageparams->startdate && $this->pageparams->enddate) {
				$sql .= " AND sessdate >= :sdate AND sessdate < :edate";
			}

			echo $this->pageparams->startdate;

			// Prepare the parameters
			$params = array (
					'aid' => $this->id,
					'studentid' => $student,
					'sdate' => $this->pageparams->startdate,
					'edate' => $this->pageparams->enddate
			);
		
								
			$count = $DB->count_records_sql($sql, $params);
		}
			
		return $count;
	}

    /**
     * Gets the student detail
     * @param $student student id
     * @return mixed user detail information
     */
    public function get_student_info($student) {
		global $DB;
		
		$st = $DB->get_records("user", array("id" => $student));
		
		return $st[key($st)];
	}

    /**
     * Gets the individual report
     * @param $student student id
     * @param int $offset offset of elements
     * @param int $perpage number of elements per page
     * @return array elements of the individual report
     */
    public function get_individual_report($student, $offset = null, $perpage = null) {
		global $DB;
		
		// Only working if student is selected.
		if (isset ( $student ) && $student != att_individualreport_page_params::SELSTUDENT_NONE) {
			
			$sql = " SELECT atl.*, ats.*, atr.remarks 
    		FROM {attcontrol_sessions} ats
    		JOIN {attcontrol_log} atl ON ats.attcontrolid = :aid AND atl.sessionid = ats.id
			LEFT JOIN {attcontrol_remarks} atr ON atl.id = atr.id 
    		WHERE atl.studentid = :studentid ";
			
			if ($this->pageparams->startdate && $this->pageparams->enddate) {
				$sql .= " AND sessdate >= :sdate AND sessdate < :edate";
			}
			
			// Prepare the parameters
			$params = array (
					'aid' => $this->id,
					'studentid' => $student,
					'sdate' => $this->pageparams->startdate,
					'edate' => $this->pageparams->enddate 
			);

			if (isset ( $offset ) && isset ( $perpage )) {
				$logs = $DB->get_records_sql ( $sql, $params, $offset, $perpage );
			} else {
				$logs = $DB->get_records_sql ( $sql, $params );
			}

		} else {
			// Empty logs array.
			$logs = array ();
		}
		
		// Need to do it with usort as it's an associative array by id.
		usort ( $logs, "self::sessioncmp" );
		
		return $logs;
	}
	
	/**
	 * Returns today sessions for this attcontrol
	 *
	 * Fetches data from {attcontrol_sessions}
	 *
	 * @return array of records or an empty array
	 */
	public function get_today_sessions() {
		global $DB;
		
		$start = usergetmidnight ( time () );
		$end = $start + DAYSECS;
		
		$sql = "SELECT *
                  FROM {attcontrol_sessions}
                 WHERE sessdate >= :start AND sessdate < :end
                   AND attcontrolid = :aid";
		$params = array (
				'start' => $start,
				'end' => $end,
				'aid' => $this->id 
		);
		
		return $DB->get_records_sql ( $sql, $params );
	}
	
	/**
	 * Returns today sessions suitable for copying attcontrol log
	 *
	 * Fetches data from {attcontrol_sessions}
	 *
	 * @return array of records or an empty array
	 */
	public function get_today_sessions_for_copy($sess) {
		global $DB;
		
		$start = usergetmidnight ( $sess->sessdate );
		
		$sql = "SELECT *
                  FROM {attcontrol_sessions}
                 WHERE sessdate >= :start AND sessdate <= :end AND
                       (groupid = 0 OR groupid = :groupid) AND
                       lasttaken > 0 AND attcontrolid = :aid";
		$params = array (
				'start' => $start,
				'end' => $sess->sessdate,
				'groupid' => $sess->groupid,
				'aid' => $this->id 
		);
		
		return $DB->get_records_sql ( $sql, $params );
	}
	
	/**
	 * Gets de number of sessions according to the current filters.
	 *
	 * Fetches data from {attcontrol_sessions}
	 *
	 * @return count of sessions according to the defined filters.
	 */
	public function get_filtered_sessions_count() {
		global $DB;
		
		if ($this->pageparams->startdate && $this->pageparams->enddate) {
			$where = "attcontrolid = :aid AND sessdate >= :sdate AND sessdate < :edate";
		} else {
			$where = "attcontrolid = :aid ";
		}
		if ($this->pageparams->get_current_sesstype () > att_page_with_filter_controls::SESSTYPE_ALL) {
			$where .= " AND groupid=:cgroup";
		}
		$params = array (
				'aid' => $this->id,
				'sdate' => $this->pageparams->startdate,
				'edate' => $this->pageparams->enddate,
				'cgroup' => $this->pageparams->get_current_sesstype () 
		);
		
		return $DB->count_records_select ( 'attcontrol_sessions', $where, $params );
	}

    /**
     * Gets the sessions filtered by the criteria set in the view
     * @param int $offset first element to show
     * @param int $perpage number of elements per page
     * @return session records according to the criteria
     */
    public function get_filtered_sessions($offset = null, $perpage = null) {
		global $DB;
		
		if ($this->pageparams->startdate && $this->pageparams->enddate) {
			$where = "attcontrolid = :aid AND sessdate >= :sdate AND sessdate < :edate";
		} else {
			$where = "attcontrolid = :aid ";
		}
		
		if ($this->pageparams->get_current_sesstype () > att_page_with_filter_controls::SESSTYPE_ALL) {
			$where .= " AND groupid=:cgroup";
		}
		
		$params = array (
				'aid' => $this->id,
				'sdate' => $this->pageparams->startdate,
				'edate' => $this->pageparams->enddate,
				'cgroup' => $this->pageparams->get_current_sesstype () 
		);
		// Modified query in order to accept offset and number of elements per page.
		if (isset ( $offset ) && isset ( $perpage ))
			$sessions = $DB->get_records_select ( 'attcontrol_sessions', $where, $params, 'sessdate asc', '*', $offset, $perpage );
		else
			$sessions = $DB->get_records_select ( 'attcontrol_sessions', $where, $params, 'sessdate asc' );
		foreach ( $sessions as $sess ) {
			if (empty ( $sess->description )) {
				$sess->description = get_string ( 'nodescription', 'attcontrol' );
			} else {
				$sess->description = file_rewrite_pluginfile_urls ( $sess->description, 'pluginfile.php', $this->context->id, 'mod_attcontrol', 'session', $sess->id );
			}
		}
		
		return $sessions;
	}

    /**
     * Gets all sessions, for the reports
     * @return session records according to the report criteria
     */
    public function get_sessions() {
		global $DB;
		
		if ($this->pageparams->startdate && $this->pageparams->enddate) {
			$where = "attcontrolid = :aid AND sessdate >= :sdate AND sessdate < :edate";
		} else {
			$where = "attcontrolid = :aid ";
		}
		
		$where .= " AND lasttaken IS NOT NULL";
		
		if ($this->pageparams->get_current_sesstype() != att_report_page_params::SESSTYPE_ALL ) {
			$where .= " AND (groupid = 0 OR groupid = :cgroup) ";
			
		}
		
		$params = array (
				'aid' => $this->id,
				'sdate' => $this->pageparams->startdate,
				'edate' => $this->pageparams->enddate,
				'cgroup' => $this->pageparams->get_current_sesstype()
		);

		if (isset ( $offset ) && isset ( $perpage ))
			$sessions = $DB->get_records_select ( 'attcontrol_sessions', $where, $params, 'sessdate asc', '*', $offset, $perpage );
		else
			$sessions = $DB->get_records_select ( 'attcontrol_sessions', $where, $params, 'sessdate asc' );
		
		return $sessions;
	}
	
	/**
	 * Gets the url of the manage page
	 * @return moodle_url of manage.php for attcontrol instance
	 */
	public function url_manage($params = array()) {
		$params = array_merge ( array (
				'id' => $this->cm->id 
		), $params );
		return new moodle_url ( '/mod/attcontrol/manage.php', $params );
	}
	
	/**
	 * Gets the url of the sessions page
	 * @return moodle_url of sessions.php for attcontrol instance
	 */
	public function url_sessions($params = array()) {
		$params = array_merge ( array (
				'id' => $this->cm->id 
		), $params );
		return new moodle_url ( '/mod/attcontrol/sessions.php', $params );
	}
	
	/**
	 * Gets the url of the report page
	 * @return moodle_url of report.php for attcontrol instance
	 */
	public function url_report($params = array()) {
		$params = array_merge ( array (
				'id' => $this->cm->id 
		), $params );
		return new moodle_url ( '/mod/attcontrol/report.php', $params );
	}
	
	/**
	 * Gets the url of the export page
	 * @return moodle_url of export.php for attcontrol instance
	 */
	public function url_export() {
		$params = array (
				'id' => $this->cm->id 
		);
		return new moodle_url ( '/mod/attcontrol/export.php', $params );
	}
	
	/**
	 * Gets the url of the individual report page
	 * @return moodle_url of individualreport.php for attcontrol instance
	 */
	public function url_individualreport($params = array()) {
		$params = array_merge ( array (
				'id' => $this->cm->id 
		), $params );
		return new moodle_url ( '/mod/attcontrol/individualreport.php', $params );
	}
	
	/**
	 * Gets the url of the individual export page
	 * @return moodle_url of export.php for attcontrol instance
	 */
	public function url_individualexport() {
		$params = array (
				'id' => $this->cm->id 
		);
		return new moodle_url ( '/mod/attcontrol/individualexport.php', $params );
	}
	
	/**
	 * Gets the url of the take attendance page
	 * @return moodle_url of attcontrols.php for attcontrol instance
	 */
	public function url_take($params = array()) {
		$params = array_merge ( array (
				'id' => $this->cm->id 
		), $params );
		return new moodle_url ( '/mod/attcontrol/take.php', $params );
	}

    /**
     * Gets the url of the view page
     * @param array $params parameters of this view
     * @return moodle_url of view.php for attcontrol instance
     */
    public function url_view($params = array()) {
		$params = array_merge ( array (
				'id' => $this->cm->id 
		), $params );
		return new moodle_url ( '/mod/attcontrol/view.php', $params );
	}

    /**
     * Add sessions to this attcontrol instance
     * @param $sessions sessions to be added
     */
    public function add_sessions($sessions) {
		global $DB;
		
		foreach ( $sessions as $sess ) {
			$sess->attcontrolid = $this->id;
			
			$sess->id = $DB->insert_record ( 'attcontrol_sessions', $sess );
		}
		
		$info_array = array ();
		$maxlog = 7; // Only log first 10 sessions and last session in the log info. as we can only store 255 chars.
		$i = 0;
		foreach ( $sessions as $sess ) {
			if ($i > $maxlog) {
				$lastsession = end ( $sessions );
				$info_array [] = '...';
				$info_array [] = construct_session_full_date_time ( $lastsession->sessdate, $lastsession->duration );
				break;
			} else {
				$info_array [] = construct_session_full_date_time ( $sess->sessdate, $sess->duration );
			}
			$i ++;
		}
		add_to_log ( $this->course->id, 'attcontrol', count ( $info_array ) . ' sessions added', $this->url_manage (), '', $this->cm->id );
	}

    /**
     * Updates a session
     * @param $formdata information from the form to be updated
     * @param $sessionid session id
     */
    public function update_session_from_form_data($formdata, $sessionid) {
		global $DB;
		
		if (! $sess = $DB->get_record ( 'attcontrol_sessions', array (
				'id' => $sessionid 
		) )) {
			print_error ( 'No such session in this course' );
		}
		
		$sess->sessdate = $formdata->sessiondate;
		$sess->duration = $formdata->durtime ['hours'] * HOURSECS + $formdata->durtime ['minutes'] * MINSECS;
		$sess->description = $formdata->description;
		$sess->timemodified = time ();
		$DB->update_record ( 'attcontrol_sessions', $sess );
		
		$url = $this->url_sessions ( array (
				'sessionid' => $sessionid,
				'action' => att_sessions_page_params::ACTION_UPDATE 
		) );
		$info = construct_session_full_date_time ( $sess->sessdate, $sess->duration );
		add_to_log ( $this->course->id, 'attcontrol', 'session updated', $url, $info, $this->cm->id );
	}

    /**
     * Takes attendance from form data
     * @param $formdata form data to be taken
     */
    public function take_from_form_data($formdata) {
		global $DB, $USER;
		
		$now = time ();
		$sesslog = array ();
		$sessremarks = array ();
		$formdata = ( array ) $formdata;
		foreach ( $formdata as $key => $value ) {
			if (substr ( $key, 0, 4 ) == 'user') {
				$sid = substr ( $key, 4 );
				if (! (is_numeric ( $sid ) && is_numeric ( $value ))) { // Sanity check on $sid and $value.
					print_error ( 'nonnumericid', 'attcontrol' );
				}
				$sesslog [$sid] = new stdClass ();
				$sesslog [$sid]->studentid = $sid; // We check is_numeric on this above.
				$sesslog [$sid]->statusid = $value; // We check is_numeric on this above.
				
				$sesslog [$sid]->sessionid = $this->pageparams->sessionid;
				$sesslog [$sid]->timetaken = $now;
				$sesslog [$sid]->takenby = $USER->id;
				
				if (array_key_exists ( 'remarks' . $sid, $formdata ) && trim ( $formdata ['remarks' . $sid] )) {
					$sessremarks [$sid] = clean_param ( $formdata ['remarks' . $sid], PARAM_TEXT );
				}
			}
		}
		
		$dbsesslog = $this->get_session_log ( $this->pageparams->sessionid );
		foreach ( $sesslog as $key => $log ) {
			if ($log->statusid) {
				if (array_key_exists ( $log->studentid, $dbsesslog )) {
					$log->id = $dbsesslog [$log->studentid]->id;
					$DB->update_record ( 'attcontrol_log', $log );
				} else {
					$log->id = $DB->insert_record ( 'attcontrol_log', $log, true );
				}
				
				if (isset ( $sessremarks [$key] ) || isset ( $dbsesslog [$log->studentid] ) && trim ( $dbsesslog [$log->studentid]->remarks )) {
					if (! isset ( $sessremarks [$key] ))
						$DB->execute ( 'DELETE FROM {attcontrol_remarks} WHERE id = ?', array (
								$log->id 
						) );
					else if (isset($dbsesslog [$log->studentid]) && trim ( $dbsesslog [$log->studentid]->remarks ))
						$DB->execute ( 'UPDATE {attcontrol_remarks} SET remarks = ? WHERE id = ?', array (
								$sessremarks [$key],
								$log->id 
						) );
					else
						$DB->execute ( 'INSERT INTO {attcontrol_remarks} VALUES (?,?)', array (
								$log->id,
								$sessremarks [$key] 
						) );
				}
			}
		}
		
		$rec = new stdClass ();
		$rec->id = $this->pageparams->sessionid;
		$rec->lasttaken = $now;
		$rec->lasttakenby = $USER->id;
		$DB->update_record ( 'attcontrol_sessions', $rec );
		
		$params = array (
				'sessionid' => $this->pageparams->sessionid,
				'grouptype' => $this->pageparams->grouptype 
		);
		$url = $this->url_take ( $params );
		add_to_log ( $this->course->id, 'attcontrol', 'taken', $url, '', $this->cm->id );
		
		redirect ( $this->url_manage (), get_string ( 'attcontrolsuccess', 'attcontrol' ) );
	}
	
	/**
	 * Gets the users
	 */
	public function get_users($groupid = 0) {
		global $DB;
		
		// Fields we need from the user table.
		$userfields = user_picture::fields ( 'u' ) . ',u.username';
		
		if (isset ( $this->pageparams->sort ) and ($this->pageparams->sort == ATT_SORT_FIRSTNAME)) {
			$orderby = "u.firstname ASC, u.lastname ASC";
		} else {
			$orderby = "u.lastname ASC, u.firstname ASC";
		}
		
		$users = get_enrolled_users ( $this->context, 'mod/attcontrol:canbelisted', $groupid, $userfields, $orderby );
		
		// Add a flag to each user indicating whether their enrolment is active.
		if (! empty ( $users )) {
			list ( $usql, $uparams ) = $DB->get_in_or_equal ( array_keys ( $users ), SQL_PARAMS_NAMED, 'usid0' );
			
			// CONTRIB-3549.
			$sql = "SELECT ue.userid, ue.status, ue.timestart, ue.timeend
                      FROM {user_enrolments} ue
                      JOIN {enrol} e ON e.id = ue.enrolid
                     WHERE ue.userid $usql
                           AND e.status = :estatus
                           AND e.courseid = :courseid
                  GROUP BY ue.userid, ue.status, ue.timestart, ue.timeend";
			$params = array_merge ( $uparams, array (
					'estatus' => ENROL_INSTANCE_ENABLED,
					'courseid' => $this->course->id 
			) );
			$enrolmentsparams = $DB->get_records_sql ( $sql, $params );
			
			foreach ( $users as $user ) {
				$users [$user->id]->enrolmentstatus = $enrolmentsparams [$user->id]->status;
			}
		}
		
		return $users;
	}

    /**
     * Gets the students
     * @param int $groupid selected group id
     * @param int $offset first element to show
     * @param int $perpage number of elements per page
     * @return array students that can be listed in this attcontrol instance
     */
    public function get_students($groupid = 0, $offset = null, $perpage = null) {
		global $DB;
		
		if (isset ( $this->pageparams->sort ) and ($this->pageparams->sort == ATT_SORT_FIRSTNAME)) {
			$orderby = " ORDER BY u.firstname ASC, u.lastname ASC";
		} else {
			$orderby = " ORDER BY u.lastname ASC, u.firstname ASC";
		}
		
		if ($groupid > att_page_with_filter_controls::SELTYPE_ALL) {
			list ( $esql, $params ) = get_enrolled_sql ( $this->context, 'mod/attcontrol:canbelisted', $groupid );
		} else {
			list ( $esql, $params ) = get_enrolled_sql ( $this->context, 'mod/attcontrol:canbelisted' );
		}
		
		$sql = "SELECT " . user_picture::fields ( 'u' ) . ", u.username FROM {user} u LEFT JOIN ($esql) eu ON eu.id=u.id WHERE u.deleted = 0 AND eu.id=u.id" . $orderby;
		
		if (isset ( $offset ) && isset ( $perpage )) {
			$students = $DB->get_records_sql ( $sql, $params, $offset, $perpage );
		} else {
			$students = $DB->get_records_sql ( $sql, $params );
		}
		
		return $students;
	}

    /**
     * Gets the number of students that can be listed in this instance
     * @param int $groupid selected group id
     * @return int number of students that can be listed
     */
    public function get_students_count($groupid = 0) {
		global $DB;
	
		if ($groupid > att_page_with_filter_controls::SELTYPE_ALL) {
			list ( $esql, $params ) = get_enrolled_sql ( $this->context, 'mod/attcontrol:canbelisted', $groupid );
		} else {
			list ( $esql, $params ) = get_enrolled_sql ( $this->context, 'mod/attcontrol:canbelisted' );
		}
	
		$sql = "SELECT COUNT(*) FROM {user} u LEFT JOIN ($esql) eu ON eu.id=u.id WHERE u.deleted = 0 AND eu.id=u.id";
	
		$students = $DB->count_records_sql ( $sql, $params );
	
		return $students;
	}

    /**
     * Gets all sessions of this attcontrol instance, in between the dates of the filter
     */
    public function get_all_sessions() {
		global $DB;
		
		$select = " SELECT ats.* ";
		$from = " FROM {attcontrol} at
				 JOIN {attcontrol_sessions} ats ON at.id = ats.attcontrolid AND at.course = " . $this->course->id . " ";
		
		if ($this->pageparams->startdate && $this->pageparams->enddate) {
			$where = " WHERE sessdate >= :sdate AND sessdate < :edate";
		} else {
			$where = " ";
		}
	}

    /**
     * Gets the course report
     * @param int $offset first item to show
     * @param int $perpage number of elements by page
     * @return array course report
     */
    public function get_course_report($offset = null, $perpage = null) {
		global $DB;
		
		$students = $this->get_students ( $this->pageparams->get_current_sesstype (), $offset, $perpage );
		
		$select = " SELECT atl.* ";
		
		$from = " FROM {attcontrol} at 
				 JOIN {attcontrol_sessions} ats ON at.id = ats.attcontrolid AND at.course = " . $this->course->id . "
				 JOIN {attcontrol_log} atl ON ats.id = atl.sessionid ";
		
		if ($this->pageparams->get_current_sesstype () > att_page_with_filter_controls::SELTYPE_ALL) {
			$from .= " JOIN {groups_members} gm ON gm.groupid = :cgroup AND gm.userid = atl.studentid ";
		} else {
			$from .= " JOIN {enrol} e ON e.courseid = at.course
					   JOIN {user_enrolments} ue ON ue.enrolid = e.id AND atl.studentid = ue.userid ";
		}
		
		$from .= " JOIN {user} u ON u.id = atl.studentid ";
		
		if ($this->pageparams->startdate && $this->pageparams->enddate) {
			$where = " WHERE sessdate >= :sdate AND sessdate < :edate";
		} else {
			$where = " ";
		}
		
		// Construct the query
		$sql = $select . $from . $where;
		
		// Prepare the parameters
		$params = array (
				'sdate' => $this->pageparams->startdate,
				'edate' => $this->pageparams->enddate,
				'cgroup' => $this->pageparams->get_current_sesstype () 
		);
		
		$logs = $DB->get_recordset_sql ( $sql, $params, 'sessdate asc' );
		
		foreach ( $logs as $log ) {
			if (isset ( $students [$log->studentid] )) {
				$students [$log->studentid]->sessions [$log->sessionid] = $log;
			}
		}
		
		$logs->close ();
		
		return $students;
	}

    /**
     * Gets the current user information
     * @param $userid user id
     * @return object current user
     */
    public function get_user($userid) {
		global $DB;
		
		$user = $DB->get_record ( 'user', array (
				'id' => $userid 
		), '*', MUST_EXIST );
		
		$sql = "SELECT ue.userid, ue.status, ue.timestart, ue.timeend
                  FROM {user_enrolments} ue
                  JOIN {enrol} e ON e.id = ue.enrolid
                 WHERE ue.userid = :uid
                       AND e.status = :estatus
                       AND e.courseid = :courseid
              GROUP BY ue.userid, ue.status, ue.timestart, ue.timeend";
		$params = array (
				'uid' => $userid,
				'estatus' => ENROL_INSTANCE_ENABLED,
				'courseid' => $this->course->id 
		);
		$enrolmentsparams = $DB->get_record_sql ( $sql, $params );
		
		$user->enrolmentstatus = $enrolmentsparams->status;
		
		return $user;
	}

    /**
     * Gets the available attcontrol statuses
     * @return array statuses for attcontrol taking
     */
    public function get_statuses() {
		if (! isset ( $this->statuses )) {
			$this->statuses = att_get_statuses ();
		}
		
		return $this->statuses;
	}

    /**
     * Gets full session information
     * @param $sessionid session id
     * @return object session information
     */
    public function get_session_info($sessionid) {
		global $DB;
		
		if (! array_key_exists ( $sessionid, $this->sessioninfo )) {
			$this->sessioninfo [$sessionid] = $DB->get_record ( 'attcontrol_sessions', array (
					'id' => $sessionid 
			) );
		}
		if (empty ( $this->sessioninfo [$sessionid]->description )) {
			$this->sessioninfo [$sessionid]->description = get_string ( 'nodescription', 'attcontrol' );
		} else {
			$this->sessioninfo [$sessionid]->description = file_rewrite_pluginfile_urls ( $this->sessioninfo [$sessionid]->description, 'pluginfile.php', $this->context->id, 'mod_attcontrol', 'session', $this->sessioninfo [$sessionid]->id );
		}
		return $this->sessioninfo [$sessionid];
	}

    /**
     * Gets full sessions information
     * @param $sessionids session ids
     * @return array sessions and information about them
     */
    public function get_sessions_info($sessionids) {
		global $DB;
		
		list ( $sql, $params ) = $DB->get_in_or_equal ( $sessionids );
		$sessions = $DB->get_records_select ( 'attcontrol_sessions', "id $sql", $params, 'sessdate asc' );
		
		foreach ( $sessions as $sess ) {
			if (empty ( $sess->description )) {
				$sess->description = get_string ( 'nodescription', 'attcontrol' );
			} else {
				$sess->description = file_rewrite_pluginfile_urls ( $sess->description, 'pluginfile.php', $this->context->id, 'mod_attcontrol', 'session', $sess->id );
			}
		}
		
		return $sessions;
	}

    /**
     * Gets the session log
     * @param $sessionid session id
     * @return array of session logs
     */
    public function get_session_log($sessionid) {
		global $DB;
		
		$sql = "SELECT l.studentid, l.statusid, r.remarks, l.id
        		FROM {attcontrol_log} l
        		LEFT JOIN {attcontrol_remarks} r ON l.id = r.id
        		WHERE l.sessionid = ? ";
		
		$results = $DB->get_records_sql ( $sql, array (
				$sessionid 
		) );
		
		return $results;
	}

    /**
     * Gets the user statistics
     * @param $userid user id
     * @return array with the user statistics
     */
    public function get_user_stat($userid) {
		$ret = array ();
		$ret ['completed'] = $this->get_user_taken_sessions_count ( $userid );
		$ret ['statuses'] = $this->get_user_statuses_stat ( $userid );
		
		return $ret;
	}

    /**
     * Gets the number of taken sessions for a user
     * @param $userid user id
     * @return int number of taken sessions
     */
    public function get_user_taken_sessions_count($userid) {
		if (! array_key_exists ( $userid, $this->usertakensesscount )) {
			$this->usertakensesscount [$userid] = att_get_user_taken_sessions_count ( $this->id, $this->course->startdate, $userid );
		}
		return $this->usertakensesscount [$userid];
	}

    /**
     * Gets the statistics for the user statuses
     * @param $userid user id
     * @return object user statistics
     */
    public function get_user_statuses_stat($userid) {
		global $DB;
		
		if (! array_key_exists ( $userid, $this->userstatusesstat )) {
			$qry = "SELECT al.statusid, count(al.statusid) AS stcnt
                      FROM {attcontrol_log} al
                      JOIN {attcontrol_sessions} ats
                        ON al.sessionid = ats.id
                     WHERE ats.attcontrolid = :aid AND
                           ats.sessdate >= :cstartdate AND
                           al.studentid = :uid
                  GROUP BY al.statusid";
			$params = array (
					'aid' => $this->id,
					'cstartdate' => $this->course->startdate,
					'uid' => $userid 
			);
			
			$this->userstatusesstat [$userid] = $DB->get_records_sql ( $qry, $params );
		}
		
		return $this->userstatusesstat [$userid];
	}

    /**
     * Gets the sessions log, filtered by the criteria in the view
     * @param $userid user id
     * @return array logs filtered
     */
    public function get_user_filtered_sessions_log($userid) {
		global $DB;
		
		if ($this->pageparams->startdate && $this->pageparams->enddate) {
			$where = "ats.attcontrolid = :aid AND
                      ats.sessdate >= :sdate AND ats.sessdate < :edate";
		} else {
			$where = "ats.attcontrolid = :aid ";
		}
		
		$sql = "SELECT ats.id, ats.sessdate, ats.groupid, al.statusid
                  FROM {attcontrol_sessions} ats
                  JOIN {attcontrol_log} al
                    ON ats.id = al.sessionid AND al.studentid = :uid
                 WHERE $where
              ORDER BY ats.sessdate ASC";
		
		$params = array (
				'uid' => $userid,
				'aid' => $this->id,
				'sdate' => $this->pageparams->startdate,
				'edate' => $this->pageparams->enddate 
		);
		$sessions = $DB->get_records_sql ( $sql, $params );
		
		return $sessions;
	}

    /**
     * Gets the extended log, filtered by the criteria in the view
     * @param $userid user id
     * @return array logs filtered
     */
    public function get_user_filtered_sessions_log_extended($userid) {
		global $DB;
		
		// All taken sessions (including previous groups).
		
		$groups = array_keys ( groups_get_all_groups ( $this->course->id, $userid ) );
		$groups [] = 0;
		list ( $gsql, $gparams ) = $DB->get_in_or_equal ( $groups, SQL_PARAMS_NAMED, 'gid0' );
		
		if ($this->pageparams->startdate && $this->pageparams->enddate) {
			$where = "ats.attcontrolid = :aid AND
                      ats.sessdate >= :sdate AND ats.sessdate < :edate";
			$where2 = "ats.attcontrolid = :aid2 AND
                      ats.sessdate >= :sdate2 AND ats.sessdate < :edate2 AND ats.groupid $gsql";
		} else {
			$where = "ats.attcontrolid = :aid ";
			$where2 = "ats.attcontrolid = :aid2 AND ats.groupid $gsql";
		}
		
		$sql = "SELECT ats.id, ats.groupid, ats.sessdate, ats.duration, ats.description, al.statusid, ar.remarks
                  FROM {attcontrol_sessions} ats
                RIGHT JOIN {attcontrol_log} al
                    ON ats.id = al.sessionid AND al.studentid = :uid
                RIGHT JOIN {attcontrol_remarks} ar
                	ON al.id = ar.id
                 WHERE $where
            UNION
                SELECT ats.id, ats.groupid, ats.sessdate, ats.duration, ats.description, al.statusid, ar.remarks
                  FROM {attcontrol_sessions} ats
                LEFT JOIN {attcontrol_log} al
                    ON ats.id = al.sessionid AND al.studentid = :uid2
                LEFT JOIN {attcontrol_remarks} ar
                	ON al.id = ar.id
                 WHERE $where2
             ORDER BY sessdate ASC";
		
		$params = array (
				'uid' => $userid,
				'aid' => $this->id,
				'sdate' => $this->pageparams->startdate,
				'edate' => $this->pageparams->enddate,
				'uid2' => $userid,
				'aid2' => $this->id,
				'sdate2' => $this->pageparams->startdate,
				'edate2' => $this->pageparams->enddate 
		);
		$params = array_merge ( $params, $gparams );
		$sessions = $DB->get_records_sql ( $sql, $params );
		
		foreach ( $sessions as $sess ) {
			if (empty ( $sess->description )) {
				$sess->description = get_string ( 'nodescription', 'attcontrol' );
			} else {
				$sess->description = file_rewrite_pluginfile_urls ( $sess->description, 'pluginfile.php', $this->context->id, 'mod_attcontrol', 'session', $sess->id );
			}
		}
		
		return $sessions;
	}

    /**
     * Deletes the indicated sessions
     * @param $sessionsids session ids to be deleted
     */
    public function delete_sessions($sessionsids) {
		global $DB;
		
		list ( $sql, $params ) = $DB->get_in_or_equal ( $sessionsids );
		$DB->delete_records_select ( 'attcontrol_log', "sessionid $sql", $params );
		$DB->delete_records_list ( 'attcontrol_sessions', 'id', $sessionsids );
		add_to_log ( $this->course->id, 'attcontrol', 'sessions deleted', $this->url_manage (), get_string ( 'sessionsids', 'attcontrol' ) . implode ( ', ', $sessionsids ), $this->cm->id );
	}

    /**
     * Updates the duration of the indicated sessions
     * @param $sessionsids session ids to be changed
     * @param $duration new duration of the sessions
     */
    public function update_sessions_duration($sessionsids, $duration) {
		global $DB;
		
		$now = time ();
		$sessions = $DB->get_records_list ( 'attcontrol_sessions', 'id', $sessionsids );
		foreach ( $sessions as $sess ) {
			$sess->duration = $duration;
			$sess->timemodified = $now;
			$DB->update_record ( 'attcontrol_sessions', $sess );
		}
		add_to_log ( $this->course->id, 'attcontrol', 'sessions duration updated', $this->url_manage (), get_string ( 'sessionsids', 'attcontrol' ) . implode ( ', ', $sessionsids ), $this->cm->id );
	}

    /**
     * Compare two sessions, in order to sort them
     * @param $sessA first session
     * @param $sessB second session
     * @return int result of the comparison
     */
    private function sessioncmp ($sessA, $sessB) {
		return $sessA->sessdate - $sessB->sessdate;
	}
	
	/**
	 * Set the perpage preference for the user.
	 *
	 * @access public
	 * @param int $perpage        	
	 * @return void
	 */
	public function set_attcontrol_perpage($perpage) {
		set_user_preference ( 'attcontrol_perpage', $perpage );
	}
}


/**
 * Gets all the statuses in attcontrol
 * @return array statuses in attcontrol
 */
function att_get_statuses() {
	$config = get_config ( 'attcontrol' );
	$statuses = array ();
	for($i = 1; $i < 9; $i ++) {
		if (trim ( $config->{"status" . $i} )) {
			$status = new stdClass ();
			$status->id = $i;
			$status->acronym = $config->{"status" . $i};
			$status->description = $config->{"statusdesc" . $i};
			
			$statuses [$i] = $status;
		}
	}
	
	return $statuses;
}

/**
 * Gets the number of taken sessions for a particular user
 * @param $attid attcontrol id
 * @param $coursestartdate course start date
 * @param $userid user id
 * @return int number of sessions according to the parameters
 */
function att_get_user_taken_sessions_count($attid, $coursestartdate, $userid) {
	global $DB;
	
	$qry = "SELECT count(*) as cnt
              FROM {attcontrol_log} al
              JOIN {attcontrol_sessions} ats
                ON al.sessionid = ats.id
             WHERE ats.attcontrolid = :aid AND
                   ats.sessdate >= :cstartdate AND
                   al.studentid = :uid";
	$params = array (
			'aid' => $attid,
			'cstartdate' => $coursestartdate,
			'uid' => $userid 
	);
	
	return $DB->count_records_sql ( $qry, $params );
}

/**
 * Gets the user statistics according to the statuses
 * @param $attid attcontrol id
 * @param $coursestartdate course start date
 * @param $userid user id
 * @return array statistics according to the parameters set
 */
function att_get_user_statuses_stat($attid, $coursestartdate, $userid) {
	global $DB;
	
	$qry = "SELECT al.statusid, count(al.statusid) AS stcnt
              FROM {attcontrol_log} al
              JOIN {attcontrol_sessions} ats
                ON al.sessionid = ats.id
             WHERE ats.attcontrolid = :aid AND
                   ats.sessdate >= :cstartdate AND
                   al.studentid = :uid
          GROUP BY al.statusid";
	$params = array (
			'aid' => $attid,
			'cstartdate' => $coursestartdate,
			'uid' => $userid 
	);
	
	return $DB->get_records_sql ( $qry, $params );
}

/**
 * Gets the available attcontrols for a user
 * @param $userid user id
 * @return array of attcontrol records
 */
function att_get_user_courses_attcontrols($userid) {
	global $DB;
	
	$usercourses = enrol_get_users_courses ( $userid );
	
	list ( $usql, $uparams ) = $DB->get_in_or_equal ( array_keys ( $usercourses ), SQL_PARAMS_NAMED, 'cid0' );
	
	$sql = "SELECT att.id as attid, att.course as courseid, course.fullname as coursefullname,
                   course.startdate as coursestartdate, att.name as attname 
              FROM {attcontrol} att
              JOIN {course} course
                   ON att.course = course.id
             WHERE att.course $usql
          ORDER BY coursefullname ASC, attname ASC";
	
	$params = array_merge ( $uparams, array (
			'uid' => $userid 
	) );
	
	return $DB->get_records_sql ( $sql, $params );
}

/**
 * Returns whether a user has logs for a certain attcontrol status
 * @param $statusid status id
 * @return bool whether this user has logs or not
 */
function att_has_logs_for_status($statusid) {
	global $DB;
	
	return $DB->count_records ( 'attcontrol_log', array (
			'statusid' => $statusid 
	) ) > 0;
}

/**
 * Convert the url for the attcontrol log
 * @param moodle_url $fullurl
 * @return string
 */
function att_log_convert_url(moodle_url $fullurl) {
	static $baseurl;
	
	if (! isset ( $baseurl )) {
		$baseurl = new moodle_url ( '/mod/attcontrol/' );
		$baseurl = $baseurl->out ();
	}
	
	return substr ( $fullurl->out (), strlen ( $baseurl ) );
}

/**
 * Upgrade the old attfor block
 */
function attforblock_upgrade() {
	global $DB, $CFG;
	$module = $DB->get_record ( 'modules', array (
			'name' => 'attforblock' 
	) );
	if ($module->version <= '2011061800') {
		print_error ( "noupgradefromthisversion", 'attcontrol' );
	}
	if (file_exists ( $CFG->dirroot . '/mod/attforblock' )) {
		print_error ( "attforblockdirstillexists", 'attcontrol' );
	}
	
	// Now rename attforblock table and replace with attcontrol?
	$dbman = $DB->get_manager (); // Loads ddl manager and xmldb classes.
	$table = new xmldb_table ( 'attforblock' );
	$newtable = new xmldb_table ( 'attcontrol' ); // Sanity check to make sure 'attcontrol' table doesn't already exist.
	if ($dbman->table_exists ( $table ) && ! $dbman->table_exists ( $newtable )) {
		$dbman->rename_table ( $table, 'attcontrol' );
	} else {
		print_error ( "tablerenamefailed", 'attcontrol' );
	}
	// Now convert module record.
	$module->name = 'attcontrol';
	$DB->update_record ( 'modules', $module );
	
	// Clear cache for courses with attcontrols.
	$attcontrols = $DB->get_recordset ( 'attcontrol', array (), '', 'course' );
	foreach ( $attcontrols as $attcontrol ) {
		rebuild_course_cache ( $attcontrol->course, true );
	}
	$attcontrols->close ();
}