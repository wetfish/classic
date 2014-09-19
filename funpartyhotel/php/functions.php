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

function FormatURL($Text)
{
	$PotentialURL = trim(str_replace(array('&nbsp;', '<br>', 'Â'), ' ', $Text));
	
	if(filter_var($PotentialURL, FILTER_VALIDATE_URL))
		$Text = str_replace($PotentialURL, "<a href='$PotentialURL' target='_blank'>$PotentialURL</a>", $Text);
	else
	{
		foreach(explode(" ", str_replace(array('&nbsp;', '<br>', 'Â'), ' ', $Text)) as $URL)
		{
			if(filter_var($URL, FILTER_VALIDATE_URL))
				$Text = str_replace($URL, "<a href='$URL' target='_blank'>$URL</a>", $Text);
		}
	}
	
	return $Text;
}

?>