<?php get_header(); ?>
<div class="Batty_Body">

	<h2>Batty : Home</h2>

	<?php include_once 'Batty.nav.php'; ?>
	<div class="Batty_section Batty_issueList" id="Batty_byHandler">
		<h3>Open Issues Assigned To You</h3>
		<?php
		if (!isset($byHandler) || !is_array($byHandler)) {
			$aIssues = array();
		} else {
			$aIssues = $byHandler;
		}
		include 'Batty.issueTable.php';
		?>
	</div>
	<div class="Batty_section Batty_issueList" id="Batty_byReporter">
		<h3>Open Issues Reported By You</h3>
		<?php
		if (!isset($byReporter) || !is_array($byReporter)) {
			$aIssues = array();
		} else {
			$aIssues = $byReporter;
		}
		include 'Batty.issueTable.php';
		?>
	</div>
	<div class="Batty_section Batty_issueList" id="Batty_byOpen">
		<h3>Open Issues</h3>
		<?php
		if (!isset($openIssues) || !is_array($openIssues)) {
			$aIssues = array();
		} else {
			$aIssues = $openIssues;
		}
		include 'Batty.issueTable.php';
		?>
	</div>
	<div class="Batty_section Batty_issueList" id="Batty_byRecent">
		<h3>Recent Issues</h3>
		<?php
		if (!isset($recentIssues) || !is_array($recentIssues)) {
			$aIssues = array();
		} else {
			$aIssues = $recentIssues;
		}
		include 'Batty.issueTable.php';
		?>
	</div>

</div>

<?php get_footer(); ?>
