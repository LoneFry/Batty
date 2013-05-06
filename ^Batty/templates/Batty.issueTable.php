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
				<th>Project</th>
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
						<a class="Batty_incognito" href="/Batty/user/<?php html($issue['reporter_id']); ?>"><?php html($issue['reporter']);?></a>
					</td>
					<td title="<?php if ($issue['iAssignDate']) {
						echo 'as of '.date('Y-m-d H:i:s', $issue['iAssignDate']);
					} ?>">
						<a class="Batty_incognito" href="/Batty/user/<?php html($issue['handler_id']); ?>"><?php html($issue['handler']);?></a>
					</td>
					<td><a class="Batty_incognito" href="/Batty/project/<?php html($issue['project_id']); ?>"><?php html($projects[$issue['project_id']]->label);?></a></td>
					<td><?php html($issue['type']);?></td>
					<td title="<?php if ($issue['iClosedDate'] && in_array($issue['status'],
						array('Completed', 'Abandoned'))
					) {
						echo 'as of '.date('Y-m-d H:i:s', $issue['iClosedDate']);
					} ?>"><?php echo str_replace(' ', '&nbsp;', htmlspecialchars($issue['status']));?></td>
					<td title="<?php html($priorities[$issue['priority']]); ?>"><?php html($issue['priority']);?></td>
					<td title="<?php echo date('Y-m-d H:i:s', strtotime($issue['recordChanged'])); ?>"><?php
						echo str_replace(' ', '&nbsp;', date('Y-m-d H:i', strtotime($issue['recordChanged'])));?></td>
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
