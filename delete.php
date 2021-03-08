<?php
require_once "config.php";

//$db = new PDO("mysql:host=$hostname;dbname=$dbname;charset=utf8", $username, $password);
$db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$query = "DELETE FROM `umiestnenia` WHERE `person_id` = ".$_GET['id'].";";
$query .= "DELETE FROM `osoby` WHERE id = ".$_GET['id'].";";

$stmt = $db->query($query);

header('Location:index.php');
?>