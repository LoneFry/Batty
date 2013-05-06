<?php
/**
 * Main Report for 'Batty' issue tracking system
 * File: /^Batty/reports/Issues.php
 *
 * PHP version 5.3
 *
 * @category LoneFry
 * @package  Batty
 * @author   LoneFry <dev@lonefry.com>
 * @license  Creative Commons CC-NC-BY-SA
 * @link     http://github.com/LoneFry/Batty
 */

require_once LIB.'/Report.php';
require_once dirname(__DIR__).'/models/Issue.php';
require_once SITE.CORE.'/models/Login.php';

/**
 * Class IssueReport
 *
 * @category LoneFry
 * @package  Batty
 * @author   LoneFry <dev@lonefry.com>
 * @license  Creative Commons CC-NC-BY-SA
 * @link     http://github.com/LoneFry/Batty
 * @see      Report.php
 */
class IssueReport extends Report {
	protected static $query = '';
	protected static $vars = array(
		'project_id'  => array('type' => 'i', 'sql' => '`project_id` = %d'),
		'reporter_id' => array('type' => 'i', 'sql' => '`reporter_id` = %d'),
		'handler_id'  => array('type' => 'i', 'sql' => '`handler_id` = %d'),
		'open'        => array('type' => 'i', 'sql' => '(`status` IN (\'New\', \'In Progress\', \'Under Review\') = %d)'),
	);
	protected $_orders = array('recordChanged', 'priority', 'status', 'type');

	protected $_order = "(`status` IN ('Completed', 'Abandoned')), (`type` = 'Bug') DESC, `priority`, 'project_id'";

	/**
	 * Initializes static properties
	 *
	 * @return void
	 */
	public static function prime() {
		self::$query = "SELECT i.`issue_id`, i.`project_id`, i.`label`, i.`description`, i.`type`, i.`priority`,"
			." i.`status`, i.`reporter_id`, i.`handler_id`, i.`iCreateDate`, i.`iAssignDate`, i.`iClosedDate`,"
			." i.`recordChanged`, l1.`loginname` as `reporter`, l2.`loginname` as `handler`"
			." FROM `".Issue::getTable()."` i"
			." LEFT JOIN `".Login::getTable()."` l1 ON i.reporter_id = l1.login_id"
			." LEFT JOIN `".Login::getTable()."` l2 ON i.handler_id = l2.login_id"
			." WHERE %s";
	}

	/**
	 * Report issues for specified project
	 *
	 * @param int $project_id The project_id to report issues for
	 * @param int $open Flag indicating whether to return open or closed issues
	 *
	 * @return array
	 */
	public static function byProject($project_id, $open = null) {
		if (!is_numeric($project_id)) {
			return false;
		}

		$R = new static(array('project_id' => $project_id));
		$R->open = $open;
		$R->load();

		return $R->toArray();
	}

	/**
	 * Report leads for specified reporter
	 *
	 * @param int $reporter_id the reporter_id to report issues for
	 * @param int $open Flag indicating whether to return open or closed issues
	 *
	 * @return bool
	 */
	public static function byReporter($reporter_id, $open = null) {
		if (!is_numeric($reporter_id)) {
			return false;
		}

		$R = new static(array('reporter_id' => $reporter_id));
		$R->open = $open;
		$R->load();

		return $R->toArray();
	}

	/**
	 * Report issues for specified handler
	 *
	 * @param int $handler_id the handler_id to report issues for
	 * @param int $open Flag indicating whether to return open or closed issues
	 *
	 * @return bool
	 */
	public static function byHandler($handler_id, $open = null) {
		if (!is_numeric($handler_id)) {
			trigger_error($handler_id);

			return false;
		}

		$R = new static(array('handler_id' => $handler_id));
		$R->open = $open;
		$R->load();

		return $R->toArray();
	}

	/**
	 * Report open issues
	 *
	 * @param int $open Flag indicating whether to return open or closed issues
	 *
	 * @return mixed
	 */
	public static function byOpen($open = 1) {
		$open = (int)$open;

		$R = new static(array('open' => $open));
		$R->load();

		return $R->toArray();
	}

	/**
	 * Report recent issues
	 *
	 * @param int $count How many recent issues to return
	 *
	 * @return mixed
	 */
	public static function byRecent($count = 30) {
		$R = new static();
		$R->open = 0;
		$R->_order = 'recordChanged';
		$R->_asc = false;
		$R->_count = $count;
		$R->load();

		return $R->toArray();
	}

	/**
	 * Add a padded issue number for each issue_id
	 *
	 * @return void
	 */
	public function onload() {
		if (is_array($this->_data)) {
			foreach ($this->_data as $k => $v) {
				$this->_data[$k]['num'] = str_pad($v['issue_id'], 4, 0, STR_PAD_LEFT);
			}
		}
	}
}

IssueReport::prime();
