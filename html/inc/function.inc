<?php
include_once("/var/www/inc/secret.inc");
$con = mysqli_connect($host,$user_name,$password);
$db = mysqli_select_db($con,$dbname);

function mysql_query($query){
    global $con;
    return mysqli_query($con,$query);
}

?>