<?php
require 'connection.php';
session_start();
$msg = '';
if (isset($_SESSION['admin_id']))
	$welcome_id = $_SESSION['admin_id'];
else if (isset($_SESSION['student_id']))
	$welcome_id = $_SESSION['student_id'];
else if (isset($_SESSION['lecturer_id']))
	$welcome_id = $_SESSION['lecturer_id'];
	

if (isset($_POST['update']))
{
	if (trim($_POST['new_pass']) != trim($_POST['confirm_pass'])) // Check if passwords are matching 
	{
		$msg = '<p style="color: red;">*Password doesn\'t match!</p>';
	}
	else
	{
		if (isset($_SESSION['student_id'])) // If current session is student
		{
			$student_id = $_SESSION['student_id'];

			$student_password = password_hash($_POST['new_pass'], PASSWORD_DEFAULT);
			$sql = 'UPDATE students SET student_password = ? WHERE student_id = ?';
			$stmt = $conn->prepare($sql);
			$stmt->bind_param('ss', $student_password, $student_id);
			
			if($stmt->execute())
	 		{
				header('location: student/home.php');
	 		}
			else
	 		{
	 			echo $conn->error;
			}
		}
		else if (isset($_SESSION['lecturer_id'])) // If current session is lecturer
		{
			$lecturer_id = $_SESSION['lecturer_id'];

			$lecturer_password = password_hash($_POST['new_pass'], PASSWORD_DEFAULT);
			$sql = 'UPDATE lecturers SET lecturer_password = ? WHERE lecturer_id = ?';
			$stmt = $conn->prepare($sql);
			$stmt->bind_param('si', $lecturer_password, $lecturer_id);
			if($stmt->execute())
			{
				header('location: lecturer/home.php');
			}
			else
			{
				echo $conn->error;
			}

		} 
		else if (isset($_SESSION['admin_id'])) // If current session is admin
		{
			$admin_id = $_SESSION['admin_id'];

			$admin_password = password_hash($_POST['new_pass'], PASSWORD_DEFAULT);
			$sql = 'UPDATE admins SET admin_password = ? WHERE admin_id = ?';
			$stmt = $conn->prepare($sql);
			$stmt->bind_param('si', $admin_password, $admin_id);
			if($stmt->execute())
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
	<div class="w3-container">
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
		<?php echo $msg; ?>
	</div>	
</body>
</html>