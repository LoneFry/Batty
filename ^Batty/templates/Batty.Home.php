<?php get_header(); ?>
<div class="Batty_Body">

	<h2>Batty : Home</h2>

	<div id="Batty_nav">
		<a href="/Batty/home">Home</a>
		<a href="/Batty/report">Report an Issue</a>
		<a href="/Batty/projects">Projects</a>
	</div>

	<div class="Batty_section Batty_issueList" id="Batty_byHandler">
		<h3>Issues Assigned To You</h3>
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
		<h3>Issues Reported By You</h3>
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
		<h3>Issues</h3>
		<?php
		if (!isset($openIssues) || !is_array($openIssues)) {
			$aIssues = array();
		} else {
			$aIssues = $openIssues;
		}
		include 'Batty.issueTable.php';
		?>
	</div>

</div>

<?php get_footer(); ?>
