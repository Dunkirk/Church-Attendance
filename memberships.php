<?php

	include "security.php";

?>

<html>

<head>
<title>Edit Memberships</title>
</head>

<body>

<div style="text-align: center">

<?php include ("header.html"); ?>

<?php
	
	if ($_SESSION['access'] < 2) :
	
		echo "<h2>You must be logged in and be an administrator to view this page!</h2>";

	else :
	
		echo "<h2>Edit Memberships</h2>";

		include ('connect.php');


		// Deleting a member(s) from a group...
		if (isset ($_REQUEST["remove_member"])) :

			$group_id = $_REQUEST["group_id"];
			$list = $_REQUEST["remover"];
			$members = "";
			foreach ($list as $member) {
				if ($members == "") {
					$members = $member;
				} else {
					$members = $members . ", " . $member;
				}
			}
//echo "<p>$members</p>\n";
			$sql = "DELETE FROM memberships WHERE \"group\" = $group_id AND " .
				"person IN ($members)";
//echo "<p>$sql</p>\n";
			$result = pg_query ($db, $sql);
			if (pg_result_status ($result)) {
				echo "<p>Operation finished successfully!</p>";
			} else {
				echo "<p>Operation DID NOT finish successfully!</br>" .
					"Please contact the administrator.</p>";
			}	


		// Adding a member to a group...
		elseif (isset ($_REQUEST["add_member"])) :

			$person_id = $_REQUEST["adder"];
			$group_id = $_REQUEST["group_id"];
			$sql = "INSERT INTO memberships VALUES ($person_id, $group_id)"; 
			$result = pg_query ($db, $sql);
			if (pg_result_status ($result)) {
				echo "<p>Operation finished successfully!</p>";
			} else {
				echo "<p>Operation DID NOT finish successfully!</br>" .
					"Please contact the administrator.</p>";
			}	


		// Form for doing the edits...
		elseif (isset ($_REQUEST["choose_group"])) :

?>

<form action="<?php echo $SCRIPT_NAME ?>" method="get">
<table border="1" align="center">
<tr><th>Person<th>Remove?

<?php

			$group_id = $_REQUEST["group_id"];
			$sql = "SELECT p.id, p.last_name, p.first_name FROM people p, " .
				"memberships m WHERE m.\"group\" = $group_id AND m.person = p.id " .
				"ORDER BY last_name, first_name";
//echo "<p>$sql</p>\n";
			$result = pg_query ($db, $sql);
			while ($row = pg_fetch_object ($result)) {
				echo "<tr><td>" . $row->last_name . ", " . $row->first_name .
					"<td align='center'><input type='checkbox' " .
					"name='remover[]' value='$row->id'>";
			}

?>

</table>
<p><input type="submit" name="remove_member" value="Remove">
<p><select name="adder">

<?php

			$sql = "SELECT id, last_name, first_name, suffix FROM people " .
				"ORDER BY last_name, first_name";
			$result = pg_query ($db, $sql);
			while ($row = pg_fetch_object ($result)) {
				echo "<option value='" . $row->id . "'>" . $row->last_name .
					", " . $row->first_name . " " . $row->suffix . "\n";
			}

?>

</select>
<input type="submit" name="add_member" value="Add">
<input type="hidden" name="group_id" value="<?php echo $_REQUEST["group_id"] ?>">
</form>

<?php

		// Default action
		else :

?>

<form action="<?php echo $SCRIPT_NAME ?>" method="post">
<p>Please select a group to edit.</p>
<p>Name:
<select size="1" name="group_id">

<?php

			$sql = "SELECT g.id, p.last_name, p.first_name, p.suffix, g.description " .
				"FROM groups g, people p WHERE " .
				"g.head = p.id ORDER BY p.last_name, p.first_name";
			$result = pg_query ($db, $sql);
			while ($row = pg_fetch_object ($result)) {
				if ($row->suffix <> '') {
					echo "<option value='" . $row->id . "'>" . $row->last_name .
						", " . $row->first_name . " " . $row->suffix . "\n";
				} else {
					echo "<option value='" . $row->id . "'>" . $row->last_name .
						", " . $row->first_name . "\n";
				}
			}
		
?>

</select>
<input type="submit" name="choose_group" value="Choose"></p>
</form>

<?php
	
		endif;

	endif;

?>

</div>

</body>

</html>
