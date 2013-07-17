<?php get_header(); ?>
<div class="Batty_Body">

    <h2><?php html($_title); ?></h2>

    <?php include_once 'Batty.nav.php'; ?>
    <form method="POST" action="/Batty/report">
        <div id="Batty_issueID">
            <a href="/Batty/report">New</a>
        </div>

        <div id="Batty_Subscriber_Status">
            <label>Alert Me When:
                <select name="level" class="Batty_Issue_Select">
                    <option value="allUpdates"<?php echo $subscr->level == 'allUpdates' && is_numeric($subscr->subscription_id)?' selected':''; ?>>Issue is updated</option>
                    <option value="statusChange"<?php echo $subscr->level == 'statusChange'?' selected':''; ?>>Status is changed</option>
                    <option value="closed"<?php echo $subscr->level == 'closed'?' selected':''; ?>>Issue is closed</option>
                    <option value="none"<?php echo $subscr->level == 'none'?' selected':''; ?>>Never</option>
                    <option value="projectLevel"<?php echo $subscr->level == 'projectLevel'?' selected':''; ?>>[Use My Project Setting]</option>
                </select>
            </label>
        </div>

        <div class="Batty_section Batty_issue">
            <label class="Batty_h3" id="Batty_Report_label"><span>Issue Title:</span>
                <input type="text" name="label" value="<?php html($issue->label); ?>" maxlength="50">
            </label>

            <div class="Batty_tableWrapper">
                <table id="Batty_issueHead">
                    <tr>
                        <th>Project</th>
                        <th>Type</th>
                        <th>Priority</th>
                        <th>Status</th>
                    </tr>
                    <tr>
                        <td>
                            <select name="project_id">
                                <option value="0">--- Select a Project ---</option>
                                <?php
                                if (isset($projects) && is_array($projects)) {
                                    foreach ($projects as $project) {
                                        if ($project->project_id == $issue->project_id) {
                                            $selected = ' selected="selected"';
                                        } else {
                                            $selected = '';
                                        }
                                        echo '
                                <option value="', $project->project_id, '"', $selected, '>', $project->label, '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        <td>
                            <select name="type" onchange="Batty_type_change(this);">
                                <option value="">--- Select a Type ---</option>
                                <?php
                                if (isset($types) && is_array($types)) {
                                    foreach ($types as $type) {
                                        if ($type == $issue->type) {
                                            $selected = ' selected="selected"';
                                        } else {
                                            $selected = '';
                                        }
                                        echo '
                                <option value="', $type, '"', $selected, '>', $type, '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        <td>
                            <select name="priority">
                                <option value="0">--- Select a Priority ---</option>
                                <?php
                                if (isset($priorities) && is_array($priorities)) {
                                    foreach ($priorities as $priority => $description) {
                                        if ($priority == $issue->priority) {
                                            $selected = ' selected="selected"';
                                        } else {
                                            $selected = '';
                                        }
                                        echo '
                                <option value="', $priority, '"', $selected, '>', $priority, ': ', $description, '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        <td><?php html($issue->status);?></td>
                    </tr>
                    <tr>
                        <th>Reported By</th>
                        <th>Reported Time</th>
                        <th>Assigned To</th>
                        <th>Assigned Time</th>
                    </tr>
                    <tr>
                        <td><?php html(G::$S->Login->loginname);?></td>
                        <td><?php echo date('Y-m-d H:i:s');?></td>
                        <td>
                            <select name="handler_id">
                                <option value="0">--- Assign a Handler ---</option>
                                <?php
                                if (isset($users) && is_array($users)) {
                                    foreach ($users as $login_id => $loginname) {
                                        if ($login_id == $issue->handler_id) {
                                            $selected = ' selected="selected"';
                                        } else {
                                            $selected = '';
                                        }
                                        echo '
                                <option value="', $login_id, '"', $selected, '>', $loginname, '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        <td><br></td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <textarea name="description" id="description" maxlength="65535"
                                ><?php html($issue->description); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <input type="submit" value="Report Issue">
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </form>

</div>
<script type="text/javascript">
    function Batty_type_change(oSelect) {
        var oTextarea = document.getElementById('description');
        var defaultBug = 'Full steps to reproduce the bug:\n'
            + '--------------------------------\n'
            + '[Enter details here]\n\n\n\n'
            + 'Expected behavior:\n'
            + '------------------\n'
            + '[Enter details here]\n\n\n\n'
            + 'Buggy behavior:\n'
            + '---------------\n'
            + '[Enter details here]\n';
        var defaultFeature = 'In order to [what?]\nAs a [who?]\nI want to [how?]';
        if ('Bug' == oSelect.value && ('' == oTextarea.value || defaultFeature == oTextarea.value)) {
            oTextarea.value = defaultBug;
        } else if ('Feature' == oSelect.value && ('' == oTextarea.value || defaultBug == oTextarea.value)) {
            oTextarea.value = defaultFeature;
        } else if ('' == oSelect.value && (defaultFeature == oTextarea.value || defaultBug == oTextarea.value)) {
            oTextarea.value = '';
        }
    }
</script>
<?php get_footer();
