<?php

include_once('mysql.php');
include_once('functions.php');
include_once('paginate.php');

$RecentQuery = "Select `ID`, `Time`, `Caption`, `Permalink` from `Images` order by `ID` desc";
list($Data, $Links) = Paginate($RecentQuery, 25, $_GET['page'], $_SERVER['QUERY_STRING'], 'redirect');

if($Data)
{		
	echo "<center>$Links</center>";
	echo "<table>";

	foreach($Data as $Result)
	{
		list($ID, $Time, $Caption, $Permalink) = $Result;
		$Time = FormatTime($Time);

		echo "<tr><td class='time'>$Time</td> <td><a href='/view/$ID/$Permalink/'>$Caption</a></a></td></tr>\n";
	}

	echo "</table>";
	echo "<center>$Links</center>";
}

$Caption = "Recent Images";
$Title = "Recent Images";

?>
