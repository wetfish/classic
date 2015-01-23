<?php

include_once('mysql.php');
include_once('simple_html_dom.php');

# Sites
########
# ID
# Freshness
# URL
# Tags


# Queue
#########
# ID
# Time
# URL
# Source
# Caption


$Query = mysql_query("Select `ID`, `URL` from `Sites` order by `Freshness` limit 1");
list($SourceID, $Source) = mysql_fetch_array($Query);

echo "$Source<hr />";

$HTML = file_get_html($Source);

$SourceInfo = parse_url($Source);

if(preg_match("/reddit.com$/", $SourceInfo['host']))
{
	foreach($HTML->find("a.title") as $Link)
	{
		$URL = parse_url($Link->href);

		if($URL['host'])
		{
			$Time = time();
			$URL = filter_var(html_entity_decode($Link->href, ENT_QUOTES), FILTER_SANITIZE_SPECIAL_CHARS);
			$Caption = filter_var(html_entity_decode($Link->innertext, ENT_QUOTES), FILTER_SANITIZE_SPECIAL_CHARS);
	
			mysql_query("Insert into `Queue` values ('', '$Time', '$SourceID', '$URL', '$Caption')");
			mysql_query("Update `Sites` set `Freshness`='$Time' where `ID`='$SourceID'");
	
			echo "$URL &mdash; $Caption<br /><br />\n";
		}
	
		$LinkCount++;
	}

	if(empty($LinkCount))
	{
		$HTML->clear();
		unset($HTML);
	
		$Response = html_entity_decode(file_get_contents("$Source/.rss"), ENT_QUOTES);
		$HTML = str_get_html($Response);

		foreach($HTML->find("item") as $Element)
		{
			$Caption = filter_var(html_entity_decode($Element->find('title', 0)->innertext, ENT_QUOTES), FILTER_SANITIZE_SPECIAL_CHARS);
	
			foreach($Element->find('a') as $Link)
			{
				if($Link->innertext == "[link]")
					$URL = filter_var(html_entity_decode($Link->href, ENT_QUOTES), FILTER_SANITIZE_SPECIAL_CHARS);
			}
		

			$Time = time();
		
			mysql_query("Insert into `Queue` values ('', '$Time', '$SourceID', '$URL', '$Caption')");
			mysql_query("Update `Sites` set `Freshness`='$Time' where `ID`='$SourceID'");

			echo "$URL &mdash; $Caption<br /><br />\n";
		}
	}
}
elseif(preg_match("/flickr.com$/", $SourceInfo['host']))
{
	foreach($HTML->find("img.pc_img") as $Image)
	{
		$URL = parse_url($Image->src);

		if($URL['host'])
		{
			$Time = time();
			$URL = filter_var(html_entity_decode($Image->src, ENT_QUOTES), FILTER_SANITIZE_SPECIAL_CHARS);
			$Caption = filter_var(html_entity_decode($Image->alt, ENT_QUOTES), FILTER_SANITIZE_SPECIAL_CHARS);
	
			mysql_query("Insert into `Queue` values ('', '$Time', '$SourceID', '$URL', '$Caption')");
			mysql_query("Update `Sites` set `Freshness`='$Time' where `ID`='$SourceID'");
	
			echo "$URL &mdash; $Caption<br /><br />\n";
		}
	}
}


?>
