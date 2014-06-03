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
 * Library of functions and constants for module attcontrol
 *
 * @package   mod_attcontrol
 * @copyright  2013 José Luis Antúnez <jantunez@xtec.cat>
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Returns if the module supports a Moodle feature
 *
 * @see plugin_supports() in lib/moodlelib.php
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function attcontrol_supports($feature) {
    switch($feature) {
        case FEATURE_GRADE_HAS_GRADE:
            return false;
        case FEATURE_GROUPS:
            return true;
        case FEATURE_GROUPINGS:
            return false;
        case FEATURE_GROUPMEMBERSONLY:
            return false;
        case FEATURE_MOD_INTRO:
            return false;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return false;
        default:
            return null;
    }
}

/**
 * Adds a new attcontrol instance
 * @param $attcontrol attcontrol instance
 * @return object attcontrol instance
 */
function attcontrol_add_instance($attcontrol) {
    global $DB;

    $attcontrol->timemodified = time();

    $attcontrol->id = $DB->insert_record('attcontrol', $attcontrol);

    return $attcontrol->id;
}


/**
 * Updates an attcontrol instance
 * @param $attcontrol attcontrol instance
 * @return bool whether the update was done or not
 */
function attcontrol_update_instance($attcontrol) {
    global $DB;

    $attcontrol->timemodified = time();
    $attcontrol->id = $attcontrol->instance;

    if (! $DB->update_record('attcontrol', $attcontrol)) {
        return false;
    }

    return true;
}


/**
 * Deleted an attcontrol instance and all its information
 * @param $id attcontrol id
 * @return bool whether the deletion was right or not
 */
function attcontrol_delete_instance($id) {
    global $DB;

    if (! $attcontrol = $DB->get_record('attcontrol', array('id'=> $id))) {            
        return false;
    }

    if ($sessids = array_keys($DB->get_records('attcontrol_sessions', array('attcontrolid'=> $id), '', 'id'))) {

        foreach ($sessids as $sess) {
            if ($logs = array_keys($DB->get_records('attcontrol_log', array('sessionid'=> $sess), '', 'id'))) {

                $DB->delete_records_list('attcontrol_log', 'id', $logs);
                $DB->delete_records_list('attcontrol_remarks', 'id', $logs);
            }
        }

        $DB->delete_records('attcontrol_sessions', array('attcontrolid'=> $id));
    }

    $DB->delete_records('attcontrol', array('id'=> $id));

    return true;
}

/**
 * Operations to be done when deleting a course
 * @param $course course deleted
 * @param bool $feedback whether feedback must be shown or not
 * @return bool if the attcontrol instances were successfully deleted
 */
function attcontrol_delete_course($course, $feedback=true) {
    global $DB;

    $attids = array_keys($DB->get_records('attcontrol', array('course'=> $course->id), '', 'id'));

    foreach ($attids as $attid) {
        self::attcontrol_delete_instance($attid);
    }

    return true;
}

/**
 * Reset course form
 * Called by course/reset.php
 * @param $mform form passed by reference
 */
function attcontrol_reset_course_form_definition(&$mform) {
    $mform->addElement('header', 'attcontrolheader', get_string('modulename', 'attcontrol'));

    $mform->addElement('static', 'description', get_string('description', 'attcontrol'),
                                get_string('resetdescription', 'attcontrol'));
    $mform->addElement('checkbox', 'reset_attcontrol_log', get_string('deletelogs', 'attcontrol'));

    $mform->addElement('checkbox', 'reset_attcontrol_sessions', get_string('deletesessions', 'attcontrol'));
    $mform->disabledIf('reset_attcontrol_sessions', 'reset_attcontrol_log', 'notchecked');

}

/**
 * Form defaults for reset course view.
 * Course reset form defaults.
 */
function attcontrol_reset_course_form_defaults($course) {
    return array('reset_attcontrol_log'=>0, 'reset_attcontrol_statuses'=>0, 'reset_attcontrol_sessions'=>0);
}

/**
 * Reset user data from attcontrol
 * @param $data data to be reset
 * @return array status description for the reset operation
 */
function attcontrol_reset_userdata($data) {
    global $DB;

    $status = array();

    $attids = array_keys($DB->get_records('attcontrol', array('course'=> $data->courseid), '', 'id'));

    if (!empty($data->reset_attcontrol_log)) {
        $sess = $DB->get_records_list('attcontrol_sessions', 'attcontrolid', $attids, '', 'id');
        if (!empty($sess)) {
            foreach ($sess as $ses) {
                if ($logs = array_keys($DB->get_records('attcontrol_log', array('sessionid'=> $ses->id), '', 'id'))) {

                    $DB->delete_records_list('attcontrol_log', 'id', $logs);
                    $DB->delete_records_list('attcontrol_remarks', 'id', $logs);
                }
            }

            list($sql, $params) = $DB->get_in_or_equal($attids);
            $DB->set_field_select('attcontrol_sessions', 'lasttaken', 0, "attcontrolid $sql", $params);

            $status[] = array(
                'component' => get_string('modulenameplural', 'attcontrol'),
                'item' => get_string('attcontroldata', 'attcontrol'),
                'error' => false
            );
        }
    }

    if (!empty($data->reset_attcontrol_sessions)) {
        $DB->delete_records_list('attcontrol_sessions', 'attcontrolid', $attids);

        $status[] = array(
            'component' => get_string('modulenameplural', 'attcontrol'),
            'item' => get_string('statuses', 'attcontrol'),
            'error' => false
        );
    }

    return $status;
}
/*
 * Return a small object with summary information about what a
 *  user has done with a given particular instance of this module
 *  Used for user activity reports.
 *  $return->time = the time they did it
 *  $return->info = a short text description
 */
/**
 * @param $course
 * @param $user
 * @param $mod
 * @param $attcontrol
 * @return null
 */
function attcontrol_user_outline($course, $user, $mod, $attcontrol) {
    return null;
}
/*
 * Print a detailed representation of what a  user has done with
 * a given particular instance of this module, for user activity reports.
 *
 */
/**
 * @param $course
 * @param $user
 * @param $mod
 * @param $attcontrol
 * @return null
 */
function attcontrol_user_complete($course, $user, $mod, $attcontrol) {
    return null;
}

/**
 * @param $course
 * @param $isteacher
 * @param $timestart
 * @return bool
 */
function attcontrol_print_recent_activity($course, $isteacher, $timestart) {
    return false;
}

/**
 * @return bool
 */
function attcontrol_cron () {
    return true;
}

/**
 * @param $attcontrol
 * @param int $userid
 * @param bool $nullifnone
 */
function attcontrol_update_grades($attcontrol, $userid=0, $nullifnone=true) {
    // We need this function to exist so that quick editing of module name is passed to gradebook.
}
/**
 * Create grade item for given attcontrol
 *
 * @param object $attcontrol object with extra cmidnumber
 * @param mixed optional array/object of grade(s); 'reset' means reset grades in gradebook
 * @return int 0 if ok, error code otherwise
 */
function attcontrol_grade_item_update($attcontrol, $grades=null) {
    return false;
}

/**
 * Delete grade item for given attcontrol
 *
 * @param object $attcontrol object
 * @return object attcontrol
 */
function attcontrol_grade_item_delete($attcontrol) {
    return false;
}

/**
 * @param $attcontrolid
 * @return bool
 */
function attcontrol_get_participants($attcontrolid) {
    return false;
}

/**
 * This function returns if a scale is being used by one attcontrol
 * it it has support for grading and scales. Commented code should be
 * modified if necessary. See book, glossary or journal modules
 * as reference.
 *
 * @param int $attcontrolid
 * @param int $scaleid
 * @return boolean True if the scale is used by any attcontrol
 */
function attcontrol_scale_used ($attcontrolid, $scaleid) {
    return false;
}

/**
 * Checks if scale is being used by any instance of attcontrol
 *
 * This is used to find out if scale used anywhere
 *
 * @param int $scaleid
 * @return bool true if the scale is used by any book
 */
function attcontrol_scale_used_anywhere($scaleid) {
    return false;
}

/**
 * Serves the attcontrol sessions descriptions files.
 *
 * @param object $course
 * @param object $cm
 * @param object $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @return bool false if file not found, does not return if found - just sends the file
 */
function attcontrol_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload) {
    return null;
}
