<html>

<head>
<title>Summary</title>
</head>

<body>

<div style='text-align: center'>

<?php include ('header.html'); ?>

<?php
	
	include ('connect.php');

	// This is needed because it's (probably) using midnight...
	$today = strftime ("%Y-%m-%d", strtotime ("tomorrow"));
	if (isset ($_REQUEST['service'])) {
		$service = $_REQUEST['service'];
	} else {
		$sql = "SELECT service FROM services " .
			"WHERE service <= '$today' ORDER BY service DESC LIMIT 1";
		$services_result = pg_query ($db, $sql);
		$row = pg_fetch_object ($services_result);
		$service = $row->service;
	}
	
	$date = strftime ("%a, %m/%d @ %I:%M", strtotime ($service));
	echo "<h2>Absentee Report for $date</h2>";

	echo "<h3>" . $group->description . "</h3>";
	echo "<table align='center' border='1'>";
	echo "<tr><th>Person<th>Reason<th>Leader<th>Contacted";

	$sql = "SELECT p1.id, p1.last_name, p1.first_name, a.status, a.comment, a.contacted, " .
		"p2.last_name as leader FROM people p1, attendance a, memberships m, " .
		"groups g, people p2 WHERE a.service = '$service' AND p1.id = a.person " .
		"AND a.comment = 'Concern' AND m.person = p1.id AND m.\"group\" = g.id " .
		"AND p2.id = g.head ORDER BY p2.last_name, p1.last_name, p1.first_name";
	$absentee_result = pg_query ($db, $sql);
	while ($row = pg_fetch_object ($absentee_result)) {
		echo "<tr><td>" . $row->last_name . ", " . $row->first_name;
		echo "<td align='center' bgcolor='red'>" . $row->comment;
		echo "<td>" . $row->leader . "\n";
		if ($row->contacted == "t") {
			echo "<td align='center'>Yes";
		} else {
			echo "<td align='center'>No";
		}
	}

	$sql = "SELECT p1.id, p1.last_name, p1.first_name, a.status, a.comment, a.contacted, " .
		"p2.last_name as leader FROM people p1, attendance a, memberships m, " .
		"groups g, people p2 WHERE a.service = '$service' AND p1.id = a.person " .
		"AND a.comment = 'Unknown' AND m.person = p1.id AND m.\"group\" = g.id " .
		"AND p2.id = g.head ORDER BY p2.last_name, p1.last_name, p1.first_name";
	$absentee_result = pg_query ($db, $sql);
	while ($row = pg_fetch_object ($absentee_result)) {
		echo "<tr><td>" . $row->last_name . ", " . $row->first_name;
		echo "<td align='center' bgcolor='orange'>" . $row->comment;
		echo "<td>" . $row->leader . "\n";
		if ($row->contacted == "t") {
			echo "<td align='center'>Yes";
		} else {
			echo "<td align='center'>No";
		}
	}

	$sql = "SELECT p1.id, p1.last_name, p1.first_name, a.status, a.comment, a.contacted, " .
		"p2.last_name as leader FROM people p1, attendance a, memberships m, " .
		"groups g, people p2 WHERE a.service = '$service' AND p1.id = a.person " .
		"AND a.comment NOT IN ('Present', 'Unknown', 'Concern') AND m.person = p1.id " .
		"AND m.\"group\" = g.id AND p2.id = g.head ORDER BY p2.last_name, p1.last_name, " .
		"p1.first_name";
	$absentee_result = pg_query ($db, $sql);
	while ($row = pg_fetch_object ($absentee_result)) {
		echo "<tr><td>" . $row->last_name . ", " . $row->first_name;
		echo "<td align='center'>" . $row->comment;
		echo "<td>" . $row->leader . "\n";
		echo "<td>&nbsp;";
	}

	echo "</table>";

?>

<form action='<?php echo $SCRIPT_NAME ?>' method='post'>
<p>Or choose another date for a report:</p>
<p>Service:
<select size='1' name='service'>

<?php

	$sql = "SELECT service FROM services WHERE service <= '$today' " .
		"ORDER BY service DESC LIMIT 12";
	$result = pg_exec ($db, $sql);
	while ($row = pg_fetch_object ($result)) {
		$date = strftime ("%a, %m/%d/%y @ %I:%M", strtotime ($row->service));
		echo "<option value='" . $row->service . "'>" . $date;
	}

?>

</select>
<p><input type='submit' name='choose' value='Choose'>
</form>

</div>

</body>

</html>
