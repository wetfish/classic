<?php

session_start();
include_once('mysql.php');
include_once('functions.php');

if(is_numeric($Redirect[1]))
{
	$ImageQuery = mysql_query("Select `URL`, `Caption`, `Permalink` from `Images` where `ID`='$Redirect[1]'");
	list($URL, $Caption, $Permalink) = mysql_fetch_array($ImageQuery);

	$Title = "<a href='/view/$Redirect[1]/$Permalink/'>$Caption</a>";
	$Image = "$URL";
	echo "<center><a href='/'><img src='$Image' /></a></center>";
	$Extra = "<a href='$Image' target='_blank'>View Full Size</a>";

	$Tags = DisplayTags($Redirect[1]);
}
elseif(empty($Redirect[1]))
{
	$Tags = DisplayAllTags();
}
else
{
	$Tag = filter_var(str_replace('-', ' ', $Redirect[1]), FILTER_SANITIZE_SPECIAL_CHARS);
	$CountQuery = mysql_query("Select `ID` from `Tags` where `Name`='$Tag'");
	$ImageCount = mysql_num_rows($CountQuery);
	
	$RandomOffset = rand(0, $ImageCount - 1);
	
	#echo "Count: $ImageCount<br />";
	#echo "Seen: ".count($_SESSION[$Tag]['Seen'])."<br />";
	
	while($_SESSION[$Tag]['Seen'][$RandomOffset])
	{
		if($ImageCount - 1 <= count($_SESSION[$Tag]['Seen']))
			unset($_SESSION[$Tag]);
		
		$RandomOffset = rand(0, $ImageCount - 1);
	}

	$_SESSION[$Tag]['Seen'][$RandomOffset] = true;
	
	$RandomQuery = mysql_query("Select `Image` from `Tags` where `Name`='$Tag' limit $RandomOffset, 1");
	list($ImageID) = mysql_fetch_array($RandomQuery);
	
	$ImageQuery = mysql_query("Select `URL`, `Caption`, `Permalink` from `Images` where `ID`='$ImageID'");
	list($URL, $Caption, $Permalink) = mysql_fetch_array($ImageQuery);

	$Title = "<a href='/view/$ImageID/$Permalink/'>$Caption</a>";
	$Image = "$URL";
	$TagPermalink = Permalink($Tag);
	
	echo "<center><a href='/view/$TagPermalink/'><img src='$Image' /></a></center>";
	$Extra = "<a href='$Image' target='_blank'>View Full Size</a>";

	$Tags = DisplayTags($ImageID);
	
	#echo "Displaying a random image from tag: $Tag ($ImageCount)";
}

?>
