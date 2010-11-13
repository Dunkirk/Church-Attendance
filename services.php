<?php

	include "security.php";

?>

<html>

<head>
<title>Edit Services</title>
</head>

<body>

<div style='text-align: center'>

<?php include ('header.html'); ?>

<?php
	
	if ($_SESSION['access'] < 2) :
	
		echo "<h2>You must be logged in and be an administrator to view this page!</h2>";

	else :
	
		echo "<h2>Edit Services</h2>";
		
		include ('connect.php');

		// Deleting a service entirely...
		if (isset ($_REQUEST['delete_service'])) :

			$sql = "DELETE FROM services WHERE service = '" .  $_REQUEST["old_service"] . "'";
			$result = pg_query ($db, $sql);
			if (pg_result_status ($result)) {
				echo "<p>Group deleted successfully!</p>";
			} else {
				echo "<p>Group DID NOT delete successfully!</br>" .
					"Please contact the administrator.</p>";
			}	



		// Adding or editing a service...
		elseif ((isset ($_REQUEST['add_service'])) or
			(isset ($_REQUEST['edit_service']))) :

			$month = $_REQUEST['month'];
			$day = $_REQUEST['day'];
			$year = $_REQUEST['year'];
			$time = $_REQUEST['time'];
			$service = "$month-$day-$year $time";

			if (isset ($_REQUEST['add_service'])) {
				$sql = "INSERT INTO services VALUES " .
					"('$service')";
			} else {
				$sql = "UPDATE services SET service = '$service' " .
					"WHERE service = '" . $_REQUEST['old_service'] . "'";
			}
//echo "<p>$sql</p>\n";
			$result = pg_query ($db, $sql);
			if (pg_result_status ($result)) {
				echo "<p>Operation finished successfully!</p>";
			} else {
				echo "<p>Operation DID NOT finish successfully!</br>" .
					"Please contact the administrator.</p>";
			}	



		// Form for editing...
		elseif (isset ($_REQUEST['choose_service'])) :

			list ($date, $time) = explode (" ", $_REQUEST['service']);
			list ($year, $month, $day) = explode ("-", $date);
			$year = substr ($year, 2, 2);
			list ($hour, $minute, $second) = explode (":", $time);

?>

<form action='<?php echo $SCRIPT_NAME ?>' method='post'>
<table align='center' border='1'>
<tr><th>Field<th>Value</tr>
<tr><td>Date<td>
<table>
<tr><td>MM<td>DD<td>YY
<tr>
<td><input type='text' name='month' size=2 value='<?php echo $month ?>'>
<td><input type='text' name='day' size=2 value='<?php echo $day ?>'>
<td><input type='text' name='year' size=2 value='<?php echo $year ?>'>
</table>
<tr><td>Time<td><select name='time'>

<?php

			for ($counter = 1; $counter <= 24; $counter++) {
				if ($counter == $hour) {
					if ($minute == 30) {
						echo "<option>$counter:00";
						echo "<option selected>$counter:30";
					} else {
						echo "<option selected>$counter:00";
						echo "<option>$counter:30";
					}
				} else {
					echo "<option>$counter:00";
					echo "<option>$counter:30";
				}
			}

?>

</select>
</table>
<p>&nbsp;</p>
<input type='hidden' name='old_service' value='<?php echo $_REQUEST['service'] ?>'>
<input type='submit' name='edit_service' value='Edit'>&nbsp;
<input type='submit' name='delete_service' value='Delete'>
</form>

<?php


		// Form for a new service...
		elseif (isset ($_REQUEST['new_service'])) :

?>


<form action='<?php echo $SCRIPT_NAME ?>' method='post'>
<table align='center' border='1'>
<tr><th>Field<th>Value</tr>
<tr><td>Date<td>
<table>
<tr><td>MM<td>DD<td>YY
<tr>
<td><input type='text' name='month' size=2>
<td><input type='text' name='day' size=2>
<td><input type='text' name='year' size=2>
</table>
<tr><td>Time<td><select name='time'>

<?php

			for ($counter = 1; $counter <= 24; $counter++) {
				if ($counter == 14) {
					echo "<option selected>$counter:00";
					echo "<option>$counter:30";
				} else {
					echo "<option>$counter:00";
					echo "<option>$counter:30";
				}
			}

?>

</select>
</table>
<p>&nbsp;</p>
<input type='submit' name='add_service' value='Add'>
</form>


<?php


		// Default action...
		else :

?>

<form action='<?php echo $SCRIPT_NAME ?>' method='post'>
<p>Please select a service to edit.</p>
<p>Name:
<select size='1' name='service'>

<?php

			$sql = "SELECT * FROM services ORDER BY service DESC";
			$result = pg_query ($db, $sql);
			while ($row = pg_fetch_object ($result)) {
				$date = strftime ("%a, %m/%d/%y @ %I:%M%P", strtotime ($row->service));
				echo "<option value='" . $row->service . "'>" . $date;
			}
			
?>

</select>
<input type='submit' name='choose_service' value='Choose'></p>
<p>Or choose to add a new service.</p>
<p><input type='submit' name='new_service' value='New'></p>
</form>

<?php

		endif;

	endif;

?>

</body>

</html>
