<?php


include('mysql.php');
session_start();

if($_POST):

	$Name = filter_var(stripslashes(trim($_POST['name'])), FILTER_SANITIZE_SPECIAL_CHARS);
	$Password = filter_var(stripslashes($_POST['password']), FILTER_SANITIZE_SPECIAL_CHARS);

	if($Name and $Password)
	{
		$Now = time();
		$Password = hash('whirlpool', $Password);
		$Session = session_id();
		$IP = $_SERVER['REMOTE_ADDR'];

		$Query = mysql_query("Select `ID`, `Password` 
								from `Accounts` 
								where `Name`='$Name'");
								
		list($AccountID, $AccountPassword) = mysql_fetch_array($Query);
		
		if($AccountPassword == $Password)
		{
			$_SESSION['AccountID'] = $AccountID;
			$_SESSION['Name'] = $Name;
		
			mysql_query("Update `Accounts` set `Active`='$Now', `Session`='$Session', `IP`='$IP' where `ID`='$AccountID'");
	
			echo "Welcome back $Name. We're happy to enjoy with you.<br />";
			echo "Continue to <a href='#lobby'>the lobby</a>?";
		}
		else
		{
			echo "Oh no, your login failed.<br />";
			echo "<a href='javascript:SignIn()'>Try Again?</a>";		
		}
	}
	else
	{
		echo "Wow, you're really that much of a fuckup?<br />";
		echo "<a href='javascript:SignIn()'>Try Again?</a>";
	}
	
else: ?>

<form id='form'>
	<table>
	<tr><td> Name </td><td> <input type='text' name='name' /> </td></tr>
	<tr><td> Password </td><td> <input type='text' name='password' /> </td></tr>
	</table>
	<input type='submit' value="Sign Here" />
</from>

<a href='#home'>Back</a>

<?php endif; ?>