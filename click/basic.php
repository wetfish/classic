<?php

session_start();
require('include.php');


$ID = stripslashes($_GET['id']);
$ID = filter_var($ID, FILTER_SANITIZE_SPECIAL_CHARS);

$Query = mysql_query("Select `Page`,`Time`,`Data`,`Deleted`
						from `Pages`
						where `ID`='$ID'
						limit 1");
						
list($Page, $Time, $Data, $Deleted) = mysql_fetch_array($Query);

if($Time and (!$Deleted))
{
    echo replace($Data);
}

?>
