<?php get_header(); ?>
<div class="Batty_Body">
	<h2>Batty : Manage Projects</h2>

	<div id="Batty_nav">
		<a href="/Batty/home">Home</a>
		<a href="/Batty/projects">Projects</a>
		<a href="/Batty/users">Users</a>
		<a href="/Batty/report">Report an Issue</a>
		<a href="/Batty/projectAdd">Add a Project</a>
	</div>

	<div class="Batty_section Batty_issueList">
		<h3>Existing Projects</h3>

		<div class="Batty_tableWrapper">
			<?php
			if (!isset($projects) || !is_array($projects) || !count($projects)) {
				?>
				<p>There are no projects.</p>
			<?php
			} else {
				?>
				<table>
					<thead>
					<tr>
						<th>Id</th>
						<th>Label</th>
						<th>Create Date</th>
						<th>Description</th>
					</tr>
					</thead>
					<tbody>
					<?php
					foreach ($projects as $project) {
						?>
						<tr>
							<td>
								<a href="/Batty/project/<?php html($project->project_id); ?>"><?php html($project->project_id);?></a>
							</td>
							<td><?php html($project->label);?></td>
							<td><?php echo date('Y-m-d H:i:s', $project->iCreateDate);?></td>
							<td><?php html(substr($project->description, 0, 50));?></td>
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
