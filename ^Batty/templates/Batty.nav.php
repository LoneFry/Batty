<div class="Batty_section">
    <div id="Batty_nav">
        <a href="/Batty/home">Home</a>
        <a href="/Batty/projects">Projects</a>
        <a href="/Batty/users">Users</a>
        <a href="/Batty/report">Report an Issue</a>
    </div>
        <form action="/Batty/search" method="GET">
            <div class="Batty_searchContainer">
                <div id="Batty_searchForm">
                    <input class="Batty_searchBox" type="text" name="search" value="<?php isset($search) ? html($search) : '' ; ?>" />
                    <input type="submit" value="Search" />
                </div>

                <details class="Batty_details<?php echo isset($openFlag)?' open':''; ?>">
                    <summary>Additional Options</summary>
                    <div class="Batty_tableWrapper">
                        <table class="Batty_search">
                            <thead>
                                <tr>
                                    <th>
                                        <label title="Click to toggle all.">
                                            <input type="checkbox" onchange="checkAllClick(this);" name="project_id_checkAll"<?php echo count($projectsUsed) == count($projects)?' checked="checked"':''; ?> />
                                            Project:
                                        </label>
                                    </th>
                                    <th>
                                        <label title="Click to toggle all.">
                                            <input type="checkbox" onchange="checkAllClick(this);" name="handler_id_checkAll"<?php echo count($handlersUsed) == count($users)?' checked="checked"':''; ?> />
                                            Assignee:
                                        </label>
                                    </th>
                                    <th>
                                        <label title="Click to toggle all.">
                                            <input type="checkbox" onchange="checkAllClick(this);" name="reporter_id_checkAll"<?php echo count($reportersUsed) == count($users)?' checked="checked"':''; ?> />
                                            Reporter:
                                        </label>
                                    </th>
                                    <th>
                                        <label title="Click to toggle all.">
                                            <input type="checkbox" onchange="checkAllClick(this);" name="status_checkAll"<?php echo count($statusesUsed) == count($statuses)?' checked="checked"':''; ?> />
                                            Status:
                                        </label>
                                    </th>
                                    <th>
                                        <label title="Click to toggle all.">
                                            <input type="checkbox" onchange="checkAllClick(this);" name="type_checkAll"<?php echo count($typesUsed) == count($types)?' checked="checked"':''; ?> />
                                            Type:
                                        </label>
                                    </th>
                                    <th>
                                        <label title="Click to toggle all.">
                                            <input type="checkbox" onchange="checkAllClick(this);" name="priority_checkAll"<?php echo count($prioritiesUsed) == count($priorities)?' checked="checked"':''; ?> />
                                            Priority:
                                        </label>
                                    </th>
                                </tr>
                            </thead>
                            <tr>
                                <td>
                                    <ul>
                                        <?php
                                        foreach($projects as $project_id => $project) {
                                            if (in_array($project_id, $projectsUsed)) {
                                                $checked = ' checked="checked"';
                                            } else {
                                                $checked = '';
                                            }
                                        ?>
                                        <li>
                                            <label>
                                                <input type="checkbox" name="project_id[]" class="project_id" onchange="updateCheckAll(this);" value="<?php echo $project_id; ?>"<?php echo $checked; ?> />
                                                <?php echo $project->label; ?>
                                            </label>
                                        </li>
                                    <?php } ?>
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        <?php
                                        foreach($users as $login_id => $loginnam) {
                                            if (in_array($login_id, $handlersUsed)) {
                                                $checked = ' checked="checked"';
                                            } else {
                                                $checked = '';
                                            }
                                        ?>
                                        <li>
                                            <label>
                                                <input type="checkbox" name="handler_id[]" class="handler_id" onchange="updateCheckAll(this);" value="<?php echo $login_id; ?>"<?php echo $checked; ?> />
                                                <?php echo $loginnam; ?>
                                            </label>
                                        </li>
                                    <?php } ?>
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        <?php
                                        foreach($users as $login_id => $loginnam) {
                                            if (in_array($login_id, $reportersUsed)) {
                                                $checked = ' checked="checked"';
                                            } else {
                                                $checked = '';
                                            }
                                        ?>
                                        <li>
                                            <label>
                                                <input type="checkbox" name="reporter_id[]" class="reporter_id" onchange="updateCheckAll(this);" value="<?php echo $login_id; ?>"<?php echo $checked; ?> />
                                                <?php echo $loginnam; ?>
                                            </label>
                                        </li>
                                    <?php } ?>
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        <?php
                                        foreach($statuses as $statusLevel => $description) {
                                            if (in_array($description, $statusesUsed)) {
                                                $checked = ' checked="checked"';
                                            } else {
                                                $checked = '';
                                            }
                                        ?>
                                        <li>
                                            <label>
                                                <input type="checkbox" name="status[]" class="status" onchange="updateCheckAll(this);" value="<?php echo $description; ?>"<?php echo $checked; ?> />
                                                <?php echo $description; ?>
                                            </label>
                                        </li>
                                    <?php } ?>
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        <?php
                                        foreach($types as $type) {
                                            if (in_array($type, $typesUsed)) {
                                                $checked = ' checked="checked"';
                                            } else {
                                                $checked = '';
                                            }
                                        ?>
                                        <li>
                                            <label>
                                                <input type="checkbox" name="type[]" class="type" onchange="updateCheckAll(this);" value="<?php echo $type; ?>"<?php echo $checked; ?> />
                                                <?php echo $type; ?>
                                            </label>
                                        </li>
                                    <?php } ?>
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        <?php
                                        foreach($priorities as $priorityLevel => $description) {
                                            if (in_array($priorityLevel, $prioritiesUsed)) {
                                                $checked = ' checked="checked"';
                                            } else {
                                                $checked = '';
                                            }
                                        ?>
                                        <li>
                                            <label>
                                                <input type="checkbox" name="priority[]" class="priority" onchange="updateCheckAll(this);" value="<?php echo $priorityLevel; ?>"<?php echo $checked; ?> />
                                                <?php echo $priorityLevel.': '.$description; ?>
                                            </label>
                                        </li>
                                    <?php } ?>
                                    </ul>
                                </td>
                            </tr>
                        </table>
                    </div>
                </details>
            </div>
        </form>
    </div>
