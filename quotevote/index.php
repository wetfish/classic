<?php

include("config.php");

if($_SERVER['REMOTE_ADDR'] == "24.222.148.113")
	die("lol banned");

if($_COOKIE['sort'] == "")
	$sort = "id";
else
	$sort = $_COOKIE['sort'];

if (!function_exists("str_split"))
{
	function str_split($str,$length = 1)
	{
		if ($length < 1) return false;
			$strlen = strlen($str);
		$ret = array();
		for ($i = 0; $i < $strlen; $i += $length)
		{
			$ret[] = substr($str,$i,$length);
		}
		return $ret;
	}
}

function natksort(&$arrIn)
{
   $key_array = array();
   $arrOut = array();
  
   foreach ( $arrIn as $key=>$value ) {
       $key_array[]=$key;
   }
  natsort( $key_array);
  foreach ( $key_array as $key=>$value ) {
     $arrOut[$value]=$arrIn[$value];
  }
  $arrIn=$arrOut;

}

function cleanArray($array) {
   foreach ($array as $index => $value) {
       if (empty($value)) unset($array[$index]);
   }
   return $array;
}

@mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD) or die("Error: Unable to connect to the MySQL server.");
@mysql_select_db(MYSQL_DATABASE) or die("Error: Unable to access the MySQL database.");

mysql_set_charset('utf8');

foreach($_GET as $key => $value)
{
    $_GET[$key] = mysql_real_escape_string($value);
}

foreach($_COOKIE as $key => $value)
{
    $_COOKIE[$key] = mysql_real_escape_string($value);
}

if($_GET['quotesrc'] != "" && $_GET['admin'] == ADMIN_PASSWORD)
{
	$quotes = str_replace("<", "&lt;", file_get_contents($_GET['quotesrc']));
	$quotes = explode("\n", str_replace("\r", "", $quotes));
	$quotes = cleanArray($quotes);
	mysql_query("DROP TABLE IF EXISTS `redvote`");
	mysql_query("CREATE TABLE `redvote` (`id` int(10) NOT NULL auto_increment,`quote` text NOT NULL,`vote` int(10) NOT NULL default '0',`ip` text NOT NULL,PRIMARY KEY  (`id`))");
	foreach($quotes as $quote)
		mysql_query("INSERT INTO `redvote` VALUES ('NULL','".str_replace(array("\\", "'", "\""), array("\\\\", "\'", "\\\""), $quote)."','','')");
	if(mysql_error() == "")
		die("Quote list updated successfully. Redirecting...<meta http-equiv = \"refresh\" content = \"1;url=./\">");
}

if($_GET['quotereset'] != "" && $_GET['admin'] == ADMIN_PASSWORD)
{
	$quotes = str_replace("<", "&lt;", file_get_contents($_GET['quotereset']));
	$quotes = explode("\n", $quotes);
	$quotes = cleanArray($quotes);
	foreach($quotes as $key=>$quote)
	{
		list($tempkey, $tempvote, $tempquote) = explode("	", $quote);
		$temparray[str_replace("#", "", $tempkey)] = array($tempquote, str_replace(array("- ", " + "), array("", ""), $tempvote));
	}
	$quotes = $temparray;
	natksort($quotes);
	mysql_query("DROP TABLE IF EXISTS `redvote`");
	mysql_query("CREATE TABLE `redvote` (`id` int(10) NOT NULL auto_increment,`quote` text NOT NULL,`vote` int(10) NOT NULL default '0',`ip` text NOT NULL,PRIMARY KEY  (`id`))");
	foreach($quotes as $quote)
		mysql_query("INSERT INTO `redvote` VALUES ('NULL','".str_replace(array("\\", "'", "\""), array("\\\\", "\'", "\\\""), $quote[0])."','$quote[1]','')");
	if(mysql_error() == "")
		die("Quote list reset successfully. Redirecting...<meta http-equiv = \"refresh\" content = \"1;url=./\">");

}

if($_GET['fail'] != "")
{
	$quoteinfo = mysql_query("SELECT `vote`,`ip` FROM `redvote` WHERE `id` = '{$_GET['fail']}'");
	list($vote, $ip) = mysql_fetch_array($quoteinfo);

	$ip = explode(",", $ip);
	if(in_array($_SERVER['REMOTE_ADDR'], $ip))
		die("You've already voted on this quote!");

	$vote = $vote - 1;
	$ip[] = $_SERVER['REMOTE_ADDR'];
	$ip = implode(",", $ip);
	mysql_query("UPDATE `redvote` SET `ip` = '$ip' WHERE `id` = '{$_GET['fail']}'") or die("Unable to update quote #{$_GET['fail']}'s IP list.");
	mysql_query("UPDATE `redvote` SET `vote` = '$vote' WHERE `id` = '{$_GET['fail']}'") or die("Unable to update quote #{$_GET['fail']}'s vote.");

	die("Quote #{$_GET['fail']} fails! Redirecting...<meta http-equiv = \"refresh\" content = \"1;url=./#{$_GET['fail']}\">");
}

if($_GET['win'] != "")
{
	$quoteinfo = mysql_query("SELECT `vote`,`ip` FROM `redvote` WHERE `id` = '{$_GET['win']}'");
	list($vote, $ip) = mysql_fetch_array($quoteinfo);

	$ip = explode(",", $ip);
	if(in_array($_SERVER['REMOTE_ADDR'], $ip))
		die("You've already voted on this quote!");

	$vote = $vote + 1;
	$ip[] = $_SERVER['REMOTE_ADDR'];
	$ip = implode(",", $ip);
	mysql_query("UPDATE `redvote` SET `ip` = '$ip' WHERE `id` = '{$_GET['win']}'") or die("Unable to update quote #{$_GET['fail']}'s IP list.");
	mysql_query("UPDATE `redvote` SET `vote` = '$vote' WHERE `id` = '{$_GET['win']}'") or die("Unable to update quote #{$_GET['fail']}'s vote.");

	die("Quote #{$_GET['win']} wins! Redirecting...<meta http-equiv = \"refresh\" content = \"1;url=./#{$_GET['win']}\">");
}

if($_GET['fix'] != "" && $_GET['admin'] == ADMIN_PASSWORD)
{
	$quoteinfo = mysql_query("SELECT `id`,`vote`,`ip` FROM `redvote`");
	while(list($id, $vote, $ip) = mysql_fetch_array($quoteinfo))
	{
		if(in_array($_GET['fix'], explode(",", $ip)))
			$vote = $vote + 1;		
		mysql_query("UPDATE `redvote` SET `vote` = '$vote' WHERE `id` = '$id'");
	}
	die("Quote database problem regarding {$_GET['fix']} being a moron fixed. Redirecting...<meta http-equiv = \"refresh\" content = \"1;url=./>");
}


if(isset($_GET['export']))
{
	echo "<pre>";
	$quoteinfo = mysql_query("SELECT `quote`,`vote` FROM `redvote`");
	while(list($quote, $vote) = mysql_fetch_array($quoteinfo))
	{
		if($vote >= 0)
			echo str_replace("&lt;red>", "(red)", $quote)."<br>";
	}
	die("</pre>");
}

echo <<<CONTENT

<html>
<head>
<title>Red's Quote Vote</title>

<style type="text/css">

.body { background: #18395B; color: #FFFFFF; font-family: tahoma; font-size: 10pt; }
a {color: #A5B7D1; text-decoration: none;}
a:hover {color: #A5B7D1; text-decoration: underline;}
a:visited { color: #FFD1DC; }
td { font-family: tahoma; font-size: 10pt; }
td.idea { background: #294868; }
wbr:after { content: "\\00200B" }

</style>

<script type = "text/javascript">

function sortid() { document.cookie = "sort = id; expires = Thu, 31 Dec 2037 23:59:59 UTC; path = /;"; } 
function sortvote() { document.cookie = "sort = vote; expires = Thu, 31 Dec 2037 23:59:59 UTC; path = /;"; } 

</script>

</head>

<body class="body">

Hello and welcome to the super awesome red quote vote page!

<br><br>

The purpose of this page is to determine which of red's quotes are good, and which are bad. Over time, some quotes have been added which are, well, not really quote worthy. It is the purpose of this page to determine which quotes actually deserve to be on the list. Some quotes were funny when red said them, but now, in retrospect, it's easy to see that they really aren't that funny. Please note that you may only vote <b>ONCE</b> on each quote. If you vote incorrectly, you're fucked!<br><br>Thanks in advance for your help.

<br><br>

<table width = "100%" border = "1">
	<tr>
		<td><a href = "" onClick = "sortid()">ID</a></td><td><a href = "" onClick = "sortvote()">Vote</a></td>
		<td>Quote</td>
	</tr>

CONTENT;

if($sort == "id")
	$quoteinfo = mysql_query("SELECT `id`,`quote`,`vote` FROM `redvote`");
elseif($sort == "vote")
	 $quoteinfo = mysql_query("SELECT `id`,`quote`,`vote` FROM `redvote` ORDER BY `{$_COOKIE['sort']}` DESC");

while(list($id, $quote, $vote) = mysql_fetch_array($quoteinfo))
{
	if($vote < 0)
		$vote = "<font color=\"#E34234\">$vote</font>";
	elseif($vote > 0)
		$vote = "<font color=\"#B0E0E6\">$vote</font>";
	if($toggle == TRUE)
	{
		echo "<tr><td width = \"5%\" align = \"right\" valign = \"top\"><a name = \"$id\" id = \"$id\">#$id</a></td><td with = \"5%\" align = \"center\" valign = \"top\"> <a href = \"?fail=$id\">-</a> <b>$vote</b> <a href= \"?win=$id\">+</a> </td><td width = \"90%\">".implode("<wbr>", str_split($quote, 50))."</td></tr>";
		$toggle = FALSE;
	}
	else
	{
		echo "<tr><td width = \"5%\" align = \"right\" valign = \"top\" class=\"idea\"><a name = \"$id\" id = \"$id\">#$id</a></td><td with = \"5%\" align = \"center\" valign = \"top\" class=\"idea\"> <a href = \"?fail=$id\">-</a> <b>$vote</b> <a href= \"?win=$id\">+</a> </td><td width = \"90%\" class=\"idea\">".implode("<wbr>", str_split($quote, 50))."</td></tr>";
		$toggle = TRUE;
	}
}
echo "</table>";

?>
	</body>
</html>
