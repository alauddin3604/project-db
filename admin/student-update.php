<?php
require '../connection.php';
session_start();
date_default_timezone_set('Asia/Kuala_Lumpur');

// Check admin session
if (isset($_SESSION['adm_id']))
	$adm_id = $_SESSION['adm_id'];
else 
	header('location: ../index.php');

if(isset($_POST['update'])) {
	$id = $_POST['stud_id'];
	$sql = "SELECT * FROM student WHERE Stud_ID = '$id'";
}

if(isset($_POST['save'])) {
	$stud_id = $_POST['stud_id'];
	$stud_name = $_POST['stud_name'];
	$stud_email = $stud_id.'@siswa.uthm.edu.my';

	$mod_on = date('Y-m-d H:i:s');

	$q1 = "UPDATE student
			SET Stud_ID = '$stud_id', Stud_Name = '$stud_name', Stud_Email = '$stud_email'
			WHERE Stud_ID = '$id'";

	$q2 = "UPDATE adm_stud 
			SET Adm_ID = '$adm_id', Mod_On = '$mod_on'
			WHERE Stud_ID = '$id'";

	$result = $conn->query($q1);

	if(!$result) {
		die($conn->error);
	}
	else {
		if (!$conn->query($q2))
			die($conn->error);
		else {
			echo "<script>alert('Data is successfully updated.');
				window.location.href = 'student-reg.php'</script>";
		}
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Update Student</title>
	<link rel="stylesheet" href="../css/w3.css">
	<style>
	</style>
</head>
<body>
	<div class="w3-container">
		<form class="w3-container w3-display-middle w3-card-4 w3-padding-24" action="" method="POST">
			<?php 
			foreach ($conn->query($sql) as $row) {
			?>
			<label for="">Student ID</label>
			<input class="w3-input w3-border" type="text" name="stud_id" value="<?php echo $row['Stud_ID']; ?>">
			<br>
			<label for="">Student Name</label>
			<textarea class="w3-input w3-border" type="text" name="stud_name" cols="24" rows="3" style="resize: none;"><?php echo $row['Stud_Name']; ?></textarea>
			<?php } ?>
			<p>
				<input class="w3-button w3-block w3-light-grey w3-border" type="submit" name="save" value="Save">
			</p>
		</form>
	</div>
</body>
</html>