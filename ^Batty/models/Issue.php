<?php
/**
 * Issue model 'Batty' issue tracking system
 * File: /^Batty/models/Issue.php
 *
 * PHP version 5.3
 *
 * @category LoneFry
 * @package  Batty
 * @author   LoneFry <dev@lonefry.com>
 * @license  Creative Commons CC-NC-BY-SA
 * @link     http://github.com/LoneFry/Batty
 */

require_once SITE.'/^/lib/Record.php';

/**
 * Class Issue
 *
 * @category LoneFry
 * @package  Batty
 * @author   LoneFry <dev@lonefry.com>
 * @license  Creative Commons CC-NC-BY-SA
 * @link     http://github.com/LoneFry/Batty
 * @see      Record.php
 */
class Issue extends Record {
    protected static $table = 'Batty_Issues';
    protected static $pkey = 'issue_id';
    protected static $query = '';
    protected static $vars = array(
        'issue_id'      => array('type' => 'i', 'min' => 0),
        'project_id'    => array('type' => 'i', 'min' => 0, 'max' => 255),
        'label'         => array('type' => 's', 'min' => 1, 'max' => 50),
        'description'   => array('type' => 's', 'min' => 0, 'max' => 65535),
        'type'          => array('type' => 'e', 'values' => array('Bug', 'Feature')),
        'priority'      => array('type' => 'i', 'min' => 1),
        'status'        => array(
            'type' => 'e', 'values' => array('New', 'In Progress', 'Under Review', 'Completed', 'Abandoned'),
            'def'  => 'New'
        ),
        'reporter_id'   => array('type' => 'i', 'min' => 0),
        'handler_id'    => array('type' => 'i', 'min' => 0),
        'iCreateDate'   => array('type' => 'ts'),
        'iAssignDate'   => array('type' => 'ts'),
        'iClosedDate'   => array('type' => 'ts'),
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
     * Return list of valid issue types
     *
     * @return array
     */
    public static function getTypes() {
        return static::$vars['type']['values'];
    }

    /**
     * Return list of valid issue statuses
     *
     * @return array
     */
    public static function getStatuses() {
        return static::$vars['status']['values'];
    }

    /**
     * Return all issues attached to specified project_id
     *
     * @param int $project_id The project for which to fetch issues
     *
     * @return bool
     */
    public static function byProject($project_id) {
        if (!is_numeric($project_id)) {
            return false;
        }

        $R = new static(array('project_id' => $project_id));

        return $R->search();
    }

    /**
     * Return all issues reported by specified login_id
     *
     * @param int $reporter_id The reporter for which to fetch issues
     *
     * @return bool
     */
    public static function byReporter($reporter_id) {
        if (!is_numeric($reporter_id)) {
            return false;
        }

        $R = new static(array('reporter_id' => $reporter_id));

        return $R->search();
    }

    /**
     * Return all issues assigned to specified login_id
     *
     * @param int $handler_id The Handler for which to fetch issues
     *
     * @return bool
     */
    public static function byHandler($handler_id) {
        if (!is_numeric($handler_id)) {
            return false;
        }

        $R = new static(array('handler_id' => $handler_id));

        return $R->search();
    }

    /**
     * return a padded issue_id suitable for display
     *
     * @return string
     */
    public function num() {
        return str_pad($this->issue_id, 4, 0, STR_PAD_LEFT);
    }

    /**
     * Create table in database
     *
     * @param bool $returnQuery If true, return query instead of running it
     *
     * @return mixed
     */
    public static function create($returnQuery = false) {
        $query = "CREATE TABLE IF NOT EXISTS `".self::$table."` ("
            ." `issue_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,"
            ." `project_id` TINYINT UNSIGNED NOT NULL,"
            ." `label` VARCHAR(50) NOT NULL,"
            ." `description` MEDIUMTEXT NOT NULL,"
            ." `type` ENUM('Bug', 'Feature') NOT NULL,"
            ." `priority` TINYINT UNSIGNED NOT NULL,"
            ." `status` ENUM('New', 'In Progress', 'Under Review', 'Completed', 'Abandoned') NOT NULL,"
            ." `reporter_id` INT UNSIGNED NOT NULL,"
            ." `handler_id` INT UNSIGNED NOT NULL,"
            ." `iCreateDate` INT UNSIGNED NOT NULL,"
            ." `iAssignDate` INT UNSIGNED NOT NULL,"
            ." `iClosedDate` INT UNSIGNED NOT NULL,"
            ." `recordChanged` TIMESTAMP NOT NULL,"
            ." KEY `project_id` (`project_id`)"
            ." )"
            ;
        if ($returnQuery) {
            return $query;
        }

        return G::$M->query($query);
    }

    /**
     * Retrieves user feature request statistics
     *
     * @param array $aIDs Login_ids of the given users
     *
     * @return Array
     */
    public static function getUserStats($aIDs) {
        //Validate parameters
        if (!is_array($aIDs) || !count($aIDs)) {
            return false;
        }
        $a = array();
        foreach ($aIDs as $k => $v) {
            if (is_numeric($v)) {
                $a[] = (int)$v;
            }
        }
        //IF no IDs exist, return empty array
        if (!count($a)) {
            return array();
        }
        //List of IDs
        $idList = implode(', ', $a);

        //Gathers the statistics
        $query = "SELECT l.`login_id`, l.`realname`,"
                ." SUM(IF(`type` = 'Feature' AND `status` != 'Abandoned', 1, 0)) as `featureCount`,"
                ." SUM(IF(`type` = 'Bug' AND `status` != 'Abandoned', 1, 0)) as `bugCount`,"
                ." SUM(IF(`status` = 'Abandoned', 1, 0)) as `abandonedCount`"
                ." FROM ".self::$table." i"
                ." RIGHT JOIN ".Login::getTable()." l ON l.`login_id` = i.`reporter_id`"
                ." WHERE l.`login_id` IN ($idList)"
                ." GROUP BY l.`login_id` ORDER BY l.`realname`"
                ;
        if ((false === $result=G::$m->query($query))) {
            return false;
        }

        //Constructs data array
        $data = array();
        while ($row = $result->fetch_assoc()) {
            //Sets the login_id
            $id = $row['login_id'];
            //Sets null fields to 0
            $data[$id]['featureCount']   = (int)$row['featureCount'];
            $data[$id]['bugCount']       = (int)$row['bugCount'];
            $data[$id]['abandonedCount'] = (int)$row['abandonedCount'];
            //Calculates the total score
            $data[$id]['totalScore'] =
                $row['featureCount'] + (2 * $row['bugCount']) - $row['abandonedCount'];
            $data[$id]['realname'] = $row['realname'];
        }
        return $data;
    }
}

Issue::prime();
