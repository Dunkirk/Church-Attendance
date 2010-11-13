<?php

	include "security.php";

?>

<html>

<head>
<title>Edit People</title>
</head>

<body>

<div style="text-align: center">

<?php include ("header.html"); ?>

<?php
	
	if ($_SESSION['access'] < 2) :
	
		echo "<h2>You must be logged in and be an administrator to view this page!</h2>";

	else :
	
		echo "<h2>Edit People</h2>";

		include ('connect.php');

#phpinfo(48);

		// Deleting a person entirely...
		if (isset ($_REQUEST["delete_person"])) :

/*
			$sql = "SELECT residence FROM people WHERE id = " . $_REQUEST["person_id"];
			$result = pg_query ($db, $sql);
			$row = pg_fetch_object ($result);
			$residence = $row->residence;
*/

			$sql = "DELETE FROM people WHERE id = " . $_REQUEST["person_id"];
			$result = pg_query ($db, $sql);
			if (pg_result_status ($result)) {
				echo "<p>Person deleted successfully!</p>";
			} else {
				echo "<p>Person DID NOT delete successfully!<br>" .
					"Please make sure that you have deleted them from " .
					"any groups they were part of, and, while you're at<br> " .
					"it, please delete their residence if no one's " .
					"living there any more. Thanks!</p>";
			}	

/*
			$sql = "SELECT id FROM people WHERE residence = " . $residence;
			$result = pg_query ($db, $sql);
			if (pg_num_rows ($result) > 0) {
				echo "<p>Note that there are still " . pg_num_rows ($result) .
					" people (person) left in the database with that person's address.</p>";
			} else {
				$sql = "DELETE FROM residences WHERE id = " . $residence;
				$result = pg_query ($db, $sql);
				if (pg_result_status ($result)) {
					echo "<p>Residence also cleared out!</p>";
				} else {
					echo "<p>Residence DID NOT clear successfully!<br>" .
						"Please contact the administrator.</p>";
				}
			}
*/


		// Adding a new, or editing an old, person in the database...
		elseif ((isset ($_REQUEST["add_person"])) or 
			(isset ($_REQUEST["edit_person"]))) :

			$person_id = $_REQUEST["person_id"];
			if ($_REQUEST["title"] <> "") {
				$title = "'" . $_REQUEST["title"] . "'";
			} else {
				$title = "NULL";
			}
			$first_name = $_REQUEST["first_name"];
			if ($_REQUEST["middle_initial"] <> "") {
				$middle_initial = "'" . $_REQUEST["middle_initial"] . "'";
			} else {
				$middle_initial = "NULL";
			}
			$last_name = $_REQUEST["last_name"];
			if ($_REQUEST["suffix"] <> "") {
				$suffix = "'" . $_REQUEST["suffix"] . "'";
			} else {
				$suffix = "NULL";
			}
			$member_type = $_REQUEST["member_type"];
			if (($member_type == "Head") || ($_REQUEST["head_id"] == "")) {
				$head = "NULL";
			} else {
				$head = $_REQUEST["head_id"];
			}
			$gender = $_REQUEST["gender"];
			$residence_id = $_REQUEST["residence_id"];
			if ($_REQUEST["year"] <> "") {	
				$month = $_REQUEST["month"];
				$day = $_REQUEST["day"];
				$year = '20' . $_REQUEST["year"];
				$birthdate = "'$year-$month-$day'";
			} else {
				$birthdate = "NULL";
			}

			if (isset ($_REQUEST["add_person"])) {
				$sql = "INSERT INTO people (title, first_name, middle_initial, " .
					"last_name, suffix, member_type, head, gender, residence, " .
					"birthdate) VALUES ($title, '$first_name', $middle_initial, " .
					"'$last_name', $suffix, '$member_type', $head, '$gender', " .
					"$residence_id, $birthdate)";
			} else {
				$sql = "UPDATE people SET title = $title, first_name = '$first_name', " .
					"middle_initial = $middle_initial, last_name = '$last_name', " .
					"suffix = $suffix, member_type = '$member_type', head = $head, " .
					"gender = '$gender', residence = $residence_id, birthdate = " .
					"$birthdate WHERE id = $person_id";
			}
#echo "<p>$sql</p>\n";
			$result = pg_query ($db, $sql);
			if (pg_result_status ($result)) {
				echo "<p>Operation finished successfully!</p>";
			} else {
				echo "<p>Operation DID NOT finish successfully!<br>" .
					"Please contact the administrator.</p>";
			}	



		// Form for requesting a new person...
		elseif (isset ($_REQUEST["new_person"])) :

?>

<form action="<?php echo $SCRIPT_NAME ?>" method="post">
<table align="center" border="1">
<tr><th>Field<th>Value</tr>
<tr><td>Title<td><select size="1" name="title">
<option></option>

<?php

			$sql = "SELECT * FROM titles";
			$result = pg_query ($db, $sql);
			while ($row = pg_fetch_object ($result)) {
				echo "<option>" . $row->title;
			}

?>

<tr><td>First Name<td><input type="text" name="first_name">
<tr><td>Middle Initial<td><input type="text" name="middle_initial">
<tr><td>Last Name<td><input type="text" name="last_name">
<tr><td>Suffix<td><input type="text" name="suffix">
<tr><td>Member Type<td><select size="1" name="member_type">

<?php

			$sql = "SELECT * FROM member_types";
			$result = pg_query ($db, $sql);
			while ($row = pg_fetch_object ($result)) {
				if ($row->type == "Primary") {
					echo "<option selected>" . $row->type;
				} else {
					echo "<option>" . $row->type;
				}
			}

?>

<tr><td>Gender<td><select name="gender">
<option>M
<option>F
</select>
<tr><td>Residence<td><select name="residence_id">

<?php

			$sql = "SELECT h.id, p.last_name, p.first_name, h.address1, h.address2 FROM " .
				"residences h LEFT JOIN people p " .
				"ON p.member_type = 'Primary' AND p.residence = h.id " .
				"ORDER BY p.last_name, p.first_name";
			$result = pg_exec ($db, $sql);
			while ($row = pg_fetch_object ($result)) {
				$option = "";
				if ($row->last_name <> "") {
					$option = "(" . $row->last_name . ", " . $row->first_name . ") ";
				}
				if ($row->address2 <> "") {
					$option = $option . $row->address1 . ", " . $row->address2;
				} else {
					$option = $option . $row->address1;
				}
				echo "<option value='" . $row->id . "'>" . $option;
			}

?>

</select>
<tr><td>Birthday<td>Mo/Dy/Yr: <input type="text" name="month" size="2" value="<?php echo $month ?>">/<input type="text" name="day" size="2" value="<?php echo $day ?>">/<input type="text" name="year" size="2" value="<?php echo $year ?>">
</table>
<p>&nbsp;</p>
<input type="submit" name="add_person" value="Add">
</form>

<?php

		// Form for editing a person...
		elseif (isset ($_REQUEST["choose_person"])) :

			$sql = "SELECT * FROM people WHERE id = " . $_REQUEST["person_id"];
			$result = pg_query ($db, $sql);
			$row = pg_fetch_object ($result);
			$title = $row->title;
			$first_name = $row->first_name;
			$middle_initial = $row->middle_initial;
			$last_name = $row->last_name;
			$suffix = $row->suffix;
			$member_type = $row->member_type;
			$head = $row->head;
			$gender = $row->gender;
			$residence_id = $row->residence;
			$month = substr ($row->birthdate, 5, 2);
			$day = substr ($row->birthdate, 8, 2);
			$year = substr ($row->birthdate, 2, 2);

?>

<form action="<?php echo $SCRIPT_NAME ?>" method="post">
<table align="center" border="1">
<tr><th>Field<th>Value</tr>
<tr><td>Title<td><select size="1" name="title">
<option></option>

<?php

			$sql = "SELECT * FROM titles";
			$result = pg_query ($db, $sql);
			while ($row = pg_fetch_object ($result)) {
				if ($row->title == $title) {
					echo "<option selected>" . $row->title;
				} else {
					echo "<option>" . $row->title;
				}
			}

?>

<tr><td>First Name<td><input type="text" name="first_name" value="<?php echo $first_name ?>">
<tr><td>Middle Initial<td><input type="text" name="middle_initial" value="<?php echo $middle_initial ?>">
<tr><td>Last Name<td><input type="text" name="last_name" value="<?php echo $last_name ?>">
<tr><td>Suffix<td><input type="text" name="suffix" value="<?php echo $suffix ?>">
<tr><td>Member Type<td><select size="1" name="member_type">

<?php

			$sql = "SELECT * FROM member_types";
			$result = pg_query ($db, $sql);
			while ($row = pg_fetch_object ($result)) {
				if ($row->type == $member_type) {
					echo "<option selected>" . $row->type;
				} else {
					echo "<option>" . $row->type;
				}
			}

?>

</select>
<tr><td>Head<td><select size="1" name="head_id">
<option>

<?php

			$sql = "SELECT id, last_name, first_name FROM people " .
				"WHERE member_type = 'Head' ORDER BY last_name";
			$result = pg_exec ($db, $sql);
			while ($row = pg_fetch_object ($result)) {
				if ($head == $row->id) {
					echo "<option selected value='" . $row->id . "'>" . $row->last_name . 
						", " . $row->first_name;
				} else {
					echo "<option value='" . $row->id . "'>" . $row->last_name .  ", " . 
						$row->first_name;
				}
			}

?>

</select>
<tr><td>Gender<td><select name="gender">

<?php

			if ($gender == "M") {
				echo "<option selected>M";
				echo "<option>F";
			} else {
				echo "<option>M";
				echo "<option selected>F";
			}

?>

</select>
<tr><td>Residence<td><select name="residence_id">

<?php

			$sql = "SELECT h.id, p.last_name, p.first_name, h.address1, h.address2 FROM " .
				"residences h LEFT JOIN people p " .
				"ON p.member_type = 'Primary' AND p.residence = h.id " .
				"ORDER BY p.last_name, p.first_name";
			$result = pg_exec ($db, $sql);
			while ($row = pg_fetch_object ($result)) {
				if ($row->address2 <> "") {
					$option = $row->address1 . ", " . $row->address2;
				} else {
					$option = $row->address1;
				}
				if ($row->last_name <> "") {
					$option = $option . " (" . $row->last_name . ", " .
						$row->first_name . ") ";
				}
				if ($residence_id == $row->id) {
					echo "<option selected value='" . $row->id . "'>" . $option;
				} else {
					echo "<option value='" . $row->id . "'>" . $option;
				}
			}

?>

</select>
<tr><td>Birthday<td>Mo/Dy/Yr: <input type="text" name="month" size="2" value="<?php echo $month ?>">/<input type="text" name="day" size="2" value="<?php echo $day ?>">/<input type="text" name="year" size="2" value="<?php echo $year ?>">
</table>
<p>&nbsp;</p>
<input type="hidden" name="person_id" value="<?php echo $_REQUEST["person_id"] ?>">
<input type="submit" name="edit_person" value="Edit">&nbsp;
<input type="submit" name="delete_person" value="Delete">
</form>

<?php


		// Default action...
		else:

?>

<form action="<?php echo $SCRIPT_NAME ?>" method="get">
<p>Please select a person to edit.</p>
<p>Name:
<select size="1" name="person_id">

<?php

			$sql = "SELECT id, last_name, first_name, suffix FROM people " .
				"ORDER BY last_name, first_name";
			$result = pg_query ($db, $sql);
			while ($row = pg_fetch_object ($result)) {
				echo "<option value='" . $row->id . "'>" . $row->last_name . ", " . 
					$row->first_name . " " . $row->suffix . "\n";
			}

?>

</select>
<input type="submit" name="choose_person" value="Choose"></p>
<p>Or choose to add a new person.</p>
<p><input type="submit" name="new_person" value="New"></p>
</form>

<?php

		endif;

	endif; # The main IF for security...

?>

</div>

</body>

</html>
