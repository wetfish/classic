<?php

# Get all activity since this session's refresh time.
# Set new refresh time.

include('mysql.php');
session_start();
$Session = session_id();

$Map = filter_var(trim(stripslashes($_GET['map'])), FILTER_SANITIZE_SPECIAL_CHARS);
$_SESSION['Map'] = $Map;

$AccountQuery = mysql_query("Select `Character`, `Name`
							from `Accounts`
							where `ID`='{$_SESSION['AccountID']}'");
								
list($CharacterID, $Name) = mysql_fetch_array($AccountQuery);

$CharacterQuery = mysql_query("Select `Image`
								from `Characters`
								where `ID`='$CharacterID'"); 
								
list($Image) = mysql_fetch_array($CharacterQuery);

$Data['me'] = array("name" => $Name,
							"image" => $Image,
							"location" => "50,50");
	
echo json_encode($Data);

?>