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

?>

<h1>lol now you can change your character</h1>


<?php

if($_POST):

	$Name = filter_var(stripslashes(trim($_POST['name'])), FILTER_SANITIZE_SPECIAL_CHARS);
	$Description = filter_var(stripslashes($_POST['description']), FILTER_SANITIZE_SPECIAL_CHARS);
	$Link = filter_var(stripslashes($_POST['url']), FILTER_SANITIZE_SPECIAL_CHARS);
	
	if($Name and $Description and $Link)
	{
		$URL = parse_url($Link);
		
		if($URL['host'])
		{
			$File = pathinfo($URL['path']);
			
			if(in_array($File['extension'], array('jpg', 'jpeg', 'png', 'gif')))
			{
				mysql_query("Insert into `Characters` values ('', '$Name', '$Description', '$Link')");
				$CharacterID = mysql_insert_id();
				
				mysql_query("Update `Accounts` set `Character`='$CharacterID' where `ID`='{$_SESSION['AccountID']}'");
				
				echo "Your avatar has been changed, refresh to make it happen.<br />";
			}
			else
			{
				echo "Oh no, that's not an image.<br />";
				echo "<a href='javascript:NewCharacter()'>Try Again?</a>";
			}
		}
		else
		{
			echo "Oh no, that's not a URL.<br />";
			echo "<a href='javascript:NewCharacter()'>Try Again?</a>";
		}

	}
	else
	{
		echo "You need to enter everything.<br />";
		echo "<a href='javascript:NewCharacter()'>Try Again?</a>";
	}
	
else: ?>

Upload a new image for your character.

<form id='new-character' method='post'>
	<table>
	<tr><td> Name </td><td> <input type='text' name='name' /> </td></tr>
	<tr><td> Description </td><td> <input type='text' name='description' /> </td></tr>
	<tr><td> URL </td><td> <input type='text' name='url' /> </td></tr>
	</table>
	<input type='submit' value="Happy TIme!" />
</from>

<?php endif; ?>