<html>

<head>
<title>History</title>
</head>

<body>

<div style='text-align: center'>

<?php include ('header.html'); ?>

<?php
	
	include ('connect.php');

	if (isset ($_REQUEST['person'])) {

		$person = $_REQUEST['person'];
		$start = $_REQUEST['start'];
		$end = $_REQUEST['end'];

		$sql = "SELECT * FROM people WHERE id = $person";
		$person_result = pg_query ($db, $sql);
		$person_object = pg_fetch_object ($person_result);
		$last_name = $person_object->last_name;
		$first_name = $person_object->first_name;

		if (substr ($last_name, -1, 1) == "s") {
			echo "<h3>Historical Report on " . $first_name . " " .
				$last_name . "' Attendance</h3>";
		} else {
			echo "<h3>Historical Report on " . $first_name . " " .
				$last_name . "'s Attendance</h3>";
		}

		echo "<table border='1' align='center'>";
		echo "<tr><th>Service<th>Status<th>Comment";
		$sql = "SELECT * FROM attendance WHERE person = $person AND
			service <= '$end' AND service >= '$start' AND 
			status != 'Present'";
//echo "<p>$sql</p>\n";
		$result = pg_exec ($db, $sql);
		while ($row = pg_fetch_object ($result)) {
			echo "<tr><td>". $row->service . "<td>" . $row->status .
				"<td>" . $row->comment;
		}
		echo "</table>";
		
		// Sundays...
		$sql = "SELECT * FROM attendance WHERE person = $person AND " .
			"status = 'Present' AND EXTRACT (DOW FROM service) = 0";
		$present_result = pg_query ($db, $sql);
		$sql = "SELECT * FROM attendance WHERE person = $person AND " .
			"status = 'Absent' AND EXTRACT (DOW FROM service) = 0";
		$absent_result = pg_query ($db, $sql);
		$hits = pg_num_rows ($present_result);
		$misses = pg_num_rows ($absent_result);
		echo "<h3>Sundays</h3>\n";
		if ($hits == 0 && $misses == 0) {
			echo "<p>There's no attendance data for this person!</p>\n";
		} elseif ($hits == 0 && $misses <> 0) {
			echo "<p>Overall to-date attendance rate is 0%!</p>\n";
		} else {
			$hit_rate = round (($hits - $misses) / $hits * 100);
			echo "<p>Overall to-date attendance rate is $hit_rate%.</p>\n";
		}

		// Wednesdays...
		$sql = "SELECT * FROM attendance WHERE person = $person AND " .
			"status = 'Present' AND EXTRACT (DOW FROM service) = 3";
		$present_result = pg_query ($db, $sql);
		$sql = "SELECT * FROM attendance WHERE person = $person AND " .
			"status = 'Absent' AND EXTRACT (DOW FROM service) = 3";
		$absent_result = pg_query ($db, $sql);
		$hits = pg_num_rows ($present_result);
		$misses = pg_num_rows ($absent_result);
		echo "<h3>Wednesdays</h3>\n";
		if ($hits == 0 && $misses == 0) {
			echo "<p>There's no attendance data for this person!</p>\n";
		} elseif ($hits == 0 && $misses <> 0) {
			echo "<p>Overall to-date attendance rate is 0%!</p>\n";
		} else {
			$hit_rate = round (($hits - $misses) / $hits * 100);
			echo "<p>Overall to-date attendance rate is $hit_rate%.</p>\n";
		}
		
	} else {

?>

<form action='<?php echo $SCRIPT_NAME ?>' method='post'>
<p>Choose person for report:</p>
<p>Person:
<select size='1' name='person'>

<?php

	$sql = "SELECT id, last_name, first_name FROM people " .
		"ORDER BY last_name, first_name";
	$result = pg_exec ($db, $sql);
	while ($row = pg_fetch_object ($result)) {
		echo "<option value='" . $row->id . "'>" . $row->last_name . ", "
		   . $row->first_name . "\n";
	}

?>

</select>

<p>Start Date:
<select size='1' name='start'>

<?php

	$sql = "SELECT service FROM services";
	$result = pg_exec ($db, $sql);
	while ($row = pg_fetch_object ($result)) {
		$print = strftime ("%a, %m/%d @ %I:%M", strtotime ($row->service));
		echo "<option value='" . $row->service . "'>" . $print . "\n";
	}

?>

</select>

<p>End Date:
<select size='1' name='end'>

<?php

	$now = time ();
	$sql = "SELECT service FROM services";
	$result = pg_exec ($db, $sql);
	$flag_greater = "FALSE";
	while ($row = pg_fetch_object ($result)) {
		$print = strftime ("%a, %m/%d @ %I:%M", strtotime ($row->service));
		if ($flag_greater == "FALSE") {
			if (strtotime ($row->service) > $now) {
				echo "<option selected value='" . $row->service . "'>" . $print . "\n";
				$flag_greater = "TRUE";
			} else {
				echo "<option value='" . $row->service . "'>" . $print . "\n";
			}
		} else {
			echo "<option value='" . $row->service . "'>" . $print . "\n";
		}
	}

?>

</select>

<p><input type='submit' name='choose' value='Choose'>
</form>

<?php

	}

?>


</div>

</body>

</html>
