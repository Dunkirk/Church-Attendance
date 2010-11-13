<html>

<head>
<title>WoP Directory</title>
</head>

<body>

<div style="text-align: center">

<?php include ("header.html"); ?>

<table border='1' align='center'>

<?php

	include ('connect.php');

	// Just take the second level to start from. (Assume the first...) This
	// gives us the starting list of top-level groups.
	$sql = "SELECT level FROM groups WHERE level = 2 ORDER BY level ASC LIMIT 1";
	$result = pg_query ($db, $sql);
	$row = pg_fetch_object ($result);
	$level = $row->level;

	$sql = "SELECT g.id, p.last_name, p.first_name FROM " .
		"people p, groups g WHERE g.level = $level AND " .
		"p.id = g.head ORDER BY p.last_name";
	$top_result = pg_query ($db, $sql);
	while ($top = pg_fetch_object ($top_result)) {
		subgroups ($top->id, $top->last_name, $top->first_name);
	}

?>
	
</table>

</div>

</body>

</html>	


<?php

function subgroups ($group_id, $last_name, $first_name) {

	// This selects subgroups by seeing who in the group is, themselves, a head.
	$sql = "SELECT g1.id FROM groups g1, groups g2, memberships m WHERE " .
		"g2.id = $group_id AND m.\"group\" = g2.id AND g1.head = m.person";
	$group_result = pg_query ($db, $sql);

	if (pg_num_rows ($group_result) <> 0) {

		// This works only for now, while the second-level groups have only 
		// one level of subgroups. Once we get to where there are more than three 
		// total levels, this whole thing is going to need an overhaul. I guess
		// I'll need to run down the tree to get the total number of subgroups 
		// all at once. It'll be painful, but it's necessary to make the HTML
		// work.
		// Plus, there's the problem that the second level groups are actually
		// just "placeholder" groups, and, if shown, will make things very
		// confusing.
		echo "<tr dk><th rowspan='" . pg_num_rows ($group_result) . "'>" . 
			"<a href='memberships.php?group_id=$group_id&choose_group=Choose'>" . 
			$first_name . " " . $last_name . "</a>\n";

		while ($group = pg_fetch_object ($group_result)) {
			// Gets the head of the subgroup
			$sql = "SELECT p.last_name, p.first_name FROM " .
				"people p, groups g WHERE p.id = g.head AND " .
				"g.id = " . $group->id;
			$head_result = pg_query ($db, $sql);
			$head = pg_fetch_object ($head_result);
			
			subgroups ($group->id, $head->last_name, $head->first_name);
			
			// Gets the members of the group
			$sql = "SELECT p.last_name, p.first_name FROM " .
				"people p, memberships m WHERE p.id = m.person AND " .
				"m.\"group\" = " . $group->id . " ORDER BY p.last_name";
			$member_result = pg_query ($db, $sql);
			// This will work around spitting out the "groups" comprised of leaders
			if (pg_num_rows ($member_result) > 10) {
				echo "<td>";
				$members = "";
				while ($member = pg_fetch_object ($member_result)) {
					$members .= $member->first_name . " " . $member->last_name . ", ";
				}
				$members = substr ($members, 0, strlen ($members) - 2);
				echo $members . "</tr>\n";
			}
		}

	} else {
		echo "<th><a href='memberships.php?group_id=$group_id" .
			"&choose_group=Choose'>" . $first_name . " " .
			$last_name . "</a>\n";
	}

}
