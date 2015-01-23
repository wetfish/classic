<?php

function FormatTime($Timestamp)
{
	$Now = time();
	$Passed = $Now - $Timestamp;
	
	if($Passed < 60)
	{
		if($Passed != 1)
			$Plural = 's';
			
		return "$Passed second{$Plural} ago";
	}
	elseif($Passed < 3600)
	{
		$Passed = round($Passed / 60);
		
		if($Passed != 1)
			$Plural = 's';
		
		return "$Passed minute{$Plural} ago";
	}
	elseif($Passed < 86400)
	{	
		$Passed = round($Passed / 60);
		$Passed = round($Passed / 60);

		if($Passed != 1)
			$Plural = 's';
		
		return "$Passed hour{$Plural} ago";
	}
	else
	{	
		$Passed = round($Passed / 24);
		$Passed = round($Passed / 60);
		$Passed = round($Passed / 60);	
		
		if($Passed != 1)
			$Plural = 's';
		
		return "$Passed day{$Plural} ago";
	}
}

function Permalink($Text)
{
	$Text = html_entity_decode($Text, ENT_QUOTES);
	$Text = preg_replace('/[^0-9a-z-_ ]/i', '', $Text);
	$Text = str_replace(array('-', '_'), ' ', $Text);
	
	while(strpos($Text, "  ") !== FALSE)
	{
		$Text = str_replace("  ", " ", $Text);
	}
	
	$Text = explode(' ', $Text);
	$Text = array_slice($Text, 0, 16);
	$Text = implode('-', $Text);
	$Text = trim($Text, '-');
	
	return $Text;
}

function DisplayTags($ImageID)
{
	$TagQuery = mysql_query("Select `Name` from `Tags` where `Image`='$ImageID'");
	while(list($TagName) = mysql_fetch_array($TagQuery))
	{
		$CountQuery = mysql_query("Select `ID` from `Tags` where `Name`='$TagName'");
		$ImageCount = mysql_num_rows($CountQuery);
		$TagPermalink = Permalink($TagName);
	
		$Tags .= "<span class='tag'><a href='/view/$TagPermalink/'>$TagName</a> ($ImageCount)</span> ";
	}
	
	return $Tags;
}

function DisplayAllTags()
{
	$TagQuery = mysql_query("Select `Name` from `Tags` group by `Name`");
	while(list($TagName) = mysql_fetch_array($TagQuery))
	{
		$CountQuery = mysql_query("Select `ID` from `Tags` where `Name`='$TagName'");
		$ImageCount = mysql_num_rows($CountQuery);
		$TagPermalink = Permalink($TagName);
	
		$Tags .= "<div class='tag' style='display:inline-block'><a href='/view/$TagPermalink/'>$TagName</a> ($ImageCount)</div> ";
	}
	
	return $Tags;
}

?>
