<?php get_header(); ?>
<div class="Batty_Body">

	<h2>Batty : Home</h2>

	<div id="Batty_nav">
		<a href="/Batty/home">Home</a>
		<a href="/Batty/report">Report an Issue</a>
		<a href="/Batty/projects">Projects</a>
	</div>

	<div class="Batty_section" id="Batty_byOpen">
		<h3>Issues</h3>

		<div class="Batty_tableWrapper">
			<?php
			if (!isset($openIssues) || !is_array($openIssues) || !count($openIssues)) {
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
					foreach ($openIssues as $k => $issue) {
						?>
						<tr>
							<td>
								<a href="/Batty/issue/<?php html($issue['issue_id']); ?>"><?php html($issue['issue_id']);?></a>
							</td>
							<td>
								<a href="/Batty/user/<?php html($issue['reporter_id']); ?>"><?php html($issue['reporter']);?></a>
							</td>
							<td>
								<a href="/Batty/user/<?php html($issue['handler_id']); ?>"><?php html($issue['handler']);?></a>
							</td>
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
	<div class="Batty_section" id="Batty_byReporter">
		<h3>Issues Reported By You</h3>

		<div class="Batty_tableWrapper">
			<?php
			if (!isset($byReporter) || !is_array($byReporter) || !count($byReporter)) {
				?>
				<p>There are no issues created by you.</p>
			<?php
			} else {
				?>
				<table>
					<thead>
					<tr>
						<th>Id</th>
						<th>Handler</th>
						<th>Updated</th>
					</tr>
					</thead>
					<tbody>
					<?php
					foreach ($byReporter as $k => $issue) {
						?>
						<tr>
							<td>
								<a href="/Batty/issue/<?php html($issue['issue_id']); ?>"><?php html($issue['issue_id']);?></a>
							</td>
							<td>
								<a href="/Batty/user/<?php html($issue['handler_id']); ?>"><?php html($issue['handler']);?></a>
							</td>
							<td><?php echo date('Y-m-d H:i:s', strtotime($issue['recordChanged']));?></td>
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
	<div class="Batty_section" id="Batty_byHandler">
		<h3>Issues Assigned To You</h3>

		<div class="Batty_tableWrapper">
			<?php
			if (!isset($byHandler) || !is_array($byHandler) || !count($byHandler)) {
				?>
				<p>There are no issues handled by you.</p>
			<?php
			} else {
				?>
				<table>
					<thead>
					<tr>
						<th>Id</th>
						<th>Reporter</th>
						<th>Updated</th>
					</tr>
					</thead>
					<tbody>
					<?php
					foreach ($byHandler as $k => $issue) {
						?>
						<tr>
							<td>
								<a href="/Batty/issue/<?php html($issue['issue_id']); ?>"><?php html($issue['issue_id']);?></a>
							</td>
							<td>
								<a href="/Batty/user/<?php html($issue['reporter_id']); ?>"><?php html($issue['reporter']);?></a>
							</td>
							<td><?php echo date('Y-m-d H:i:s', strtotime($issue['recordChanged']));?></td>
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
