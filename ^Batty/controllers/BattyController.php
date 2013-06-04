<?php
/**
 * Main Controller for 'Batty' issue tracking system
 * File: /^Batty/controllers/BattyController.php
 *
 * PHP version 5.3
 *
 * @category LoneFry
 * @package  Batty
 * @author   LoneFry <dev@lonefry.com>
 * @license  Creative Commons CC-NC-BY-SA
 * @link     http://github.com/LoneFry/Batty
 */

require_once SITE.CORE.'/models/Role.php';
require_once dirname(__DIR__).'/models/Project.php';
require_once dirname(__DIR__).'/models/Issue.php';
require_once dirname(__DIR__).'/models/Update.php';
require_once dirname(__DIR__).'/models/Subscription.php';

/**
 * Class BattyController
 *
 * @category LoneFry
 * @package  Batty
 * @author   LoneFry <dev@lonefry.com>
 * @license  Creative Commons CC-NC-BY-SA
 */
class BattyController extends Controller {
	protected $action = 'home';
	protected static $role = 'Batty';

	/**
	 * Sets all Batty actions to include the Batty CSS
	 *
	 * @param array $argv command arguments vector
	 *
	 * @return void
	 */
	public function __construct($argv) {
		parent::__construct($argv);

		G::$V->_style('/^Batty/css/Batty.css');
		G::$V->priorities = G::$G['Batty']['priorities'];
		G::$V->projects = Project::all();
	}

	/**
	 * Batty start screen
	 *
	 * @param array $argv command arguments vector
	 *
	 * @return void
	 */
	public function do_home($argv) {
		if (!G::$S->roleTest(self::$role)) {
			return $this->do_403($argv);
		}
		G::$V->_title    = 'Batty : Home';
		G::$V->_template = 'Batty.Home.php';

		require_once dirname(__DIR__).'/reports/IssueReport.php';
		G::$V->byHandler    = IssueReport::byHandler(G::$S->Login->login_id, true);
		G::$V->byReporter   = IssueReport::byReporter(G::$S->Login->login_id, true);
		G::$V->openIssues   = IssueReport::byOpen();
		G::$V->recentIssues = IssueReport::byRecent();
	}

	/**
	 * List Batty users
	 *
	 * @param array $argv command arguments vector
	 *
	 * @return void
	 */
	public function do_users($argv) {
		if (!G::$S->roleTest(self::$role)) {
			return $this->do_403($argv);
		}
		$Role = new Role(array('label' => 'Batty'));
		$Role->fill();
		G::$V->users = $Role->getMembers('loginname');

		G::$V->_title    = 'Batty : Users';
		G::$V->_template = 'Batty.Users.php';
	}

	/**
	 * View User and their issues
	 *
	 * @param array $argv command arguments vector
	 *
	 * @return void
	 */
	public function do_user($argv) {
		if (!G::$S->roleTest(self::$role)) {
			return $this->do_403($argv);
		}
		if (!isset($argv[1]) || !is_numeric($argv[1])) {
			return $this->do_users($argv);
		}
		$Role = new Role(array('label' => 'Batty'));
		$Role->fill();
		$users = $Role->getMembers('loginname');
		if (!isset($users[$argv[1]])) {
			G::msg('Requested user not a current Batty user.', 'error');

			return $this->do_users($argv);
		}

		G::$V->login_id  = $user_id = $argv[1];
		G::$V->loginname = $users[$argv[1]];

		G::$V->_title    = 'Batty : User';
		G::$V->_template = 'Batty.User.php';

		require_once dirname(__DIR__).'/reports/IssueReport.php';
		G::$V->byHandler  = IssueReport::byHandler($user_id);
		G::$V->byReporter = IssueReport::byReporter($user_id);
	}

	/**
	 * View/Update an Issue
	 *
	 * @param array $argv command arguments vector
	 *
	 * @return void
	 */
	public function do_issue($argv) {
		if (!G::$S->roleTest(self::$role)) {
			return $this->do_403($argv);
		}

		if (!isset($argv[1])) {
			return $this->do_report($argv);
		}

		//Include batty.js in template
		G::$V->_script('/^Batty/js/batty.js');

		$issue = Issue::byPK($argv[1]);
		$Role  = new Role(array('label' => 'Batty'));
		$Role->fill();
		$users = $Role->getMembers('loginname');

		//Check to see if an existing subscription exists
		$subscr = new IssueSubscription(array('login_id' => G::$S->Login->login_id, 'issue_id' => $issue->issue_id));
		$subscr->fill();

		if (!is_null($subscr->subscription_id)) {
			//Updates the lastSeen time
			$subscr->lastSeen = NOW;
			$subscr->save();
		}

		if (isset($_POST['issue_id']) && is_numeric($_POST['issue_id']) && $_POST['issue_id'] == $issue->issue_id
			&& isset($_POST['project_id']) && is_numeric($_POST['project_id'])
			&& isset($_POST['type'])
			&& isset($_POST['priority']) && is_numeric($_POST['priority'])
			&& isset($_POST['status'])
			&& isset($_POST['handler_id']) && is_numeric($_POST['handler_id'])
			&& isset($_POST['comment'])
		) {
			$issue->project_id = $_POST['project_id'];
			$issue->type       = $_POST['type'];
			$issue->priority   = $_POST['priority'];
			$issue->status     = $_POST['status'];
			$issue->handler_id = $_POST['handler_id'];

			$update           = new Update(true);
			$update->login_id = G::$S->Login->login_id;
			$update->issue_id = $_POST['issue_id'];
			$update->comment  = $_POST['comment'];
			$update->changes  = $diff = $issue->getDiff();

			if (isset($update->changes['handler_id'])) {
				$issue->iAssignDate = NOW;
			}
			if (isset($update->changes['status'])
				&& in_array($update->changes['status'], array('Completed', 'Abandoned'))
			) {
				$issue->iClosedDate = NOW;
			}
			if (0 == $issue->project_id) {
				G::msg('You must select a project', 'error');
			} elseif (0 == $issue->priority) {
				G::msg('You must set a priority', 'error');
			} elseif (!in_array($_POST['type'], Issue::getTypes())) {
				G::msg('You must select a type', 'error');
			} elseif (0 < $_POST['handler_id'] && !in_array($_POST['handler_id'], array_keys($users))) {
				G::msg('Unknown handler selected', 'error');
			} elseif (!$update->comment && !$diff) {
				G::msg('No comment or changes detected.');
			} else {
				G::msg('Saving Update');

				if ($id = $update->insert()) {
					G::msg('Update saved with ID '.$id);
					if ($result = $issue->update()) {
						G::msg('Issue updated');

						//IF handler changed, change subscription
						if (isset($diff['handler_id'])
							&& false === IssueSubscription::subscribe($issue->handler_id, $issue->issue_id)
						) {
							G::msg('Could not subscribe new assignee.', 'error');
						}
					} elseif (null === $result) {
						G::msg('No changes to issue detected.');
					} else {
						G::msg('Failed to update issue.', 'error');
					}

					//IF current user not already subscribed, subscribe
					if (is_null($subscr->subscription_id)) {
						$subscr->level = 'allUpdates';
						$subscr->lastSeen = NOW;
						$subscr->save();
						G::msg('You have been subscribed to all updates for this issue.');
					}

					//IF the issue was closed
					if ($issue->iClosedDate > NOW - 2) {
						$change = 'closed';
					//IF the status was changed
					} elseif (isset($diff['status'])) {
						$change = 'status';
					//ELSE any other change
					} else {
						$change = 'other';
					}

					//Grabs the subscriber's emails
					$emails = Subscription::getSubscriberEmails(
						$change,
						$issue->issue_id,
						$issue->project_id,
						G::$S->Login->login_id
					);

					if (is_array($emails) && count($emails)) {
						$to      = implode(',', $emails);
						$subject = 'Updated Batty #'.$issue->num.': '.$issue->label;
						$body    = 'A new update has been submitted to an issue you subscribe to<br>'
							.'<a href="http://'.$_SERVER['SERVER_NAME'].'/Batty/issue/'.$issue->issue_id.'">'
							.'#'.$issue->issue_id.': '.$issue->label.'</a><br><br>'
							.'<b>Comment:</b>'
							.'<pre style="white-space:pre-wrap;">'.htmlspecialchars($update->comment).'</pre>'
							.'<b>Changes:</b>'
							.'<pre style="white-space:pre-wrap;">'
							.print_r(array_map('htmlspecialchars', $update->changes), 1).'</pre>'
							;
						$headers = 'From: "Batty" <'.G::$G['siteEmail'].">"
							."\r\nReply-To: ".G::$G['siteEmail']
							."\r\nReturn-Path: ".G::$G['siteEmail']
							."\r\nContent-Type: text/html"
							;
						mail($to, $subject, $body, $headers, '-f'.G::$G['siteEmail']);
					}
				} else {
					G::msg('Failed to save update.', 'error');
				}
			}
		}

		G::$V->subscr     = $subscr;
		G::$V->users      = $users;
		G::$V->issue      = $issue;
		G::$V->reporter   = !G::$V->issue->reporter_id ? new Login() : Login::byPK(G::$V->issue->reporter_id);
		G::$V->handler    = !G::$V->issue->handler_id ? new Login() : Login::byPK(G::$V->issue->handler_id);
		G::$V->types      = Issue::getTypes();
		G::$V->statuses   = Issue::getStatuses();
		G::$V->updates    = Update::byIssue(G::$V->issue->issue_id);
		G::$V->_title     = 'Batty : Issue';
		G::$V->_template  = 'Batty.Issue.php';
	}

	/**
	 * Report and Issue
	 *
	 * @param array $argv command arguments vector
	 *
	 * @return void
	 */
	public function do_report($argv) {
		if (!G::$S->roleTest(self::$role)) {
			return $this->do_403($argv);
		}

		$issue = new Issue(true);

		$Role = new Role(array('label' => 'Batty'));
		$Role->fill();
		$users = $Role->getMembers('loginname');

		//Initialize issue subscription
		$subscr = new IssueSubscription(array('login_id' => G::$S->Login->login_id));

		if (isset($_POST['project_id']) && is_numeric($_POST['project_id'])
			&& isset($_POST['label'])
			&& isset($_POST['description'])
			&& isset($_POST['priority']) && is_numeric($_POST['priority'])
			&& isset($_POST['type'])
			&& isset($_POST['handler_id']) && is_numeric($_POST['handler_id'])
			&& isset($_POST['level'])
		) {
			unset($_POST['issue_id']);
			$issue->setAll($_POST);

			//Sets the user's issue subscription
			$subscr->level = $_POST['level'];

			if (0 == $issue->project_id) {
				G::msg('You must select a project', 'error');
			} elseif ('' == $issue->label) {
				G::msg('You must label this issue', 'error');
			} elseif (0 == $issue->priority) {
				G::msg('You must set a priority', 'error');
			} elseif (!in_array($_POST['type'], Issue::getTypes())) {
				G::msg('You must select a type', 'error');
			} elseif (0 < $_POST['handler_id'] && !in_array($_POST['handler_id'], array_keys($users))) {
				G::msg('Unknown handler selected', 'error');
			} else {
				G::msg('Saving Issue');
				if (0 < $_POST['handler_id']) {
					$issue->iAssignDate = NOW;
				}
				$issue->reporter_id = G::$S->Login->login_id;
				if ($id = $issue->insert()) {
					G::msg('Issue saved with ID '.$id);

					//Insert the new subscription
					$subscr->issue_id = $issue->issue_id;
					$subscr->lastSeen = NOW;
					if ($subscr->insert() === false) {
						G::msg('Failed to save subscription.', 'error');
					}

					//IF the assignee is different than the user who submitted the issue, subcribe the assignee
					if ($issue->handler_id != G::$S->Login->login_id
						&& false === IssueSubscription::subscribe($issue->handler_id, $issue->issue_id)
					) {
						G::msg('Failed to subscribe assignee.', 'error');
					}

					return $this->do_issue(array('report', $id));
				} else {
					G::msg('Failed to save issue.', 'error');
				}
			}
		}

		G::$V->subscr     = $subscr;
		G::$V->users      = $users;
		G::$V->types      = Issue::getTypes();
		G::$V->issue      = $issue;
		G::$V->_title     = 'Batty : Report Issue';
		G::$V->_template  = 'Batty.Report.php';
	}

	/**
	 * List Batty Projects
	 *
	 * @param array $argv command arguments vector
	 *
	 * @return void
	 */
	public function do_projects($argv) {
		if (!G::$S->roleTest(self::$role)) {
			return $this->do_403($argv);
		}

		G::$V->_title    = 'Batty : Projects';
		G::$V->_template = 'Batty.Projects.php';
	}

	/**
	 * View Project and its issues
	 *
	 * @param array $argv command arguments vector
	 *
	 * @return void
	 */
	public function do_project($argv) {
		if (!G::$S->roleTest(self::$role)) {
			return $this->do_403($argv);
		}

		if (isset($argv[1])) {
			$project = Project::byPK($argv[1]);
			if (!$project) {
				G::msg('Requested Project not found.', 'error');

				return $this->do_projects($argv);
			}
		} else {
			return $this->do_projects($argv);
		}

		//Include batty.js in template
		G::$V->_script('/^Batty/js/batty.js');

		if (G::$S->roleTest(self::$role.'/Admin')
			&& isset($_POST['label'])
			&& isset($_POST['description'])
		) {
			$project->label       = $_POST['label'];
			$project->description = $_POST['description'];

			if ('' == $project->label) {
				G::msg('You must label this Project', 'error');
			} else {
				G::msg('Saving Project');
				$ret = $project->save();
				if ($ret) {
					G::msg('Project saved');
				} elseif (null === $ret) {
					G::msg('No changes detected.');
				} else {
					G::msg('Failed to save Project.', 'error');
				}
			}
		}
		require_once dirname(__DIR__).'/reports/IssueReport.php';

		//Fetch the project subscription
		$subscr = new ProjectSubscription(
				array(
					'login_id'   => G::$S->Login->login_id,
					'project_id' => $project->project_id
					)
				);
		$subscr->fill();

		G::$V->subscr    = $subscr;
		G::$V->byProject = IssueReport::byProject($project->project_id);
		G::$V->project   = $project;
		G::$V->_title    = 'Batty : Project';
		G::$V->_template = 'Batty.Project.php';
	}

	/**
	 * Add a new project
	 *
	 * @param array $argv Array of URL arguments
	 *
	 * @return void
	 */
	public function do_projectAdd($argv) {
		if (!G::$S->roleTest('Batty/Admin')) {
			return $this->do_403($argv);
		}

		//Initialize project
		$project = new Project(true);

		if (isset($_POST['label']) && isset($_POST['description'])) {
			$project->label       = $_POST['label'];
			$project->description = $_POST['description'];

			if ('' == $project->label) {
				G::msg('You must label this Project', 'error');
			} else {
				G::msg('Saving Project');
				$ret = $project->insert();

				if (is_numeric($ret)) {
					G::msg('Project saved with ID '.$ret);

					//Unset the post array so do_project does not try to save
					unset($_POST);

					//Send user to do_project
					$argv[1] = $project->project_id;
					return $this->do_project($argv);
				} else {
					G::msg('Failed to save Project.', 'error');
				}
			}
		}
		G::$V->project   = $project;
		G::$V->_title    = 'Batty : Add A New Project';
		G::$V->_template = 'Batty.ProjectAdd.php';
	}

	/**
	 * Subscribes users to a Project/Issue
	 *
	 * @param array $argv Array of URL arguments
	 *
	 * @return void
	 */
	public function do_subscribe($argv) {
		if (!G::$S->roleTest(self::$role)) {
			die('0');
		}
		//Indicates whether the subscription worked
		$worked = false;

		if (isset($_POST['json'])) {
			$json = json_decode($_POST['json'], true);

			//Subscribes the user to a Project
			if (isset($json['project_id'])) {
				$worked = ProjectSubscription::subscribe(
					G::$S->Login->login_id,
					$json['project_id'],
					false,
					$json['level']
				);
			//Subscribes the user to an Issue
			} elseif (isset($json['issue_id'])) {
				$worked = IssueSubscription::subscribe(
					G::$S->Login->login_id,
					$json['issue_id'],
					true,
					$json['level']
				);
			}
		}

		//IF subscription was successful
		if ($worked) {
			die('1');
		}
		die('0');
	}
}
