<?php

# Characters
# ID
# Name
# Description
# Image


# Accounts
#
# ID
# Created
# Active
# Character
# Name
# Password
# Session
# IP

include('mysql.php');
session_start();

if($_POST):

	#$Character = 1; # Until there's actually a character system.
	$CharacterQuery = mysql_query("Select `ID` from `Characters`");
	$Characters = mysql_num_rows($CharacterQuery);
	
	#$Character = rand(1, $Characters);
	$Character = 333;
	$Name = filter_var(stripslashes(trim($_POST['name'])), FILTER_SANITIZE_SPECIAL_CHARS);
	$Password = filter_var(stripslashes($_POST['password']), FILTER_SANITIZE_SPECIAL_CHARS);

	if($Name and $Password)
	{
	
		$Query = mysql_query("Select `ID`
								from `Accounts` 
								where `Name`='$Name'");
								
		list($AccountID) = mysql_fetch_array($Query);
	
		if(empty($AccountID))
		{
			$Now = time();
			$Password = hash('whirlpool', $Password);
			$Session = session_id();
			$IP = $_SERVER['REMOTE_ADDR'];
			
			mysql_query("Insert into `Accounts` values ('', '$Now', '$Now', '$Character', '$Name', '$Password', '$Session', '$IP', '')");
			
			$_SESSION['AccountID'] = mysql_insert_id();
			$_SESSION['Name'] = $Name;

			echo "Welcome Friend! Your character has been created.<br />";
			echo "Continue to <a href='#lobby'>the lobby</a>?";
		}
		else
		{
			echo "Oh no, that character name already exists.<br />";
			echo "<a href='javascript:Register()'>Try Again?</a>";
		}
	}
	else
	{
		echo "Wow, you're really that much of a fuckup?<br />";
		echo "<a href='javascript:CharacterMenu()'>Try Again?</a>";
	}
	
else: ?>

Right now there's only one character type to choose from.<br />
Please <a href='http://wetfish.net/chat/' target='_blank'>blame #wetfish</a> for their lack of support.<br />
<img src='/characters/template.png' /><hr />

<form id='new-character'>
	<table>
	<tr><td> Name </td><td> <input type='text' name='name' /> </td></tr>
	<tr><td> Password </td><td> <input type='text' name='password' /> </td></tr>
	</table>
	<input type='submit' value="Let's PARTY!" />
</from>

<a href='#home'>Back</a>

<?php endif; ?>
