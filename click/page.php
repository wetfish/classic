<?php

require('include.php');

$Page = stripslashes($_GET['page']);
$Page = filter_var($Page, FILTER_SANITIZE_SPECIAL_CHARS);

$Query = mysql_query("Select `Data` from `Pages` where `Page`='$Page'");
list($Data) = mysql_fetch_array($Query);

if($Data)
{
	echo $Data;
}
else
{
	echo "There's nothing here yet.";
}

?>
