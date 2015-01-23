<link href="/jetpack.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/jquery.mapAttributes.js"></script>
<script type='text/javascript' src='/jetpack.js'></script>

<form>
	<input type='text' name='url'>
	<input type='submit' value='Scrape'>
</form>

<?php

$Caption = "Find New Content";

function is_url($URL)
{
	$URL = parse_url($URL);
	
	if($URL['host'])
	{	
		return true;
	}
	
	return false;
}

if($_GET['url'])
{
	echo "<hr />";
	$URL = $_GET['url'];
	
	if(is_url($URL))
	{
		echo preg_replace("{<script.*?>.*?</script>}", "", file_get_contents($URL));
	}
}

?>