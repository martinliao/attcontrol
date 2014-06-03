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
 * attcontrol module renderering methods
 *
 * @package    mod_attcontrol
 * @copyright  2013 José Luis Antúnez <jantunez@xtec.cat>
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__).'/locallib.php');
require_once(dirname(__FILE__).'/renderables.php');
require_once(dirname(__FILE__).'/renderhelpers.php');

/**
 * attcontrol module renderer class
 *
 * @copyright  2013 José Luis Antúnez <jantunez@xtec.cat>
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_attcontrol_renderer extends plugin_renderer_base {
	// External API - methods to render attcontrol renderable components.

	/**
	 * Renders tabs for attcontrol
	 *
	 * @param atttabs - tabs to display
	 * @return string html code
	 */
	protected function render_attcontrol_tabs(attcontrol_tabs $atttabs) {
		return print_tabs($atttabs->get_tabs(), $atttabs->currenttab, null, null, true);
	}

	/**
	 * Renders filter controls for attcontrol
	 *
	 * @param fcontrols - filter controls data to display
	 * @return string html code
	 */
	protected function render_attcontrol_filter_controls(attcontrol_filter_controls $fcontrols) {
		
		$filtertable = new html_table();
		$filtertable->attributes['class'] = ' ';
		$filtertable->width = '100%';
		$filtertable->align = array('left', 'center', 'right');

		$filtertable->data[0][] = $this->render_sess_group_selector($fcontrols);

		$filtertable->data[0][] = $this->render_curdate_controls($fcontrols);

		$filtertable->data[0][] = $this->render_view_controls($fcontrols);

		$o = html_writer::table($filtertable);
		$o = $this->output->container($o, 'attfiltercontrols');

		return $o;
	}


	protected function render_attcontrol_pagination_selector($total, $page, $perpage, $link) {
		global $OUTPUT;
		return $OUTPUT->paging_bar($total, $page, $perpage, $link);
	}
	
	protected function render_attcontrol_individual_report_data(attcontrol_individual_report_data $reportdata) {
		$o = "";
		if ($reportdata->studentinfo) $o .= $this->construct_user_box($reportdata->studentinfo);
		
		$o .= $this->generate_individual_report_table ( $reportdata );
		$o .= $this->render_attcontrol_pagination ( $reportdata->url () );
	
		return $o;
	}
	
	protected function generate_individual_report_table(attcontrol_individual_report_data &$reportdata) {
		if (isset($reportdata->currentstudent) && $reportdata->currentstudent == att_individualreport_page_params::SELSTUDENT_NONE) {
			$o = "<p class='nostudent'>".get_string("nostudentselected", "attcontrol")."</p>";
		} else {
				
			$table = new html_table ();
				
			$table->attributes ['class'] = 'generaltable';

			$table->head [] = '#';
			$table->align [] = 'left';
			$table->size [] = '1px';
				
			$table->head [] = get_string ( "date", "attcontrol" );
			$table->align [] = 'left';
			$table->size [] = '';
				
			$table->head [] = get_string ( "time", "attcontrol" );
			$table->align [] = 'left';
			$table->size [] = '';
			
			$sumstatuses = array();
			foreach ( $reportdata->statuses as $key => $status ) {
				$table->head [] = $status->acronym;
				$table->align [] = 'center';
				$table->size [] = '20px';
				
				$sumstatuses [$key] = 0;
			}
			$table->head [] = get_string ( "status", "attcontrol" );
			$table->align [] = 'left';
			$table->size [] = '';
				
			$table->head [] = get_string ( "remark", "attcontrol" );
			$table->align [] = 'left';
			$table->size [] = '';
				
			$i = 1;

			
			foreach ( $reportdata->logs as $log ) {
				$row = new html_table_row ();
	
				$row->cells [] = $i + $reportdata->offset;
				
				
				if ($reportdata->perm->can_take()) {
					$url = $reportdata->url_take($log->sessionid, $log->groupid);
					$row->cells [] = html_writer::tag('a',
											userdate ($log->sessdate, get_string ( 'strftimedmyw', 'attcontrol' )),
											array('href' => $url));
					$row->cells [] = html_writer::tag('a',
											userdate ( $log->sessdate, get_string ( 'strftimehm', 'attcontrol' ) ) . " - " . userdate ( $log->sessdate + $log->duration, get_string ( 'strftimehm', 'attcontrol' ) ),
											array('href' => $url));
				}
				else {
					$row->cells [] = userdate ($log->sessdate, get_string ( 'strftimedmyw', 'attcontrol' ));
					$row->cells [] = userdate ( $log->sessdate, get_string ( 'strftimehm', 'attcontrol' ) );
				}
				
				foreach ( $reportdata->statuses as $key => $status ) {
					if ($key == $log->statusid) {
						$row->cells[] = "&#215;";
						$sumstatuses[$key]++;
					}
					else {
						$row->cells[] = "&nbsp;";
					}
					
				}
				
				
				$row->cells [] = $reportdata->statuses[$log->statusid]->description;
				if ($log->remarks) {
					$row->cells [] = $log->remarks;
				}
				else {
					$row->cells [] = "&nbsp;";
					
				}
				
				$selectedhidden ="";	
	
				$table->data [] = $row;
	
				$i ++;
			}
			
			$row = new html_table_row();
			$row->attributes = array("class" => "reportfooter");
				
			for ($i=0;$i<3;$i++) {
				$row->cells[] = "&nbsp;";
			}
				
			foreach ($sumstatuses as $status) {
				$row->cells[] = $status;
			}
				
			$row->cells[] = "&nbsp;";
			$row->cells[] = "&nbsp;";
			
			$table->data [] = $row;
				
			$o = html_writer::table ( $table );
				
			$table = new html_table ();
			$table->attributes ['class'] = ' ';
			$table->width = '100%';
			$table->align = array (
					'left',
					'right'
			);
				
			$table->data [0] [] = $this->render_attcontrol_pagination_selector ( $reportdata->total, $reportdata->page, $reportdata->perpage, $reportdata->url () );

			$o .= html_writer::table ( $table );
		}
	
	
	
		return $o;
	}

	protected function render_attcontrol_individualreport_filter_controls(attcontrol_filter_controls $fcontrols) {
		
		$filtertable = new html_table ();
		$filtertable->attributes ['class'] = ' filtertable ';
		$filtertable->align = array (
				'left',
				'center',
				'right'
		);
	
		$filtertable->data [0] [] = $this->render_student_selector ( $fcontrols );
	
		$filtertable->data [0] [] = $this->render_curdate_controls ( $fcontrols );
	
		$filtertable->data [0] [] = $this->render_view_controls ( $fcontrols );
	
		$o = html_writer::table ( $filtertable );
		$o = $this->output->container ( $o, 'attfiltercontrols' );
	
		return $o;
	}
	
	protected function render_attcontrol_ownreport_filter_controls(attcontrol_filter_controls $fcontrols) {
		global $USER;
		
		$filtertable = new html_table ();
		$filtertable->attributes ['class'] = ' filtertable ';
		$filtertable->align = array (
				'left',
				'center',
				'right'
		);
	
		$filtertable->data [0] [] = "";
	
		$filtertable->data [0] [] = $this->render_curdate_controls ( $fcontrols );
	
		$filtertable->data [0] [] = $this->render_view_controls ( $fcontrols );
	
		$o = html_writer::table ( $filtertable );
		$o = $this->output->container ( $o, 'attfiltercontrols' );
	
		return $o;
	}
	
	protected function render_student_selector(attcontrol_individualreport_filter_controls $fcontrols) {
		$students = $fcontrols->get_students_list ();
	
		if ($students) {
			$select = new single_select ( $fcontrols->url (), 'studentid', $students, $fcontrols->get_current_student (), array (
					att_individualreport_page_params::SELSTUDENT_NONE => get_string ( 'studentselect', 'attcontrol' )
			), 'selectstudent' );
			$select->label = get_string ( 'student', 'attcontrol' );
			$output = $this->output->render ( $select );
				
			return html_writer::tag ( 'div', $output, array (
					'class' => 'studentselector'
			) );
		}
	
		return '';
	}
	
	
	
	protected function render_attcontrol_pagination($link) {
		$o = html_writer::start_tag('form', array('id' => 'paginationform', 'method' => 'post',
				'action' => $link, 'class' => 'optionspref mform'));
		$o .= html_writer::start_tag('fieldset', array('class' => 'clearfix'));
		$o .= html_writer::tag('legend', get_string('pagination_options', 'attcontrol'));

		$o .= html_writer::start_tag('div', array('class' => 'fcontainer'));

		$o .= html_writer::start_tag('div', array('class' => 'fitem fitem_ftext', 'id' => 'fitem_id_perpage'));
		$o .= html_writer::start_tag('div', array('class' => 'fitemtitle'));

		$o .= html_writer::tag('label', get_string('pagination_perpage', 'attcontrol').":", array('for' => 'id_perpage'));
		$o .= html_writer::end_tag('div');

		$o .= html_writer::start_tag('div', array('class' => 'felement ftext'));

		$o .= html_writer::empty_tag('input', array('type' => 'text', 'id' => 'id_perpage', 'value' => get_user_preferences('attcontrol_perpage', 10), 'name' => 'perpage', 'size' => '3'));

		$o .= html_writer::end_tag('div');


		$o .= html_writer::end_tag('div');


		$o .= html_writer::start_tag('div', array('class' => 'fitem fitem_fsubmit', 'id' => 'fitem_id_savepreferences'));
		$o .= html_writer::start_tag('div', array('class' => 'felement fsubmit'));

		$o .= html_writer::empty_tag('input', array('type' => 'submit', 'id' => 'id_savepreferences', 'value' => get_string ( 'savepreferences', 'attcontrol' ), 'name' => 'savepreferences'));

		$o .= html_writer::end_tag('div');
		$o .= html_writer::end_tag('div');

		$o .= html_writer::end_tag('div');

		$o .= html_writer::end_tag('fieldset');


		$o .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'action', 'value' => att_manage_page_params::ACTION_CHANGE_PERPAGE));




		$o .= html_writer::end_tag('form');

		return $o;
	}

	protected function render_sess_group_selector(attcontrol_filter_controls $fcontrols) {
		switch ($fcontrols->pageparams->selectortype) {
		case att_page_with_filter_controls::SELECTOR_SESS_TYPE:
			$sessgroups = $fcontrols->get_sess_groups_list();
			if ($sessgroups) {
				$select = new single_select($fcontrols->url(), 'group', $sessgroups,
					$fcontrols->get_current_sesstype(), null, 'selectgroup');
				$select->label = get_string('sessions', 'attcontrol');
				$output = $this->output->render($select);

				return html_writer::tag('div', $output, array('class' => 'groupselector'));
			}
			break;
		case att_page_with_filter_controls::SELECTOR_GROUP:
			return groups_print_activity_menu($fcontrols->cm, $fcontrols->url(), true);
		}

		return '';
	}

	protected function render_curdate_controls(attcontrol_filter_controls $fcontrols) {
		global $CFG;

		$curdate_controls = '';
		if ($fcontrols->curdatetxt) {
			$this->page->requires->strings_for_js(array('calclose', 'caltoday'), 'attcontrol');
			$jsvals = array(
				'cal_months'    => explode(',', get_string('calmonths', 'attcontrol')),
				'cal_week_days' => explode(',', get_string('calweekdays', 'attcontrol')),
				'cal_start_weekday' => $CFG->calendar_startwday,
				'cal_cur_date'  => $fcontrols->curdate);
			$curdate_controls = html_writer::script(js_writer::set_variable('M.attcontrol', $jsvals));

			$this->page->requires->js('/mod/attcontrol/calendar.js');

			$curdate_controls .= html_writer::link($fcontrols->url(array('curdate' => $fcontrols->prevcur)),
				$this->output->larrow());
			$params = array(
				'title' => get_string('calshow', 'attcontrol'),
				'id'    => 'show',
				'type'  => 'button');
			$button_form = html_writer::tag('button', $fcontrols->curdatetxt, $params);
			foreach ($fcontrols->url_params(array('curdate' => '')) as $name => $value) {
				$params = array(
					'type'  => 'hidden',
					'id'    => $name,
					'name'  => $name,
					'value' => $value);
				$button_form .= html_writer::empty_tag('input', $params);
			}
			$params = array(
				'id'        => 'currentdate',
				'action'    => $fcontrols->url_path(),
				'method'    => 'post'
			);

			$button_form = html_writer::tag('form', $button_form, $params);
			$curdate_controls .= $button_form;

			$curdate_controls .= html_writer::link($fcontrols->url(array('curdate' => $fcontrols->nextcur)),
				$this->output->rarrow());
		}

		return $curdate_controls;
	}

	protected function render_view_controls(attcontrol_filter_controls $fcontrols) {
		$views[ATT_VIEW_ALL] = get_string('all', 'attcontrol');
		$views[ATT_VIEW_ALLPAST] = get_string('allpast', 'attcontrol');
		$views[ATT_VIEW_MONTHS] = get_string('months', 'attcontrol');
		$views[ATT_VIEW_WEEKS] = get_string('weeks', 'attcontrol');
		$views[ATT_VIEW_DAYS] = get_string('days', 'attcontrol');
		$viewcontrols = '';
		foreach ($views as $key => $sview) {
			if ($key != $fcontrols->pageparams->view) {
				$link = html_writer::link($fcontrols->url(array('view' => $key)), $sview);
				$viewcontrols .= html_writer::tag('span', $link, array('class' => 'attbtn'));
			} else {
				$viewcontrols .= html_writer::tag('span', $sview, array('class' => 'attcurbtn'));
			}
		}

		return html_writer::tag('nobr', $viewcontrols);
	}

	/**
	 * Renders attcontrol sessions managing table
	 *
	 * @param attcontrol_manage_data $sessdata to display
	 * @return string html code
	 */
	protected function render_attcontrol_manage_data(attcontrol_manage_data $sessdata) {
		$o = $this->render_sess_manage_table($sessdata) . $this->render_sess_manage_control($sessdata);
		$o = html_writer::tag('form', $o, array('method' => 'post', 'action' => $sessdata->url_sessions()->out()));
		$o .= $this->render_attcontrol_pagination($sessdata->url_manage());
		$o = $this->output->container($o, 'generalbox');
		$o = $this->output->container($o, 'attsessions_manage_table');

		return $o;
	}

	protected function render_sess_manage_table(attcontrol_manage_data $sessdata) {
		$this->page->requires->js ( '/mod/attcontrol/module.js' );
		$this->page->requires->js_init_call('M.mod_attcontrol.init_manage');

		$table = new html_table();
		$table->width = '100%';
		$table->head = array(
			'#',
			get_string('sessiontypeshort', 'attcontrol'),
			get_string('date'),
			get_string('time'),
			get_string('description', 'attcontrol'),
			get_string('actions'),
			html_writer::checkbox('cb_selector', 0, false, '', array('id' => 'cb_selector'))
		);
		$table->align = array('center', '', '', 'center', '', 'center', 'center');
		$table->size = array('5%', '20%', '8%', '8%', '20%', '10%', '10%');

		$i = 0;
		foreach ($sessdata->sessions as $key => $sess) {
			$i++;

			$dta = $this->construct_date_time_actions($sessdata, $sess);

			$table->data[$sess->id][] = $i + $sessdata->offset; //Added offset for pagination
			$table->data[$sess->id][] = $sess->groupid ? $sessdata->groups[$sess->groupid]->name :
			get_string('commonsession', 'attcontrol');
			$table->data[$sess->id][] = $dta['date'];
			$table->data[$sess->id][] = $dta['time'];
			$table->data[$sess->id][] = strip_tags($sess->description); //removed html format from cell.
			$table->data[$sess->id][] = $dta['actions'];
			$table->data[$sess->id][] = html_writer::checkbox('sessid[]', $sess->id, false);
		}

		return html_writer::table($table);
	}

	private function construct_date_time_actions(attcontrol_manage_data $sessdata, $sess) {
		$actions = '';

		$date = userdate($sess->sessdate, get_string('strftimedmyw', 'attcontrol'));
		$time = $this->construct_time($sess->sessdate, $sess->duration);
		if ($sess->lasttaken > 0) {
			if ($sessdata->perm->can_change()) {
				$url = $sessdata->url_take($sess->id, $sess->groupid);
				$title = get_string('changeattcontrol', 'attcontrol');

				$date = html_writer::link($url, $date, array('title' => $title));
				$time = html_writer::link($url, $time, array('title' => $title));

				$actions = $this->output->action_icon($url, new pix_icon('redo', $title, 'attcontrol'));
			} else {
				$date = '<i>' . $date . '</i>';
				$time = '<i>' . $time . '</i>';
			}
		} else {
			if ($sessdata->perm->can_take()) {
				$url = $sessdata->url_take($sess->id, $sess->groupid);
				$title = get_string('takeattcontrol', 'attcontrol');
				$actions = $this->output->action_icon($url, new pix_icon('t/go', $title));
			}
		}
		if ($sessdata->perm->can_manage()) {
			$url = $sessdata->url_sessions($sess->id, att_sessions_page_params::ACTION_UPDATE);
			$title = get_string('editsession', 'attcontrol');
			$actions .= $this->output->action_icon($url, new pix_icon('t/edit', $title));

			$url = $sessdata->url_sessions($sess->id, att_sessions_page_params::ACTION_DELETE);
			$title = get_string('deletesession', 'attcontrol');
			$actions .= $this->output->action_icon($url, new pix_icon('t/delete', $title));
		}

		return array('date' => $date, 'time' => $time, 'actions' => $actions);
	}

	protected function render_sess_manage_control(attcontrol_manage_data $sessdata) {
		$table = new html_table();
		$table->attributes['class'] = ' ';
		$table->width = '100%';
		$table->align = array('left', 'right');


		$table->data[0][] = $this->render_attcontrol_pagination_selector($sessdata->total, $sessdata->page, $sessdata->perpage, $sessdata->url_manage());


		if ($sessdata->perm->can_manage()) {
			$options = array(
				att_sessions_page_params::ACTION_DELETE_SELECTED => get_string('delete'),
				att_sessions_page_params::ACTION_CHANGE_DURATION => get_string('changeduration', 'attcontrol'));
			$controls = html_writer::select($options, 'action');
			$attributes = array(
				'type'  => 'submit',
				'name'  => 'ok',
				'class' => 'form-submit',
				'value' => get_string('ok'));
			$controls .= html_writer::empty_tag('input', $attributes);
		} else {
			$controls = get_string('youcantdo', 'attcontrol'); // You can't do anything.
		}
		$table->data[0][] = $controls;



		return html_writer::table($table);
	}

	protected function render_attcontrol_take_data(attcontrol_take_data $takedata) {

		
		$controls = $this->render_attcontrol_take_controls($takedata);

		if ($takedata->pageparams->viewmode == att_take_page_params::SORTED_LIST) {
			$table = $this->render_attcontrol_take_list($takedata);
		} else {
			$table = $this->render_attcontrol_take_grid($takedata);
		}
		$table .= html_writer::input_hidden_params($takedata->url(array('sesskey' => sesskey())));
		$params = array(
			'type'  => 'submit',
			'class' => 'form-submit',
			'value' => get_string('save', 'attcontrol'));
		$table .= html_writer::tag('center', html_writer::empty_tag('input', $params));
		$table = html_writer::tag('form', $table, array('method' => 'post', 'action' => $takedata->url_path()));
		
		return $controls.$table;
	}

	protected function render_attcontrol_take_controls(attcontrol_take_data $takedata) {
		$table = new html_table();
		$table->attributes['class'] = ' ';

		$table->data[0][] = $this->construct_take_session_info($takedata);
		$table->data[0][] = $this->construct_take_controls($takedata);

		return $this->output->container(html_writer::table($table), 'generalbox takecontrols');
	}

	private function construct_take_session_info(attcontrol_take_data $takedata) {
		$sess = $takedata->sessioninfo;
		$date = userdate($sess->sessdate, get_string('strftimedate'));
		$starttime = userdate($sess->sessdate, get_string('strftimehm', 'attcontrol'));
		$endtime = userdate($sess->sessdate + $sess->duration, get_string('strftimehm', 'attcontrol'));
		$time = html_writer::tag('nobr', $starttime . ($sess->duration > 0 ? ' - ' . $endtime : ''));
		$sessinfo = $date.' '.$time;
		$sessinfo .= html_writer::empty_tag('br');
		$sessinfo .= html_writer::empty_tag('br');
		$sessinfo .= $sess->description;

		return $sessinfo;
	}

	private function construct_take_controls(attcontrol_take_data $takedata) {
		$controls = '';
		if ($takedata->pageparams->grouptype == attcontrol::SESSION_COMMON and
			($takedata->groupmode == VISIBLEGROUPS or
				($takedata->groupmode and $takedata->perm->can_access_all_groups()))) {
			$controls .= groups_print_activity_menu($takedata->cm, $takedata->url(), true);
		}

		$controls .= html_writer::empty_tag('br');

		$options = array(
			att_take_page_params::SORTED_LIST   => get_string('sortedlist', 'attcontrol'),
			att_take_page_params::SORTED_GRID   => get_string('sortedgrid', 'attcontrol'));
		$select = new single_select($takedata->url(), 'viewmode', $options, $takedata->pageparams->viewmode, null);
		$select->set_label(get_string('viewmode', 'attcontrol'));
		$select->class = 'singleselect inline';
		$controls .= $this->output->render($select);

		if ($takedata->pageparams->viewmode == att_take_page_params::SORTED_GRID) {
			$options = array (1 => '1 '.get_string('column', 'attcontrol'), '2 '.get_string('columns', 'attcontrol'),
				'3 '.get_string('columns', 'attcontrol'), '4 '.get_string('columns', 'attcontrol'),
				'5 '.get_string('columns', 'attcontrol'), '6 '.get_string('columns', 'attcontrol'),
				'7 '.get_string('columns', 'attcontrol'), '8 '.get_string('columns', 'attcontrol'),
				'9 '.get_string('columns', 'attcontrol'), '10 '.get_string('columns', 'attcontrol'));
			$select = new single_select($takedata->url(), 'gridcols', $options, $takedata->pageparams->gridcols, null);
			$select->class = 'singleselect inline';
			$controls .= $this->output->render($select);
		}

		if (count($takedata->sessions4copy) > 1) {
			$controls .= html_writer::empty_tag('br');
			$controls .= html_writer::empty_tag('br');

			$options = array();
			foreach ($takedata->sessions4copy as $sess) {
				$start = userdate($sess->sessdate, get_string('strftimehm', 'attcontrol'));
				$end = $sess->duration ? ' - '.userdate($sess->sessdate + $sess->duration,
					get_string('strftimehm', 'attcontrol')) : '';
				$options[$sess->id] = $start . $end;
			}
			$select = new single_select($takedata->url(array(), array('copyfrom')), 'copyfrom', $options);
			$select->set_label(get_string('copyfrom', 'attcontrol'));
			$select->class = 'singleselect inline';
			$controls .= $this->output->render($select);
		}

		return $controls;
	}

	protected function render_attcontrol_take_list(attcontrol_take_data $takedata) {
		$table = new html_table();
		$table->width = '0%';
		$table->head = array(
			'#',
			$this->construct_fullname_head($takedata)
		);
		$table->align = array('left', 'left');
		$table->size = array('20px', '');
		$table->wrap[1] = 'nowrap';
		foreach ($takedata->statuses as $st) {
			$table->head[] = html_writer::link("javascript:select_all_in(null, 'st" . $st->id . "', null);", $st->acronym,
				array('title' => get_string('setallstatusesto', 'attcontrol', $st->description)));
			$table->align[] = 'center';
			$table->size[] = '20px';
		}
		$table->head[] = get_string('remarks', 'attcontrol');
		$table->align[] = 'center';
		$table->size[] = '20px';
		$table->attributes['class'] = 'generaltable takelist';

		$i = 0;
		foreach ($takedata->users as $user) {
			$i++;
			$row = new html_table_row();
			$row->cells[] = $i;
			$fullname = html_writer::link($takedata->url_individualreport(array('studentid' => $user->id)), fullname($user));
			$fullname = $this->output->user_picture($user).$fullname;

			$ucdata = $this->construct_take_user_controls($takedata, $user);
			if (array_key_exists('warning', $ucdata)) {
				$fullname .= html_writer::empty_tag('br');
				$fullname .= $ucdata['warning'];
			}
			$row->cells[] = $fullname;

			if (array_key_exists('colspan', $ucdata)) {
				$cell = new html_table_cell($ucdata['text']);
				$cell->colspan = $ucdata['colspan'];
				$row->cells[] = $cell;
			} else {
				$row->cells = array_merge($row->cells, $ucdata['text']);
			}

			if (array_key_exists('class', $ucdata)) {
				$row->attributes['class'] = $ucdata['class'];
			}

			$table->data[] = $row;
		}

		return html_writer::table($table);
	}

	protected function render_attcontrol_take_grid(attcontrol_take_data $takedata) {
		$table = new html_table();
		for ($i=0; $i < $takedata->pageparams->gridcols; $i++) {
			$table->align[] = 'center';
			$table->size[] = '110px';
		}
		$table->attributes['class'] = 'generaltable takegrid';
		$table->headspan = $takedata->pageparams->gridcols;
		$head = array();
		foreach ($takedata->statuses as $st) {
			$head[] = html_writer::link("javascript:select_all_in(null, 'st" . $st->id . "', null);", $st->acronym,
				array('title' => get_string('setallstatusesto', 'attcontrol', $st->description)));
		}
		$table->head[] = implode('&nbsp;&nbsp;', $head);

		$i = 0;
		$row = new html_table_row();
		foreach ($takedata->users as $user) {
			$celltext = $this->output->user_picture($user, array('size' => 100));
			$celltext .= html_writer::empty_tag('br');
			$fullname = html_writer::link($takedata->url_individualreport(array('studentid' => $user->id)), fullname($user));
			$celltext .= html_writer::tag('span', $fullname, array('class' => 'fullname'));
			$celltext .= html_writer::empty_tag('br');
			$ucdata = $this->construct_take_user_controls($takedata, $user);
			$celltext .= is_array($ucdata['text']) ? implode('', $ucdata['text']) : $ucdata['text'];
			if (array_key_exists('warning', $ucdata)) {
				$celltext .= html_writer::empty_tag('br');
				$celltext .= $ucdata['warning'];
			}

			$cell = new html_table_cell($celltext);
			if (array_key_exists('class', $ucdata)) {
				$cell->attributes['class'] = $ucdata['class'];
			}
			$row->cells[] = $cell;

			$i++;
			if ($i % $takedata->pageparams->gridcols == 0) {
				$table->data[] = $row;
				$row = new html_table_row();
			}
		}
		if ($i % $takedata->pageparams->gridcols > 0) {
			$table->data[] = $row;
		}

		return html_writer::table($table);
	}

	private function construct_fullname_head($data) {
		global $CFG;

		if ($data->pageparams->sort == ATT_SORT_LASTNAME) {
			$firstname = html_writer::link($data->url(array('sort' => ATT_SORT_FIRSTNAME)), get_string('firstname'));
		} else {
			$firstname = get_string('firstname');
		}

		if ($data->pageparams->sort == ATT_SORT_FIRSTNAME) {
			$lastname = html_writer::link($data->url(array('sort' => ATT_SORT_LASTNAME)), get_string('lastname'));
		} else {
			$lastname = get_string('lastname');
		}

		if ($CFG->fullnamedisplay == 'lastname firstname') {
			$fullnamehead = "$lastname / $firstname";
		} else {
			$fullnamehead = "$firstname / $lastname";
		}

		return $fullnamehead;
	}

	//Modified method in order to remove not enroled users data (only completely suspended accounts)
	private function construct_take_user_controls(attcontrol_take_data $takedata, $user) {
		$celldata = array();
		if ($user->enrolmentstatus == ENROL_USER_SUSPENDED) {
				// No enrolmentend and ENROL_USER_SUSPENDED.
				$celldata['text'] = get_string('enrolmentsuspended', 'attcontrol');
				$celldata['colspan'] = count($takedata->statuses) + 1;
				$celldata['class'] = 'userwithoutenrol';
			} else {
			if ($takedata->updatemode and !array_key_exists($user->id, $takedata->sessionlog)) {
				$celldata['class'] = 'userwithoutdata';
			}

			$celldata['text'] = array();
			foreach ($takedata->statuses as $st) {
				$params = array(
					'type'  => 'radio',
					'name'  => 'user'.$user->id,
					'class' => 'st'.$st->id,
					'value' => $st->id);
				if (array_key_exists($user->id, $takedata->sessionlog) and $st->id == $takedata->sessionlog[$user->id]->statusid) {
					$params['checked'] = '';
				}

				$input = html_writer::empty_tag('input', $params);

				if ($takedata->pageparams->viewmode == att_take_page_params::SORTED_GRID) {
					$input = html_writer::tag('nobr', $input . $st->acronym);
				}

				$celldata['text'][] = $input;
			}
			$params = array(
				'type'  => 'text',
				'name'  => 'remarks'.$user->id);
			if (array_key_exists($user->id, $takedata->sessionlog)) {
				$params['value'] = $takedata->sessionlog[$user->id]->remarks;
			}
			$celldata['text'][] = html_writer::empty_tag('input', $params);
		}

		return $celldata;
	}
	
	private function construct_user_box($userdata) {
		$table = new html_table();
		
		$table->attributes['class'] = 'userinfobox';
		$table->colclasses = array('left side', '');
		$table->data[0][] = $this->output->user_picture($userdata, array('size' => 50));
		$table->data[0][] = html_writer::tag('h2', $userdata->lastname.", ".$userdata->firstname);
		
		$o = html_writer::table($table);
		
		return $o;
		
	}
	

	private function construct_time($datetime, $duration) {
		$time = html_writer::tag('nobr', construct_session_time($datetime, $duration));

		return $time;
	}
	
	protected function render_attcontrol_report_data(attcontrol_report_data $reportdata) {
		$o = $this->generate_global_report_table ( $reportdata );
		$o .= $this->render_attcontrol_pagination ( $reportdata->url () );
	
		return $o;
	}

	protected function generate_global_report_table(attcontrol_report_data &$reportdata) {
		$table = new html_table();

		$table->attributes['class'] = 'generaltable';

		$table->head[] = '#';
		$table->align[] = 'left';
		$table->size[] = '1px';
		
		// User picture.
		$table->head[] = '';
		$table->align[] = 'left';
		$table->size[] = '1px';

		$table->head[] = $this->construct_fullname_head($reportdata);
		$table->align[] = 'left';
		$table->size[] = '';
		


		foreach ($reportdata->sessions as $sess) {
			$sesstext = userdate($sess->sessdate, get_string('strftimedm', 'attcontrol'));
			$sesstext .= html_writer::empty_tag('br');
			$sesstext .= userdate($sess->sessdate, '('.get_string('strftimehm', 'attcontrol').')');
			if (is_null($sess->lasttaken) and $reportdata->perm->can_take() or $reportdata->perm->can_change()) {
				$sesstext = html_writer::link($reportdata->url_take($sess->id, $sess->groupid), $sesstext);
			}
			$sesstext .= html_writer::empty_tag('br');
			$sesstext .= $sess->groupid ? $reportdata->groups[$sess->groupid]->name : get_string('commonsession', 'attcontrol');

			$table->head[] = $sesstext;
			$table->align[] = 'center';
			$table->size[] = '1px';
		}

		$emptystatuses = array();
		
		foreach ($reportdata->statuses as $status) {
			$table->head[] = $status->acronym;
			$table->align[] = 'center';
			$table->size[] = '1px';
			
			$emptystatuses[$status->id] = 0;
		}

		$i = $reportdata->offset +1;
		
		foreach ($reportdata->users as $user) {
			$row = new html_table_row();
			
			
			$row->cells[] = $i++;
			$row->cells[] = $this->output->user_picture($user);
			$row->cells[] = html_writer::link($reportdata->url_individualreport(array('studentid' => $user->id)), fullname($user));
			
			$countstatuses = $emptystatuses;
			
			foreach ($reportdata->sessions as $sess) {
				
				if (isset($user->sessions[$sess->id])) {
					$thistatus = $user->sessions[$sess->id]->statusid;
					$row->cells[] = $reportdata->statuses[$thistatus]->acronym;
					$countstatuses[$thistatus]++;
				}
				else {
					$row->cells[] = "&nbsp;";
				}
			}
			
			foreach ($countstatuses as $countstatus) {
				$row->cells[] = $countstatus;
			}
			
			$table->data[] = $row;
		}
		
		$o = html_writer::table ( $table );
		
		$table = new html_table ();
		$table->attributes ['class'] = ' ';
		$table->width = '100%';
		$table->align = array (
				'left',
				'right'
		);
		
		$table->data [0] [] = $this->render_attcontrol_pagination_selector ( $reportdata->total, $reportdata->page, $reportdata->perpage, $reportdata->url () );
		
		$o .= html_writer::table ( $table );

		return $o;
	}

	private function construct_text_input($name, $size, $maxlength, $value='') {
		$attributes = array(
			'type'      => 'text',
			'name'      => $name,
			'size'      => $size,
			'maxlength' => $maxlength,
			'value'     => $value);
		return html_writer::empty_tag('input', $attributes);
	}

	private function construct_course_select($name, $size, $elements) {
		$attributes = array(
			'name'      => $name,
			'size'      => $size,
			'class'      => 'courseselect');

		$opts = "";
		foreach ($elements as $element) {
			$opts .= html_writer::tag('option', $element->fullname, array('value' => $element->id));
		}

		return html_writer::tag('select', $opts, $attributes);

	}

}
