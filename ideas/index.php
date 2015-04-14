<?php

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
		$query = mysql_query("SELECT `IdeaID` FROM `ideas` ORDER BY `IdeaID` DESC LIMIT 1");
		list($last) = @mysql_fetch_array($query);

		if(((!preg_match("/^[1-9][0-9]*$/", $path[1])) || ($path[1] < 1) || ($path[1] > $last)) && ($path[1] != ""))
			$error = "<span class=\"error\">excuse me wtf r u doin?</span><br /><br />";

		elseif($path[1] != "")
		{
			$title = "Commenting on Idea $path[1]";

			$links = "<a href=\"/share/\">Share an Idea</a> | <a href=\"/find/\">Find an Idea</a> | <a href=\"/user/\">Find Someone's Ideas</a>";

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
					$ParentIdeaID = $path[1];
					$CommentTimePosted = time();
					$CommentIP = $_SERVER['REMOTE_ADDR'];
					$CommentName = format($_POST['name']);
					$CommentBody = format($_POST['body']);

					$query = mysql_query("SELECT `IdeaReplyCount` FROM `ideas` WHERE `IdeaID`='$path[1]' ORDER BY `IdeaID` DESC LIMIT 1");
					list($IdeaReplyCount) = @mysql_fetch_array($query);

					$IdeaReplyCount = $IdeaReplyCount + 1;
					$IdeaTimeUpdated = $CommentTimePosted;

					mysql_query("UPDATE `ideas` SET `IdeaReplyCount`='$IdeaReplyCount' WHERE `IdeaID`='$path[1]'");
					mysql_query("UPDATE `ideas` SET `IdeaTimeUpdated`='$IdeaTimeUpdated' WHERE `IdeaID`='$path[1]'");
					mysql_query("INSERT INTO `comments` VALUES('$CommentID', '$ParentIdeaID', '$CommentTimePosted', '$CommentIP', '$CommentName', '$CommentBody')");
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
			$title = "Share an Idea";

			$links = "<a href=\"/find/\">Find an Idea</a> | <a href=\"/user/\">Find Someone's Ideas</a>";

			if(!empty($_POST))
			{
				if(!preg_match("/^[a-z0-9]*$/i", $_POST['name']))
					$errors['name'] = "<span class=\"error\">Don't put funny characters in your name.</span>";
				elseif(empty($_POST['name']))
					$_POST['name'] = "Anonymous"; //$errors['name'] = "<span class=\"error\">Surely you have a name!?</span>";
				elseif(strlen($_POST['name']) > 30)
					$errors['name'] = "<span class=\"error\">Your name is awfully long...</span>";

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

				if(trim($_POST['body']) == "")
					$errors['body'] = "<span class=\"error\">That's not such a bright idea.</span>";
				elseif(strlen($_POST['body']) > 10000)
					$errors['body'] = "<span class=\"error\">Your idea is a bit too verbose.</span>";

				if(empty($errors))
				{
					$IdeaID = NULL;
					$IdeaReplyCount = 0;
					$IdeaTimeUpdated = time();
					$IdeaTimePosted = $IdeaTimeUpdated; //To account for script lag?
					$IdeaIP = $_SERVER['REMOTE_ADDR'];
					$IdeaName = format($_POST['name']);
					$IdeaSummary = format($_POST['summary']);
					$IdeaTags = format($_POST['tags'], "taginput");
					$IdeaBody = format($_POST['body']);

					mysql_query("INSERT INTO `ideas` VALUES('$IdeaID', '$IdeaReplyCount', '$IdeaTimeUpdated', '$IdeaTimePosted', '$IdeaIP', '$IdeaName', '$IdeaSummary', '$IdeaTags', '$IdeaBody')");
					if(mysql_error() != "")
						$error = "Unable to post your idea!";
					else
						$content = "<meta http-equiv=\"refresh\" content=\"2;url=/\">Your idea has been posted. You will now be redirected...";
				}

			}

			if(((!empty($_POST)) && (!empty($errors))) || (empty($_POST)))
			{
				$content = "<form action=\"/share/\" method=\"post\"><table class=\"body\">";

				if(!empty($errors['name']))
					$content .= "<tr><td colspan=\"2\">{$errors['name']}</td></tr>";
				$content .= "<tr><td>Name:</td><td><input type=\"text\" name=\"name\" value=\"{$_POST['name']}\" size=\"30\" maxlength=\"30\"></td></tr>";

				if(!empty($errors['summary']))
					$content .= "<tr><td colspan=\"2\">{$errors['summary']}</td></tr>";
				$content .= "<tr><td>Summary:</td><td><input type=\"text\" name=\"summary\" value=\"{$_POST['summary']}\" size=\"100\" maxlength=\"100\"></td></tr>";

				if(!empty($errors['tags']))
					$content .= "<tr><td colspan=\"2\">{$errors['tags']}</td></tr>";
				$content .= "<tr><td>Tags:</td><td><input type=\"text\" name=\"tags\" value=\"{$_POST['tags']}\" size=\"100\" maxlength=\"100\"></td></tr><tr><td>&nbsp;</td><td><span style=\"font-size: 8pt; font-style: italic;\">Separate tags with commas.</span></td></tr>";

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

			$links = "<a href=\"/share/\">Share an Idea</a> | <a href=\"/find/\">Find an Idea</a> | <a href=\"/user/\">Find Someone's Ideas</a>";

			$query = mysql_query("SELECT `IdeaID`,`IdeaReplyCount`,`IdeaName`,`IdeaSummary` FROM `ideas` WHERE `IdeaTags` LIKE CONVERT(_utf8 '%$path[1]%' USING latin1) COLLATE latin1_swedish_ci");
			while(list($IdeaID, $IdeaReplyCount, $IdeaName, $IdeaSummary) = @mysql_fetch_array($query))
			{
				if($toggle == TRUE)
				{
					$content .= "<div class=\"idea\"><a href=\"/idea/$IdeaID\">$IdeaSummary</a> by $IdeaName. Replies: $IdeaReplyCount</div>";
					$toggle = FALSE;
				}
				else
				{
					$content .= "<a href=\"/idea/$IdeaID\">$IdeaSummary</a> by $IdeaName. Replies: $IdeaReplyCount";
					$toggle = TRUE;
				}

				$QueryReturned = TRUE;
			}

			if($QueryReturned != TRUE)
				$error = "<span class=\"error\">$path[1] does not match any ideas.</span><br /><br />";
		}
		else
		{
			if(!preg_match("/^[a-z0-9_,]*$/i", $path[1]))
				$error = "<span class=\"error\">The idea you searched for is invalid.</span><br /><br />";

			$title = "Find an Idea";

			$links = "<a href=\"/share/\">Share an Idea</a> | <a href=\"/user/\">Find Someone's Ideas</a>";

			$content = "<form action = \"\" name=\"search\" method=\"post\" onSubmit=\"SearchAction('Tag')\">Tags: <input type=\"text\" name=\"search\" value=\"{$_POST['search']}\">&nbsp;<input type=\"submit\" value=\"Submit\" style=\"background-color:#294868; border: 2px solid #A5B7D1; color: #FFFFFF;\"></form>";
		}
	break;

	case "user":
		if((preg_match("/^[a-z0-9]*$/i", $path[1])) && ($path[1] != ""))
		{
			$title = "$path[1]'s Ideas";

			$links = "<a href=\"/share/\">Share an Idea</a> | <a href=\"/find/\">Find an Idea</a> | <a href=\"/user/\">Find Someone's Ideas</a>";

			$query = mysql_query("SELECT `IdeaID`,`IdeaReplyCount`,`IdeaSummary` FROM `ideas` WHERE `IdeaName`='$path[1]' ORDER BY `IdeaTimeUpdated` DESC");
			while(list($IdeaID, $IdeaReplyCount, $IdeaSummary) = @mysql_fetch_array($query))
			{
				if($toggle == TRUE)
				{
					$content .= "<div class=\"idea\"><a href=\"/idea/$IdeaID\">$IdeaSummary</a> Replies: $IdeaReplyCount</div>";
					$toggle = FALSE;
				}
				else
				{
					$content .= "<a href=\"/idea/$IdeaID\">$IdeaSummary</a> Replies: $IdeaReplyCount";
					$toggle = TRUE;
				}

				$QueryReturned = TRUE;
			}

			if($QueryReturned != TRUE)
				$error = "<span class=\"error\">$path[1] has never shared any ideas.</span><br /><br />";

		}
		else
		{
			if(!preg_match("/^[a-z0-9]*$/i", $path[1]))
				$error = "<span class=\"error\">The user name you searched for is invalid.</span><br /><br />";

			$title = "Find Someone's Ideas";

			$links = "<a href=\"/share/\">Share an Idea</a> | <a href=\"/find/\">Find an Idea</a>";

			$content = "<form action = \"\" name=\"search\" method=\"post\" onSubmit=\"SearchAction('User')\">Name: <input type=\"text\" name=\"search\" value=\"{$_POST['search']}\">&nbsp;<input type=\"submit\" value=\"Submit\" style=\"background-color:#294868; border: 2px solid #A5B7D1; color: #FFFFFF;\"></form>";
		}
	break;

	case "idea":
		$query = mysql_query("SELECT `IdeaID` FROM `ideas` ORDER BY `IdeaID` DESC LIMIT 1");
		list($last) = @mysql_fetch_array($query);

		if((!preg_match("/^[1-9][0-9]*$/", $path[1])) || ($path[1] < 1) || ($path[1] > $last))
			$error = "<span class=\"error\">excuse me wtf r u doin?</span><br /><br />";

		else
		{
			$query = mysql_query("SELECT `IdeaTimePosted`,`IdeaName`,`IdeaSummary`,`IdeaTags`,`IdeaBody` FROM `ideas` WHERE `IdeaID`='$path[1]'");
			list($IdeaTimePosted, $IdeaName, $IdeaSummary, $IdeaTags, $IdeaBody) = @mysql_fetch_array($query);

			$IdeaName = format($IdeaName);
			$IdeaSummary = format($IdeaSummary, "summary");
			$IdeaTags = format($IdeaTags, "tags");
			$IdeaBody = format($IdeaBody, "body");

			$title = $IdeaSummary;

			$previous = $path[1] - 1;
			if($previous < 1)
				$previous = 1;
			$next = $path[1] + 1;
			if($next > $last)
				$next = $last;


			$links = "<a href=\"/share/$path[1]\">Comment on this Idea</a> | <a href=\"/share/\">Share an Idea</a> | <a href=\"/find/\">Find an Idea</a> | <a href=\"/user/\">Find Someone's Ideas</a> | <a href=\"/idea/1\">First Idea</a> | <a href=\"/idea/$previous\">Previous Idea</a> | <a href=\"/idea/$next\">Next Idea</a> | <a href=\"/idea/$last\">Last Idea</a>";

			$IdeaTimeFormatted = date("F j\, Y G:i:s", $IdeaTimePosted);

			$content = "Idea Posted by: <a href=\"/user/$IdeaName\">$IdeaName</a> on $IdeaTimeFormatted.<br />Tags: $IdeaTags<hr />$IdeaBody<br /><br />";

			$query = mysql_query("SELECT `CommentTimePosted`,`CommentName`,`CommentBody` FROM `comments` WHERE `ParentIdeaID`='$path[1]' ORDER BY `CommentID` ASC");
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
		$query = mysql_query("SELECT `IdeaID` FROM `ideas` ORDER BY `IdeaID` DESC LIMIT 1");
		list($last) = @mysql_fetch_array($query);

		if($path[0] != "list")
		{
			if($path[0] != "")
				$error = "<span class=\"error\">The page you requested does not exist. For your convenience, you've been redirected to the listing of the newest 25 ideas.</span><br /><br />";
			$path[0] = "list";
		}

		if($path[1] == "")
			$path[1] = "1-25";

		elseif(!preg_match("/^[1-9][0-9]*-[1-9][0-9]*$/", $path[1]))
		{
			$error = "<span class=\"error\">The page you requested does not exist. For your convenience, you've been redirected to the listing of the newest 25 ideas.</span><br /><br />";
			$path[1] = "1-25";
		}

		$range = explode("-", $path[1]);
		
		if(($range[0] > $range[1]) || ($range[0] == $range[1]) || (($range[1] > $last) && ($range[1] != 25)))
		{
			$error = "<span class=\"error\">The page you requested does not exist. For your convenience, you've been redirected to the listing of the newest 25 ideas.</span><br /><br />";
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

		$title = "Listing ideas $range[0] to $range[1]";

		$links = "<a href=\"/share/\">Share an Idea</a> | <a href=\"/find/\">Find an Idea</a> | <a href=\"/user/\">Find Someone's Ideas</a> | <a href=\"/list/1-25\">First 25 Ideas</a> | <a href=\"/list/$previous_begin-$previous_end\">Previous 25 Ideas</a> | <a href=\"/list/$next_begin-$next_end\">Next 25 Ideas</a> | <a href=\"/list/$last_begin-$last_end\">Last 25 Ideas</a>";

		$query = mysql_query("SELECT `IdeaID`,`IdeaReplyCount`,`IdeaName`,`IdeaSummary` FROM `ideas` ORDER BY `IdeaTimeUpdated` DESC LIMIT $range[2], $range[3]");
		while(list($IdeaID, $IdeaReplyCount, $IdeaName, $IdeaSummary) = @mysql_fetch_array($query))
		{
			$IdeaSummary = format($IdeaSummary, "summary");

			if($toggle == TRUE)
			{
				$content .= "<div class=\"idea\"><a href=\"/idea/$IdeaID\">$IdeaSummary</a> by <a href=\"/user/$IdeaName\">$IdeaName</a>. Replies: $IdeaReplyCount</div>";
				$toggle = FALSE;
			}
			else
			{
				$content .= "<a href=\"/idea/$IdeaID\">$IdeaSummary</a> by <a href=\"/user/$IdeaName\">$IdeaName</a>. Replies: $IdeaReplyCount";
				$toggle = TRUE;
			}
		}
	break;
}

echo <<<CONTENT

<html>
<head>
<title>ideas.wetfish.net: $title</title>

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
div.idea { background: #294868; }
div.comment { background: #777788; padding: 2px; }
div.comment2 { background: #224466; }

</style>

</head>
<body class="body">

<a href="http://wetfish.net">Home</a> | <a href="http://things.wetfish.net">Things</a> | <a href="http://ideas.wetfish.net">Ideas</a> | <a href="http://irc.wetfish.net">IRC</a> | <a href="http://radio.wetfish.net">Radio</a>

<div style="margin-left:179px;">
<span style="font-size:17pt; font-weight:bold;"><a href="http://ideas.wetfish.net" style="color: #FFFFFF; text-decoration: none;">ideas.wetfish.net</a></span>

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
