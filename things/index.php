<?php

/*

NO DUPLIUCATE FILES DO THAT FIX IT OKAY DO GO TIDO!!


*/

require("config.php");

@mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD) or die("Unable to connect to the MySQL server.");
@mysql_select_db(MYSQL_DATABASE) or die("Unable to access the MySQL database.");

function str_replace_times($search, $replace, $subject, $times = 1)
{
	if($times > 0)
	{
		$find = strpos($subject, $search);
		return str_replace_times($search, $replace, substr_replace($subject, $replace, $find, strlen($search)), $times - 1);
	}
	else
		return $subject;
}

function format($data, $type="default")
{
	//Generic input/output formatting

	$data = str_replace(array("<", ">", "\"", "'", "\\", "/", "`"), array("&lt;", "&gt;", "&#34;", "&#39;", "&#92", "&#47;", "&#96;"), $data);
	$data = trim($data);

	//Specialized output formatting	

	if($type != "default")
		$data = preg_replace("/([^ \r\n]{50})/", "\\1 ", $data);

	if($type == "taginput")
	{
		$tagarray = array_unique(explode(",", $data));
		foreach($tagarray as $key => $tag)
		{
			if(trim($tag) == "")
				unset($tagarray[$key]);
			else
				$tagarray[$key] = str_replace(" ", "_", trim($tag));
		}
		$data = implode(", ", $tagarray);
	}
	elseif($type == "tags")
	{
		$tags = explode(",", $data);
		foreach($tags as $key => $tag)
		{
			$tag = trim($tag);
			$tags[$key] = "<a href=\"/find/$tag\">$tag</a>";
		}
		$data = implode(", ", $tags);

	}
	elseif($type == "body")
	{
		while(strpos($data, "\r\n\r\n\r\n") !== FALSE)
			$data = str_replace("\r\n\r\n\r\n", "\r\n\r\n", $data);
		$data = nl2br($data);
	}
	return $data;
}


$path = explode("/", $_GET['page']);

switch($path[0])
{
	case "share":
		$query = mysql_query("SELECT `ThingID` FROM `things` ORDER BY `ThingID` DESC LIMIT 1");
		list($last) = @mysql_fetch_array($query);

		if(((!preg_match("/^[1-9][0-9]*$/", $path[1])) || ($path[1] < 1) || ($path[1] > $last)) && ($path[1] != ""))
			$error = "<span class=\"error\">excuse me wtf r u doin?</span><br /><br />";

		elseif($path[1] != "")
		{
			$title = "Commenting on Thing $path[1]";

			$links = "<a href=\"/share/\">Share a Thing</a> | <a href=\"/find/\">Find a Thing</a> | <a href=\"/user/\">Find Someone's Things</a>";

			if(!empty($_POST))
			{
				if(!preg_match("/^[a-z0-9]*$/i", $_POST['name']))
					$errors['name'] = "<span class=\"error\">Don't put funny characters in your name.</span>";
				elseif(empty($_POST['name']))
					$_POST['name'] = "Anonymous"; //$errors['name'] = "<span class=\"error\">Surely you have a name!?</span>";
				elseif(strlen($_POST['name']) > 30)
					$errors['name'] = "<span class=\"error\">Your name is awfully long...</span>";

				if(trim($_POST['body']) == "")
					$errors['body'] = "<span class=\"error\">Your comment seems a bit...lacking.</span>";
				elseif(strlen($_POST['body']) > 10000)
					$errors['body'] = "<span class=\"error\">Your comment is a bit too verbose.</span>";

				if(empty($errors))
				{
					$CommentID = NULL;
					$ParentThingID = $path[1];
					$CommentTimePosted = time();
					$CommentIP = $_SERVER['REMOTE_ADDR'];
					$CommentName = format($_POST['name']);
					$CommentBody = format($_POST['body']);

					$query = mysql_query("SELECT `ThingReplyCount` FROM `things` WHERE `ThingID`='$path[1]' ORDER BY `ThingID` DESC LIMIT 1");
					list($ThingReplyCount) = @mysql_fetch_array($query);

					$ThingReplyCount = $ThingReplyCount + 1;
					$ThingTimeUpdated = $CommentTimePosted;

					mysql_query("UPDATE `things` SET `ThingReplyCount`='$ThingReplyCount' WHERE `ThingID`='$path[1]'");
					mysql_query("UPDATE `things` SET `ThingTimeUpdated`='$ThingTimeUpdated' WHERE `ThingID`='$path[1]'");
					mysql_query("INSERT INTO `comments` VALUES('$CommentID', '$ParentThingID', '$CommentTimePosted', '$CommentIP', '$CommentName', '$CommentBody')");
					if(mysql_error() != "")
						$error = "Unable to post your comment!";
					else
						$content = "<meta http-equiv=\"refresh\" content=\"2;url=/\">Your comment has been posted. You will now be redirected...";
				}
			}

			if(((!empty($_POST)) && (!empty($errors))) || (empty($_POST)))
			{
				$content = "<form action=\"/share/$path[1]\" method=\"post\"><table class=\"body\">";

				if(!empty($errors['name']))
					$content .= "<tr><td colspan=\"2\">{$errors['name']}</td></tr>";
				$content .= "<tr><td>Name:</td><td><input type=\"text\" name=\"name\" value=\"{$_POST['name']}\" size=\"30\" maxlength=\"30\"></td></tr>";

				if(!empty($errors['body']))
					$content .= "<tr><td colspan=\"2\">{$errors['body']}</td></tr>";
				$content .= "<tr><td>Body:</td><td><textarea name=\"body\" rows=\"15\" cols=\"100\">{$_POST['body']}</textarea></td></tr>";

				$content .= "<tr><td colspan=\"2\"><input type=\"submit\" value=\"Submit\" style=\"background-color:#294868; border: 2px solid #A5B7D1; color: #FFFFFF;\"></td></tr>";

				$content .= "</table></form>";
			}
		}

		elseif($path[1] == "")
		{
			$title = "Share a Thing";

			$links = "<a href=\"/find/\">Find a Thing</a> | <a href=\"/user/\">Find Someone's Things</a>";

			if(!empty($_POST))
			{
				if(!preg_match("/^[a-z0-9]*$/i", $_POST['name']))
					$errors['name'] = "<span class=\"error\">Don't put funny characters in your name.</span>";
				elseif(empty($_POST['name']))
					$errors['name'] = "<span class=\"error\">You need an account to share a new thing!</span>";
				elseif(strlen($_POST['name']) > 30)
					$errors['name'] = "<span class=\"error\">Your name is awfully long...</span>";
				elseif(empty($account[strtolower($_POST['name'])]))
					$errors['name'] = "<span class=\"error\">Only registered users may share a new thing.</span>";

				if(!preg_match("/^[a-z0-9]*$/i", $_POST['password']))
					$errors['password'] = "<span class=\"error\">Don't put funny characters in your password.</span>";
				elseif(strlen($_POST['password']) > 30)
					$errors['password'] = "<span class=\"error\">Your password is awfully long...</span>";
				elseif($account[strtolower($_POST['name'])] != $_POST['password'])
					$errors['password'] = "<span class=\"error\">The password you entered is incorrect.</span>";

				if(strpos($_POST['summary'], "&") !== FALSE)
					$errors['summary'] = "<span class=\"error\">ONLY JERKS USE AMPERSANDS.</span>";
				elseif(trim($_POST['summary']) == "")
					$errors['summary'] = "<span class=\"error\">You must include a summary!</span>";
				elseif(strlen($_POST['summary']) > 100)
					$errors['summary'] = "<span class=\"error\">Your summary is awfully long...</span>";

				if(!preg_match("/^[a-z0-9 ,]*$/i", $_POST['tags']))
					$errors['tags'] = "<span class=\"error\">Don't put funny characters in your tags.</span>";
				elseif(strlen($_POST['tags']) > 100)
					$errors['tags'] = "<span class=\"error\">I think you have too many tags...</span>";

				$pathinfo = pathinfo($_FILES['file']['name']);
				$pathinfo['extension'] = strtolower($pathinfo['extension']);

				if($_FILES['file']['error'] != 0)
					$errors['file'] = "<span class=\"error\">Something bad happened...try again?</span>";
				elseif(trim($_FILES['file']['name']) == "")
					$errors['file'] = "<span class=\"error\">You must upload a thing!</span>";
				elseif($_FILES['file']['size'] > 26214400)
					$errors['file'] = "<span class=\"error\">Your thing is too big... ;)</span>";
				elseif(($pathinfo['extension'] != "jpg") && ($pathinfo['extension'] != "jpeg") && ($pathinfo['extension'] != "png") && ($pathinfo['extension'] != "gif") && ($pathinfo['extension'] != "doc") && ($pathinfo['extension'] != "rtf") && ($pathinfo['extension'] != "pdf") && ($pathinfo['extension'] != "swf") && ($pathinfo['extension'] != "mov") && ($pathinfo['extension'] != "mp3") && ($pathinfo['extension'] != "rar") && ($pathinfo['extension'] != "zip") && ($pathinfo['extension'] != "txt"))
					$errors['file'] = "<span class=\"error\">That file type isn't allowed!</span>";

				if(trim($_POST['body']) == "")
					$errors['body'] = "<span class=\"error\">That's not such a bright thing.</span>";
				elseif(strlen($_POST['body']) > 10000)
					$errors['body'] = "<span class=\"error\">Your thing is a bit too verbose.</span>";

				if(empty($errors))
				{
 					$pathinfo['filename'] = preg_replace("/[^a-z0-9]/i", "_", $pathinfo['filename']);
					move_uploaded_file($_FILES['file']['tmp_name'], "./upload/{$pathinfo['filename']}.{$pathinfo['extension']}");

					$ThingID = NULL;
					$ThingReplyCount = 0;
					$ThingTimeUpdated = time();
					$ThingTimePosted = $ThingTimeUpdated; //To account for script lag?
					$ThingIP = $_SERVER['REMOTE_ADDR'];
					$ThingName = format($_POST['name']);
					$ThingSummary = format($_POST['summary']);
					$ThingTags = format($_POST['tags'], "taginput");
					$ThingFile = "/upload/{$pathinfo['filename']}.{$pathinfo['extension']}";
					$ThingBody = format($_POST['body']);

					mysql_query("INSERT INTO `things` VALUES('$ThingID', '$ThingReplyCount', '$ThingTimeUpdated', '$ThingTimePosted', '$ThingIP', '$ThingName', '$ThingSummary', '$ThingTags', '$ThingFile', '$ThingBody')");
					if(mysql_error() != "")
						$error = "Unable to post your thing!";
					else
						$content = "<meta http-equiv=\"refresh\" content=\"2;url=/\">Your thing has been posted. You will now be redirected...";
				}

			}

			if(((!empty($_POST)) && (!empty($errors))) || (empty($_POST)))
			{
				$content = "<form action=\"/share/\" enctype=\"multipart/form-data\" method=\"post\"><table class=\"body\">";

				if(!empty($errors['name']))
					$content .= "<tr><td colspan=\"2\">{$errors['name']}</td></tr>";
				$content .= "<tr><td>Name:</td><td><input type=\"text\" name=\"name\" value=\"{$_POST['name']}\" size=\"30\" maxlength=\"30\"></td></tr>";

				if(!empty($errors['password']))
					$content .= "<tr><td colspan=\"2\">{$errors['password']}</td></tr>";
				$content .= "<tr><td>Password:</td><td><input type=\"password\" name=\"password\" size=\"30\" maxlength=\"30\"></td></tr>";

				if(!empty($errors['summary']))
					$content .= "<tr><td colspan=\"2\">{$errors['summary']}</td></tr>";
				$content .= "<tr><td>Summary:</td><td><input type=\"text\" name=\"summary\" value=\"{$_POST['summary']}\" size=\"100\" maxlength=\"100\"></td></tr>";

				if(!empty($errors['tags']))
					$content .= "<tr><td colspan=\"2\">{$errors['tags']}</td></tr>";
				$content .= "<tr><td>Tags:</td><td><input type=\"text\" name=\"tags\" value=\"{$_POST['tags']}\" size=\"100\" maxlength=\"100\"></td></tr><tr><td>&nbsp;</td><td><span style=\"font-size: 8pt; font-style: italic;\">Separate tags with commas.</span></td></tr>";

				if(!empty($errors['file']))
					$content .= "<tr><td colspan=\"2\">{$errors['file']}</td></tr>";
				$content .= "<tr><td>File:</td><td><input type=\"file\" name=\"file\" size=\"100\"></td></tr><tr><td>&nbsp;</td><td><span style=\"font-size: 8pt; font-style: italic;\">Allowed file types: [jpg] [png] [gif] [doc] [rtf] [pdf] [swf] [mov] [mp3] [rar] [zip] [txt].<br />Maximum File Size: [25MB].</span></td></tr>";

				if(!empty($errors['body']))
					$content .= "<tr><td colspan=\"2\">{$errors['body']}</td></tr>";
				$content .= "<tr><td>Body:</td><td><textarea name=\"body\" rows=\"15\" cols=\"100\">{$_POST['body']}</textarea></td></tr>";

				$content .= "<tr><td colspan=\"2\"><input type=\"submit\" value=\"Submit\" style=\"background-color:#294868; border: 2px solid #A5B7D1; color: #FFFFFF;\"></td></tr>";

				$content .= "</table></form>";
			}
		}
	break;

	case "find":
		if((preg_match("/^[a-z0-9_,]*$/i", $path[1])) && ($path[1] != ""))
		{
			$path[1] = str_replace(",_", ", ", $path[1]);

			$title = "Tag search for: $path[1]";

			$links = "<a href=\"/share/\">Share a Thing</a> | <a href=\"/find/\">Find a Thing</a> | <a href=\"/user/\">Find Someone's Things</a>";

			$query = mysql_query("SELECT `ThingID`,`ThingReplyCount`,`ThingName`,`ThingSummary` FROM `things` WHERE `ThingTags` LIKE CONVERT(_utf8 '%$path[1]%' USING latin1) COLLATE latin1_swedish_ci");
			while(list($ThingID, $ThingReplyCount, $ThingName, $ThingSummary) = @mysql_fetch_array($query))
			{
				if($toggle == TRUE)
				{
					$content .= "<div class=\"thing\"><a href=\"/thing/$ThingID\">$ThingSummary</a> by $ThingName. Replies: $ThingReplyCount</div>";
					$toggle = FALSE;
				}
				else
				{
					$content .= "<a href=\"/thing/$ThingID\">$ThingSummary</a> by $ThingName. Replies: $ThingReplyCount";
					$toggle = TRUE;
				}

				$QueryReturned = TRUE;
			}

			if($QueryReturned != TRUE)
				$error = "<span class=\"error\">$path[1] does not match any things.</span><br /><br />";
		}
		else
		{
			if(!preg_match("/^[a-z0-9_,]*$/i", $path[1]))
				$error = "<span class=\"error\">The thing you searched for is invalid.</span><br /><br />";

			$title = "Find a Thing";

			$links = "<a href=\"/share/\">Share a Thing</a> | <a href=\"/user/\">Find Someone's Things</a>";

			$content = "<form action = \"\" name=\"search\" method=\"post\" onSubmit=\"SearchAction('Tag')\">Tags: <input type=\"text\" name=\"search\" value=\"{$_POST['search']}\">&nbsp;<input type=\"submit\" value=\"Submit\" style=\"background-color:#294868; border: 2px solid #A5B7D1; color: #FFFFFF;\"></form>";
		}
	break;

	case "user":
		if((preg_match("/^[a-z0-9]*$/i", $path[1])) && ($path[1] != ""))
		{
			$title = "$path[1]'s Things";

			$links = "<a href=\"/share/\">Share a Thing</a> | <a href=\"/find/\">Find a Thing</a> | <a href=\"/user/\">Find Someone's Things</a>";

			$query = mysql_query("SELECT `ThingID`,`ThingReplyCount`,`ThingSummary` FROM `things` WHERE `ThingName`='$path[1]' ORDER BY `ThingTimeUpdated` DESC");
			while(list($ThingID, $ThingReplyCount, $ThingSummary) = @mysql_fetch_array($query))
			{
				if($toggle == TRUE)
				{
					$content .= "<div class=\"thing\"><a href=\"/thing/$ThingID\">$ThingSummary</a> Replies: $ThingReplyCount</div>";
					$toggle = FALSE;
				}
				else
				{
					$content .= "<a href=\"/thing/$ThingID\">$ThingSummary</a> Replies: $ThingReplyCount";
					$toggle = TRUE;
				}

				$QueryReturned = TRUE;
			}

			if($QueryReturned != TRUE)
				$error = "<span class=\"error\">$path[1] has never shared any things.</span><br /><br />";

		}
		else
		{
			if(!preg_match("/^[a-z0-9]*$/i", $path[1]))
				$error = "<span class=\"error\">The user name you searched for is invalid.</span><br /><br />";

			$title = "Find Someone's Things";

			$links = "<a href=\"/share/\">Share a Thing</a> | <a href=\"/find/\">Find a Thing</a>";

			$content = "<form action = \"\" name=\"search\" method=\"post\" onSubmit=\"SearchAction('User')\">Name: <input type=\"text\" name=\"search\" value=\"{$_POST['search']}\">&nbsp;<input type=\"submit\" value=\"Submit\" style=\"background-color:#294868; border: 2px solid #A5B7D1; color: #FFFFFF;\"></form>";
		}
	break;

	case "thing":
		$query = mysql_query("SELECT `ThingID` FROM `things` ORDER BY `ThingID` DESC LIMIT 1");
		list($last) = @mysql_fetch_array($query);

		if((!preg_match("/^[1-9][0-9]*$/", $path[1])) || ($path[1] < 1) || ($path[1] > $last))
			$error = "<span class=\"error\">excuse me wtf r u doin?</span><br /><br />";

		else
		{
			$query = mysql_query("SELECT `ThingTimePosted`,`ThingName`,`ThingSummary`,`ThingTags`,`ThingFile`,`ThingBody` FROM `things` WHERE `ThingID`='$path[1]'");
			list($ThingTimePosted, $ThingName, $ThingSummary, $ThingTags, $ThingFile, $ThingBody) = @mysql_fetch_array($query);

			$ThingName = format($ThingName);
			$ThingSummary = format($ThingSummary, "summary");
			$ThingTags = format($ThingTags, "tags");
			$ThingBody = format($ThingBody, "body");

			$title = $ThingSummary;

			$previous = $path[1] - 1;
			if($previous < 1)
				$previous = 1;
			$next = $path[1] + 1;
			if($next > $last)
				$next = $last;


			$links = "<a href=\"/share/$path[1]\">Comment on this Thing</a> | <a href=\"/share/\">Share a Thing</a> | <a href=\"/find/\">Find a Thing</a> | <a href=\"/user/\">Find Someone's Things</a> | <a href=\"/thing/1\">First Thing</a> | <a href=\"/thing/$previous\">Previous Thing</a> | <a href=\"/thing/$next\">Next Thing</a> | <a href=\"/thing/$last\">Last Thing</a>";

			$ThingTimeFormatted = date("F j\, Y G:i:s", $ThingTimePosted);

			$content = "Thing Posted by: <a href=\"/user/$ThingName\">$ThingName</a> on $ThingTimeFormatted.<br />Tags: $ThingTags<br/>File: <a href=\"$ThingFile\">$ThingFile</a><hr />$ThingBody<br /><br />";

			$query = mysql_query("SELECT `CommentTimePosted`,`CommentName`,`CommentBody` FROM `comments` WHERE `ParentThingID`='$path[1]' ORDER BY `CommentID` ASC");
			while(list($CommentTimePosted, $CommentName, $CommentBody) = @mysql_fetch_array($query))
			{
				$CommentName = format($CommentName);
				$CommentBody = format($CommentBody, "body");

				$CommentTimeFormatted = date("F j\, Y G:i:s", $CommentTimePosted);

				if($toggle == TRUE)
				{
					$content .= "<div class=\"comment2\">Comment Posted by: <a href=\"/user/$CommentName\">$CommentName</a> on $CommentTimeFormatted.<hr />$CommentBody</div><br />";
					$toggle = FALSE;
				}
				else
				{
					$content .= "<div class=\"comment\">Comment Posted by: <a href=\"/user/$CommentName\">$CommentName</a> on $CommentTimeFormatted.<hr />$CommentBody</div><br />";
					$toggle = TRUE;
				}
			}
		}
	break;

	default:
		$query = mysql_query("SELECT `ThingID` FROM `things` ORDER BY `ThingID` DESC LIMIT 1");
		list($last) = @mysql_fetch_array($query);

		if($path[0] != "list")
		{
			if($path[0] != "")
				$error = "<span class=\"error\">The page you requested does not exist. For your convenience, you've been redirected to the listing of the newest 25 things.</span><br /><br />";
			$path[0] = "list";
		}

		if($path[1] == "")
			$path[1] = "1-25";

		elseif(!preg_match("/^[1-9][0-9]*-[1-9][0-9]*$/", $path[1]))
		{
			$error = "<span class=\"error\">The page you requested does not exist. For your convenience, you've been redirected to the listing of the newest 25 things.</span><br /><br />";
			$path[1] = "1-25";
		}

		$range = explode("-", $path[1]);
		
		if(($range[0] > $range[1]) || ($range[0] == $range[1]) || (($range[1] > $last) && ($range[1] != 25)))
		{
			$error = "<span class=\"error\">The page you requested does not exist. For your convenience, you've been redirected to the listing of the newest 25 things.</span><br /><br />";
			$range[0] = 1;
			$range[1] = 25;

		}

		$range[2] = $range[0] - 1;
		$range[3] = $range[1] - $range[2];

		$previous_begin = $range[0] - 25;
		if($previous_begin < 1)
			$previous_begin = 1;
		$previous_end = $range[1] - 25;
		if($previous_end < 1)
			$previous_end = 25;

		$next_begin = $range[0] + 25;
		if($next_begin > $last)
			$next_begin = $last - 25;
		if($next_begin < 1)
			$next_begin = 1;
		$next_end = $range[1] + 25;
		if($next_end > $last)
			$next_end = $last;
		if($next_begin == $next_end)
			$next_end = 25;

		$last_begin = $last - 25;
		if($last_begin < 1)
			$last_begin = 1;
		$last_end = $last;
		if($last_begin == $last_end)
			$last_end = 25;

		$title = "Listing things $range[0] to $range[1]";

		$links = "<a href=\"/share/\">Share a Thing</a> | <a href=\"/find/\">Find a Thing</a> | <a href=\"/user/\">Find Someone's Things</a> | <a href=\"/list/1-25\">First 25 Things</a> | <a href=\"/list/$previous_begin-$previous_end\">Previous 25 Things</a> | <a href=\"/list/$next_begin-$next_end\">Next 25 Things</a> | <a href=\"/list/$last_begin-$last_end\">Last 25 Things</a>";

		$query = mysql_query("SELECT `ThingID`,`ThingReplyCount`,`ThingName`,`ThingSummary` FROM `things` ORDER BY `ThingTimeUpdated` DESC LIMIT $range[2], $range[3]");
		while(list($ThingID, $ThingReplyCount, $ThingName, $ThingSummary) = @mysql_fetch_array($query))
		{
			$ThingSummary = format($ThingSummary, "summary");

			if($toggle == TRUE)
			{
				$content .= "<div class=\"thing\"><a href=\"/thing/$ThingID\">$ThingSummary</a> by <a href=\"/user/$ThingName\">$ThingName</a>. Replies: $ThingReplyCount</div>";
				$toggle = FALSE;
			}
			else
			{
				$content .= "<a href=\"/thing/$ThingID\">$ThingSummary</a> by <a href=\"/user/$ThingName\">$ThingName</a>. Replies: $ThingReplyCount";
				$toggle = TRUE;
			}
		}
	break;
}

echo <<<CONTENT

<html>
<head>
<title>things.wetfish.net: $title</title>

<script type="text/javascript" language="javascript">

function SearchAction(Type)
{
	if(Type == 'Tag')
	{
		string = new String(document.search.search.value); 
		string = string.replace(/ /g, "_");

		document.search.action = '/find/' + string;
		document.search.submit();
	}
	else if(Type == 'User')
	{
		document.search.action = '/user/' + document.search.search.value;
		document.search.submit();
	}
}

</script>

<style type="text/css">

.body { background: #18395B; color: #FFFFFF; font-family: tahoma; font-size: 10pt; }
a {color: #A5B7D1; text-decoration: none;}
a:hover {color: #A5B7D1; text-decoration: underline;}
span.error { background: #5E1739; padding: 1px; border: 1px solid #FFFFFF; }
div.thing { background: #294868; }
div.comment { background: #777788; padding: 2px; }
div.comment2 { background: #224466; }

</style>

</head>
<body class="body">

<a href="http://wetfish.net">Home</a> | <a href="http://things.wetfish.net">Things</a> | <a href="http://ideas.wetfish.net">Ideas</a> | <a href="http://irc.wetfish.net">IRC</a> | <a href="http://radio.wetfish.net">Radio</a>

<div style="margin-left:179px;">
<span style="font-size:17pt; font-weight:bold;"><a href="http://things.wetfish.net" style="color: #FFFFFF; text-decoration: none;">things.wetfish.net</a></span>

<br />

<span style="font-weight:bold;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$title</span>

<br />

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$links

</div>

<br />

<div style="margin-left:85px; margin-right:130px;">

$error

$content

</div>

<br />

<div style="margin-left:179px;">

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$links

</div>

</body>
</html>

CONTENT;

?>
