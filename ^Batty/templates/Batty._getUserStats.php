<?php if (is_array($userStats) && count($userStats)) { ?>
	<div class="Batty_section Batty_issueList">
		<h3>Scoreboard</h3>
		<div class="Batty_tableWrapper">
			<table>
				<tr>
					<th>User</th>
					<th>Feature Requests</th>
					<th>Bugs Found</th>
					<th>Issues Abandoned</th>
					<th>Total Score</th>
				</tr>
		<?php foreach ($userStats as $val) { ?>
				<tr>
					<td><?php echo $val['realname']; ?></td>
					<td><?php echo $val['featureCount']; ?></td>
					<td><?php echo $val['bugCount']; ?></td>
					<td><?php echo $val['abandonedCount']; ?></td>
					<td><?php echo $val['totalScore']; ?></td>
				</tr>
		<?php } ?>
			</table>
		</div>
	</div>
<?php } ?>
