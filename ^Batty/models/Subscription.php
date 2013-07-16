<?php
/**
 * Subscription - models a user's subscription to Batty issues
 * File: /^Batty/models/Subscription.php
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
require_once SITE.'/^/models/Login.php';

/**
 * Subscription - notification relationship between a user and issue/project
 *
 * @category LoneFry
 * @package  Batty
 * @author   LoneFry <dev@lonefry.com>
 * @license  Creative Commons CC-NC-BY-SA
 * @link     http://github.com/LoneFry/Batty
 * @see      Record.php
 */
abstract class Subscription extends Record {

    /**
     * Subscribes a login to a project or issue
     *
     * @param int     $login_id The login ID of the user being subscribed
     * @param int     $task_id  The issue/project ID
     * @param boolean $lastSeen If you want to add the lastSeen datetime
     * @param string  $level    The level of subscription the user requests
     *
     * @return boolean
     */
    public static function subscribe($login_id, $task_id, $lastSeen = false, $level = 'allUpdates') {
        // Validate parameters
        if (!is_numeric($task_id) || !is_numeric($login_id)
            || !in_array($level, static::$vars['level']['values'])
        ) {
            return false;
        }
        $task_id  = (int)$task_id;
        $login_id = (int)$login_id;
        $lastSeen = $lastSeen?NOW:0;

        // Create a new subscription object
        $subscr = new static(array('login_id' => $login_id, static::$fkey => $task_id));

        // IF an existing subscription DOES NOT exist, INSERT
        if (false === $subscr->fill()) {
            $subscr->level    = $level;
            if ($lastSeen) {
                $subscr->lastSeen = $lastSeen;
            }
            return $subscr->insert();
        // IF an existing subscription exists, UPDATE
        } else {
            $subscr->level    = $level;
            if ($lastSeen) {
                $subscr->lastSeen = $lastSeen;
            }
            return $subscr->save();
        }
    }

    /**
     * Get all logins' email addresses associated with the issue
     *
     * @param string $change     Type of change that was made
     * @param int    $issue_id   Issue ID of where the change occurred
     * @param int    $project_id Project ID of where the change occurred
     * @param int    $exclude    A login_id to exclude, typically current user
     *
     * @return array list of email addresses
     */
    public static function getSubscriberEmails($change, $issue_id, $project_id, $exclude = 0) {
        // Validate parameters
        if (!is_numeric($issue_id) || !is_numeric($exclude) || !is_numeric($project_id)) {
            return false;
        }
        $issue_id   = (int)$issue_id;
        $exclude    = (int)$exclude;
        $project_id = (int)$project_id;

        // Gets the issue subscriptions
        $iSubscriptions = IssueSubscription::getIssueSubscriptions($issue_id, $exclude);

        // Gets the project subscriptions
        $pSubscriptions = ProjectSubscription::getProjectSubscriptions($project_id, $exclude);

        // Emails to be returned
        $emails  = array();

        // Loop through the issue subscriptions
        foreach ($iSubscriptions as $login_id => $subsr) {
            // IF level is 'projectLevel', skip
            if ($subsr['level'] == 'projectLevel') {
                continue;
            }

            // IF user is subscribed to all updates
            if ($subsr['level'] == 'allUpdates') {
                $emails[] = $subsr['email'];
            // ELSEIF status change
            } elseif (($change == 'status' || $change == 'closed') && $subsr['level'] == 'statusChange') {
                $emails[] = $subsr['email'];
            // ELSEIF issue was closed
            } elseif ($change == 'closed' && $subsr['level'] == 'closed') {
                $emails[] = $subsr['email'];
            }

            // IF login_id has a project subscription, unset it
            unset($pSubscriptions[$login_id]);
        }

        // Loop through the project subscriptions (if any are left)
        foreach ($pSubscriptions as $subsr) {
            if ($subsr['level'] == 'allUpdates') {
                $emails[] = $subsr['email'];
            // ELSEIF status change
            } elseif ($change == 'status' && $subsr['level'] == 'statusChange') {
                $emails[] = $subsr['email'];
            // ELSEIF issue was closed
            } elseif ($change == 'closed' && $subsr['level'] == 'closed') {
                $emails[] = $subsr['email'];
            }
        }
        return $emails;
    }
}

/**
 * IssueSubscription class - represents a relationship between a user and an issue
 *
 * @category LoneFry
 * @package  Batty
 * @author   LoneFry <dev@lonefry.com>
 * @license  Creative Commons CC-NC-BY-SA
 * @link     http://github.com/LoneFry/Batty
 * @see      Subscription.php
 */
class IssueSubscription extends Subscription {
    protected static $table = 'Batty_IssueSubscriptions';
    protected static $pkey  = 'subscription_id';
    protected static $fkey  = 'issue_id';
    protected static $query = '';
    protected static $vars  = array(
        'subscription_id' => array('type' => 'i', 'min' => 1),
        'issue_id'        => array('type' => 'i', 'min' => 0),
        'login_id'        => array('type' => 'i', 'min' => 0),
        'level'           => array('type' => 'e', 'def' => 'allUpdates', 'values' =>
            array('allUpdates', 'statusChange', 'closed', 'none', 'projectLevel')),
        'lastSeen'        => array('type' => 'dt'),
        'recordChanged'   => array('type' => 'dt'),
    );

    /**
     * Gets all subscriptions for a user
     *
     * @param int $login_id The user's login ID
     *
     * @return array
     */
    public static function getSubscriptions($login_id) {
        // Validate function parameters
        if (!is_numeric($login_id)) {
            return false;
        }
        $login_id = (int)$login_id;

        // Array to be returned
        $return = array();

        // Searches for a user's subscriptions
        $sub = new IssueSubscription(array('login_id' => $login_id));
        $sub = $sub->search();

        // Resets the array to use issue_id for the array key
        foreach ($sub as $val) {
            $return[$val->{self::$fkey}] = $val;
        }
        return $return;
    }

    /**
     * Creates IssueSubscription table
     *
     * @param bool $returnQuery If true, return query instead of running it
     *
     * @return mixed
     */
    public static function create($returnQuery = false) {
        $query = "CREATE TABLE IF NOT EXISTS `".self::$table."` ("
            ." `subscription_id` int(10) unsigned NOT NULL auto_increment,"
            ." `issue_id` int(10) unsigned NOT NULL,"
            ." `login_id` int(10) unsigned NOT NULL,"
            ." `level` enum('allUpdates','statusChange','closed','none','projectLevel') NOT NULL,"
            ." `lastSeen` datetime NOT NULL,"
            ." `recordChanged` timestamp NOT NULL default CURRENT_TIMESTAMP,"
            ." PRIMARY KEY  (`subscription_id`),"
            ." KEY `issue_id` (`issue_id`,`login_id`)"
            .")"
            ;
        if ($returnQuery) {
            return $query;
        }

        return G::$M->query($query);
    }

    /**
     * Gets the subscriptions for an issue
     *
     * @param int $issue_id
     * @param int $exclude
     *
     * @return array
     */
    public static function getIssueSubscriptions($issue_id, $exclude) {
        // Validate parameters
        if (!is_numeric($issue_id) || !is_numeric($exclude)) {
            return false;
        }
        $issue_id = (int)$issue_id;
        $exclude  = (int)$exclude;

        $query = "SELECT i.`level`, l.`login_id`, l.`email`, l.`loginname`"
                ." FROM ".Login::getTable()." l"
                ." INNER JOIN ".self::$table." i ON l.login_id = i.login_id"
                ." WHERE i.".self::$fkey." = $issue_id AND l.login_id != $exclude"
                ." GROUP BY l.login_id"
                ;
        if (false === $result = G::$m->query($query)) {
            return false;
        }

        // Array to be returned
        $data = array();

        while ($row = $result->fetch_assoc()) {
            $data[$row['login_id']] = $row;
        }
        return $data;
    }
}
IssueSubscription::prime();

/**
 * ProjectSubscription class - represents a relationship between a user and a project
 *
 * @category LoneFry
 * @package  Batty
 * @author   LoneFry <dev@lonefry.com>
 * @license  Creative Commons CC-NC-BY-SA
 * @link     http://github.com/LoneFry/Batty
 * @see      Subscription.php
 */
class ProjectSubscription extends Subscription {
    protected static $table = 'Batty_ProjectSubscriptions';
    protected static $pkey  = 'subscription_id';
    protected static $fkey  = 'project_id';
    protected static $query = '';
    protected static $vars  = array(
        'subscription_id' => array('type' => 'i', 'min' => 1),
        'project_id'      => array('type' => 'i', 'min' => 0),
        'login_id'        => array('type' => 'i', 'min' => 0),
        'level'           => array('type' => 'e', 'def' => 'allUpdates', 'values' =>
            array('allUpdates', 'statusChange', 'closed', 'none')),
        'recordChanged'   => array('type' => 'dt'),
    );

    /**
     * Creates ProjectSubscription table
     *
     * @param bool $returnQuery If true, return query instead of running it
     *
     * @return mixed
     */
    public static function create($returnQuery = false) {
        $query = "CREATE TABLE IF NOT EXISTS `".self::$table."` ("
            ." `subscription_id` int(10) unsigned NOT NULL auto_increment,"
            ." `project_id` int(10) unsigned NOT NULL,"
            ." `login_id` int(10) unsigned NOT NULL,"
            ." `level` enum('allUpdates','statusChange','closed','none') NOT NULL,"
            ." `recordChanged` timestamp NOT NULL default CURRENT_TIMESTAMP,"
            ." PRIMARY KEY  (`subscription_id`),"
            ." KEY `project_id` (`project_id`,`login_id`)"
            .")"
            ;
        if ($returnQuery) {
            return $query;
        }

        return G::$M->query($query);
    }

    /**
     * Gets the subscriptions for a project
     *
     * @param int $issue_id
     * @param int $exclude
     *
     * @return array
     */
    public static function getProjectSubscriptions($project_id, $exclude) {
        // Validate parameters
        if (!is_numeric($project_id) || !is_numeric($exclude)) {
            return false;
        }
        $project_id = (int)$project_id;
        $exclude    = (int)$exclude;

        $query = "SELECT p.`level`, l.`login_id`, l.`email`, l.`loginname`"
                ." FROM ".self::$table." p"
                ." INNER JOIN ".Login::getTable()." l ON l.login_id = p.login_id"
                ." WHERE p.project_id = $project_id AND l.login_id != $exclude"
                ." GROUP BY l.login_id"
                ;
        if (false === $result = G::$m->query($query)) {
            return false;
        }

        // Array to be returned
        $data = array();

        while ($row = $result->fetch_assoc()) {
            $data[$row['login_id']] = $row;
        }
        return $data;
    }
}
ProjectSubscription::prime();
