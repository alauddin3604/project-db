<?php
session_start();
include_once 'class.user.php';

$user = new User();
$error = "";

if (isset($_POST['login']))
{
	if (trim($_POST['id']) == '' || trim($_POST['pass']) == '') { // Check if both fields are not filled
		$error = "*Please fill both input fields!";
	}
	else if (isset($_POST['user'])) { // If radio button is checked
		
		$id = $_POST['id'];
		$pass = $_POST['pass'];

		if ($_POST['user'] == 'student') // If student is selected
		{ 
			$chck_login = $user->student_login($id, $pass);
			if ($chck_login)
				$error = $chck_login;
		}
		else if ($_POST['user'] == 'lecturer') // For lecturer
		{
			$chck_login = $user->lecturer_login($id, $pass);
			if ($chck_login)
				$error = $chck_login;
		}
		else if ($_POST['user'] == 'admin') // If admin is selected
		{ 
			$chck_login = $user->admin_login($id, $pass);
			if ($chck_login)
				$error = $chck_login;
		}
	}	
	else {
		$error = '*Choose either Student, Lecturer or Admin!';
	}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Log In</title>
	<link rel="stylesheet" href="css/w3.css">
	<style>
		.error
		{
			color:red;
		}
	</style>
</head>
<body>
	<div class="w3-container w3-half w3-margin">
		<form class="w3-container w3-card-4" action="" method="POST" autocomplete="off">
			<h3>Login Page</h3>
			<input class="w3-input" type="text" name="id" id="" placeholder="ID">
			<br>
			<br>
			<input class="w3-input" type="password" name="pass" id="pass" placeholder="Password">
			<br>
			<input class="w3-button w3-light-grey w3-border" type="submit" name="login" value="Login">
			<br>
			<p>
				<input class="w3-radio" type="radio" id="student" name="user" value="student">
				<label for="student">Student</label>
			</p>
			<p>
				<input class="w3-radio" type="radio" id="lecturer" name="user" value="lecturer">
				<label for="lecturer">Lecturer</label>
			</p>
			<p>
				<input class="w3-radio" type="radio" id="admin" name="user" value="admin">
				<label for="admin">Admin</label>
			</p>					
		</form>
		<p class="error"><?php echo $error; ?></p>
	</div>
	
</body>
</html>