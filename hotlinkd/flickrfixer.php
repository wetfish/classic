<?php

include_once('mysql.php');

$Flickr = mysql_query("Select `ID`, `URL` from `Images` where `URL` LIKE '%flickr.com%'");
while(list($ID, $URL) = mysql_fetch_array($Flickr))
{
	echo "$ID - $URL<br />";
	
	$ParsedURL = parse_url($URL);
	$PathInfo = pathinfo($ParsedURL['path']);			
	$FixedFilename = preg_replace("/_t$/", "_b", $PathInfo['filename']);

	$NewURL = str_replace($PathInfo['filename'], $FixedFilename, $URL);
	
	if($NewURL != $URL)
	{
		mysql_query("Update `Images` set `URL`='$NewURL' where `ID`='$ID'");
		echo "$NewURL<br />";
	}
}

?>
