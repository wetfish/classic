<?php

# Maps
#
# ID
# Name
# Description
# Image
# Spawn

include('mysql.php');
include('functions.php');
session_start();

echo "Welcome to the lobby.<hr />";

echo "<table>";

$MapQuery = mysql_query("Select `ID`, `Name`, `URL`,  `Description`
							from `Maps`");
							
while(list($ID, $Name, $URL, $Description) = mysql_fetch_array($MapQuery))
{
	$Activity = mysql_query("Select `Active`
								from `Players`
								where `Map`='$ID'
								order by `Active`
								desc limit 1");
	
	list($ActiveTime) = mysql_fetch_array($Activity);
	$ActiveTime = FormatTime($ActiveTime);
	
	echo "<tr><td style='width:111px;'> <a href='$URL'>$Name</a><br />$ActiveTime </td><td> $Description </td></tr>";
}

echo "</table>";

?>