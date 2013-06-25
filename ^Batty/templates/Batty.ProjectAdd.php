<?php get_header(); ?>
<div class="Batty_Body">
	<h2>Batty : Manage Projects</h2>

<?php include_once 'Batty.nav.php'; ?>
	<div class="Batty_section" id="Batty_Project">
		<h3>Add Project</h3>

		<div class="Batty_tableWrapper">
			<form method="POST" action="/Batty/projectAdd">
				<table>
					<tr>
						<th>Label</th>
						<td><input type="text" name="label" maxlength="50" value="<?php html($project->label); ?>"></td>
					</tr>
					<tr>
						<th>Description</th>
						<td><input type="text" name="description" maxlength="255" value="<?php html($project->description); ?>"></td>
					</tr>
				</table>
				<input type="submit" value="Save Project">
			</form>
		</div>
	</div>
</div>
<script type="text/javascript" src="/^Batty/js/charsRemaining.js"></script>
<?php get_footer(); ?>
