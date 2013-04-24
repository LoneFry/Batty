<div class="Batty_tableWrapper">
	<?php
	if (!count($aIssues)) {
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
				<th>Type</th>
				<th>Status</th>
				<th>Priority</th>
				<th>Updated</th>
				<th>Label</th>
			</tr>
			</thead>
			<tbody>
			<?php
			foreach ($aIssues as $k => $issue) {
				?>
				<tr>
					<td>
						<a href="/Batty/issue/<?php html($issue['issue_id']); ?>"><?php html($issue['num']);?></a>
					</td>
					<td>
						<a href="/Batty/user/<?php html($issue['reporter_id']); ?>"><?php html($issue['reporter']);?></a>
					</td>
					<td>
						<a href="/Batty/user/<?php html($issue['handler_id']); ?>"><?php html($issue['handler']);?></a>
					</td>
					<td><?php html($issue['type']);?></td>
					<td><?php html($issue['status']);?></td>
					<td title="<?php html($priorities[$issue['priority']]); ?>"><?php html($issue['priority']);?></td>
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
