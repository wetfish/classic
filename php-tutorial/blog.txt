<?php

mysql_connect("localhost", "username", "password");
mysql_select_db("database");

function Clean($Input, $Type="text")
{
	if($Type == "textarea")
		$Break = "<br />";

	return trim(str_replace(array("<", ">", "\"", "'", "\\", "`", "\r", "\n"), array("&lt;", "&gt;", "&#34;", "&#39;", "&#92", "&#96;", "", "$Break"), stripslashes($Input)));
}

$Go = explode("/", $_GET['Go']);

switch($Go[0])
{
	case "post":
		if(is_numeric($Go[1]))
		{
			if(!empty($_POST))
			{
			     $Author = Clean($_POST['Author']);
			     $Comment = Clean($_POST['Comment'], "textarea");

			     if($Author == "")
			          $Errors['Author'] = "Error: You must enter a name!";
			     elseif(strlen($Author) > 32)
			          $Errors['Author'] = "Error: Your name is too long!";

			     if($Comment == "")
			          $Errors['Comment'] = "Error: You must write something.";
			     elseif(strlen($Comment) > 5000)
			          $Errors['Comment'] = "Error: Oh come on, your life isn't THAT interesting.";

			     if(empty($Errors))
			     {
			          $Query = mysql_query("SELECT `Author`,`Comments` FROM `Posts` WHERE `ID`='$Go[1]'");
			          list($Author, $Comments) = mysql_fetch_array($Query);

			          if($Author == "")
			               $Errors['_Global'] = "Error: This post does not exist!";
			          else
			          {
			               $Time = time();
			               $Comments++;

			               mysql_query("INSERT INTO `Comments` VALUES('NULL', '$Go[1]', '$Time', '$Author', '$Comment')");
			               mysql_query("UPDATE `Posts` SET `Comments`='$Comments' WHERE `ID`='$Go[1]'");

			               $Content = "<meta http-equiv='refresh' content='2;url=blog.php?Go=view/$Go[1]'>Comment successful!";
			          }
			     }
			}

			if((empty($_POST)) || (!empty($Errors)))
			{
			     $Content = "<form method='post'><table>";

			     if($Errors['_Global'] != "")
			          $Content .= "<tr><td colspan='2'>".$Errors['_Global']."</td></tr>";

			     if($Errors['Author'] != "")
			          $Content .= "<tr><td colspan='2'>".$Errors['Author']."</td></tr>";
			     $Content .= "<tr><td>Author:</td><td><input type='text' name='Author' value='$Author'></td></tr>";

			     if($Errors['Comment'] != "")
			          $Content .= "<tr><td colspan='2'>".$Errors['Comment']."</td></tr>";
			     $Content .= "<tr><td>Post:</td><td><textarea name='Comment' rows='4' cols='40'>$Comment</textarea></td></tr>";

			     $Content .= "<tr><td colspan='2'><input type='submit' value='Submit'></td></tr>";
			     $Content .= "</table></form>";
			}			
		}

		else
		{
			if(!empty($_POST))
			{
			     	$Author = Clean($_POST['Author']);
			     	$Password = Clean($_POST['Password']);
			     	$Post = Clean($_POST['Post'], "textarea");

			     	if($Author == "")
			     	     	$Errors['Author'] = "Error: You must enter a name!";
			     	elseif(strlen($Author) > 32)
			     	     	$Errors['Author'] = "Error: Your name is too long!";

			     	if($Password != "qwerty")
			     	     	$Errors['Password'] = "Error: Invalid password.";

			     	if($Post == "")
			     	     	$Errors['Post'] = "Error: You must write something.";
			     	elseif(strlen($Post) > 5000)
			     	     	$Errors['Post'] = "Error: Oh come on, your life isn't THAT interesting.";

			     	if(empty($Errors))
			     	{
			     	     	$Time = time();

			     	     	mysql_query("INSERT INTO `Posts` VALUES('NULL', '$Time', '0', '$Author', '$Post')");
			     	     	$Content = "<meta http-equiv='refresh' content='2;url=blog.php'>Post successful!";

			     	}
			}

			if((empty($_POST)) || (!empty($Errors)))
			{
			     	$Content = "<form method='post'><table>";

			     	if($Errors['Author'] != "")
			     	     	$Content .= "<tr><td colspan='2'>".$Errors['Author']."</td></tr>";
			     	$Content .= "<tr><td>Author:</td><td><input type='text' name='Author' value='$Author'></td></tr>";

     				if($Errors['Password'] != "")
     				     	$Content .= "<tr><td colspan='2'>".$Errors['Password']."</td></tr>";
     				$Content .= "<tr><td>Password:</td><td><input type='password' name='Password' value='$Post'></td></tr>";

     				if($Errors['Post'] != "")
     				     	$Content .= "<tr><td colspan='2'>".$Errors['Post']."</td></tr>";
     				$Content .= "<tr><td>Post:</td><td><textarea name='Post' rows='4' cols='40'>$Post</textarea></td></tr>";

     				$Content .= "<tr><td colspan='2'><input type='submit' value='Submit'></td></tr>";
     				$Content .= "</table></form>";
			}
		}
	break;

	case "view":
		if(is_numeric($Go[1]))
		{
			$PostQuery = mysql_query("SELECT `Time`,`Author`,`Post` FROM `Posts` WHERE `ID`='$Go[1]'");
			list($Time, $Author, $Post) = mysql_fetch_array($PostQuery);

			$Time = date("F j\, Y G:i:s", $Time);

			$Content .= "<b>Post by: $Author<br />On: $Time<br />";
			$Content .= "<br /></b><br />$Post<hr />";
			$Content .= "<a href='?Go=post/$Go[1]'>Comment on this Post</a><hr />";

			$Content .= "<table>";

			$CommentQuery = mysql_query("SELECT `Time`,`Author`,`Comment` FROM `Comments` WHERE `PostID`='$Go[1]'");
			while(list($CommentTime, $CommentAuthor, $Comment) = mysql_fetch_array($CommentQuery))
			{
				$CommentTime = date("F j\, Y G:i:s", $CommentTime);

				$Content .= "<tr><td><b>Comment by: $Author<br />On: $CommentTime<br />";
				$Content .= "<br /></b><br />$Comment<hr /></td></tr>";
			}

			$Content .= "</table>";
		}
	break;

	default:
		$Content = "<a href='?Go=post'>Write a New Post</a><hr />";

		$Content .= "<table>";

		$Query = mysql_query("SELECT `ID`,`Time`,`Comments`,`Author`,`Post` FROM `Posts` ORDER BY ID DESC LIMIT 10");
		while(list($ID, $Time, $Comments, $Author, $Post) = mysql_fetch_array($Query))
		{
			$Time = date("F j\, Y G:i:s", $Time);

			if(strlen($Post) > 1000)
				$Post = substr($Post, 0, 1000)."...<br />(<a href='?Go=view/$ID'>Read More</a>)";

			$Content .= "<tr><td><b>Post by: $Author<br />On: $Time<br />";
			$Content .= "<a href='?Go=view/$ID'>Comments: $Comments</a><br /></b><br />$Post<hr /></td></tr>";
		}

		$Content .= "</table>";
	break;
}

echo <<<HTML
<html>
<head>
<title>The Super Great Tutorial Blog!!</title>
</head>
<body>
$Content
</body>
</html>
HTML;

?>
