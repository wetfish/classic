<?php

$Vowels = array('a', 'ar', 'e', 'eg', 'eugh', 'i', 'in', 'o', 'or', 'u', 'un');
shuffle($Vowels);

$Blog = "Bl{$Vowels[0]}g";

if($_GET['redirect'])
{
	session_start();
	include_once('template.php');
	
	$Redirect = explode('/', $_GET['redirect']);
	$File = strtolower("{$Redirect[0]}.php");
	
	if(is_file($File))
	{
		ob_start();
		include($File);
		$Content = ob_get_contents();
		ob_end_clean();
	}
	
	echo Template('template.html');

}

?>
