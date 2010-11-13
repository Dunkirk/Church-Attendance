<?php

	include "security.php";

?>

<html>

<head>
<title>Edit Residences</title>
</head>

<body>

<div style="text-align: center">

<?php include ("header.html"); ?>

<?php
	
	if ($_SESSION['access'] < 2) :
	
		echo "<h2>You must be logged in and be an administrator to view this page!</h2>";

	else :
	
		echo "<h2>Edit Residences</h2>";

		include ('connect.php');


		
		// Deleting a residence entirely...
		if  (isset ($_REQUEST["delete_residence"])) :

			$sql = "SELECT * FROM people WHERE residence = " . $_REQUEST["residence_id"];
			$result = pg_query ($db, $sql);
			if (pg_num_rows ($result) > 0) {
				echo "<p>DENIED! There are still " . pg_num_rows ($result) .
					" people in the database with that address.</br>" .
					"Please delete the following before deleting the residence.</br>" .
					"<ul>";
				while ($row = pg_fetch_object ($result)) {
					echo "<li>" . $row->last_name . ", " . $row->first_name;
				}
				echo "</ul>";
			} else {
				$sql = "DELETE FROM residences WHERE id = ". $_REQUEST["residence_id"];
				$result = pg_query ($db, $sql);
				if (pg_result_status ($result)) {
					echo "<p>Residence deleted successfully!</p>";
				} else {
					echo "<p>Residence DID NOT delete successfully!</br>" .
						"Please contact the administrator.</p>";
				}	
			}



		// Adding a new, or editing an old, residence in the database...
		elseif ((isset ($_REQUEST["add_residence"])) or
			(isset ($_REQUEST["edit_residence"]))) :

			$residence_id = $_REQUEST["residence_id"];
			$address1 = $_REQUEST["address1"];
			if ($_REQUEST["address2"] <> "") {
				$address2 = $_REQUEST["address2"];
			} else {
				$address2 = "NULL";
			}
			$city = $_REQUEST["city"];
			$state = $_REQUEST["state"];
			$zip = $_REQUEST["zip"];
			$phone = $_REQUEST["phone"];

			if (isset ($_REQUEST["add_residence"])) {
				$sql = "INSERT INTO residences (address1, address2, city, state, " .
					"zip, phone) VALUES ('$address1', '$address2', '$city', " .
					"'$state', '$zip', '$phone')";
			} else {
				$sql = "UPDATE residences SET address1 = '$address1', address2 = " .
					"$address2, city = '$city', state = '$state', zip = '$zip', " .
					"phone = '$phone' WHERE id = $residence_id";	
			}
#echo "<p>" . $sql . "</p>\n";
			$residences_result = pg_query ($db, $sql);
			if (pg_result_status ($residences_result)) {
				echo "<p>Operation finished successfully!</p>";
			} else {
				echo "<p>Operation DID NOT finish successfully!</br>" .
					"Please contact the administrator.</p>";
			}	
		


		// Form for requesting a new residence...
		elseif (isset ($_REQUEST["new_residence"])) :

?>

<form action="<?php echo $SCRIPT_NAME ?>" method="post">
<table align="center" border="1">
<tr><th>Field<th>Value</tr>
<tr><td>Address 1<td><input type="text" name="address1">
<tr><td>Address 2<td><input type="text" name="address2">
<tr><td>City<td><input type="text" name="city" value="Columbus">
<tr><td>State<td><input type="text" name="state" value="IN">
<tr><td>Zip<td><input type="text" name="zip">
<tr><td>Phone<td><input type="text" name="phone">
</table>
<p>&nbsp;</p>
<input type="submit" name="add_residence" value="Add">
</form>

<?php


		// Form for editing a residence...
		elseif (isset ($_REQUEST["choose_residence"])) :

			$sql = "SELECT * FROM residences WHERE id = " . 
				$_REQUEST["residence_id"];
			$result = pg_query ($db, $sql);
			$row = pg_fetch_object ($result);
			$address1 = $row->address1;
			$address2 = $row->address2;
			$city = $row->city;
			$state = $row->state;
			$zip = $row->zip;
			$phone = $row->phone;

?>

<form>
<table align="center" border="1">
<tr><th>Field<th>Value</tr>
<tr><td>Address1<td><input type="text" name="address1" value="<?php echo $address1 ?>">
<tr><td>Address2<td><input type="text" name="address2" value="<?php echo $address2 ?>">
<tr><td>City<td><input type="text" name="city" value="<?php echo $city ?>">
<tr><td>State<td><input type="text" name="state" value="<?php echo $state ?>">
<tr><td>Zip<td><input type="text" name="zip" value="<?php echo $zip ?>">
<tr><td>Phone<td><input type="text" name="phone" value="<?php echo $phone ?>">
</table>
<p>&nbsp;</p>
<input type="hidden" name="residence_id" value="<?php echo $_REQUEST["residence_id"] ?>">
<input type="submit" name="edit_residence" value="Edit">&nbsp;
<input type="submit" name="delete_residence" value="Delete">
</form>

<?php


		// Default action...
		else:

?>

<form action="<?php echo $SCRIPT_NAME ?>" method="post">
<p>Please select a residence to edit.</p>
<p>Head:
<select size="1" name="residence_id">

<?php

			$sql = "SELECT r.id, p.last_name, p.first_name, r.address1, r.address2 FROM " .
				"residences r LEFT JOIN people p " .
				"ON p.member_type = 'Head' AND p.residence = r.id " .
				"ORDER BY p.last_name, p.first_name";
			$result = pg_query ($db, $sql);
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
				echo "<option value='" . $row->id . "'>" . $option;
			}

?>

</select>
<input type="submit" name="choose_residence" value="Choose"></p>
<p>Or choose to add a new residence.</p>
<p><input type="submit" name="new_residence" value="New"></p>
</form>

<?php

		endif;

	endif; # Of the big IF for security...

?>

</div>

</body>

</html>
