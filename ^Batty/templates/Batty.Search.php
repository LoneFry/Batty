<?php get_header(); ?>
<div class="Batty_Body">

    <h2><?php html($_title); ?></h2>
    <?php include_once 'Batty.nav.php'; ?>
    <div class="Batty_section">
<?php
if (isset($results)) {
    $aIssues = $results;
?>
            <div class="Batty_section Batty_issueList">
                <h3>Search Results</h3>
                <?php include 'Batty.issueTable.php'; ?>
            </div>
<?php
}
?>
    </div>
</div>
<?php get_footer();
