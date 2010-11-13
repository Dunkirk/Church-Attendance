<html>

<head>
<title>Detail</title>
</head>

<body>

<div style='text-align: center'>

<?php include ('header.html'); ?>

<?php
	
	include ('connect.php');

	// This was in the "else" clause, but needs pulled out so that it's defined for
	// the last bit, in order to pick other weeks of attendance to view.
	$this_sunday = strftime ("%Y-%m-%d", strtotime ("this sunday"));
	$this_sunday .= " 17:00:00";
	$last_sunday = strftime ("%Y-%m-%d", strtotime ("last sunday"));
	$last_sunday .= " 17:00:00";
#echo "<p>$last_sunday, $this_sunday</p>";
	if (isset ($_REQUEST['week'])) {
		$week = $_REQUEST['week'];
	} else {
		$sql = "SELECT EXTRACT (WEEK FROM service) AS week, service FROM services " .
			"WHERE service <= '$last_sunday' ORDER BY service DESC LIMIT 1";
		$result = pg_query ($db, $sql);
		$week_object = pg_fetch_object ($result);
		$week = $week_object->week;
	}

	# The "limit" business comes because we now have multiple years of dates in the database,
	# and previous years were getting included. The problem now is that the Wed. and Sun. are
	# selected in backwards order, and my reverse array-loading trick isn't working for some
	# reason...
	$sql = "SELECT service FROM services " .
		"WHERE EXTRACT (WEEK FROM service) = $week " .
		"ORDER BY service DESC LIMIT 2";
#echo "<p>$sql</p>";
	$services_result = pg_query ($db, $sql);
	$services_count = pg_num_rows ($services_result);
	#while ($row = pg_fetch_object ($services_result)) {
	#	$services[] = $row->service;
	#}
	$row = pg_fetch_object ($services_result);
	$services[1] = $row->service;
	$row = pg_fetch_object ($services_result);
	$services[0] = $row->service;

	echo "<h2>Attendance Detail for Week $week</h2>";

	$sql = "SELECT g.id, g.head, g.description, p.last_name, p.first_name FROM " .
		"groups g, people p WHERE g.head = p.id AND g.level <> 2 ORDER BY g.level";
	$group_result = pg_query ($db, $sql);
	while ($group = pg_fetch_object ($group_result)) {		

		$group_id = $group->id;
		$head_id = $group->head;

		echo "<h3>" . $group->description . "</h3>";
		echo "<table width='90%' align='center' border='1'>";
		echo "<tr><th width='30%'>Person";
		foreach ($services as $service) {
			$date = strftime ("%a, %m/%d @ %I:%M", strtotime ($service));
			echo "<th width='15%'>$date<th width='15%'>Comment";
			echo "<th>Contacted\n";
		}

		$people = "";
		$sql = "SELECT head FROM groups WHERE id = $group_id";
		$result = pg_query ($db, $sql);
		$row = pg_fetch_object ($result);
		$people = $row->head;
		$sql = "SELECT id FROM people WHERE " .
			"head = " . $row->head . " AND member_type = 'Spouse'";
		$result = pg_query ($db, $sql);
		if (pg_num_rows ($result) > 0) {
			$row = pg_fetch_object ($result);
			$people = $people . ", " . $row->id;
		}
		$sql = "SELECT p.id FROM people p, memberships m WHERE " .
			"m.\"group\" = $group_id AND m.person = p.id " .
			"ORDER BY p.last_name, p.first_name";
		$result = pg_query ($db, $sql);
		while ($row = pg_fetch_object ($result)) {
			$people = $people . ", " . $row->id;
		}
		
		if ($people <> "") {

			//echo "<form action='$SCRIPT_NAME' method='post'>";

			$last_names = array ();
			$first_names = array ();
			$statuses = array ();
			$comments = array ();
			foreach ($services as $service) {
				// The LEFT JOIN here grabs services for the person even if there's
				// no record of them attending or not.
				$sql = "SELECT p.id, p.last_name, p.first_name, a.status, a.comment, " .
					"a.contacted FROM (SELECT * FROM people WHERE id IN ($people) " .
					"ORDER BY last_name, first_name) AS p LEFT JOIN " .
					"(SELECT * FROM attendance WHERE service = '$service') AS a ON " .
					"a.person = p.id";
				$result = pg_query ($db, $sql);
				while ($row = pg_fetch_object ($result)) {
					$last_names[$row->id] = $row->last_name;
					$first_names[$row->id] = $row->first_name;
					$statuses[$row->id][$service] = $row->status;
					$comments[$row->id][$service] = $row->comment;
					$contacts[$row->id][$service] = $row->contacted;
				}
			}

			// Note the trick here: using array_keys() to get the keys of the
			// $people_name array to use *as* an array in looping over each person.
			foreach (array_keys ($last_names) as $person) {
				echo "<tr><td>" . $last_names[$person] . ", " . $first_names[$person];
				foreach ($services as $service) {
					if ($statuses[$person][$service]) {
						$status = $statuses[$person][$service];
					} else {
						$status = "&nbsp;";
					}
					if ($comments[$person][$service]) {
						$comment = $comments[$person][$service];
					} else {
						$comment = "&nbsp;";
					}
					if ($status == "Present") {
						echo "<td align='center'>" . $status . "<td align='center'>" .
						   $comment;
					} elseif ($status == "&nbsp;") {
						echo "<td align='center' bgcolor='gray'>" . $status .
						   	"<td align='center' bgcolor='gray'>" . $comment;
					} elseif ($comment == "Concern") {
						echo "<td align='center' bgcolor='red'>" . $status .
						   	"<td align='center' bgcolor='red'>" . $comment;
					} elseif ($comment == "Unknown") {
						echo "<td align='center' bgcolor='orange'>" . $status .
							"<td align='center' bgcolor='orange'>" . $comment;
					} else {
						echo "<td align='center' bgcolor='yellow'>" . $status .
							"<td align='center' bgcolor='yellow'>" . $comment;
					}
					if ($contacts[$person][$service] == "t") {
						echo "<td align='center'><input type='checkbox' " .
							"checked='checked' disabled></td>";
					} else {
						echo "<td align='center'><input type='checkbox' " .
							"disabled></td>";
					}
				}
			}

			echo "</form>\n";

		}
		
		echo "</table>&nbsp;\n";
	}

?>

<form action='<?php echo $SCRIPT_NAME ?>' method='post'>
<p>Or choose another date for a detailed report:</p>
<p>Week:
<select size='1' name='week'>

<?php

	$sql = "SELECT DISTINCT EXTRACT (WEEK FROM service) AS week, service FROM services " .
		"WHERE service <= '2008-03-23 14:00:00' AND EXTRACT (DOW FROM service) = 0 " .
		"ORDER BY service DESC LIMIT 12";
	$result = pg_exec ($db, $sql);
	while ($row = pg_fetch_object ($result)) {
		echo "<option>" . $row->week;
	}

?>

</select>
<p><input type='submit' name='choose' value='Choose'>
</form>

</div>

</body>

</html>
