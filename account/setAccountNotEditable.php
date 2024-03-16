<?php
session_start();
$_SESSION["accounteditable"] = false;
header("account.php");
