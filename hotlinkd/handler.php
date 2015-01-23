<?php

include_once('mysql.php');
include_once('simple_html_dom.php');
include_once('functions.php');

#print_r(get_html_translation_table(HTML_ENTITIES));

function HandleImage($ID, $Source, $URL, $Caption)
{
	$SourceQuery = mysql_query("Select `URL`, `Tags` from `Sites` where `ID`='$Source'");
	list($SourceURL, $SourceTags) = mysql_fetch_array($SourceQuery);

	$Time = time();
	$Permalink = Permalink($Caption);

	mysql_query("Insert into `Images` values ('', '$Time', '$URL', '$SourceURL', '$Caption', '$Permalink', '0')");
	$ImageID = mysql_insert_id();

	echo "$ID - $Source - $URL - $Permalink - $Caption<br />\n";

	$Tags = explode(',', $SourceTags);

	foreach($Tags as $Tag)
	{
		$Tag = trim($Tag);
		mysql_query("Insert into `Tags` values ('', '$Tag', '$ImageID')");
	}
	
	mysql_query("Delete from `Queue` where `ID`='$ID'");
}

function is_image($URL)
{
	$ParsedURL = parse_url($URL);

	if($ParsedURL['host'])
	{
		$File = pathinfo($ParsedURL['path']);
		
		if(in_array(strtolower($File['extension']), array('jpg', 'jpeg', 'png', 'gif')))
		{
			return true;
		}
	}
	
	return false;
}

$Query = mysql_query("Select `ID`, `Source`, `URL`, `Caption` from `Queue` order by `ID` desc limit 1000");
while(list($ID, $Source, $URL, $Caption) = mysql_fetch_array($Query))
{
	$ImageQuery = mysql_query("Select `ID` from `Images` where `URL`='$URL'");
	list($Duplicate) = mysql_fetch_array($ImageQuery);
	
	if($Duplicate)
	{
		mysql_query("Delete from `Queue` where `ID`='$ID'");
		echo "This image already exists.<br />\n";
	}
	else
	{
		$ParsedURL = parse_url($URL);
	
		if(preg_match("/imgur.com$/", $ParsedURL['host']))
		{
			if(is_image($URL) == false)
			{			
				$URL = substr($URL, 0, strpos($URL, '?'));
				$HTML = file_get_html($URL);
				
				foreach($HTML->find("div.image") as $Container)
				{
					$URL = $Container->find("a", 0)->href;
				}
				
				$HTML->clear(); 
				unset($HTML);
			}
			
			$ImageQuery = mysql_query("Select `ID` from `Images` where `URL`='$URL'");
			list($Duplicate) = mysql_fetch_array($ImageQuery);
	
			if($Duplicate)
			{
				mysql_query("Delete from `Queue` where `ID`='$ID'");
				echo "This image already exists.<br />\n";
			}
			else
			{	
				HandleImage($ID, $Source, $URL, $Caption);
			}
		}
		elseif(preg_match("/imageshack.us$/", $ParsedURL['host']))
		{
			if(is_image($URL))
			{
				HandleImage($ID, $Source, $URL, $Caption);
			}
		}
		elseif(preg_match("/amazonaws.com$/", $ParsedURL['host']))
		{
			# Content hosted on amazon usually expires. Man, I loved that slurpee image too. Fuck.
		}
		elseif(preg_match("/wiki(media|pedia)/", $ParsedURL['host']))
		{
			# Custom SHIT
		}
		elseif(preg_match("/flickr.com$/", $ParsedURL['host']))
		{
			if(is_image($URL))
			{
				#print_r($ParsedURL);
				
				$PathInfo = pathinfo($ParsedURL['path']);			
				$FixedFilename = preg_replace("/_t$/", "_b", $PathInfo['filename']);

				$URL = str_replace($PathInfo['filename'], $FixedFilename, $URL);
				
				HandleImage($ID, $Source, $URL, $Caption);
			}
		}
		else
		{
			if(is_image($URL))
			{
				HandleImage($ID, $Source, $URL, $Caption);
			}
		}
	}
}


?>
