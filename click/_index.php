<?php

# We need a database.
# And a user account, for that matter.

#mysql_connect... how do you do that again?

session_start();
require('mysql.php');


if(empty($_SESSION['Timezone']))
{
	$XML = simplexml_load_file("http://ipinfodb.com/ip_query.php?ip={$_SERVER['REMOTE_ADDR']}&timezone=true");
	$_SESSION['Timezone'] = (string)$XML->TimezoneName[0];
}


# Now what?
# I wanna be able to click on the screen to start typing.

$Page = stripslashes($_GET['page']);
$Page = filter_var($Page, FILTER_SANITIZE_SPECIAL_CHARS);

# I should probably get around to changing page to path.
# That way derp can be page.
# Maybe even revamp the database for more statistical information.

$Derp = stripslashes($_GET['p']);
$Derp = filter_var($Derp, FILTER_SANITIZE_SPECIAL_CHARS);
if(empty($Derp))
	$Derp = 1;


require('template.php');
echo Template('template.html');

?>