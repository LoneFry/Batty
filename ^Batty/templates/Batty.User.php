<?php get_header(); ?>
<div class="Batty_Body">

    <h2>Batty : User : <?php html($loginname);?></h2>

    <?php include_once 'Batty.nav.php'; ?>
    <?php include 'Batty._getUserStats.php'; ?>
    <div class="Batty_section Batty_issueList" id="Batty_byReporter">
        <h3>Issues Reported By <?php html($loginname);?></h3>
        <?php
        if (!isset($byReporter) || !is_array($byReporter)) {
            $aIssues = array();
        } else {
            $aIssues = $byReporter;
        }
        include 'Batty.issueTable.php';
        ?>
    </div>
    <div class="Batty_section Batty_issueList" id="Batty_byHandler">
        <h3>Issues Assigned To <?php html($loginname);?></h3>
        <?php
        if (!isset($byHandler) || !is_array($byHandler)) {
            $aIssues = array();
        } else {
            $aIssues = $byHandler;
        }
        include 'Batty.issueTable.php';
        ?>
    </div>
</div>

<?php get_footer(); ?>
