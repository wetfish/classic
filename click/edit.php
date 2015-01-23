<?php

require('mysql.php');

if($_POST)
{
	$Page = stripslashes($_GET['page']);
	$Page = filter_var($Page, FILTER_SANITIZE_SPECIAL_CHARS);
	$Time = time();
	$IP = $_SERVER['REMOTE_ADDR'];
	$Data = stripslashes($_POST['data']);
	$Data = mysql_real_escape_string($Data);
	$ID = mysql_real_escape_string($_GET['id']);
	
	if(in_array(trim(str_replace('&nbsp;', ' ', $Data)), array("", "<br>", "<br />", "Care to write something?")))
	{
		# Useless content.
	}
	else
	{
		mysql_query("Update `Pages` set `Data`='$Data', `Time`='$Time', `IP`='$IP' where `ID`='$ID'");
	}
}


?>