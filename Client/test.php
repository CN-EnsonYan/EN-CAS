<?php

session_start();
$stat = $_SESSION['encas_status'];

echo $stat;
echo session_id();

?>