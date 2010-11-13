<html>

<head>
<title>WoP Directory</title>
</head>

<body>

<div style="text-align: center">

<?php include ("header.html"); ?>

<?php

	include ('connect.php');

	$sql = "SELECT g.id, g.level, p.last_name, p.first_name FROM " .
		"people p, groups g WHERE p.id = g.head ORDER BY " .
		"g.level, p.last_name";
	$result = pg_query ($db, $sql);
	while ($group = pg_fetch_object ($result)) {

		echo "<table border='1' align='center' width='25%'>";

		echo "<tr><th><a href='memberships.php?group_id=" . $group->id .
			"&choose_group=Choose'>" . $group->first_name . " " .
			$group->last_name . "</a>\n";

		$sql = "SELECT p.last_name, p.first_name FROM " .
			"people p, memberships m WHERE p.id = m.person AND " .
			"m.\"group\" = " . $group->id . " ORDER BY p.last_name";
		$member_result = pg_query ($db, $sql);
		while ($member = pg_fetch_object ($member_result)) {
			echo "<tr><td>" . $member->first_name . " " . $member->last_name . "</tr>\n";
		}

		echo "</table><p>";

	}

?>

</div>

</body>

</html>	
