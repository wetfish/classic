<?php

require "class.phpmailer.php";
require "class.smtp.php";
require "config.php";

function encrypt($CryptTime, $hash)
{
	$HashArray = str_split($hash);
	$CryptArray = str_split($CryptTime);

	foreach($HashArray as $character)
	{
		if($x == 2)
			$output .= $CryptArray[0];
		elseif($x == 5)
			$output .= $CryptArray[1];
		elseif($x == 7)
			$output .= $CryptArray[2];
		elseif($x == 9)
			$output .= $CryptArray[3];
		elseif($x == 16)
			$output .= $CryptArray[4];
		elseif($x == 24)
			$output .= $CryptArray[5];
		elseif($x == 27)
			$output .= $CryptArray[6];
		elseif($x == 33)
			$output .= $CryptArray[7];
		elseif($x == 34)
			$output .= $CryptArray[8];
		elseif($x == 36)
			$output .= $CryptArray[9];
		else
			$output .= $character;
		$x++;
	}

	return md5($output);
}

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


function GetQueries($Str)
{
	preg_match_all("/\s*(.*?)\s*:\s*(?:{(.*?)}|(.*?));/", $Str, $Matches);

	foreach($Matches[2] as $Key=>$Data)
	{
		if(trim($Data) == "")
			$Data = $Matches[3][$Key];

		if((trim($Matches[1][$Key]) != "") && (trim($Data) != ""))
			$Queries[strtolower(trim($Matches[1][$Key]))] = trim($Data);
	}

	return $Queries;
}

function FormatTags($tags)
{
	$TagArray = array_unique(explode(",", $tags));
	foreach($TagArray as $key => $tag)
	{
		if(trim($tag) == "")
			unset($TagArray[$key]);
		else
			$TagArray[$key] =  $_SERVER['REMOTE_ADDR'].":".strtolower(str_replace(" ", "_", trim($tag)));
	}

	return implode(", ", $TagArray);
}

function ResizeImage($inputFilename, $new_side)
{
	$exten = array_reverse(explode(".", $inputFilename));
	$exten = $exten[0];

	$imagedata = @getimagesize($inputFilename);
	$w = $imagedata[0];
	$h = $imagedata[1];

	if((($h > $w) || ($h == $w)) && ($new_side < $h))
	{
		$new_w = ($new_side / $h) * $w;
		$new_h = $new_side;
	}

	elseif(($w > $h) && ($new_side < $w))
	{
		$new_h = ($new_side / $w) * $h;
		$new_w = $new_side;
	}

	else
	{
		$new_w = $w;
		$new_h = $h;
	}


	$im2 = @ImageCreateTrueColor($new_w, $new_h);
	$ErrorArray = error_get_last();

	if($ErrorArray['type'] == 2)
		$im2 = @ImageCreateFromJpeg("uploadfail.jpg");

	else
	{
		if(($exten == "gif") || ($exten == "GIF"))
			$image = @ImageCreateFromGif($inputFilename);
		elseif(($exten == "png") || ($exten == "PNG"))
			$image = @ImageCreateFromPng($inputFilename);
		else
			$image = @ImageCreateFromJpeg($inputFilename);

		@imagecopyResampled($im2, $image, 0, 0, 0, 0, $new_w, $new_h, $imagedata[0], $imagedata[1]);
	}

	return $im2;
}


function DisplayImage($Image, $Size)
{
	if(($Image != "") && ($Size != ""))
	{
		if(!(file_exists("upload/$Image...$Size")))
		{
			$NewThumb = ResizeImage("upload/$Image", $Size);
			imagejpeg($NewThumb, "upload/$Image...$Size");
			chmod("upload/$Image...$Size", 0644);
		}

		return "<img src=\"/upload/$Image...$Size\" border=\"0\">";
	}
}

function DisplayText($Text, $Size)
{
	$characters = array("a" => "7", "b" => "7", "c" => "6", "d" => "7", "e" => "7", "f" => "4", "g" => "7", "h" => "7", "i" => "3", "j" => "4", "k" => "6", "l" => "3", "m" => "10", "n" => "7", "o" => "7", "p" => "7", "q" => "7", "r" => "5", "s" => "6", "t" => "4", "u" => "7", "v" => "6", "w" => "10", "x" => "6", "y" => "6", "z" => "6", "A" => "8", "B" => "7", "C" => "8", "D" => "8", "E" => "7", "F" => "7", "G" => "8", "H" => "8", "I" => "4", "J" => "5", "K" => "7", "L" => "6", "M" => "10", "N" => "8", "O" => "9", "P" => "6", "Q" => "9", "R" => "8", "S" => "8", "T" => "8", "U" => "8", "V" => "7", "W" => "12", "X" => "8", "Y" => "7", "Z" => "7", "1" => "7", "2" => "7", "3" => "7", "4" => "7", "5" => "7", "6" => "7", "7" => "7", "8" => "7", "9" => "7", "0" => "7", " " => "4", "." => "4", "!" => "4", "(" => "5", ")" => "5", "_" => "7", "-" => "5", "$" => 6, "?" => "6", "'" => "3", "\"" => "5", "#" => "9", ":" => "5", "," => "5");
	$length = 9;

	foreach(str_split($Text, 1) as $character)
	{
		$length += $characters[$character];

		if($length < $Size)
			$string .= $character;
		else
		{
			$string .= "...";
			break;
		}
	}

	return $string;
}

function LeapYear($y)
{
	return $y % 4 == 0 && ($y % 400 == 0 || $y % 100 != 0);
}

function DateToTimestamp($date)
{
	$months = array("january", "february", "march", "april", "may", "june", "july", "august", "september", "october", "november", "december");

	$date = str_replace("_", " ", $date);
	$date = explode(" ", $date);

	if(in_array($date[0], $months))
		$timestamp = strtotime("$date[0] $date[1] $date[2]");
	else
	{
		$time = explode(",", date("G,i,s,n,j,Y"));

		switch(str_replace("s", "", $date[1]))
		{
			case "hour":
				$time[0] = $time[0] - $date[0];
				break;

			case "minute":
				$time[1] = $time[1] - $date[0];
				break;

			case "month":
				$time[3] = $time[3] - $date[0];
				break;

			case "day":
				$time[4] = $time[4] - $date[0];
				break;

			case "year":
				$time[5] = $time[5] - $date[0];
				break;
		}

		$timestamp = mktime($time[0], $time[1], $time[2], $time[3], $time[4], $time[5]);
	}

	return $timestamp;
}

function FormatLinks($links)
{
	$last = count($links) - 1;

	foreach($links as $key=>$link)
	{
		if($link['active'] == "true")
			$linkstr .= "<a class=\"active\" href=\"{$link['url']}\">{$link['text']}</a>";
		else
			$linkstr .= "<a href=\"{$link['url']}\">{$link['text']}</a>";

		if($key != $last)
			$linkstr .= " | ";
	}

	return $linkstr;
}

@mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD) or die("HOLY SHIT THE DATABASE EXPLODED FUCK");
@mysql_select_db(MYSQL_DATABASE) or die("Unnable to connect to database.");

$path = explode("/", strtolower($_GET['hf3HAdjAelsE']));
$title = "weedhat.org";
$identity = "Flash";

$nav[0]['active'] = "false";
$nav[0]['url'] = "/share";
$nav[0]['text'] = "Share a Hat";
$nav[1]['active'] = "false";
$nav[1]['url'] = "/browse";
$nav[1]['text'] = "Browse Hats";
$nav[2]['active'] = "false";
$nav[2]['url'] = "/faq";
$nav[2]['text'] = "FAQ";

$nav2[0]['active'] = "false";
$nav2[0]['url'] = "/browse/advanced/creator:$identity;";
$nav2[0]['text'] = "$identity";
$nav2[1]['active'] = "false";
$nav2[1]['url'] = "/login";
$nav2[1]['text'] = "Login";
$nav2[2]['active'] = "false";
$nav2[2]['url'] = "/register";
$nav2[2]['text'] = "Register";

$AccountCookie = trim(str_replace(array("\"", "'", "`", ";"), "", $_COOKIE['account']));
$PasswordCookie = trim(str_replace(array("\"", "'", "`", ";"), "", $_COOKIE['password']));

if(($AccountCookie != "") && ($PasswordCookie != ""))
{
	$query = mysql_query("SELECT `AccountLoginTime`,`AccountPassword`,`AccountStatus` FROM `Accounts` WHERE `AccountName`='$AccountCookie'");
	list($AccountLoginTime, $AccountPassword, $AccountStatus) = @mysql_fetch_array($query);

	if(encrypt($AccountLoginTime, $AccountPassword) == $PasswordCookie)
	{
		$identity = $AccountCookie;

		$nav2[0]['url'] = "/browse/advanced/creator:".str_replace(" ", "_", $identity).";";
		$nav2[0]['text'] = "$identity";
		$nav2[1]['url'] = "/logout";
		$nav2[1]['text'] = "Logout";

		unset($nav2[2]);
	}
}

switch($path[0])
{
	case "share":
		$title = "remember kids, sharing is caring";

		$nav[0]['active'] = "true";

		$queries = GetQueries($path[1]);

		$query = mysql_query("SELECT `AccountPostTime`,`AccountStatus` FROM `Accounts` WHERE `AccountName`='$identity'");
		list($AccountPostTime, $AccountStatus) = @mysql_fetch_array($query);

		if($identity == "Flash")
		{
			preg_match_all("/(?:^| )([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}):(.*?)(?:,|$)/", $AccountStatus, $StatusMatches);

			if(in_array($_SERVER['REMOTE_ADDR'], $StatusMatches[1]))
			{
				$ArrayLocation = array_search($_SERVER['REMOTE_ADDR'], $StatusMatches[1]);

				if($StatusMatches[2][array_search($_SERVER['REMOTE_ADDR'], $StatusMatches[1])] == "banned")
				{
					$abadthing = TRUE;
					$content = "<span class=\"error\">Your account is not allowed to share Weedhats.</span>";
				}
			}

			preg_match_all("/(?:^| )([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}):(.*?)(?:,|$)/", $AccountPostTime, $PostTimeMatches);

			if(in_array($_SERVER['REMOTE_ADDR'], $PostTimeMatches[1]))
			{
				if($PostTimeMatches[2][array_search($_SERVER['REMOTE_ADDR'], $PostTimeMatches[1])] > (time() - 900))
				{
					$abadthing = TRUE;
					$content = "<span class=\"error\">Please wait a few minutes before sharing another Weedhat.</span>";
				}
			}
		}
		else
		{
			if($AccountStatus != "registered")
			{
				$abadthing = TRUE;
				$content = "<span class=\"error\">Your account is not allowed to share Weedhats.</span>";
			}

			if($AccountPostTime > (time() - 180))
			{
				$abadthing = TRUE;
				$content = "<span class=\"error\">Please wait a few minutes before sharing another Weedhat.</span>";
			}
		}

		if((!empty($_POST)) && ($abadthing !== TRUE))
		{
			if(!empty($queries['reply']))
			{
				$query = mysql_query("SELECT `HatDeleted` FROM `Hats` WHERE `HatID`='{$queries['reply']}'");
				list($ReplyDeleted) = @mysql_fetch_array($query);

				if($ReplyDeleted > 1)
					$errors['reply'] = "<span class=\"error\">The hat you tried to reply to no longer exists.</span>";
				else
					$ReplyID = $queries['reply'];
			}

			$pathinfo = pathinfo($_FILES['hat']['name']);
			$pathinfo['extension'] = strtolower($pathinfo['extension']);

			if($_FILES['hat']['error'] == 4)
				$errors['hat'] = "<span class=\"error\">You must upload a hat!</span>";
			elseif($_FILES['hat']['error'] != 0)
				$errors['hat'] = "<span class=\"error\">Something bad happened...try again?</span>";
			elseif($_FILES['hat']['size'] > 1048576)
				$errors['hat'] = "<span class=\"error\">Your hat is too big!</span>";
			elseif(($pathinfo['extension'] != "jpg") && ($pathinfo['extension'] != "jpeg") && ($pathinfo['extension'] != "png") && ($pathinfo['extension'] != "gif"))
				$errors['hat'] = "<span class=\"error\">You can't upload that kind of file!</span>";

			if(!preg_match("/^[a-z0-9 \.\!\(\)\_\-\$\?\'\"\#\:,]*$/i", $_POST['name']))
				$errors['name'] = "<span class=\"error\">Don't put funny characters in your hat's name.</span>";
			elseif(strlen($_POST['name']) > 32)
				$errors['name'] = "<span class=\"error\">Your hat's name is too long...</span>";

			if(!preg_match("/^[a-z0-9 ,]*$/i", $_POST['tags']))
				$errors['tags'] = "<span class=\"error\">Don't put funny characters in your tags.</span>";
			elseif(strlen($_POST['tags']) > 100)
				$errors['tags'] = "<span class=\"error\">I think you have too many tags...</span>";

			if(empty($errors))
			{
				$HatCreationTime = time();
				$HatPruneTime = "";
				$HatURL = md5(rand(1, 1000).rand(1, 1000).rand(1, 1000).rand(1, 1000).rand(1, 1000)).".".$pathinfo['extension'];
				move_uploaded_file($_FILES['hat']['tmp_name'], "upload/$HatURL");
				chmod("upload/$Filename", 0644);
				if(trim($_POST['name']) == "")
					$HatName = addslashes("I'm an unimaginative fuck.");
				else
					$HatName = addslashes($_POST['name']);
				$HatCreator = $identity;
				$HatCreatorIP = $_SERVER['REMOTE_ADDR'];
				$HatCategory = "";
				$HatRaters = "";
				$HatRating = 0;
				$HatReplyTo = $ReplyID;
				$HatReplies = "";
				$HatTags = FormatTags($_POST['tags']);
				$HatDeleted = 0;

				mysql_query("INSERT INTO `Hats` VALUES('NULL', '$HatCreationTime', '$HatPruneTime', '$HatURL', '$HatName', '$HatCreator', '$HatCreatorIP', '$HatCategory', '$HatRaters', '$HatRating', '$HatReplyTo', '$HatReplies', '$HatTags', '$HatDeleted')");

				$query = mysql_query("SELECT `HatID` FROM `Hats` WHERE `HatURL`='$HatURL'");
				list($HatID) = @mysql_fetch_array($query);

				$query = mysql_query("SELECT `AccountHats` FROM `Accounts` WHERE `AccountName`='$identity'");
				list($AccountHats) = @mysql_fetch_array($query);

				if($AccountHats != "")
					mysql_query("UPDATE `Accounts` SET `AccountHats` = CONCAT(`AccountHats`, ', $HatID') WHERE `AccountName`='$identity'");
				else
					mysql_query("UPDATE `Accounts` SET `AccountHats`='$HatID' WHERE `AccountName`='$identity'");

				mysql_query("UPDATE `Accounts` SET `AccountPostTime`='$HatCreationTime' WHERE `AccountName`='$identity'");

				if($ReplyID != "")
				{
					if($HatReplies != "")
						mysql_query("UPDATE `Hats` SET `HatReplies` = CONCAT(`HatReplies`, ', $HatID') WHERE `HatID`='$ReplyID'");
					else
						mysql_query("UPDATE `Hats` SET `HatReplies`='$HatID' WHERE `HatID`='$ReplyID'");
				}

				foreach(explode(", ", $HatTags) as $tag)
				{
					$tag = substr($tag, strpos($tag, ":") + 1);

					$query = mysql_query("SELECT `TagName`,`TagHats`,`TagOccurrence` FROM `Tags` WHERE `TagName`='$tag'");
					list($TagName, $TagHats, $TagOccurrence) = @mysql_fetch_array($query);

					if(!empty($TagName))
					{
						$TagOccurrence++;

						mysql_query("UPDATE `Tags` SET `TagHats` = CONCAT(`TagHats`, ', $HatID') WHERE `TagName`='$tag'");
						mysql_query("UPDATE `Tags` SET `TagOccurrence`='$TagOccurrence' WHERE `TagName`='$tag'");
					}

					elseif($tag != "")
						mysql_query("INSERT INTO `Tags` VALUES('NULL', '$tag', '$HatID', '1')");
				}

				$content = "<meta http-equiv=\"refresh\" content=\"2;url=/hat/$HatID\">Thank you for sharing...Lord Flash is pleased.";
			}

		}


		if((((!empty($_POST)) && (!empty($errors))) || (empty($_POST))) && ($abadthing !== TRUE))
		{
			$content = "<form action=\"/share/$path[1]\" enctype=\"multipart/form-data\" method=\"post\"><table cellpadding=\"0\">";

			if(!empty($queries['reply']))
			{
				if(!empty($errors['reply']))
					$content .= "<tr><td colspan=\"2\">{$errors['reply']}</td></tr>";

				$content .= "<tr><td>Reply To:</td><td><b>{$queries['reply']}</b></td></tr>";
			}

			if(!empty($errors['hat']))
				$content .= "<tr><td colspan=\"2\">{$errors['hat']}</td></tr>";
			$content .= "<tr><td>Picture:</td><td><input type=\"file\" name=\"hat\" size=\"32\"></td></tr><tr><td>&nbsp;</td><td><span style=\"font-size: 8pt; font-style: italic;\">Allowed file types: [jpg] [png] [gif].<br />Maximum File Size: [1MB].</span></td></tr>";

			if(!empty($errors['name']))
				$content .= "<tr><td colspan=\"2\">{$errors['name']}</td></tr>";
			$content .= "<tr><td>Hat Name:</td><td><input type=\"text\" name=\"name\" value=\"{$_POST['name']}\" size=\"32\" maxlength=\"32\"></td></tr>";

			if(!empty($errors['tags']))
				$content .= "<tr><td colspan=\"2\">{$errors['tags']}</td></tr>";
			$content .= "<tr><td>Tags:</td><td><input type=\"text\" name=\"tags\" value=\"{$_POST['tags']}\" size=\"32\" maxlength=\"100\"></td></tr><tr><td>&nbsp;</td><td><span style=\"font-size: 8pt; font-style: italic;\">Separate tags with commas.</span></td></tr>";

			$content .= "<tr><td colspan=\"2\"><input type=\"submit\" value=\"Submit\"></td></tr>";

			$content .= "</table></form>";
		}

		break;

	case "browse":
		$title = "look at all the pretty creations";

		$queries = GetQueries($path[2]);

		if(empty($queries))
			$nav[1]['active'] = "true";

		if($queries['creator'] == strtolower($identity))
			$nav2[0]['active'] = "true";

		$nav3[0]['active'] = "false";
		$nav3[0]['url'] = "/browse";
		$nav3[0]['text'] = "Recent";
		$nav3[1]['active'] = "false";
		$nav3[1]['url'] = "/browse/random";
		$nav3[1]['text'] = "Random";
		$nav3[2]['active'] = "false";
		$nav3[2]['url'] = "/browse/advanced";
		$nav3[2]['text'] = "Advanced";

		switch($path[1])
		{
			case "random":
				$nav3[1]['active'] = "true";

				$subcontent = "<br /><div class=\"cntn\">";

				$query = mysql_query("SELECT `HatID`, `HatURL`, `HatName`, `HatCreator`, `HatRating`, `HatReplies` FROM `Hats` WHERE `HatDeleted` BETWEEN '0' AND '1' ORDER BY RAND() LIMIT 20");
				while(list($HatID, $HatURL, $HatName, $HatCreator, $HatRating, $HatReplies) = @mysql_fetch_array($query))
				{
					$HatNameDisplay = DisplayText($HatName, 170);
					$HatCreatorDisplay = DisplayText($HatCreator, 130);
					if($HatReplies != "")
						$HatReplies = count(explode(", ", $HatReplies));
					else
						$HatReplies = 0;

					$subcontent .= "<div class=\"indvcntn\"><div class=\"name\"><a href=\"/hat/$HatID\">$HatNameDisplay</a></div><div class=\"img\"><a href=\"/hat/$HatID\">".DisplayImage($HatURL, 150)."</a></div><div class=\"name\">by: <a href=\"/browse/advanced/creator:".str_replace(" ", "_", $HatCreator).";\">$HatCreatorDisplay</a></div><div class=\"r_left\">Rating: $HatRating</div><div class=\"r_right\">Replies: $HatReplies</div></div>";
				}

				$subcontent .= "</div>";
				break;

			case "advanced":
				if(!empty($_POST))
				{
					foreach($_POST as $key=>$value)
					{
						if(trim($value) == "")
							unset($_POST[$key]);
					}
				}

				if((!empty($queries)) || (!empty($_POST)))
				{
					$querystr = "";

					if((!empty($_POST)) && (empty($queries)))
					{
						if(trim($_POST['id']) != "")
							$queries['id'] = strtolower(trim($_POST['id']));

						if(trim($_POST['date']) != "")
							$queries['date'] = strtolower(trim($_POST['date']));

						if(trim($_POST['rating']) != "")
							$queries['rating'] = strtolower(trim($_POST['rating']));

						if(trim($_POST['replies']) != "")
							$queries['replies'] = strtolower(trim($_POST['replies']));

						if(trim($_POST['name']) != "")
							$queries['name'] = strtolower(trim($_POST['name']));

						if(trim($_POST['creator']) != "")
							$queries['creator'] = strtolower(trim($_POST['creator']));

						if(trim($_POST['category']) != "")
							$queries['category'] = strtolower(trim($_POST['category']));

						if(trim($_POST['tag']) != "")
							$queries['tag'] = strtolower(trim($_POST['tag']));
					}

					foreach($queries as $key=>$query)
					{
						if($key == "id")
						{
							if(strpos($query, "-") === FALSE)
							{
								if(!is_numeric($query))
									$errors['id'] = "Error: Non-numeric ID.";
								else
									$querystr .= "id:$query;";
							}

							else
							{
								list($id1, $id2) = explode("-", $query, 2);

								if((!is_numeric($id1)) || (!is_numeric($id2)))
									$errors['id'] = "Error: Non-numeric ID.";
								elseif($id1 > $id2)
									$querystr .= "id:$id2-$id1;";
								else
									$querystr .= "id:$id1-$id2;";
							}
						}

						elseif($key == "date")
						{
							if(strpos($query, "-") === FALSE)
							{
								$date = str_replace("_", " ", $query);
								$date = explode(" ", $date);

								$units = array("minute", "minutes", "hour", "hours", "day", "days", "week", "weeks", "month", "months", "year", "years");
								$months = array("january", "february", "march", "april", "may", "june", "july", "august", "september", "october", "november", "december");
								$maxdays = array("january" => "31", "february" => "28", "march" => "31", "april" => "30", "may" => "31", "june" => "30", "july" => "31", "august" => "31", "september" => "30", "october" => "31", "november" => "30", "december" => "31");

								if(count($date) == 1)
								{
									if(!is_numeric($date[0]))
										$errors['date'] = "Error: Invalid Unix timestamp.";
									elseif(strlen($date[0]) != 10)
										$errors['date'] = "Error: Invalid timestamp length.";
									else
										$querystr .= "date:$date;";
								}
								elseif(count($date) == 3)
								{
									if(in_array($date[0], $months))
									{
										if(!is_numeric($date[1]))
											$errors['date'] = "Error: Invalid date.<br /><br />Dates must use the following syntax:<br/>1. <b>(Number) (Hours/Days/Weeks/Months) Ago</b><br />2. <b>(Month) (Numeric Date) (Numeric Year)</b><br />3. <b>(Unix Timestamp)</b>";
										elseif(!is_numeric($date[2]))
											$errors['date'] = "Error: Since when are there non-numeric years?";
										elseif($date[2] < 2007)
											$errors['date'] = "Error: Stop trying to search for shit from before Weedhat was even conceived.";
										elseif($maxdays[$date[0]] < $date[1])
										{
											if($date[0] != "february")
												$errors['date'] = "Error: That month doesn't have that many days.";
											else
											{
												if((!LeapYear($date[2])) || ($date[1] > 29))
													$errors['date'] = "Error: That month doesn't have that many days.";
											}
										}
										else
											$querystr .= "date:$date[0]_$date[1]_$date[2];";
									}
									else
									{
										if(!is_numeric($date[0]))
											$errors['date'] = "Error: Invalid date.<br /><br />Dates must use the following syntax:<br/>1. <b>(Number) (Hours/Days/Weeks/Months) Ago</b><br />2. <b>(Month) (Numeric Date) (Numeric Year)</b><br />3. <b>(Unix Timestamp)</b>";
										elseif(!in_array($date[1], $units))
											$errors['date'] = "Error: Invalid date.<br /><br />Dates must use the following syntax:<br/>1. <b>(Number) (Hours/Days/Weeks/Months) Ago</b><br />2. <b>(Month) (Numeric Date) (Numeric Year)</b><br />3. <b>(Unix Timestamp)</b>";
										elseif($date[2] != "ago")
											$errors['date'] = "Error: Invalid date.<br /><br />Dates must use the following syntax:<br/>1. <b>(Number) (Hours/Days/Weeks/Months) Ago</b><br />2. <b>(Month) (Numeric Date) (Numeric Year)</b><br />3. <b>(Unix Timestamp)</b>";
										else
											$querystr .= "date:$date[0]_$date[1]_$date[2];";
									}
								}
							}

							else
							{
								list($date1, $date2) = explode("-", $query, 2);

								$date1 = str_replace("_", " ", $date1);
								$date1 = explode(" ", $date1);

								$date2 = str_replace("_", " ", $date2);
								$date2 = explode(" ", $date2);

								$units = array("minute", "minutes", "hour", "hours", "day", "days", "week", "weeks", "month", "months", "year", "years");
								$months = array("january", "february", "march", "april", "may", "june", "july", "august", "september", "october", "november", "december");
								$maxdays = array("january" => "31", "february" => "28", "march" => "31", "april" => "30", "may" => "31", "june" => "30", "july" => "31", "august" => "31", "september" => "30", "october" => "31", "november" => "30", "december" => "31");

								if((count($date1) == 1) && (count($date2) == 1))
								{
									if((!is_numeric($date1[0])) || (!is_numeric($date2[0])))
										$errors['date'] = "Error: Invalid Unix timestamp.";
									elseif((strlen($date1[0]) != 10) || (strlen($date2[0]) != 10))
										$errors['date'] = "Error: Invalid timestamp length.";
									else
									{
										if($date1 < $date2)
											$querystr .= "id:$date2-$date1;";
										else
											$querystr .= "id:$date1-$date2;";
									}
								}
								elseif((count($date1) == 3) && (count($date2) == 3))
								{
									if(in_array($date1[0], $months))
									{
										if((!is_numeric($date1[1])) || (!is_numeric($date2[1])))
											$errors['date'] = "Error: Invalid date.<br /><br />Dates must use the following syntax:<br/>1. <b>(Number) (Hours/Days/Weeks/Months) Ago</b><br />2. <b>(Month) (Numeric Date) (Numeric Year)</b><br />3. <b>(Unix Timestamp)</b>";
										elseif((!is_numeric($date1[2])) || (!is_numeric($date2[2])))
											$errors['date'] = "Error: Since when are there non-numeric years?";
										elseif(($date1[2] < 2007) || ($date2[2] < 2007))
											$errors['date'] = "Error: Stop trying to search for shit from before Weedhat was even conceived.";
										elseif($maxdays[$date1[0]] < $date1[1])
										{
											if($date1[0] != "february")
												$errors['date'] = "Error: That month doesn't have that many days.";
											else
											{
												if((!LeapYear($date1[2])) || ($date1[1] > 29))
													$errors['date'] = "Error: That month doesn't have that many days.";
											}
										}
										elseif($maxdays[$date2[0]] < $date2[1])
										{
											if($date2[0] != "february")
												$errors['date'] = "Error: That month doesn't have that many days.";
											else
											{
												if((!LeapYear($date2[2])) || ($date2[1] > 29))
													$errors['date'] = "Error: That month doesn't have that many days.";
											}
										}
										else
										{
											if(DateToTimestamp("$date1[0] $date1[1] $date1[2]") < DateToTimestamp("$date2[0] $date2[1] $date2[2]"))
												$querystr .= "date:$date2[0]_$date2[1]_$date2[2]-$date1[0]_$date1[1]_$date1[2];";
											else
												$querystr .= "date:$date1[0]_$date1[1]_$date1[2]-$date2[0]_$date2[1]_$date2[2];";
										}
									}
									else
									{
										if((!is_numeric($date1[0])) || (!is_numeric($date2[0])))
											$errors['date'] = "Error: Invalid date.<br /><br />Dates must use the following syntax:<br/>1. <b>(Number) (Hours/Days/Weeks/Months) Ago</b><br />2. <b>(Month) (Numeric Date) (Numeric Year)</b><br />3. <b>(Unix Timestamp)</b>";
										elseif((!in_array($date1[1], $units)) || (!in_array($date2[1], $units)))
											$errors['date'] = "Error: Invalid date.<br /><br />Dates must use the following syntax:<br/>1. <b>(Number) (Hours/Days/Weeks/Months) Ago</b><br />2. <b>(Month) (Numeric Date) (Numeric Year)</b><br />3. <b>(Unix Timestamp)</b>";
										elseif(($date1[2] != "ago") || ($date2[2] != "ago"))
											$errors['date'] = "Error: Invalid date.<br /><br />Dates must use the following syntax:<br/>1. <b>(Number) (Hours/Days/Weeks/Months) Ago</b><br />2. <b>(Month) (Numeric Date) (Numeric Year)</b><br />3. <b>(Unix Timestamp)</b>";
										else
										{
											if(DateToTimestamp("$date1[0] $date1[1] $date1[2]") < DateToTimestamp("$date2[0] $date2[1] $date2[2]"))
												$querystr .= "date:$date2[0]_$date2[1]_$date2[2]-$date1[0]_$date1[1]_$date1[2];";
											else
												$querystr .= "date:$date1[0]_$date1[1]_$date1[2]-$date2[0]_$date2[1]_$date2[2];";
										}
									}
								}
								else
									$errors['date'] = "Error: Date types don't match.";
							}
						}

						elseif($key == "rating")
						{
							if(strpos($query, "-") === FALSE)
							{
								if(!is_numeric($query))
									$errors['rating'] = "Error: Non-numeric rating.";
								else
									$querystr .= "rating:$query;";
							}

							else
							{
								list($rating1, $rating2) = explode("-", $query, 2);

								if((!is_numeric($rating1)) || (!is_numeric($rating2)))
									$errors['rating'] = "Error: Non-numeric rating.";
								elseif($rating1 > $rating2)
									$querystr .= "rating:$rating2-$rating1;";
								else
									$querystr .= "rating:$rating1-$rating2;";
							}
						}

						elseif($key == "replies")
						{
							if(strpos($query, "-") === FALSE)
							{
								if(!is_numeric($query))
									$errors['replies'] = "Error: Non-numeric reply.";
								else
									$querystr .= "replies:$query;";
							}

							else
							{
								list($replies1, $replies2) = explode("-", $query, 2);

								if((!is_numeric($replies1)) || (!is_numeric($replies2)))
									$errors['replies'] = "Error: Non-numeric reply.";
								elseif($replies1 > $replies2)
									$querystr .= "replies:$replies2-$replies1;";
								else
									$querystr .= "replies:$replies1-$replies2;";
							}
						}

						elseif($key == "name")
						{
							if(!preg_match("/^[a-z0-9 \.\!\(\)\_\-\$\?\'\"\#\:,]*$/i", $query))
								$errors['name'] = "Error: Hats can't have names with those kinds of characters.";
							elseif(strlen($query) > 32)
								$errors['name'] = "Error: That hat's name is too long...";
							else
								$querystr .= "name:".str_replace(" ", "_", $query).";";
						}

						elseif($key == "creator")
						{
							if(!preg_match("/^[a-z0-9 -_]*$/i", $query))
								$errors['creator'] = "Error: Account names can't have those kinds of characters.";
							elseif(strlen($query) > 32)
								$errors['creator'] = "Error: Account names can't be that long.";
							else
								$querystr .= "creator:".str_replace(" ", "_", $query).";";
						}

						elseif($key == "category")
						{
							if(!preg_match("/^[a-z0-9 ]*$/i", $query))
								$errors['category'] = "Error: Categories can't have those kinds of character.";
							elseif(strlen($query) > 100)
								$errors['category'] = "Error: Categories can't be that long.";
							else
								$querystr .= "category:".str_replace(" ", "_", $query).";";
						}

						elseif($key == "tag")
						{
							if(!preg_match("/^[a-z0-9 _]*$/i", $query))
								$errors['tag'] = "Error: Tags can't have those kinds of character.";
							elseif(strlen($query) > 100)
								$errors['tag'] = "Error: Tags can't be that long.";
							else
								$querystr .= "tag:".str_replace(" ", "_", $query).";";
						}
					}

					if(!empty($errors))
					{
						foreach($errors as $error)
							$subcontent .= "$error<br />";
					}
					elseif($querystr != $path[2])
						$subcontent .= "<meta http-equiv=\"refresh\" content=\"2;url=/browse/advanced/$querystr\">Processing...<br />Thinking...<br />Contemplating...";
					else
					{
						$querystr = "";

						foreach($queries as $key=>$query)
						{
							if($key == "id")
							{
								if(strpos($query, "-") === FALSE)
									$querystr .= "`HatID`='$query'";
								else
								{
									list($id1, $id2) = explode("-", $query, 2);

									$querystr .= "`HatID` BETWEEN '$id1' AND '$id2'";
								}
							}

							elseif($key == "date")
							{
								if(strpos($query, "-") === FALSE)
								{
									if(!is_numeric($query))
										$query = DateToTimestamp($query);
									$querystr .= "`HatCreationTime`='$query'";
								}
								else
								{
									list($date1, $date2) = explode("-", $query, 2);

									if(!is_numeric($date1))
									{
										$date1 = DateToTimestamp($date1);
										$date2 = DateToTimestamp($date2);
									}

									$querystr .= "`HatID` BETWEEN '$date1' AND '$date2'";
								}
							}

							elseif($key == "rating")
							{
								if(strpos($query, "-") === FALSE)
									$querystr .= "`HatRating`='$query'";
								else
								{
									list($rating1, $rating2) = explode("-", $query, 2);

									$querystr .= "`HatRating` BETWEEN '$rating1' AND '$rating2'";
								}
							}

							elseif($key == "replies")
							{
								if(strpos($query, "-") === FALSE)
									$querystr .= "`HatReplies`='$query'";
								else
								{
									list($replies1, $replies2) = explode("-", $query, 2);

									$querystr .= "`HatReplies` BETWEEN '$replies1' AND '$replies2'";
								}
							}

							elseif($key == "name")
								$querystr .= "`HatName`='".addslashes($query)."'";

							elseif($key == "creator")
								$querystr .= "`HatCreator`='".str_replace("_", " ", $query)."'";

							elseif($key == "category")
								$querystr .= "`HatCategory`='$query'";

							elseif($key == "tag")
								$querystr .= "`HatTags` LIKE CONVERT(_utf8 '%$query%' USING latin1) COLLATE latin1_swedish_ci";

							$querystr .= " AND ";
						}

						$subcontent = "<br /><div class=\"cntn\">";

						$query = mysql_query("SELECT `HatID`, `HatURL`, `HatName`, `HatCreator`, `HatRating`, `HatReplies` FROM `Hats` WHERE $querystr `HatDeleted` BETWEEN '0' AND '1' ORDER BY `HatID` DESC LIMIT 100");
						while(list($HatID, $HatURL, $HatName, $HatCreator, $HatRating, $HatReplies) = @mysql_fetch_array($query))
						{
							$result = TRUE;

							$HatNameDisplay = DisplayText($HatName, 170);
							$HatCreatorDisplay = DisplayText($HatCreator, 130);
							if($HatReplies != "")
								$HatReplies = count(explode(", ", $HatReplies));
							else
								$HatReplies = 0;

							$subcontent .= "<div class=\"indvcntn\"><div class=\"name\"><a href=\"/hat/$HatID\">$HatNameDisplay</a></div><div class=\"img\"><a href=\"/hat/$HatID\">".DisplayImage($HatURL, 150)."</a></div><div class=\"name\">by: <a href=\"/browse/advanced/creator:".str_replace(" ", "_", $HatCreator).";\">$HatCreatorDisplay</a></div><div class=\"r_left\">Rating: $HatRating</div><div class=\"r_right\">Replies: $HatReplies</div></div>";
						}

						if($result !== TRUE)
							$subcontent .= "Error: No matches found.";

						$subcontent .= "</div>";
					}
				}

				else
				{
					$nav3[2]['active'] = "true";

					$subcontent = "<form action=\"/browse/advanced\" name=\"search\" method=\"post\"><table cellpadding=\"0\">";

					$subcontent .= "<tr><td>Hat ID:</td><td><input type=\"text\" name=\"id\" size=\"16\"></td></tr>";
					$subcontent .= "<tr><td>Date Added:</td><td><input type=\"text\" name=\"date\" size=\"16\"></td></tr>";
					$subcontent .= "<tr><td colspan=\"2\"><span style=\"font-size: 8pt; font-style: italic;\">eg; 1 month ago, October 25 2007, or 1193365118</span></td></tr>";
					$subcontent .= "<tr><td>Rating:</td><td><input type=\"text\" name=\"rating\" size=\"16\"></td></tr>";
					$subcontent .= "<tr><td>Replies:</td><td><input type=\"text\" name=\"replies\" size=\"16\"></td></tr>";
					$subcontent .= "<tr><td colspan=\"2\"><span style=\"font-size: 8pt; font-style: italic;\">Note: All numeric fields support searching by range (eg; Hat ID: 390-420).</span></td></tr>";

					$subcontent .= "<tr><td colspan=\"2\">&nbsp;</td></tr>";

					$subcontent .= "<tr><td>Hat Name:</td><td><input type=\"text\" name=\"name\" size=\"32\" maxlength=\"32\"></td></tr>";
					$subcontent .= "<tr><td>Creator:</td><td><input type=\"text\" name=\"creator\" size=\"32\" maxlength=\"32\"></td></tr>";
					$subcontent .= "<tr><td>Category:</td><td><input type=\"text\" name=\"category\" size=\"32\" maxlength=\"32\"></td></tr>";
					$subcontent .= "<tr><td>Tag:</td><td><input type=\"text\" name=\"tag\" size=\"32\" maxlength=\"32\"></td></tr>";

					$subcontent .= "<tr><td colspan=\"2\"><input type=\"submit\" value=\"Submit\"></td></tr>";

					$subcontent .= "</table></form>";
				}

				break;
			default:
				$nav3[0]['active'] = "true";

				$subcontent = "<br /><div class=\"cntn\">";

				$query = mysql_query("SELECT `HatID`, `HatURL`, `HatName`, `HatCreator`, `HatRating`, `HatReplies` FROM `Hats` WHERE `HatDeleted` BETWEEN '0' AND '1' ORDER BY `HatID` DESC LIMIT 20");
				while(list($HatID, $HatURL, $HatName, $HatCreator, $HatRating, $HatReplies) = @mysql_fetch_array($query))
				{
					$HatNameDisplay = DisplayText($HatName, 170);
					$HatCreatorDisplay = DisplayText($HatCreator, 130);
					if($HatReplies != "")
						$HatReplies = count(explode(", ", $HatReplies));
					else
						$HatReplies = 0;

					$subcontent .= "<div class=\"indvcntn\"><div class=\"name\"><a href=\"/hat/$HatID\">$HatNameDisplay</a></div><div class=\"img\"><a href=\"/hat/$HatID\">".DisplayImage($HatURL, 150)."</a></div><div class=\"name\">by: <a href=\"/browse/advanced/creator:".str_replace(" ", "_", $HatCreator).";\">$HatCreatorDisplay</a></div><div class=\"r_left\">Rating: $HatRating</div><div class=\"r_right\">Replies: $HatReplies</div></div>";
				}

				$subcontent .= "</div>";
				break;
		}

		$nav3 = FormatLinks($nav3);

		$content = "<div style=\"margin-right: 10px; margin-top: -19px; text-align: right;\">Browse by: $nav3</div>$subcontent";
		break;

	case "faq":
		$title = "no question is too stupid if you can convince 100 other people to ask it with you";		

		$nav[2]['active'] = "true";

		$content = <<<CONTENT
<div style="margin-left: 4px;">
<span style="font-size: 14pt; font-weight: bold;">General:</span>
<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#whatis">What is a weedhat?</a>
<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#thepoint">What's the point of all this?</a>
<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#noweed">What if I don't have any weed?</a>
<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#comments">Why can't we post comments?</a>
<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#flash">Why is my name Flash?</a>
<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#what">What? That doesn't explain anything!</a>

<br /><br />

<span style="font-size: 14pt; font-weight: bold;">Registration:</span>
<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#password">Why should I give my password to some website about weed?</a>
<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#wheremail">Where's my authentication email?</a>

<br /><br/>

<span style="font-size: 14pt; font-weight: bold;">Rules:</span>
<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#noshit">No posting shit that isn't a weedhat.</a>
<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#replyorganization">Keep multiple pictures of the same hat together by replying to the first image you uploaded.</a>
<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#unrelatedreply">Don't make unrelated replies without good reason.</a>

<hr />

<br /><span style="font-size: 14pt; font-weight: bold;">General:</span>
<br /><a name="whatis" href="#">What is a weedhat?</a>
<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Imagine this scenario: you're sittin' around, getting high, and you see something. Just, anything. Y'know, you look at it, and it just clicks. It <i>belongs</i> on your head. That, my friend, is a weedhat. It doesn't really matter what it is, so long as it's on your head. Just try to make it awesome.
<br /><br />

<br /><a name="thepoint" href="#">What's the point of all this?</a>
<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Well, y'see, I've got this website called wetfish.net and we were hanging out on IRC like we usually do, and some people were talking about how they wanted a place to post art. We had an imageboard, but it had fallen out of usage, so I made an image to put on the main page to serve as a big advertisement for the art board. I was dicking around in photoshop when I made it and I drew a little potleaf in a part of it, to which TheSaladCaper asked if it was really necessary and anonymass concurred, saying "What's this look like, fucking weed hat.org?". Get it? It's like wet-fish (adjective-noun) except with weed, and HAT! Genius, I say. But really, enough of this etymology bullshit.<br /><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Weedhat is a place to make friends, why not? Yes, a place to have fun and make friends, by putting stupid shit on your head and taking a picture. Convincing your friends to do stupid shit is pretty fun too. <b>That is the point of this website.</b>
<br /><br />

<br /><a name="noweed" href="#">What if I don't have any weed?</a>
<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;I feel your pain, and I offer my words of solidarity. I wish there was more I could do, but if you're really completely out, at least try to be on something when you come up with the idea for your hat. Salvia is a nice legal alternative*. Hell, you could robotrip if you're into that. But please, just try to be creative.<br /><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*<span style="font-size: 8pt; font-style: italic;">Note: Salvia is nothing like weed in terms of how it affects the body. Rather, it is only a nice legal alternative in that you will be able to understand the weedhat creation process in a similar manner. What I mean is that you can't be sober when you post a weedhat, ok? <b>IS THAT GOOD ENOUGH FOR YOU ANDREW? <u>FUCK</u></b></span>

<br /><br />


<br /><a name="comments" href="#">Why can't we post comments?</a>
<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Is the name of this website weeddiscussion.org? No. It isn't. If it is, someone registered the domain and redirected it to this server as a mean joke. Really, if you want to talk about shit, just go to <a href="http://wetfish.net">wetfish</a>. Wetfish is for general discussion about whatever you want. Weedhat is for posting weedhats. Simple as that.
<br /><br />

<br /><a name="flash" href="#">Why is my name Flash?</a>
<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;This is why.
<pre style="padding: 10px;">&lt;red> and is Flash XD santsu, haha yea he
&lt;red> &lt;Flash> xd thing from this guy I demand
&lt;red> &lt;Flash> xd POLTERGEIST
&lt;red> &lt;Flash> xd nothing.
&lt;red> &lt;Flash> xd AGAIN guys.
&lt;red> &lt;Flash> xd http://dat.4chan.org/r/src/1146978287733.png
&lt;red> &lt;Flash> xd Poltergiest
&lt;red> &lt;Flash> xd Poltergiest
&lt;red> &lt;Flash> xd i think that are you have no damage
&lt;red> &lt;Flash> xd i be uuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuup?
&lt;red> &lt;Flash> xd SPLIT'D.
&lt;red> &lt;Flash> xd i meant to be masochism :( fuk ur
&lt;red> &lt;Flash> xd part &lt;3 lion king sexy.
&lt;red> &lt;Flash> xd YOURE checking accounts (that were renting
&lt;red> &lt;Flash> xd nothing.
&lt;red> &lt;Flash> xd i crave it feels like
&lt;red> &lt;Flash> xd Poltergiest
&lt;red> &lt;Flash> xd i bought out so bad and nothing to beta
&lt;red> &lt;Flash> xd i BE hawt.
&lt;red> &lt;Flash> xd http://dat.4chan.org/r/src/1146978287733.png
&lt;red> &lt;Flash> xd i LEFT my butt.
&lt;red> &lt;Flash> xd Poltergiest
&lt;red> &lt;Flash> xd nothing.
&lt;red> &lt;Flash> xd POLTERGEIST
&lt;red> &lt;Flash> xd SPLIT'D.
&lt;red> &lt;Flash> xd SPLIT'D.</pre>

<a name="what" href="#">What? That doesn't explain anything!</a>
<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Shut up.
<br /><br />

<br /><span style="font-size: 14pt; font-weight: bold;">Registration:</span>
<br /><a name="password" href="#">Why should I give my password to some website about weed?</a>
<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;I understand your concern. I guess that's why you should use different passwords for different sites. But don't worry, all of the passwords are md5 encrypted. <a href="/passhash.jpg">Look at this screenshot of the database look how pretty it is</a>. In all, this section of the FAQ seems pretty awkward and if someone really is worried about their password security would probably think this section is like, "OH GOD THEY'RE JUST SAYING THAT TO TRICK ME OH GOD", but to that I guess I must say you're just a paranoid fuck and probably not chill enough to hang out here anyway. ;)
<br /><br />

<br /><a name="wheremail" href="#">Where's my authentication email?</a>
<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Uh, check your spam folder. If it's not there, come onto <a href="irc://irc.wetfish.net/wetfish">#wetfish on irc.wetfish.net</a> and tell richjkl to fix it for you.
<br /><br />

<br /><span style="font-size: 14pt; font-weight: bold;">Rules:</span>
<br /><a name="noshit" href="#">No posting shit that isn't a weedhat.</a>
<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;This is a website about weedhats, not weedcats, nor weedflatchests. Please only post pictures containing a weedhat. And when I say pictures, I mean <i>photographs</i>. No doodles of a hat you think would be KAWAII DESU ^___^. Sure, you can have other shit in your picture, but a weedhat is required. If you post random bullshit, you will be banned. <b>No exceptions.</b>
<br /><br />

<br /><a name="replyorganization" href="#">Keep multiple pictures of the same hat together by replying to the first image you uploaded.</a>
<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;It just keeps things together. It makes things look nicer, and it makes it so you (or other people) don't have to look all over the place for multiple pictures of your hat. Just think about it&mdash;if someone likes your weedhat and wants to see more of it, wouldn't it make sense to make it more convenient for them so they can rate you up?
<br /><br />

<br /><a name="unrelatedreply" href="#">Don't make unrelated replies without good reason.</a>
<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Replies are intended to be just that&mdash;replies. Unless you have a reason to post a different kind of weedhat in a thread which already has a defined style, don't do it.
<br /><br />

<br />Thanks for reading. If you got all the way to the bottom without skipping around, it means you're one of the cool kids. ;)
</div>
CONTENT;
		break;

	case "login":
		$title = "ahoy, comrade!";

		if($nav2[1]['text'] == "Login")
		{
			$nav2[1]['active'] = "true";

			if(!empty($_POST))
			{
				if(!preg_match("/^[a-z0-9 -]*$/i", $_POST['name']))
					$errors['name'] = "<span class=\"error\">Don't put funny characters in your name.</span>";
				elseif(trim($_POST['name']) == "")
					$errors['name'] = "<span class=\"error\">Nice try.</span>";
				elseif(strlen($_POST['name']) > 32)
					$errors['name'] = "<span class=\"error\">Your name can't be that long.</span>";

				if(!preg_match("/^[a-z0-9 \.\!\(\)\_\-\$\?\#\:,]*$/i", $_POST['password']))
					$errors['password'] = "<span class=\"error\">Don't put funny characters in your password.</span>";
				elseif(trim($_POST['password']) == "")
					$errors['password'] = "<span class=\"error\">Nice try.</span>";
				elseif(strlen($_POST['password']) > 32)
					$errors['password'] = "<span class=\"error\">Your password can't be that long.</span>";

				if(empty($errors))
				{
					$query = mysql_query("SELECT `AccountName`,`AccountPassword`,`AccountStatus` FROM `Accounts` WHERE `AccountName`='{$_POST['name']}'");
					list($AccountName, $AccountPassword, $AccountStatus) = @mysql_fetch_array($query);

					if($AccountPassword != md5($_POST['password']))
						$errors['password'] = "<span class=\"error\">Incorrect password.</span>";

					if($AccountStatus != "registered")
						$errors['name'] = "<span class=\"error\">Account not authenticated.</span>";

					if($AccountName == "")
						$errors['name'] = "<span class=\"error\">Account not registered.</span>";

					if(empty($errors))
					{
						$AccountLoginTime = time();

						mysql_query("UPDATE `Accounts` SET `AccountLoginTime`='$AccountLoginTime' WHERE `AccountName`='{$_POST['name']}'");

						$EncryptedPassword = encrypt($AccountLoginTime, $AccountPassword);

						$content = "<script>document.cookie = \"account = $AccountName; expires = Thu, 31 Dec 2037 23:59:59 UTC; path = /;\"</script><script>document.cookie = \"password = $EncryptedPassword; expires = Thu, 31 Dec 2037 23:59:59 UTC; path = /;\"</script><meta http-equiv=\"refresh\" content=\"2;url=/the_beginning\">welcome back, friend ;)";
					}
				}
			}

			if(((!empty($_POST)) && (!empty($errors))) || (empty($_POST)))
			{
				$content = "<form action=\"/login\" method=\"post\"><table cellpadding=\"0\">";

				if(!empty($errors['name']))
					$content .= "<tr><td colspan=\"2\">{$errors['name']}</td></tr>";
				$content .= "<tr><td>Name:</td><td><input type=\"text\" name=\"name\" value=\"{$_POST['name']}\" size=\"32\" maxlength=\"32\"></td></tr>";

				if(!empty($errors['password']))
					$content .= "<tr><td colspan=\"2\">{$errors['password']}</td></tr>";
				$content .= "<tr><td>Password:</td><td><input type=\"password\" name=\"password\" size=\"32\" maxlength=\"32\"></td></tr>";

				$content .= "<tr><td colspan=\"2\"><input type=\"submit\" value=\"Submit\"></td></tr>";

				$content .= "</table></form>";
			}
		}

		else
			$content = "nigga what you think you doin?";
		break;

	case "logout":
		$title = "I..I'm...going to miss you :'(";

		if($nav2[1]['text'] == "Logout")
		{
			$nav2[1]['active'] = "true";
			$content = "<script>document.cookie = \"account = LOLOLOLOL; expires = Thu, 15 Aug 1990 23:59:59 UTC; path = /;\"; document.cookie = \"password = LOLOLOLOL; expires = Thu, 15 Aug 1990 23:59:59 UTC; path = /;\"</script><meta http-equiv = \"refresh\" content = \"2;url=/the_beginning\">goodbye...";
		}

		else
			$content = "wait, wait, wait, hoollldd up...uhuhh, no you didn't";
		break;

	case "register":
		$title = "you have no fucking clue what you're getting yourself into";

		$query = mysql_query("SELECT `AccountStatus` FROM `Accounts` WHERE `AccountName`='$identity'");
		list($AccountStatus) = @mysql_fetch_array($query);

		if($identity == "Flash")
		{
			preg_match_all("/(?:^| )([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}):(.*?)(?:,|$)/", $AccountStatus, $StatusMatches);

			if(in_array($_SERVER['REMOTE_ADDR'], $StatusMatches[1]))
			{
				$ArrayLocation = array_search($_SERVER['REMOTE_ADDR'], $StatusMatches[1]);

				if($StatusMatches[2][array_search($_SERVER['REMOTE_ADDR'], $StatusMatches[1])] == "banned")
				{
					$abadthing = TRUE;
					$content = "<span class=\"error\">Your account is not allowed to register a new account.</span>";
				}
			}
		}

		if(($nav2[1]['text'] == "Login") && ($abadthing !== TRUE))
		{
			if($path[1] == "auth")
			{
				if(!empty($_POST))
				{
					if(!preg_match("/^[a-z0-9 -]*$/i", $_POST['name']))
						$errors['name'] = "<span class=\"error\">Don't put funny characters in your name.</span>";
					elseif(trim($_POST['name']) == "")
						$errors['name'] = "<span class=\"error\">Nice try.</span>";
					elseif(strlen($_POST['name']) > 32)
						$errors['name'] = "<span class=\"error\">Your name can't be that long.</span>";

					if(!preg_match("/^[a-z0-9]*$/i", $_POST['auth']))
						$errors['auth'] = "<span class=\"error\">Auth codes are alphanumeric you silly butt.</span>";
					elseif(strlen($_POST['auth']) != 10)
						$errors['auth'] = "<span class=\"error\">Invalid auth code.</span>";

					if(empty($errors))
					{
						$query = mysql_query("SELECT `AccountName`,`AccountPassword`,`AccountAuthCode`,`AccountStatus` FROM `Accounts` WHERE `AccountName`='{$_POST['name']}'");
						list($AccountName, $AccountPassword, $AccountAuthCode, $AccountStatus) = @mysql_fetch_array($query);

						if($AccountName == "")
							$errors['name'] = "<span class=\"error\">Account not registered.</span>";

						if($AccountAuthCode != $_POST['auth'])
							$errors['auth'] = "<span class=\"error\">Incorrect auth code.</span>";

						if($AccountStatus != "unauthed")
							$errors['auth'] = "<span class=\"error\">Account already authed.</span>";

						if(empty($errors))
						{
							$AccountLoginTime = time();

							mysql_query("UPDATE `Accounts` SET `AccountStatus`='registered' WHERE `AccountName`='{$_POST['name']}'");
							mysql_query("UPDATE `Accounts` SET `AccountLoginTime`='$AccountLoginTime' WHERE `AccountName`='{$_POST['name']}'");

							$EncryptedPassword = encrypt($AccountLoginTime, $AccountPassword);

							$content = "<script>document.cookie = \"account = $AccountName; expires = Thu, 31 Dec 2037 23:59:59 UTC; path = /;\"</script><script>document.cookie = \"password = $EncryptedPassword; expires = Thu, 31 Dec 2037 23:59:59 UTC; path = /;\"</script><meta http-equiv=\"refresh\" content=\"2;url=/profile/".str_replace(" ", "_", $AccountName)."/edit\">Account registration completed...<b>you're in</b>.";
						}
					}
				}

				if(((!empty($_POST)) && (!empty($errors))) || (empty($_POST)))
				{
					$content = "<form action=\"/register/auth\" method=\"post\"><table cellpadding=\"0\">";

					if(!empty($errors['name']))
						$content .= "<tr><td colspan=\"2\">{$errors['name']}</td></tr>";
					$content .= "<tr><td>Name:</td><td><input type=\"text\" name=\"name\" value=\"{$_POST['name']}\" size=\"32\" maxlength=\"32\"></td></tr>";

					if(!empty($errors['auth']))
						$content .= "<tr><td colspan=\"2\">{$errors['auth']}</td></tr>";
					$content .= "<tr><td>Auth Code:</td><td><input type=\"text\" name=\"auth\" value=\"{$_POST['auth']}\" size=\"32\" maxlength=\"10\"></td></tr>";

					$content .= "<tr><td colspan=\"2\"><input type=\"submit\" value=\"Submit\"></td></tr>";

					$content .= "</table></form>";
				}
			}

			else
			{
				$nav2[2]['active'] = "true";

				session_start();

				if(!empty($_POST))
				{
					if(!preg_match("/^[a-z0-9 -]*$/i", $_POST['name']))
						$errors['name'] = "<span class=\"error\">Don't put funny characters in your name.</span>";
					elseif(trim($_POST['name']) == "")
						$errors['name'] = "<span class=\"error\">Nice try.</span>";
					elseif(strlen($_POST['name']) > 32)
						$errors['name'] = "<span class=\"error\">Your name can't be that long.</span>";

					if(!preg_match("/^[a-z0-9 \.\!\(\)\_\-\$\?\#\:,]*$/i", $_POST['password']))
						$errors['password'] = "<span class=\"error\">Don't put funny characters in your password.</span>";
					elseif(trim($_POST['password']) == "")
						$errors['password'] = "<span class=\"error\">Nice try.</span>";
					elseif(strlen($_POST['password']) > 32)
						$errors['password'] = "<span class=\"error\">Your password can't be that long.</span>";

					if($_POST['password'] != $_POST['confirm'])
						$errors['confirm'] = "<span class=\"error\">Your passwords do not match.</span>";

					if(!preg_match("/^[a-z0-9 \@\.\-\_]*$/i", $_POST['email']))
						$errors['email'] = "<span class=\"error\">I didn't know you could put letters like that in email addresses.</span>";
					elseif(strlen(trim($_POST['email'])) < 6)
						$errors['email'] = "<span class=\"error\">Shit's too short, son.</span>";
					elseif(strlen(trim($_POST['email'])) > 255)
						$errors['email'] = "<span class=\"error\">Why in the hell would you ever want such a long email address?</span>";

					$key = substr($_SESSION['key'], 0, 5);
					$number = $_REQUEST['captcha'];

					if($_REQUEST['captcha'] != substr($_SESSION['key'], 0, 5))
						$errors['captcha'] = "<span class=\"error\">Incorrect captcha.</span>";

					if(empty($errors))
					{
						$query = mysql_query("SELECT `AccountName` FROM `Accounts` WHERE `AccountName`='{$_POST['name']}'");
						list($AccountName) = @mysql_fetch_array($query);

						$query = mysql_query("SELECT `AccountEmail` FROM `Accounts` WHERE `AccountEmail`='{$_POST['email']}'");
						list($AccountEmail) = @mysql_fetch_array($query);

						if($AccountName != "")
							$errors['name'] = "<span class=\"error\">That account has already been registered.</span>";

						if($AccountEmail != "")
							$errors['email'] = "<span class=\"error\">That email address has already been registered.</span>";

						if(empty($errors))
						{
							$AccountCreationTime = time();
							$AccountPassword = md5($_POST['password']);
							$AccountAuthCode = dechex(rand(0, 15)).dechex(rand(0, 15)).dechex(rand(0, 15)).dechex(rand(0, 15)).dechex(rand(0, 15)).dechex(rand(0, 15)).dechex(rand(0, 15)).dechex(rand(0, 15)).dechex(rand(0, 15)).dechex(rand(0, 15));

							mysql_query("INSERT INTO `Accounts` VALUES ('NULL', '$AccountCreationTime', '', '', '{$_POST['name']}', '$AccountPassword', '{$_POST['email']}', '$AccountAuthCode', 'unauthed', '', '0,0,,,,')");

							$from = "registration@weedhat.org";
							$subject = "Weedhat account created!";
							$message = "Your account, <b>{$_POST['name']}</b>, has been registered with the password <b>{$_POST['password']}</b>. To complete the registration process, please enter your authorization code (<b>$AccountAuthCode</b>) at <a href=\"http://weedhat.org/register/auth\">http://weedhat.org/register/auth</a>.";

							$mail = new PHPMailer();
							$mail->SetLanguage("en", "lang/");
							$mail->IsSMTP();
							$mail->Host = "localhost;";
							$mail->SMTPAuth = false;
							$mail->Username = "";
							$mail->Password = "";
							$mail->From = "$from";
							$mail->FromName = "Weedhat Registration";
							$mail->AddAddress($_POST['email'], $_POST['email']);
							$mail->WordWrap = 50;
							$mail->IsHTML(true);
							$mail->Subject = "$subject";
							$mail->Body = "$message";
							$mail->AltBody = "$message";

							if(!$mail->Send())
								$content = "Something fucked up with the mail server. Go bitch at richjkl.<p>Mailer Error: ".$mail->ErrorInfo;
							else
								$content = "<meta http-equiv=\"refresh\" content=\"2;url=/the_beginning\">Registration successful...Lord Flash is pleased. Very pleased.";
						}
					}
				}

				if(((!empty($_POST)) && (!empty($errors))) || (empty($_POST)))
				{
					$content = "<form action=\"/register\" method=\"post\"><table cellpadding=\"0\">";

					if(!empty($errors['name']))
						$content .= "<tr><td colspan=\"2\">{$errors['name']}</td></tr>";
					$content .= "<tr><td>Name:</td><td><input type=\"text\" name=\"name\" value=\"{$_POST['name']}\" size=\"32\" maxlength=\"32\"></td></tr>";

					if(!empty($errors['password']))
						$content .= "<tr><td colspan=\"2\">{$errors['password']}</td></tr>";
					$content .= "<tr><td>Password:</td><td><input type=\"password\" name=\"password\" size=\"32\" maxlength=\"32\"></td></tr>";

					if(!empty($errors['confirm']))
						$content .= "<tr><td colspan=\"2\">{$errors['confirm']}</td></tr>";
					$content .= "<tr><td>Confirm Password:</td><td><input type=\"password\" name=\"confirm\" size=\"32\" maxlength=\"32\"></td></tr>";

					if(!empty($errors['email']))
						$content .= "<tr><td colspan=\"2\">{$errors['email']}</td></tr>";
					$content .= "<tr><td>Email:</td><td><input type=\"text\" name=\"email\" value=\"{$_POST['email']}\" size=\"32\" maxlength=\"255\"></td></tr>";

					if(!empty($errors['captcha']))
						$content .= "<tr><td colspan=\"2\">{$errors['captcha']}</td></tr>";
					$content .= "<tr><td>Captcha:</td><td><img src=\"/captcha.php\" /><br /><input type=\"text\" name=\"captcha\" size=\"5\" maxlength=\"5\"></td></tr>";

					$content .= "<tr><td colspan=\"2\"><input type=\"submit\" value=\"Submit\"></td></tr>";

					$content .= "</table></form>";
				}
			}
		}

		elseif($abadthing !== TRUE)
			$content = "nigga what?";
		break;
		
	case "hat":
		if($path[2] != "")
		{
			$queries = GetQueries($path[2]);

			if(empty($queries))
			{
				switch($path[2])
				{
					case "tag":
						$query = mysql_query("SELECT `AccountStatus` FROM `Accounts` WHERE `AccountName`='$identity'");
						list($AccountStatus) = @mysql_fetch_array($query);

						if($identity == "Flash")
						{
							preg_match_all("/(?:^| )([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}):(.*?)(?:,|$)/", $AccountStatus, $StatusMatches);

							if(in_array($_SERVER['REMOTE_ADDR'], $StatusMatches[1]))
							{
								$ArrayLocation = array_search($_SERVER['REMOTE_ADDR'], $StatusMatches[1]);

								if($StatusMatches[2][array_search($_SERVER['REMOTE_ADDR'], $StatusMatches[1])] == "banned")
								{
									$abadthing = TRUE;
									$content = "<span class=\"error\">Your account is not allowed to tag Weedhats.</span>";
								}
							}
						}
						else
						{
							if($AccountStatus != "registered")
							{
								$abadthing = TRUE;
								$content = "<span class=\"error\">Your account is not allowed to tag Weedhats.</span>";
							}
						}

						if((!empty($_POST)) && ($abadthing !== TRUE))
						{
							if(!preg_match("/^[a-z0-9 ,]*$/i", $_POST['tags']))
								$errors['tags'] = "<span class=\"error\">Don't put funny characters in your tags.</span>";
							elseif(strlen($_POST['tags']) > 100)
								$errors['tags'] = "<span class=\"error\">I think you have too many tags...</span>";

							if(empty($errors))
							{
								$query = mysql_query("SELECT `HatTags` FROM `Hats` WHERE `HatID`='$path[1]'");
								list($HatTags) = @mysql_fetch_array($query);

								if($HatTags != "")
									$OldTags = explode(", ", $HatTags);
								$NewTags = explode(", ", FormatTags($_POST['tags']));
$lol = print_r($NewTags, true);
die($lol);
								foreach($NewTags as $tag)
								{
									if(!in_array($tag, $OldTags))
										$TagList[] = $tag;
								}

								if($OldTags != "")
									$OldTags = implode(", ", $OldTags);
								if($TagList[0] != "")
									$NewTagz = implode($TagList);

								if(($OldTags != "") && ($NewTagz != ""))
									$HatTags = "$OldTags, $NewTagz";
								elseif($OldTags == "")
									$HatTags = $NewTagz;
								else
									$HatTags = $OldTags;

								mysql_query("UPDATE `Hats` SET `HatTags`='$HatTags' WHERE `HatID`='$path[1]'");

								if($TagList != "")
								{
									foreach($TagList as $tag)
									{
										$tag = substr($tag, strpos($tag, ":") + 1);

										$query = mysql_query("SELECT `TagName`,`TagHats`,`TagOccurrence` FROM `Tags` WHERE `TagName`='$tag'");
										list($TagName, $TagHats, $TagOccurrence) = @mysql_fetch_array($query);

										if(!empty($TagName))
										{
											$TagOccurrence++;

											if(!in_array($path[1], explode(", ", $TagHats)))
												mysql_query("UPDATE `Tags` SET `TagHats` = CONCAT(`TagHats`, ', $path[1]') WHERE `TagName`='$tag'");
											mysql_query("UPDATE `Tags` SET `TagOccurrence`='$TagOccurrence' WHERE `TagName`='$tag'");
										}

										elseif($tag != "")
											mysql_query("INSERT INTO `Tags` VALUES('NULL', '$tag', '$path[1]', '1')");
									}
								}

								$content = "<meta http-equiv=\"refresh\" content=\"2;url=/hat/$path[1]\">Tags added...";
							}
						}

						if((((!empty($_POST)) && (!empty($errors))) || (empty($_POST))) && ($abadthing !== TRUE))
						{
							$content = "<form action=\"/hat/$path[1]/tag\" method=\"post\"><table cellpadding=\"0\">";

							if(!empty($errors['tags']))
								$content .= "<tr><td colspan=\"2\">{$errors['tags']}</td></tr>";
							$content .= "<tr><td>Tags:</td><td><input type=\"text\" name=\"tags\" value=\"{$_POST['tags']}\" size=\"32\" maxlength=\"100\"></td></tr><tr><td>&nbsp;</td><td><span style=\"font-size: 8pt; font-style: italic;\">Separate tags with commas.</span></td></tr>";

							$content .= "<tr><td colspan=\"2\"><input type=\"submit\" value=\"Submit\"></td></tr>";

							$content .= "</table></form>";
						}

						break;

					case "edit":
						$AccountName = str_replace("_", " ", $path[1]);

						if(($AccountName == strtolower($identity)) && ($identity != "Flash"))
						{
							$query = mysql_query("SELECT `AccountStatus` FROM `Accounts` WHERE `AccountName`='$identity'");
							list($AccountStatus) = @mysql_fetch_array($query);

							if($AccountStatus != "registered")
							{
								$abadthing = TRUE;
								$content = "<span class=\"error\">Your account is not allowed to edit Weedhats.</span>";
							}

							if((!empty($_POST)) && ($abadthing !== TRUE))
							{
								if(!preg_match("/^[a-z0-9 \.\!\(\)\_\-\$\?\'\"\#\:,]*$/i", $_POST['name']))
									$errors['name'] = "<span class=\"error\">Don't put funny characters in your hat's name.</span>";
								elseif(strlen($_POST['name']) > 32)
									$errors['name'] = "<span class=\"error\">Your hat's name is too long...</span>";

								if(empty($errors))
								{
									mysql_query("UPDATE `Hats` SET `HatName`='{$_POST['name']}' WHERE `HatID`='$path[1]'");
									$content = "<meta http-equiv=\"refresh\" content=\"2;url=/hat/$path[1]\">Hat updated...";
								}
							}

							if((((!empty($_POST)) && (!empty($errors))) || (empty($_POST))) && ($abadthing !== TRUE))
							{
								$content = "<form action=\"/hat/$path[1]/edit\" enctype=\"multipart/form-data\" method=\"post\"><table cellpadding=\"0\">";

								if(!empty($errors['name']))
									$content .= "<tr><td colspan=\"2\">{$errors['name']}</td></tr>";
								$content .= "<tr><td>Hat Name:</td><td><input type=\"text\" name=\"name\" value=\"{$_POST['name']}\" size=\"32\" maxlength=\"32\"></td></tr>";

								$content .= "<tr><td colspan=\"2\"><input type=\"submit\" value=\"Submit\"></td></tr>";

								$content .= "</table></form>";
							}
						}
						else
							$content = "Nice try.";

						break;

					case "delete":
						$AccountName = str_replace("_", " ", $path[1]);

						if(($AccountName == strtolower($identity)) && ($identity != "Flash"))
						{
							$query = mysql_query("SELECT `AccountStatus` FROM `Accounts` WHERE `AccountName`='$identity'");
							list($AccountStatus) = @mysql_fetch_array($query);

							if($AccountStatus != "registered")
							{
								$abadthing = TRUE;
								$content = "<span class=\"error\">Your account is not allowed to delete Weedhats.</span>";
							}

							if((!empty($_POST)) && ($abadthing !== TRUE))
							{
								if($_POST['fuckingsure'] != "yes")
									$errors['fuckingsure'] = "<span class=\"error\">Try being more sure next time.</span>";

								if(empty($errors))
								{
									mysql_query("UPDATE `Hats` SET `HatDeleted`='3' WHERE `HatID`='$path[1]'");
									$content = "<meta http-equiv=\"refresh\" content=\"2;url=/browse\">Hat deleted...";
								}
							}

							if((((!empty($_POST)) && (!empty($errors))) || (empty($_POST))) && ($abadthing !== TRUE))
							{
								$content = "<form action=\"/hat/$path[1]/delete\" enctype=\"multipart/form-data\" method=\"post\"><table cellpadding=\"0\">";

								if(!empty($errors['fuckingsure']))
									$content .= "<tr><td colspan=\"2\">{$errors['fuckingsure']}</td></tr>";
								$content .= "<tr><td><blink><span style=\"font-size: 14pt; font-weight: bold;\">ARE YOU FUCKING SURE YOU WANT TO DELETE YOUR HAT?</span></blink></td><td><input type=\"hidden\" name=\"fuckingsure\" value=\"yes\"></td></tr>";

								$content .= "<tr><td colspan=\"2\"><input type=\"submit\" value=\"Yes\"></td></tr>";

								$content .= "</table></form>";
							}
						}
						else
							$content = "WHAT'RE YOU TRYING TO DO, MAN????";

						break;

					default:
						$content = "cut that out :(";
						break;
				}
			}

			elseif($queries['rate'] == "up")
			{
				$query = mysql_query("SELECT `AccountStatus` FROM `Accounts` WHERE `AccountName`='$identity'");
				list($AccountStatus) = @mysql_fetch_array($query);

				if($identity == "Flash")
				{
					preg_match_all("/(?:^| )([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}):(.*?)(?:,|$)/", $AccountStatus, $StatusMatches);

					if(in_array($_SERVER['REMOTE_ADDR'], $StatusMatches[1]))
					{
						$ArrayLocation = array_search($_SERVER['REMOTE_ADDR'], $StatusMatches[1]);

						if($StatusMatches[2][array_search($_SERVER['REMOTE_ADDR'], $StatusMatches[1])] == "banned")
						{
							$abadthing = TRUE;
							$content = "<span class=\"error\">Your account is not allowed to rate Weedhats.</span>";
						}
					}
				}
				else
				{
					if($AccountStatus != "registered")
					{
						$abadthing = TRUE;
						$content = "<span class=\"error\">Your account is not allowed to rate Weedhats.</span>";
					}
				}

				if($abadthing !== TRUE)
				{
					$query = mysql_query("SELECT `HatRaters`,`HatRating` FROM `Hats` WHERE `HatID`='$path[1]'");
					list($HatRaters, $HatRating) = @mysql_fetch_array($query);

					if(in_array($_SERVER['REMOTE_ADDR'], explode(", ", $HatRaters)))
						$content = "Error: You've already rated this hat.";

					else
					{
						$HatRating++;

						mysql_query("UPDATE `Hats` SET `HatRating`='$HatRating' WHERE `HatID`='$path[1]'");
						if($HatRaters != "")
							mysql_query("UPDATE `Hats` SET `HatRaters`= CONCAT(`HatRaters`, ', {$_SERVER['REMOTE_ADDR']}') WHERE `HatID`='$path[1]'");
						else
							mysql_query("UPDATE `Hats` SET `HatRaters`='{$_SERVER['REMOTE_ADDR']}' WHERE `HatID`='$path[1]'");

						$content = "<meta http-equiv=\"refresh\" content=\"2;url=/hat/$path[1]\">This hat is now a happy hat. :)";
					}
				}
			}

			elseif($queries['rate'] == "down")
			{
				$query = mysql_query("SELECT `AccountStatus` FROM `Accounts` WHERE `AccountName`='$identity'");
				list($AccountStatus) = @mysql_fetch_array($query);

				if($identity == "Flash")
				{
					preg_match_all("/(?:^| )([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}):(.*?)(?:,|$)/", $AccountStatus, $StatusMatches);

					if(in_array($_SERVER['REMOTE_ADDR'], $StatusMatches[1]))
					{
						$ArrayLocation = array_search($_SERVER['REMOTE_ADDR'], $StatusMatches[1]);

						if($StatusMatches[2][array_search($_SERVER['REMOTE_ADDR'], $StatusMatches[1])] == "banned")
						{
							$abadthing = TRUE;
							$content = "<span class=\"error\">Your account is not allowed to rate Weedhats.</span>";
						}
					}
				}
				else
				{
					if($AccountStatus != "registered")
					{
						$abadthing = TRUE;
						$content = "<span class=\"error\">Your account is not allowed to rate Weedhats.</span>";
					}
				}

				if($abadthing !== TRUE)
				{
					$query = mysql_query("SELECT `HatRaters`,`HatRating` FROM `Hats` WHERE `HatID`='$path[1]'");
					list($HatRaters, $HatRating) = @mysql_fetch_array($query);

					if(in_array($_SERVER['REMOTE_ADDR'], explode(", ", $HatRaters)))
						$content = "Error: You've already rated this hat.";

					else
					{
						$HatRating--;

						mysql_query("UPDATE `Hats` SET `HatRating`='$HatRating' WHERE `HatID`='$path[1]'");

						if($HatRaters != "")
							mysql_query("UPDATE `Hats` SET `HatRaters`= CONCAT(`HatRaters`, ', {$_SERVER['REMOTE_ADDR']}') WHERE `HatID`='$path[1]'");
						else
							mysql_query("UPDATE `Hats` SET `HatRaters`='{$_SERVER['REMOTE_ADDR']}' WHERE `HatID`='$path[1]'");

						$content = "<meta http-equiv=\"refresh\" content=\"2;url=/hat/$path[1]\">Fuck that hat.";
					}
				}
			}

			else
				$content = "nigga wtf r u doin";
		}
		else
		{
			$query = mysql_query("SELECT `HatURL`,`HatName`,`HatCreator`,`HatCategory`,`HatRating`,`HatReplyTo`,`HatReplies`,`HatTags`,`HatDeleted` FROM `Hats` WHERE `HatID`='$path[1]'");
			list($HatURL, $HatName, $HatCreator, $HatCategory, $HatRating, $HatReplyTo, $HatReplies, $HatTags, $HatDeleted) = @mysql_fetch_array($query);

			if($HatDeleted == 2)
				$content = "This hat was pruned by the community.";
			elseif($HatDeleted == 3)
				$content = "This hat was deleted by its creator.";
			else
			{
				$title = "\"$HatName\" by $HatCreator - weedhat.org";

				if(($HatCreator == $identity) && ($identity != Flash))
					$creatorfunctions = "<br /><a href=\"/hat/$path[1]/edit\">Edit</a> | <a href=\"/hat/$path[1]/delete\">Delete</a>";

				if($HatDeleted == 1)
				{
					$imgclass = "pendingprune";
					$prunetext = "<br />This hat has been marked for deletion due to low rating.";
				}
				else
					$imgclass = "imgdisp";

				$content = "<div class=\"imgdspcntn\"><div class=\"imgname\">$HatName</div><div class=\"authname\">by: <a href=\"/browse/advanced/creator:".str_replace(" ", "_", $HatCreator).";\">$HatCreator</a></div><div class=\"$imgclass\"><a href=\"/upload/$HatURL\">".DisplayImage($HatURL, 725)."</a><br />$prunetext$creatorfunctions</div>";

				if($HatReplyTo != 0)
				{
					$query = mysql_query("SELECT `HatURL`,`HatName`,`HatCreator` FROM `Hats` WHERE `HatID`='$HatReplyTo' AND `HatDeleted` BETWEEN '0' AND '1'");
					list($ReplyURL, $ReplyName, $ReplyCreator) = @mysql_fetch_array($query);

					if($ReplyName != "")
						$content .= "<div class=\"reply\">This is a reply to <a href=\"/hat/$HatReplyTo\">$ReplyName</a> by: <a href=\"/browse/advanced/creator:".str_replace(" ", "_", $ReplyCreator).";\">$ReplyCreator</a></div>";
			
					unset($ReplyURL, $ReplyName, $ReplyCreator, $ReplyRating, $ReplyReplies);
				}

				if($HatTags != "")
				{
					foreach(explode(", ", $HatTags) as $tag)
					{
						$tag = substr($tag, strpos($tag, ":") + 1);

						if(isset($tags[$tag]))
							$tags[$tag]++;
						else
							$tags[$tag] = 1;
					}

					arsort($tags);

					foreach(array_slice($tags, 0, 7) as $key=>$tag)
						$taglist .= "<a href=\"/browse/advanced/tag:$key;\">".str_replace("_", " ", $key)."</a>, ";

					$taglist = substr($taglist, 0, -2);
				}

				else
					$taglist = "This hat has no tags!";

				$content .= "<div class=\"tagttl\">Tags:</div><div class=\"ratingttl\">Rating:</div><div class=\"tagdisp\"><div class=\"tags\">$taglist</div><div class=\"ayo\"><a href=\"/hat/$path[1]/tag\">Add Your Own</a></div></div><div class=\"ratingdisp\"><a class=\"rate\" href=\"/hat/$path[1]/rate:up;\">[+]</a> $HatRating <a class=\"rate\" href=\"/hat/$path[1]/rate:down;\">[-]</a></div><div class=\"replylink\"><a href=\"/share/reply:$path[1];\">Add Reply</a></div>";

				if($HatReplies != "")
				{
					$content .= "<hr /><div class=\"spacer\">";

					foreach(explode(", ", $HatReplies) as $ReplyID)
					{
						$query = mysql_query("SELECT `HatURL`,`HatName`,`HatCreator` FROM `Hats` WHERE `HatID`='$ReplyID' AND `HatDeleted` BETWEEN '0' AND '1'");
						list($ReplyURL, $ReplyName, $ReplyCreator) = @mysql_fetch_array($query);

						if($ReplyName != "")
						{
							$ReplyNameDisplay = DisplayText($ReplyName, 170);
							$ReplyCreatorDisplay = DisplayText($ReplyCreator, 130);

							$content .= "<div class=\"replycntn\"><div class=\"replyttl\"><a href=\"/hat/$ReplyID\">$ReplyNameDisplay</a></div><div class=\"replyimg\"><a href=\"/hat/$ReplyID\">".DisplayImage($ReplyURL, 125)."</a></div><div class=\"replyauth\">by: <a href=\"/browse/advanced/creator:".str_replace(" ", "_", $ReplyCreator).";\">$ReplyCreatorDisplay</a></div></div>";
						}
					}

					$content .= "</div>";
				}
			}
		}
		break;

	case "the_beginning":
		$title = "coming soon to an internet near you";
		$content = "<div style=\"color: #FFD200; font-weight: bold; font-size: 16pt; text-align: center; background: #CC6666;  border: 1px solid #FFFF00; padding: 4px; margin-left: 9%; margin-right: 9%;\">hello, if you're new, please read the fucking <a href=\"/faq\">faq</a></div><br />You are looking at weedhat. Do you know what weedhat is? We do. <b>Everything</b> is weedhat. Maybe you could try it some time, for kicks, y'know. Smoke some ganja and find something to give a nice new home on top of your head. You see, we here at weedhat question the distinction between object and hat. Isn't it obvious? <i>Everything</i> can be a hat, so long as you're high.<br /><br />By now, I'm sure you're <i>excited</i>. Good. You should be. No longer should you see a bucket as a mere device to hold liquids. In fact, now that I've suggested it, you'll <u>never be able to unsee it</u>. No longer should perfectly useful objects be stripped of their right to also be a hat.<br /><br />Think about it.<br /><br /><blink>Just think about it.</blink><br /><br />Who wears hats? Cowboys. The pope. People with bad hair. <b>Abraham Lincoln</b>. It just makes sense.<br /><br /><blink><b>Just think about it.</b></blink><br /><br />Stop looking at this website and grab something&mdash;anything!&mdash;and put that motherfucker on your head. You'll be glad you did.";
		break;

	case "profile":
		$AccountName = str_replace("_", " ", $path[1]);

		if($path[2] == "edit")
		{
			if(($AccountName == strtolower(str_replace("_", " ", $identity))) && ($identity != "Flash"))
			{
				if(!empty($_POST))
				{
					if(strpos($_POST['website'], "://") !== FALSE)
						$website = substr($_POST['website'], strpos($_POST['website'], "://") + 3);
					else
						$website = $_POST['website'];

					if(($_POST['notify'] != 1) && ($_POST['notify'] != ""))
						$errors['notify'] = "<span class=\"error\">What the fuck are you trying to do?</span>";

					if(($_POST['display'] != 1) && ($_POST['display'] != ""))
						$errors['display'] = "<span class=\"error\">What the fuck are you trying to do?</span>";

					if(!preg_match("/^[a-z0-9\-_\/.?,:;&%+=#~]*$/i", $website))
						$errors['website'] = "<span class=\"error\">Websites can't have characters like that.</span>";
					elseif(strlen($website > 255))
						$errors['website'] = "<span class=\"error\">Your website can't be that long.</span>";

					if(!preg_match("/^[a-z0-9 ]*$/i", $_POST['aim']))
						$errors['aim'] = "<span class=\"error\">Don't put funny characters in your screen name.</span>";
					elseif(strlen($_POST['aim']) > 16)
						$errors['aim'] = "<span class=\"error\">Your screen name can't be that long.</span>";

					if(!preg_match("/^[a-z0-9\-_@.]*$/i", $_POST['msn']))
						$errors['msn'] = "<span class=\"error\">Don't put funny characters in your msn address.</span>";
					elseif(strlen($_POST['msn']) > 255)
						$errors['msn'] = "<span class=\"error\">Your msn address can't be that long.</span>";

					if(!preg_match("/^[a-z0-9\-_]*$/i", $_POST['yim']))
						$errors['yim'] = "<span class=\"error\">Don't put funny characters in your yahoo account.</span>";
					elseif(strlen($_POST['yim']) > 32)
						$errors['yim'] = "<span class=\"error\">Your yahoo account can't be that long.</span>";

					if(empty($errors))
					{
						if($_POST['notify'] != 1)
							$notify = 0;
						else
							$notify = 1;

						if($_POST['display'] != 1)
							$display = 0;
						else
							$display = 1;

						mysql_query("UPDATE `Accounts` SET `AccountProfile`='$notify,$display,$website,{$_POST['aim']},{$_POST['msn']},{$_POST['yim']}' WHERE `AccountName`='$AccountName'");
						$content = "<meta http-equiv=\"refresh\" content=\"2;url=/profile/$identity\">Profile updated...";
					}
				}

				if(((!empty($_POST)) && (!empty($errors))) || (empty($_POST)))
				{
					if(empty($_POST))
					{
						$query = mysql_query("SELECT `AccountProfile` FROM `Accounts` WHERE `AccountName`='$AccountName'");
						list($AccountProfile) = @mysql_fetch_array($query);

						list($AccountNotify, $AccountDisplay, $AccountWebsite, $AccountAIM, $AccountMSN, $AccountYIM) = explode(",", $AccountProfile);
					}
					else
					{
						$AccountNotify = $_POST['notify'];
						$AccountDisplay = $_POST['display'];
						$AccountWebsite = $_POST['website'];
						$AccountAIM = $_POST['aim'];
						$AccountMSN = $_POST['msn'];
						$AccountYIM = $_POST['yim'];
					}

					if($AccountNotify == 1)
						$AccountNotify = "checked";
					else
						$AccountNotify = "";

					if($AccountDisplay == 1)
						$AccountDisplay = "checked";
					else
						$AccountDisplay = "";

					$content = "<blink>ALL OF THIS SHIT IS OPTIONAL</blink><br /><br /><form action=\"/profile/$identity/edit\" method=\"post\"><table cellpadding=\"0\">";

					if(!empty($errors['notify']))
						$content .= "<tr><td colspan=\"2\">{$errors['notify']}</td></tr>";
					$content .= "<tr><td>Email Notification:</td><td><input type=\"checkbox\" $AccountNotify name=\"notify\" value=\"1\"></td></tr><tr><td colspan=\"2\"><span style=\"font-size: 8pt; font-style: italic;\">Notifies you whenever a reply is made to one of your hats.</span></td></tr>";

					if(!empty($errors['display']))
						$content .= "<tr><td colspan=\"2\">{$errors['display']}</td></tr>";
					$content .= "<tr><td>Display Email:</td><td><input type=\"checkbox\" $AccountDisplay name=\"display\" value=\"1\"></td></tr>";

					if(!empty($errors['website']))
						$content .= "<tr><td colspan=\"2\">{$errors['website']}</td></tr>";
					$content .= "<tr><td>Website:</td><td><input type=\"text\" name=\"website\" value=\"$AccountWebsite\" size=\"32\" maxlength=\"255\"></td></tr>";

					if(!empty($errors['aim']))
						$content .= "<tr><td colspan=\"2\">{$errors['aim']}</td></tr>";
					$content .= "<tr><td>AIM Account:</td><td><input type=\"text\" name=\"aim\" value=\"$AccountAIM\" size=\"32\" maxlength=\"16\"></td></tr>";

					if(!empty($errors['msn']))
						$content .= "<tr><td colspan=\"2\">{$errors['msn']}</td></tr>";
					$content .= "<tr><td>MSN Account:</td><td><input type=\"text\" name=\"msn\" value=\"$AccountMSN\" size=\"32\" maxlength=\"255\"></td></tr>";

					if(!empty($errors['yim']))
						$content .= "<tr><td colspan=\"2\">{$errors['yim']}</td></tr>";
					$content .= "<tr><td>Yahoo Account:</td><td><input type=\"text\" name=\"yim\" value=\"$AccountYIM\" size=\"32\" maxlength=\"32\"></td></tr>";

					$content .= "<tr><td colspan=\"2\"><input type=\"submit\" value=\"Submit\"></td></tr>";

					$content .= "</table></form>";
				}
			}
			else
				$content = "STOP PRETENDING TO BE THINGS YOU AREN'T, MAN";
		}
		else
		{
			$query = mysql_query("SELECT `AccountCreationTime`,`AccountPostTime`,`AccountEmail`,`AccountStatus`,`AccountHats`,`AccountProfile` FROM `Accounts` WHERE `AccountName`='$AccountName'");
			list($AccountCreationTime, $AccountPostTime, $AccountEmail, $AccountStatus, $AccountHats, $AccountProfile) = @mysql_fetch_array($query);

			if($AccountCreationTime == 0)
				$content = "I have no idea who that is.";
			else
			{
				$AccountCreationTime = "$AccountName has been a member of weedhat since ".date("l, F j, Y", $AccountCreationTime);
				if($AccountPostTime != 0)
				{
					$AccountPostTime = "$AccountName last shared a hat on ".date("l, F j, Y", $AccountPostTime);
					$AccountHatCount = "$AccountName has shared ".count(explode(", ", $AccountHats))." hats.<br />";
				}
				else
					$AccountPostTime = "$AccountName has never shared a hat";

				if(strtolower(str_replace("_", " ", $path[1])) == strtolower(str_replace("_", " ", $identity)))
					$editstr = " [<a href=\"/profile/$AccountName/edit\">Edit Profile</a>]";

				list($AccountNotify, $AccountDisplay, $AccountWebsite, $AccountAIM, $AccountMSN, $AccountYIM) = explode(",", $AccountProfile);

				if(($AccountDisplay != 0) || ($AccountWebsite != "") || ($AccountAIM != "") || ($AccountMSN != "") || ($AccountYIM != ""))
					$ContactTitle = "<br /><span style=\"font: bold 13pt Tahoma;\">Contact Information</span><br /><table cellpadding=\"0\">";

				if($AccountDisplay != 0)
					$AccountEmaul = "<tr><td>Email Address: </td><td style=\"padding-left: 4px;\"><a href=\"mailto:$AccountEmail\">$AccountEmail</a></td></tr>";
				if($AccountWebsite != "")
					$AccountWebsite = "<tr><td>Website: </td><td style=\"padding-left: 4px;\"><a href=\"http://$AccountWebsite\">$AccountWebsite</a></td></tr>";
				if($AccountAIM != "")
					$AccountAIM = "<tr><td>AIM Account: </td><td style=\"padding-left: 4px;\"><b>$AccountAIM</b></td></tr>";
				if($AccountMSN != "")
					$AccountMSN = "<tr><td>MSN Account: </td><td style=\"padding-left: 4px;\"><b>$AccountMSN</b></td></tr>";
				if($AccountYIM != "")
					$AccountYIM = "<tr><td>Yahoo Account: </td><td style=\"padding-left: 4px;\"><b>$AccountYIM</b></td></tr>";

				if($ContactTitle != "")
					$ContactFoot = "</table>";

				$content = <<<PROFILE
<div style="margin-left: 4px;">
<span style="font: bold 14pt Tahoma;">$AccountName</span>$editstr
<hr />
$AccountCreationTime.<br />
$AccountPostTime.<br />
$AccountHatCount
$ContactTitle
$AccountEmaul
$AccountWebsite
$AccountAIM
$AccountMSN
$AccountYIM
$ContactFoot
</div>
PROFILE;
			}
		}
		break;

	default:
		$content = "fuck you ;)";
		break;
}

$nav = FormatLinks($nav);
$nav2 = FormatLinks($nav2);

$html = <<<HTML

<html>
<head>
<title>$title (functional beta v1)</title>

<style type="text/css">

body { background: #386E0C; color: #FFFFFF; font-family: tahoma; font-size: 10pt; margin-top: 0px; margin-bottom: 0px; }
div.body { background: #28A73C; width: 808px; display: table; text-align: left; }
div.banner { text-align: center; }
div.nav { margin-left: 91px; margin-top: -15px; }
div.nav2 { margin-right: 14px; margin-top: -2px; text-align: right; }
div.content { padding: 4px; }
div.foot { background: #28A73C; text-align: center; margin: auto; display: table; clear: both; width: 808px; }
div.foottext { background: #5861BC; width: 100%; display: table; text-align: left; padding: 0 4px 2px 4px; margin: 0 4px 0 4px; }
td { font-family: tahoma; font-size: 10pt; }
span.error { background: #5E1739; padding: 1px; border: 1px solid #FFFFFF; }
a { color: #FEF200; text-decoration: none; }
a:hover { color: #FEF200; text-decoration: underline; }
a.active { color: #FFFFFF; text-decoration: none; font-weight: bold; }
a.active:hover { color: #FFFFFF; text-decoration: underline; font-weight: bold; }

.cntn { width: 100%; margin: auto; text-align: left; }
.indvcntn { width: 186px; height: 225px; margin: 7px; font-family: Tahoma; font-size: 10pt; background: #2DB942; float: left; }
.name { width: 172px; padding: 2px; margin-left: 5px; }
.img { width: 172px; padding: 2px; margin: 5px; text-align: center; }
.r_left { width: 79px; height: 12px; padding: 2px; margin: 0 5px 0 5px; float: left; }
.r_right { width: 79px; height: 12px; padding: 2px; margin: 0 5px 0 5px; float: left; }

hr{height: 0px;  border: solid #386e0c; border-width: 1px 0px 0px 0px; margin: 5px; clear: both; }
.imgdspcntn{width: 800px; margin: auto;}
.imgname{width: 50%; height: 30px;  font: bold 14pt Tahoma, Sans Serif; line-height: 30px; color: #FFFFFF; float: left; padding: 3px; }
.authname{ text-align: right; font: 10pt Tahoma, Sans Serif; line-height: 30px; color: #FFFFFF; padding: 3px; }
.imgdisp{ width: 770px; background: #2DB942; margin: auto; text-align: center; padding: 15px; }
.tagdisp{width: 50%; height: 50px; font: 10pt Tahoma, Sans Serif; color: #FFFFFF; text-align: center; float: left; }
.tags{ padding-left: 118px; width: 165px; }
.ayo{text-align: center; font-size: 10pt; width: 100%; height: 25px; }
.ratingdisp{height: 50px; font: 18pt Tahoma, Sans Serif;  line-height: 35px; color:#FFFFFF; text-align: center; padding: 5px; margin-bottom: -10px; }
.rate{font-size: 10pt; position: relative; bottom: 4px; }
.replytitle{width: 50%; float: left; font: 10pt Tahoma, Sans Serif; color: #FFFFFF; clear: both; padding: 3px; margin: 0px 0px 5px 0px; }
.replyauth{clear: right; font: 10pt Tahoma, Sans Serif; color: #FFFFFF; text-align: right; padding: 3px; }
.replylink{ margin-top: 12px; margin-left: -9px; font: bold 12pt Tahoma, Sans Serif; text-align: center; } 
.tagttl{font: 10pt Tahoma; color: #fff; width: 50%; float: left; padding:3px; margin-left: 11%; }
.ratingttl{font: 10pt Tahoma; color: #fff; padding:3px; margin-left: 11%; }
.reply{ font: 10pt Tahoma; color: #fff; width: 780px; border: 1px solid #FFFF00; padding: 5px; margin: 5px;text-align: center; background: #CC6666; }
.spacer { padding-left: 12px; }
.replycntn { width: 184px; height: 175px; margin: 4px; font-family: Tahoma; font-size: 10pt; float: left; text-align: center; background: #CC6666;  border: 1px solid #FFFF00;}
.replyttl{width: 176px; text-align: left; font: 10pt Tahoma; color: #fff; height: 15px; padding: 5px;}
.replyauth{text-align: left; height: 15px; font: 10pt Tahoma; color: #fff; width: 100%; padding: 5px;}

.pendingprune { font: 10pt Tahoma; color: #fff; width: 770px; border: 1px solid #FFFF00; margin: auto; text-align: center; padding: 15px; background: #CC6666;  }
.profile { font-size: 8pt; position:relative; bottom: 0.5em; }

</style>

</head>

<body>

<center>

<div class="body">

<div class="banner">
<a href="/the_beginning"><img src="/banner.jpg" border="0"></a>
</div>

<div class="nav">
$nav
</div>

<div class="nav2">
Currently logged in as: $nav2
</div>

<br />

<div class="content">
$content
</div>

<br />

</div>

<div class="foot">
<img src="/footer.jpg">
<div class="foottext">
<span style="float: right;"><a href="http://wetfish.net"><img src="/cuttlefishlogo75.png" border="0"></a><a href="http://tacticalcockfighting.net"><img src="/tcnowords75.png" border="0"></a></span>

Thanks to:
<br />&nbsp;&nbsp;&nbsp;<b><a href="http://thesaladcaper.com/">TheSaladCaper</a></b> for the CSS.
<br />&nbsp;&nbsp;&nbsp;<b><a href="http://ketite.com/">Kristy</a></b> for the banner.
<br />&nbsp;&nbsp;&nbsp;And <b>EVERYONE ELSE</b> for doing whatever else it is they did.

<br /><br /><span style="font-size: 8pt; font-style: italic;">By the way, weedhat.org takes no responsibility for the content of the images uploaded, that shit is the problem of whoever posted it. Also, weedhat.org does not condone the use of illicit substances. If marijuana is illegal where you live, please replace all instances of said substance on this website with "kittens".</span>

</div>
</div>

</center>

</body>
</html>

HTML;

preg_match_all("/(?:href=\"\/browse\/advanced\/creator:(.*?);\">(.*?)<\/a>)/", $html, $matches);
for($x = 0; $x < count($matches[0]); $x++)
{
	if($matches[1][$x] != "Flash")
		$html = str_replace_times($matches[0][$x], "href=\"/browse/advanced/creator:{$matches[1][$x]};\" class=\"formatted\">{$matches[2][$x]}</a> <span class=\"profile\">[<a href=\"/profile/{$matches[1][$x]}\">P</a>]</span>", $html);
}

echo $html;

?>
