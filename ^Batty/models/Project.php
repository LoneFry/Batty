<?php
/**
 * Project model 'Batty' issue tracking system
 * File: /^Batty/models/Project.php
 *
 * PHP version 5.3
 *
 * @category LoneFry
 * @package  Batty
 * @author   LoneFry <dev@lonefry.com>
 * @license  Creative Commons CC-NC-BY-SA
 * @link     http://github.com/LoneFry/Batty
 */

require_once LIB.'/Record.php';

/**
 * Class Project
 *
 * @category LoneFry
 * @package  Batty
 * @author   LoneFry <dev@lonefry.com>
 * @license  Creative Commons CC-NC-BY-SA
 * @link     http://github.com/LoneFry/Batty
 * @see      Record.php
 */
class Project extends Record {
	protected static $table = 'Batty_Projects';
	protected static $pkey = 'project_id';
	protected static $query = '';
	protected static $vars = array(
		'project_id'    => array('type' => 'i', 'min' => 0, 'max' => 255),
		'label'         => array('type' => 's', 'min' => 1, 'max' => 50),
		'description'   => array('type' => 's', 'min' => 0, 'max' => 255),
		'iCreateDate'   => array('type' => 'ts'),
		'recordChanged' => array('type' => 'dt'),
	);

	/**
	 * prime static values
	 *
	 * @return void
	 */
	public static function prime() {
		self::$vars['iCreateDate']['def'] = NOW;

		parent::prime();
	}

	/**
	 * Create table in database
	 *
	 * @return void
	 */
	public static function create() {
		$query = "CREATE TABLE IF NOT EXISTS `".self::$table."` ("
			." `project_id` TINYINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,"
			." `label` VARCHAR(50) NOT NULL,"
			." `description` MEDIUMTEXT NOT NULL,"
			." `iCreateDate` INT UNSIGNED NOT NULL,"
			." `recordChanged` TIMESTAMP NOT NULL"
			." )";
		G::$M->query($query);
	}

	/**
	 * Drop table from database
	 *
	 * @return void
	 */
	public static function drop() {
		$query = "DROP TABLE IF EXISTS `".self::$table."`";
		G::$M->query($query);
	}
}

Project::prime();
