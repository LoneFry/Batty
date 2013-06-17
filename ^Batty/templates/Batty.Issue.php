<?php get_header(); ?>
<div class="Batty_Body">

<h2>Batty : Issue</h2>

<div id="Batty_nav">
	<a href="/Batty/home">Home</a>
	<a href="/Batty/projects">Projects</a>
	<a href="/Batty/users">Users</a>
	<a href="/Batty/report">Report an Issue</a>
</div>

<div id="Batty_issueID">
	<a href="/Batty/issue/<?php html($issue->issue_id); ?>"><?php html($issue->num);?></a>
</div>
<div id="Batty_Subscriber_Status">
	<label>Alert Me When:
		<select name="level" class="Batty_Issue_Select" onclick="Batty_Ajax_Message.innerHTML = '';"
				onchange="Batty_Subscribe({'issue_id':'<?php echo $issue->issue_id; ?>', 'level': this.value});">
			<option value="projectLevel"<?php echo $subscr->level == 'projectLevel'?' selected':''; ?>>[Use My Project Setting]</option>
			<option value="none"<?php echo $subscr->level == 'none'?' selected':''; ?>>Never</option>
			<option value="closed"<?php echo $subscr->level == 'closed'?' selected':''; ?>>Issue is closed</option>
			<option value="statusChange"<?php echo $subscr->level == 'statusChange'?' selected':''; ?>>Status is changed</option>
			<option value="allUpdates"<?php echo $subscr->level == 'allUpdates' && is_numeric($subscr->subscription_id)?' selected':''; ?>>Issue is updated</option>
		</select>
	</label>
	<div id="Batty_Ajax_Message"></div>
</div>

<div class="Batty_section Batty_issue">
	<h3><?php html($issue->label); ?></h3>

	<div class="Batty_tableWrapper">
		<table id="Batty_issueHead">
			<thead>
			<tr>
				<th>Project</th>
				<th>Type</th>
				<th>Priority</th>
				<th>Status</th>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td><?php if (isset($projects) && is_array($projects)) {
						echo '<a class="Batty_incognito" href="/Batty/project/'.$issue->project_id.'">'
							.htmlspecialchars($projects[$issue->project_id]->label).'</a>';
					} ?></td>
				<td><?php html($issue->type); ?></td>
				<td><?php if (isset($priorities) && is_array($priorities)) {
						html($issue->priority.': '.$priorities[$issue->priority]);
					} ?></td>
				<td><?php html($issue->status); ?></td>
			</tr>
			<tr>
				<th>Reported By</th>
				<th>Reported Time</th>
				<th>Assigned To</th>
				<th>Assigned Time</th>
			</tr>
			<tr>
				<td><a class="Batty_incognito" href="/Batty/user/<?php
					html($issue->reporter_id); ?>"><?php html($reporter->loginname);?></a></td>
				<td><?php echo !$issue->iCreateDate ? '' : date('Y-m-d H:i:s', $issue->iCreateDate);?></td>
				<td><a class="Batty_incognito" href="/Batty/user/<?php
					html($issue->handler_id); ?>"><?php html($handler->loginname);?></a></td>
				<td><?php echo !$issue->iAssignDate ? '' : date('Y-m-d H:i:s', $issue->iAssignDate);?></td>
			</tr>
			<tr>
				<td colspan="4">
					<div class="Batty_issueComment"><?php batty_comment($issue->description); ?></div>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
</div>
<?php
if (isset($updates) && is_array($updates)) {
	foreach ($updates as $update) {
		?>
		<hr>

		<div class="Batty_byLine">
			<a href="/Batty/user/<?php html($update->login_id); ?>"><?php html($users[$update->login_id]); ?></a><br>
			<?php echo date('Y-m-d H:i:s', $update->iCreateDate); ?>
		</div>
		<div class="Batty_section Batty_issue">
			<div class="Batty_tableWrapper">
				<table id="Batty_issueHead">
					<tr>
						<td colspan="4">
							<div class="Batty_issueComment"><?php
								if ($update->comment) {
									batty_comment($update->comment);
								} else {
									echo '<i>[No comment with this update.]</i>';
								}?></div>
						</td>
					</tr>
					<tr>
						<td colspan="4">
							<?php
							$changes = $update->changes;
							if (is_array($changes)) {
								foreach ($changes as $k => $v) {
									echo 'Changed <b>', $k, '</b> to <b>', $v;
									if ('handler_id' == $k) {
										html(': '.$users[$v]);
									} elseif ('priority' == $k) {
										html(': '.$priorities[$v]);
									} elseif ('project_id' == $k) {
										html(': '.$projects[$v]->label);
									}
									echo '</b><br>';
								}
							}
							?>
						</td>
					</tr>
				</table>
			</div>
		</div>
	<?php
	}
}
?>
<hr>

<form method="POST" action="/Batty/issue/<?php html($issue->issue_id); ?>">

	<div class="Batty_byLine">
		<?php html(G::$S->Login->loginname); ?><br>
		<?php echo date('Y-m-d H:i:s'); ?>
	</div>
	<div class="Batty_section Batty_issue">
		<div class="Batty_tableWrapper">
			<table id="Batty_issueHead">
				<thead>
				<tr>
					<th>Project</th>
					<th>Type</th>
					<th>Priority</th>
					<th>Status</th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td>
						<select name="project_id">
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
						<select name="type">
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
					<td>
						<select name="status">
							<?php
							if (isset($statuses) && is_array($statuses)) {
								foreach ($statuses as $status) {
									if ($status == $issue->status) {
										$selected = ' selected="selected"';
									} else {
										$selected = '';
									}
									echo '
								<option value="', $status, '"', $selected, '>', $status, '</option>';
								}
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<th>Reported By</th>
					<th>Reported Time</th>
					<th>Assigned To</th>
					<th>Assigned Time</th>
				</tr>
				<tr>
					<td>
						<a class="Batty_incognito" href="/Batty/user/<?php html($issue->reporter_id); ?>"><?php html($reporter->loginname);?></a>
					</td>
					<td><?php echo !$issue->iCreateDate ? '' : date('Y-m-d H:i:s', $issue->iCreateDate);?></td>
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
					<td><?php echo !$issue->iAssignDate ? '' : date('Y-m-d H:i:s', $issue->iAssignDate);?></td>
				</tr>
				<tr>
					<td colspan="4">
						<textarea name="comment" maxlength="65535"></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="4">
						<input type="submit" value="Submit">
						<input type="hidden" name="issue_id" value="<?php echo $issue->issue_id; ?>">
					</td>
				</tr>
				</tbody>
			</table>
		</div>
	</div>

</form>

</div>
<script type="text/javascript" src="/^Batty/js/charsRemaining.js"></script>
<?php get_footer(); ?>
