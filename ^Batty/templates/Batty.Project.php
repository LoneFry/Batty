<?php get_header(); ?>
<div class="Batty_Body">
	<h2>Batty : Manage Projects</h2>

	<div id="batty_nav">
		<a href="/Batty/home">Home</a>
		<a href="/Batty/projects">Projects</a>
	</div>

	<div class="Batty_section" id="Batty_Project">
		<h3><?php echo $project->project_id ? 'Edit' : 'Add'; ?> Project</h3>

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
	<div class="Batty_section" id="Batty_byOpen">
		<h3>Issues</h3>

		<div class="Batty_tableWrapper">
			<?php
			if (!isset($byProject) || !is_array($byProject) || !count($byProject)) {
				?>
				<p>I don't have any issues!</p>
			<?php
			} else {
				?>
				<table>
					<thead>
					<tr>
						<th>Id</th>
						<th>Reporter</th>
						<th>Handler</th>
						<th>Status</th>
						<th>Updated</th>
						<th>Label</th>
					</tr>
					</thead>
					<tbody>
					<?php
					foreach ($byProject as $k => $issue) {
						?>
						<tr>
							<td>
								<a href="/Batty/issue/<?php html($issue['issue_id']); ?>"><?php html($issue['issue_id']);?></a>
							</td>
							<td><?php html($issue['reporter']);?></td>
							<td><?php html($issue['handler']);?></td>
							<td><?php html($issue['status']);?></td>
							<td><?php echo date('Y-m-d H:i:s', strtotime($issue['recordChanged']));?></td>
							<td><?php html($issue['label']);?></td>
						</tr>
					<?php
					}
					?>
					</tbody>
				</table>
			<?php
			}
			?>
		</div>
	</div>

</div>
<?php get_footer(); ?>
