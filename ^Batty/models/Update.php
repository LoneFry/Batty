<?php
/**
 * Update model 'Batty' issue tracking system
 * File: /^Batty/models/Update.php
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
 * Class Update
 *
 * @category LoneFry
 * @package  Batty
 * @author   LoneFry <dev@lonefry.com>
 * @license  Creative Commons CC-NC-BY-SA
 * @link     http://github.com/LoneFry/Batty
 * @see      Record.php
 */
class Update extends Record {
    protected static $table = 'Batty_Updates';
    protected static $pkey = 'update_id';
    protected static $query = '';
    protected static $vars = array(
        'update_id'     => array('type' => 'i', 'min' => 0),
        'issue_id'      => array('type' => 'i', 'min' => 0),
        'comment'       => array('type' => 's', 'min' => 0, 'max' => 65535),
        'changes'       => array('type' => 'o', 'min' => 0, 'max' => 65535),
        'login_id'      => array('type' => 'i'),
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
     * Return all updates for specified issue_id
     *
     * @param int $issue_id The issue for which to fetch updates
     *
     * @return bool|array
     */
    public static function byIssue($issue_id) {
        if (!is_numeric($issue_id)) {
            return false;
        }

        $R = new static(array('issue_id' => $issue_id));

        return $R->search();
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
            ." `update_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,"
            ." `issue_id` INT UNSIGNED NOT NULL,"
            ." `comment` MEDIUMTEXT NOT NULL,"
            ." `changes` MEDIUMTEXT NOT NULL,"
            ." `login_id` INT UNSIGNED NOT NULL,"
            ." `iCreateDate` INT UNSIGNED NOT NULL,"
            ." `recordChanged` TIMESTAMP NOT NULL,"
            ." KEY `issue_id` (`issue_id`)"
            ." )"
            ;
        if ($returnQuery) {
            return $query;
        }

        return G::$M->query($query);
    }
}

Update::prime();
