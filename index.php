<?php include ('connect.php'); ?>

<html>

<head>
<title>WoP Attendance Database</title>
<link href="attendance.css" rel="stylesheet" type="text/css">
</head>

<body>

<?php include ('header.html'); ?>

<?php

	if (isset ($_REQUEST['submit'])) :

		echo "<h2>Take Attendance</h2>";

		$group_id = $_REQUEST['group_id'];
		$service = $_REQUEST['service'];

		if ($_REQUEST['flag'] == "unset") {
			foreach ($_REQUEST['comment'] as $id => $comment) {
				if ($_REQUEST['contacted'][$id] == 'on') {
					$contacted = "t";
				} else {
					$contacted = "f";
				}
				if ($comment <> "Present") {
					$sql = "INSERT INTO attendance (service, person, comment, " .
						"status, contacted) VALUES ('$service', $id, '$comment', " .
						"'Absent', '$contacted')";
				} else {
					$sql = "INSERT INTO attendance (service, person, comment, " .
						"status, contacted) VALUES ('$service', $id, NULL, " .
						"'Present', '$contacted')";
				}
				$result = pg_query ($db, $sql);
			}
		} else {
			foreach ($_REQUEST['comment'] as $id => $comment) {
				if ($_REQUEST['contacted'][$id] == 'on') {
					$contacted = "t";
				} else {
					$contacted = "f";
				}
				if ($comment <> "Present") {
					$sql = "UPDATE attendance SET status = 'Absent', " .
						"comment = '$comment', contacted = '$contacted' " .
						"WHERE person = $id AND service = '$service'";
				} else {
					$sql = "UPDATE attendance SET status = 'Present', " .
						"comment = NULL, contacted = '$contacted' " .
						"WHERE person = $id AND service = '$service'";
				}
				$result = pg_query ($db, $sql);
			}
		}
		echo "<p>Attendance taken!</p>\n";

	elseif (isset ($_REQUEST['choose'])) :

		$service = $_REQUEST['service'];
		$date = strftime ("%a, %m/%d @ %I:%M", strtotime ($service));

?>

<h3>Taking attendance for <?php echo $date ?></h3>
<form action='<?php echo $SCRIPT_NAME ?>' method='post'>
<table align='center' border='1'>
<tr><th>Person<th>Status<th>Contacted

<?php

		// Based on the group, I collect a group of user id's. Once we have 
		// the id's, we can do a much, MUCH simpler query to get the statuses
		// of their attendance. (It just got too much to try to get the status
		// of the leader, his wife, and the rest of the group in one statement.)
		$group_id = $_REQUEST['group_id'];
		$sql = "SELECT head FROM groups WHERE id = $group_id";
		$result = pg_query ($db, $sql);
		$row = pg_fetch_object ($result);
		$people = $row->head;
		$sql = "SELECT id FROM people WHERE head = " .
			$row->head . " AND member_type = 'Spouse'";
		$result = pg_query ($db, $sql);
		if (pg_num_rows ($result) > 0) {
			$row = pg_fetch_object ($result);
			$people = $people . ", " . $row->id;
		}
		// "Group" is a reserved word in PostgreSQL, and needs to be quoted...
		$sql = "SELECT p.id FROM people p, memberships m WHERE " .
			"m.\"group\" = $group_id AND m.person = p.id ORDER BY " .
			"p.last_name, p.first_name";
		$result = pg_query ($db, $sql);
		while ($row = pg_fetch_object ($result)) {
			$people = $people . ", " . $row->id;
		}
		$sql = "SELECT p.id, p.last_name, p.first_name, a.status, a.comment, " .
			"a.contacted FROM (SELECT * FROM people WHERE id IN ($people) " .
			"ORDER by last_name, first_name) AS p LEFT JOIN " .
			"(SELECT * FROM attendance WHERE service = '$service') AS a ON " .
			"a.person = p.id ORDER BY last_name, first_name";
#echo "<p>$sql</p>\n";
		$result = pg_query ($db, $sql);
		// Set the flag if this is a re-take of attendance, so that we can do the
		// right sort of INSERT or UPDATE up above.
		$flag = "unset";

		// Here's the crux of the system. If the HTML is right, once the attendance
		// is taken, there WILL be a status of "Present" or "Absent" (or "Unknown"?)
		// recorded in the attendance table. Unless this step is performed, there
		// are no records for those people for that service. Therefore, actually 
		// PRESENTING the form to TAKE attendance requires a left join because there
		// aren't any records to start. However, I wanted to be able to RE-take 
		// attendance for those times that something needed updating. So, upon a re-
		// take of attendance, we just get the records already recorded, and the left
		// join is irrelevant.

		while ($row = pg_fetch_object ($result)) {
			echo "<tr><td>" . $row->last_name . ", " . $row->first_name;
			if ($row->status <> "") {
				$flag = "set";
			}
			echo "<td><select name='comment[$row->id]'>";
			if ($row->comment == "") {
				echo "<option>Present</option>" .
					"<option>Sick</option>" .
					"<option>Vacation</option>" .
					"<option>Work</option>" .
					"<option>Out of Town</option>" .
					"<option>Other</option>" .
					"<option>Concern</option>" .
					"<option>Unknown</option>";
			} elseif ($row->comment == "Sick") {
				echo "<option>Present</option>" .
					"<option selected>Sick</option>" .
					"<option>Vacation</option>" .
					"<option>Work</option>" .
					"<option>Out of Town</option>" .
					"<option>Other</option>" .
					"<option>Concern</option>" .
					"<option>Unknown</option>";
			} elseif ($row->comment == "Vacation") {
				echo "<option>Present</option>" .
					"<option>Sick</option>" .
					"<option selected>Vacation</option>" .
					"<option>Work</option>" .
					"<option>Out of Town</option>" .
					"<option>Other</option>" .
					"<option>Concern</option>" .
					"<option>Unknown</option>";
			} elseif ($row->comment == "Work") {
				echo "<option>Present</option>" .
					"<option>Sick</option>" .
					"<option>Vacation</option>" .
					"<option selected>Work</option>" .
					"<option>Out of Town</option>" .
					"<option>Other</option>" .
					"<option>Concern</option>" .
					"<option>Unknown</option>";
			} elseif ($row->comment == "Out of Town") {
				echo "<option>Present</option>" .
					"<option>Sick</option>" .
					"<option>Vacation</option>" .
					"<option>Work</option>" .
					"<option selected>Out of Town</option>" .
					"<option>Other</option>" .
					"<option>Concern</option>" .
					"<option>Unknown</option>";
			} elseif ($row->comment == "Other") {
				echo "<option>Present</option>" .
					"<option>Sick</option>" .
					"<option>Vacation</option>" .
					"<option>Work</option>" .
					"<option>Out of Town</option>" .
					"<option selected>Other</option>" .
					"<option>Concern</option>" .
					"<option>Unknown</option>";
			} elseif ($row->comment == "Concern") {
				echo "<option>Present</option>" .
					"<option>Sick</option>" .
					"<option>Vacation</option>" .
					"<option>Work</option>" .
					"<option>Out of Town</option>" .
					"<option>Other</option>" .
					"<option selected>Concern</option>" .
					"<option>Unknown</option>";
			} else {
				echo "<option>Present</option>" .
					"<option>Sick</option>" .
					"<option>Vacation</option>" .
					"<option>Work</option>" .
					"<option>Out of Town</option>" .
					"<option>Other</option>" .
					"<option>Concern</option>" .
					"<option selected>Unknown</option>";
			}
			echo "</select>";
			if ($row->contacted == "t") {
				echo "<td align='center'><input type='checkbox' " .
					"checked='checked' name='contacted[$row->id]'></td>";
			} else {
				echo "<td align='center'><input type='checkbox' " .
					"name='contacted[$row->id]'></td>";
			}
		}

?>

</select>
</table>
<p><input type='submit' name='submit' value='Submit'>
<input type='hidden' name='flag' value='<?php echo $flag ?>'>
<input type='hidden' name='group_id' value='<?php echo $_REQUEST['group_id'] ?>'>
<input type='hidden' name='service' value='<?php echo $_REQUEST['service'] ?>'>
</form>

<?php

	else :
				

?>

<form action='<?php echo $SCRIPT_NAME ?>' method='post'>
<h2>Take attendance</h2>
<table align=center>
<tr><th>Date:
<td><select size='1' name='service'>

<?php

		$today = strftime ("%Y-%m-%d", strtotime ("now"));
		$two_weeks_ago = strftime ("%Y-%m-%d", strtotime ("-2 weeks"));
		$week_from_now = strftime ("%Y-%m-%d", strtotime ("+1 week"));
		$sql = "SELECT * FROM services WHERE service >= '$two_weeks_ago' AND " .
			"service <= '$week_from_now' ORDER BY service DESC";
		$result = pg_exec ($db, $sql);
		while ($row = pg_fetch_object ($result)) {
			$print = strftime ("%a, %m/%d/%y @ %I:%M", strtotime ($row->service));
			$date = strftime ("%Y-%m-%d", strtotime ($row->service));
			if ($date == $today) {
				echo "<option selected value='" . $row->service . "'>" . $print;
			} else {
				echo "<option value='" . $row->service . "'>" . $print;
			}
		}


?>

</select></tr>
<tr><th>Group:
<td><select size='1' name='group_id'>

<?php

		if ($_SESSION['access'] == 1) {
			$sql = "SELECT g.id FROM groups g, users u WHERE " .
				"u.person = g.head AND u.username = '" .
				$_SESSION['username'] . "'";
			$result = pg_query ($db, $sql);
			$group_object = pg_fetch_object ($result);
			$group = $group_object->id;
		}

		$sql = "SELECT g.id, p.last_name, p.first_name, g.description " .
			"FROM groups g, people p WHERE g.level <> 2 AND " .
			"g.head = p.id ORDER BY p.last_name";
		$result = pg_query ($db, $sql);
		while ($row = pg_fetch_object ($result)) {
			if ($row->id == $group) {
				echo "<option selected value='" . $row->id . "'>" . $row->description;
			} else {
				echo "<option value='" . $row->id . "'>" . $row->description;
			}
		}
		
?>

</select></tr>
</table>
<p><input type='submit' name='choose' value='Choose'>
</form>

<?php

	endif;

?>

</body>

</html>
