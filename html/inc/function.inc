<?php
$con = mysqli_connect($_ENV['DB_HOST_NAME'],$_ENV['DB_USER_NAME'],$_ENV['DB_PASSWORD']);
$db = mysqli_select_db($con,$_ENV['DB_NAME']);

function mysql_query($query){
    global $con;
    return mysqli_query($con,$query);
}

?>