<?php

	include ('connect.php');
	
	session_start();

	//Logoff and get rid of the variables and cookies
	if (isset ($_REQUEST['logoff'])) {
		unset ($_SESSION['access']);
		unset ($_SESSION['username']);
		setcookie ('username', '', time() - 3600);
		setcookie ('password', '', time() - 3600);
		session_destroy();
	//The user has hit the "Logon" button OR...
	//If the user hasn't hit the logon or logoff buttons, then check to see if they
	//are still currently "logged in" by having the cookies stored. If so, set their
	//session variables INCLUDING ACCESS here. The password must be checked against the
	//database in order to verify permissions.
	} elseif (isset ($_REQUEST['logon']) || isset ($_COOKIE['username'])) {
		if (isset ($_REQUEST['logon'])) {
			$username = $_REQUEST['username'];
			$password = $_REQUEST['password'];
		} elseif (isset ($_COOKIE['username'])) {
			$username = $_COOKIE['username'];
			$password = $_COOKIE['password'];
		}
		//Lookup the username in the database
		$sql = "SELECT * FROM users WHERE username = '$username'";
		$result = pg_exec ($db, $sql);
		//If there is one username in the database that matches...
		if (pg_numrows ($result) == 1) {
			$data = pg_fetch_object ($result, 0);
			//Check the password...
			//Note this comparison is "backwards" so that you can't accidently ASSIGN
			//$password to what the database gives if you forget the extra `="
			if ($data->password == $password) {
				//Assign the user's access level to a session variable...
				$_SESSION['username'] = $username;
				$_SESSION['password'] = $password;
				$_SESSION['access'] = $data->access;
				//But do NOT set access as a cookie, because that could be imitated
				setcookie ('username', $username, time() + 3600 * 24 * 365);
				setcookie ('password', $password, time() + 3600 * 24 * 365);
				$message = "<b>Welcome $username!</b>";
			} else {
				unset ($_SESSION['username']);
				$message = "<b>Bad password!</b>";
			}
		} else {
			unset ($_SESSION['username']);
			$message = "<b>Bad username!</b>";
		}
	}
	
	//phpinfo (48);

?>
