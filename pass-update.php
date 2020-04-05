<?php
require 'connection.php';
session_start();

if (isset($_SESSION['adm_id']))
	$welcome_id = $_SESSION['adm_id'];
else if (isset($_SESSION['stud_id']))
	$welcome_id = $_SESSION['stud_id'];
else if (isset($_SESSION['lect_id']))
	$welcome_id = $_SESSION['lect_id'];
	

if(isset($_POST['update']))
{
	if (isset($_SESSION['stud_id']))
	{
		$stud_id = $_SESSION['stud_id'];

		if(trim($_POST['new_pass']) == trim($_POST['confirm_pass']))
		{
			$new_pass = password_hash($_POST['new_pass'], PASSWORD_DEFAULT);
			$sql = "UPDATE student SET Stud_Pass = '$new_pass' WHERE Stud_ID = '$stud_id'";
			if($conn->query($sql))
			{
				header('location: student/home.php');
			}
			else
			{
				echo $conn->error;
			}
		}
	}
	else if (isset($_SESSION['lect_id'])) {
		
		$lect_id = $_SESSION['lect_id'];

		if(trim($_POST['new_pass']) == trim($_POST['confirm_pass']))
		{
			$new_pass = password_hash($_POST['new_pass'], PASSWORD_DEFAULT);
			$sql = "UPDATE lecturer SET Lect_Pass = '$new_pass' WHERE Lect_ID = '$lect_id'";
			if($conn->query($sql))
			{
				header('location: lecturer/home.php');
			}
			else
			{
				echo $conn->error;
			}
		}
	}
	else if (isset($_SESSION['adm_id'])) {
		
		$adm_id = $_SESSION['adm_id'];

		if(trim($_POST['new_pass']) == trim($_POST['confirm_pass']))
		{
			$new_pass = password_hash($_POST['new_pass'], PASSWORD_DEFAULT);
			$sql = "UPDATE admin SET Adm_Pass = '$new_pass' WHERE Adm_ID = '$adm_id'";
			if($conn->query($sql))
			{
				header('location:admin/home.php');
			}
			else
			{
				echo $conn->error;
			}
		}
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Password Update</title>
	<link rel="stylesheet" href="css/w3.css">
	<style>
		label {
			width: 150px;
			display: inline-block;
		}
		input {
			padding: 6px;
		}
	</style>
</head>
<body>
	<h3>Update Password</h3>
	<p>Welcome <?php echo $welcome_id ?>, please update your password before proceed.</p>
	<form action="" method="post">
		<label for="new_pass">New Password</label>
		<input type="password" name="new_pass" id="new_pass">
		<br>
		<br>
		<label for="confirm_pass">Confirm Password</label>
		<input type="password" name="confirm_pass" id="confirm_pass">
		<br>
		<br>
		<label for="submit"></label>
		<input type="submit" name="update" id="submit" value="Update">
	</form>
</body>
</html>