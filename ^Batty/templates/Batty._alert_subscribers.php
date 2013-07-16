<div style="font-size: 12pt;">
    <b><?php echo $logins[G::$S->Login->login_id]->realname; ?></b> has submitted a new update to:<br>
    <a href="http://<?php echo $_SERVER['SERVER_NAME']; ?>/Batty/issue/<?php echo $issue->issue_id; ?>"><?php echo $issue->issue_id.': '.$issue->label; ?></a>
</div>
<br />

<?php if (is_array($update->changes) && count($update->changes)) { ?>
    <h2>Changes:</h2>
    <ul style="font-size: 10pt;">
    <?php if (isset($update->changes['handler_id'])) { ?>
        <li>Changed assignee to <b><?php echo ($update->changes['handler_id'] == 0?'nobody.':$logins[$update->changes['handler_id']]->realname); ?></b></li>
    <?php } ?>
    <?php if (isset($update->changes['project_id'])) { ?>
        <li>Changed project to <b><?php echo G::$V->projects[$issue->project_id]->label; ?></b></li>
    <?php } ?>
    <?php if (isset($update->changes['status'])) { ?>
        <li>Changed status to <b><?php echo $issue->status; ?></b></li>
    <?php } ?>
    <?php if (isset($update->changes['type'])) { ?>
        <li>Changed type to <b><?php echo $issue->type; ?></b></li>
    <?php } ?>
    <?php if (isset($update->changes['priority'])) { ?>
        <li>Changed priority to <b><?php echo $issue->priority; ?></b></li>
    <?php } ?>
    </ul>
    <br />
<?php } ?>

<h2>Comment:</h2>
<pre style="white-space: pre-wrap; font-size: 10pt;"><?php
if ($update->comment != '') {
    echo batty_comment($update->comment);
} else {
    echo '<i>[No comment with this update.]</i>';
}
?></pre>

<h2>Details:</h2>
<table style="font-size: 10pt;">
<?php if ($issue->handler_id != 0) { ?>
    <tr>
        <th style="text-align: right;">Assigned To:</th>
        <td><?php echo $logins[$issue->handler_id]->realname; ?></td>
    </tr>
<?php } ?>
    <tr>
        <th style="text-align: right;">Reported By:</th>
        <td><?php echo $logins[$issue->reporter_id]->realname; ?></td>
    </tr>
    <tr>
        <th style="text-align: right;">Project:</th>
        <td><?php echo G::$V->projects[$issue->project_id]->label; ?></td>
    </tr>
    <tr>
        <th style="text-align: right;">Type:</th>
        <td><?php echo $issue->type; ?></td>
    </tr>
    <tr>
        <th style="text-align: right;">Status:</th>
        <td><?php echo $issue->status; ?></td>
    </tr>
</table>
