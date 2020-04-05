<?php
require '../connection.php';
session_start();
date_default_timezone_set('Asia/Kuala_Lumpur');

// Check admin session
if (isset($_SESSION['adm_id']))
	$adm_id = $_SESSION['adm_id'];
else 
	header('location: ../index.php');

if(isset($_GET['code'])) {
	$code = $_GET['code'];
	$sql = "SELECT * FROM subject WHERE Sub_Code = '$code'";
}

if(isset($_POST['save'])) {
	$sub_code = $_POST['sub_code'];
	$sub_name = $_POST['sub_name'];

	$mod_on = date('Y-m-d H:i:s');

	$q1 = "UPDATE subject
			SET Sub_Code = '$sub_code', Sub_Name = '$sub_name'
			WHERE Sub_Code = '$code'";

	$q2 = "UPDATE adm_sub 
			SET Adm_ID = '$adm_id', Mod_On = '$mod_on'
			WHERE Sub_Code = '$sub_code'";

	if(!$conn->query($q1)) {
		die($conn->error);
	}
	else {
		if (!$conn->query($q2))
			die($conn->error);
		else {
			echo "<script>alert('Data is successfully updated.');
			window.location.href = 'subject-reg.php'</script>";
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
	<style>
		input, textarea {
			display: block;
			width: 200px;
			resize: none;
		}
	</style>
</head>
<body>
	<form action="" method="POST">
		<?php 
		foreach ($conn->query($sql) as $row) {
		?>
		<label for="">Subject Code</label>
		<input type="text" name="sub_code" value="<?php echo $row['Sub_Code']; ?>">
		<br>
		<label for="">Subject Name</label>
		<textarea type="text" name="sub_name" rows="3"><?php echo $row['Sub_Name']; ?></textarea>
		<?php } ?>
		<input type="submit" name="save" value="Save">
	</form>
	
</body>
</html>