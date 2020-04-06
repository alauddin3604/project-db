<?php
require 'connection.php';
session_start();
$error = "";

if (isset($_POST['login']))
{
	if (trim($_POST['id']) == '' || trim($_POST['pass']) == '') { // If both fields are not filled
		$error = "*Please fill both input fields!";
	}
	else if (isset($_POST['user'])) { // If radio button is checked
		
		$id = $_POST['id'];
		$pass = $_POST['pass'];

		if ($_POST['user'] == 'admin') { // If admin option is selected
			$query = "SELECT Adm_ID, Adm_Pass, Adm_Log FROM admin WHERE Adm_ID = $id";

			if ($result = $conn->query($query)) {
				
				if ($result->num_rows > 0) {
					$row = $result->fetch_assoc();
					
					if (password_verify($pass, $row['Adm_Pass'])) {
						$_SESSION['session_id'] = $id;
						if ($row['Adm_Log'] == false) {
							$conn->query("UPDATE admin SET Adm_Log = 1 WHERE Adm_ID = '$id'");
							
							header('location: pass-update.php');
						}
						else {
							header('location: admin/home.php');
						}
					}
					else {
						$error = "Invalid ID or password!";
					}
				}
				else { $error = "Invalid ID or password!"; }
			}
			else {
				$error = "Invalid ID or password!";
			}	
		}
		else if ($_POST['user'] == 'student') { // If student option is selected
			$query = "SELECT Stud_ID, Stud_Pass, Stud_Log FROM student WHERE Stud_ID = '$id'";

			if($result = $conn->query($query))
			{
				$row = $result->fetch_array(MYSQLI_ASSOC);
				if($result->num_rows > 0)
				{
					if (password_verify($pass, $row['Stud_Pass'])) {
						$_SESSION['stud_id'] = $id;
						if ($row['Stud_Log'] == false)
						{
							$conn->query("UPDATE student SET Stud_Log = 1 WHERE Stud_ID = '$id'");
							//echo "Jadi";
							header('location: pass-update.php');
						}
						else
						{
							header('location: student/home.php');
						}
					}
					else
					{
						$error = "Invalid ID or password!";
					}
				}
				else { $error = "Invalid ID or password!"; }
			}
			else
			{
				$error = "Invalid ID or password!";
			}
		}
		else if ($_POST['user'] == 'lecturer') { // For lecturer
			$query = "SELECT Lect_ID, Lect_Pass, Lect_Log FROM lecturer WHERE Lect_ID = '$id'";

			if($result = $conn->query($query)) {
				$row = $result->fetch_array(MYSQLI_ASSOC);
				if($result->num_rows > 0) {
					if (password_verify($pass, $row['Lect_Pass'])) {
						$_SESSION['lect_id'] = $id;
						if ($row['Lect_Log'] == false)
						{
							$conn->query("UPDATE lecturer SET Lect_Log = 1 WHERE Lect_ID = '$id'");
							//echo "Jadi";
							header('location: pass-update.php');
						}
						else
						{
							header('location: lecturer/home.php');
						}
					}
					else
					{
						$error = "Invalid ID or password!";
					}
				}
				else { $error = "Invalid ID or password!"; }
			}
			else
			{
				$error = "Invalid ID or password!";
			}
		}
	}	
	else {
		$error = "*Choose either Student, Lecturer or Admin!";
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
		<form class="w3-container w3-card-4" action="" method="POST">
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