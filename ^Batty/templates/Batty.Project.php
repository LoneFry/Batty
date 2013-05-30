<?php get_header(); ?>
<div class="Batty_Body">
	<h2>Batty : Manage Projects</h2>

	<div id="Batty_nav">
		<a href="/Batty/home">Home</a>
		<a href="/Batty/projects">Projects</a>
	</div>

<?php if (G::$S->roleTest('Batty/Admin')) { ?>
	<div class="Batty_section" id="Batty_Project">
		<h3>Edit Project</h3>

		<div class="Batty_tableWrapper">
			<form method="POST" action="/Batty/project/<?php html($project->project_id); ?>">
				<table>
					<tr>
						<th>Id</th>
						<td>
							<a href="/Batty/project/<?php html($project->project_id); ?>"><?php html($project->project_id);?></a>
						</td>
					</tr>
					<tr>
						<th>Label</th>
						<td><input type="text" name="label" maxlength="50" value="<?php html($project->label); ?>"></td>
					</tr>
					<tr>
						<th>Description</th>
						<td><input type="text" name="description" maxlength="255"
						           value="<?php html($project->description); ?>"></td>
					</tr>
				</table>
				<input type="submit" value="Save Project">
			</form>
		</div>
	</div>
<?php } ?>
	<div class="Batty_section" id="Batty_ProjectSubscriber">
		<h3>Subscription Level</h3>
		<div class="Batty_tableWrapper">
			<label>Alert Me When:
				<select name="level" onclick="Batty_Ajax_Message.innerHTML = '';"
						onchange="Batty_Subscribe({'project_id':'<?php echo $project->project_id; ?>', 'level': this.value});">
					<option value="none"<?php echo $subscr->level == 'none'?' selected':''; ?>>Never</option>
					<option value="closed"<?php echo $subscr->level == 'closed'?' selected':''; ?>>Issue is closed</option>
					<option value="statusChange"<?php echo $subscr->level == 'statusChange'?' selected':''; ?>>Status is change</option>
					<option value="allUpdates"<?php echo $subscr->level == 'allUpdates' && is_numeric($subscr->subscription_id)?' selected':''; ?>>Issue is updated</option>
				</select>
			</label>
			<div id="Batty_Ajax_Message"></div>
		</div>
	</div>
	<div class="Batty_section Batty_issueList" id="Batty_byOpen">
		<h3>Issues</h3>
		<?php
		if (!isset($byProject) || !is_array($byProject)) {
			$aIssues = array();
		} else {
			$aIssues = $byProject;
		}
		include 'Batty.issueTable.php';
		?>
	</div>

</div>
<script type="text/javascript" src="/^Batty/js/charsRemaining.js"></script>
<?php get_footer(); ?>
