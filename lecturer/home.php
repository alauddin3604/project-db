<?php
session_start();
require '../connection.php';

if (isset($_SESSION['lecturer_id']))
{
	$lecturer_id = $_SESSION['lecturer_id'];
}
else
{
	header('location: ../index.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
</head>
<body>
	<a href="../logout.php">Logout</a>
</body>
</html>
