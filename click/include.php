<?php

// You gotta make a config file!
require("config.php");

mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD);
mysql_select_db(MYSQL_DATABASE);

function replace($input)
{
    $output = str_ireplace('icanhazchat.com/pibdgaf', 'wetfish.net', $input);
    $output = str_ireplace('http://', 'https://', $input);
    
    return $output;
}

?>
