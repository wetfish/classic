<?php

# Wetfish Hotlink

# Images
#########
# ID
# Time
# URL
# Source
# Caption
# Views

# Tags
#######
# ID
# Name
# Image

session_start();
include_once('template.php');
include_once('mysql.php');
include_once('functions.php');

$Vowels = array('a', 'e', 'i','o', 'u');
shuffle($Vowels);

$Blog = "Bl{$Vowels[0]}g";

$TotalQuery = mysql_query("Select `ID` from `Images` order by `ID` desc limit 1");
list($Total) = mysql_fetch_array($TotalQuery);

$RandomID = rand(1, $Total);

while($_SESSION['Seen'][$RandomID])
{
	if($Total <= count($_SESSION['Seen']))
		unset($_SESSION['Seen']);

	$RandomID = rand(1, $Total);
}

$_SESSION['Seen'][$RandomID] = true;

$RandomQuery = mysql_query("Select `URL`, `Caption`, `Permalink` from `Images` where `ID`='$RandomID'");
list($URL, $Caption, $Permalink) = mysql_fetch_array($RandomQuery);

$Title = "<a href='/view/$RandomID/$Permalink/'>$Caption</a>";
$Image = "$URL";
$Time = time();
$Content = "<center><a href='/'><img src='$Image' /></a></center>";
$Extra = "<a href='$Image' target='_blank'>View Full Size</a>";

$Tags = DisplayTags($RandomID);

echo Template('template.html');

?>


