<?php
/**
 * Searches for Batty Issues
 * File: /^Batty/reports/SearchReport.php
 *
 * PHP version 5.3
 *
 * @category LoneFry
 * @package  Batty
 * @author   LoneFry <dev@lonefry.com>
 * @license  Creative Commons CC-NC-BY-SA
 * @link     http://github.com/LoneFry/Batty
 */

require_once SITE.'/^/lib/Report.php';
require_once dirname(__DIR__).'/models/Issue.php';
require_once dirname(__DIR__).'/models/Update.php';
require_once SITE.'/^/models/Login.php';

/**
 * Class SearchReport
 *
 * @category LoneFry
 * @package  Batty
 * @author   LoneFry <dev@lonefry.com>
 * @license  Creative Commons CC-NC-BY-SA
 * @link     http://github.com/LoneFry/Batty
 * @see      Report.php
 */
class SearchReport extends Report {
	protected static $query = '';

	protected static $vars = array(
		'search'      => array('type' => 's', 'sql' => "(i.`description` LIKE '%%%1\$s%%' OR i.`label` LIKE '%%%1\$s%%' OR u.`comment` LIKE '%%%1\$s%%')"),
		'priority'    => array('type' => 'a', 'values' => array(), 'sql' => "i.`priority` IN (%s)"),
		'status'      => array('type' => 'a', 'values' => array(), 'sql' => "i.`status` IN (%s)"),
		'project_id'  => array('type' => 'a', 'values' => array(), 'sql' => "i.`project_id` IN (%s)"),
		'reporter_id' => array('type' => 'a', 'values' => array(), 'sql' => "i.`reporter_id`  IN (%s)"),
		'handler_id'  => array('type' => 'a', 'values' => array(), 'sql' => "i.`handler_id`  IN (%s)"),
		'type'        => array('type' => 'a', 'values' => array(), 'sql' => "i.`type`  IN (%s)"),
	);

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
			." LEFT JOIN `".Update::getTable()."` u ON u.`issue_id` = i.`issue_id`"
			." LEFT JOIN `".Login::getTable()."` l1 ON i.`reporter_id` = l1.`login_id`"
			." LEFT JOIN `".Login::getTable()."` l2 ON i.`handler_id` = l2.`login_id`"
			." WHERE %s"
			." GROUP BY i.`issue_id`"
			;
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
SearchReport::prime();
