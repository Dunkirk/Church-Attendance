<?php

	include "security.php";

?>

<html>

<head>
<title>WoP Attendance Database Administration</title>
</head>

<body>

<div style='text-align: center'>

<?php include ("header.html"); ?>

<?php

	if (!isset ($_SESSION['username'])) :

		echo $message;

?>

<h2>Login</h2>

<form action='<?php echo $SCRIPT_NAME ?>' method='post'>
<table align=center>
<tr><td>Username:<td><input type='text' name='username'>
<tr><td>Password:<td><input type='password' name='password'>
</table>
<input type='submit' name='logon' value='Logon'>
</form>

<?php

	else :

?>

<form action='<?php echo $SCRIPT_NAME ?>' method='post'>
<table align='center'><tr><td><?php echo $message; ?>
<td><input type='submit' name='logoff' value='Logoff'></table>
</form>

<h2>Please use the links below<br>to administrate the database:</h2>

<a href='residences.php'>Residences</a><br>
<a href='people.php'>People</a><br>
<a href='memberships.php'>Memberships</a><br>
<a href='services.php'>Services</a>

<?php

	endif;

?>

</div>

</body>

</html>
