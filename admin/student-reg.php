<?php
require '../connection.php';
session_start();
date_default_timezone_set('Asia/Kuala_Lumpur');

if (isset($_SESSION['adm_id']))
	$adm_id = $_SESSION['adm_id'];
else
	header('location: ../index.php');

$q = "SELECT Adm_ID, Adm_Name FROM admin WHERE Adm_ID = '$adm_id'";
if(!$result = $conn->query($q)) {
	echo $conn->error;
}
else {
	$row = $result->fetch_array(MYSQLI_ASSOC);
	$adm_name = $row['Adm_Name'];
}

if (isset($_POST['add'])) {
	$stud_id = $_POST['stud_id'];
	$stud_name = $_POST['stud_name'];
	$stud_email = $stud_id."@siswa.uthm.edu.my";
	$stud_pass = password_hash($stud_id, PASSWORD_DEFAULT);
	$stud_log = 0;

	$mod_on = date("Y-m-d H:i:s");
	
	$sql1 = "INSERT INTO student VALUES('$stud_id', '$stud_name', '$stud_email', '$stud_pass', '$stud_log')";
	$sql2 = "INSERT INTO adm_stud VALUES('$adm_id', '$stud_id', '$mod_on')";

	if ($conn->query($sql1)) {
		if($conn->query(($sql2))) {
			echo "<script>alert('New data is successfully recorded');</script>";
		}
		else {
			echo $conn->error;
		}
	 }
	 else {
		echo $conn->error;
	 }
}

if (isset($_GET['button'])) {
	if ($_GET['button'] == "delete") {
		$stud_id = $_GET['id'];

		$query = "DELETE FROM student WHERE Stud_ID = '$stud_id'";
		$result = $conn->query($query);
		if ($result === TRUE) {
			echo "<script>alert('Record deleted successfully');</script>";
		} else {
			echo "<script>alert('Error deleting record');</script>";
		}
	}
	else if ($_GET['button'] == "save") {
		echo "test";
	}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Register Student</title>
	<link rel="stylesheet" href="../css/w3.css">
	<style>
	td:nth-of-type(1) {
		width: 200px;
	}
</style>
</head>
<body>
	<div class="w3-container">
		<div class="w3-bar w3-light-grey">
			<a href="home.php" class="w3-bar-item w3-button">Home</a>
			<a href="admin-reg.php" class="w3-bar-item w3-button">Admin</a>
			<a href="student-reg.php" class="w3-bar-item w3-button w3-black">Student</a>
			<a href="lecturer-reg.php" class="w3-bar-item w3-button">Lecturer</a>
			<a href="subject-reg.php" class="w3-bar-item w3-button">Subject</a>
			<a href="workload-reg.php" class="w3-bar-item w3-button">Workload</a>
			<a href="../logout.php" class="w3-bar-item w3-button w3-right">Log Out</a>
		</div>
		<p>Current session: <?php echo $adm_id.", ".$adm_name ?></p>
		<br>
		<br>
		<table class="w3-table w3-bordered">
			<thead>
				<tr>
					<th>Student ID</th>
					<th>Student Name</th>
					<th>Student Email</th>
					<th>Modified By</th>
					<th>Modified On</th>
					<th>Update</th>
					<th>Delete</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$sql = "SELECT s.Stud_ID, s.Stud_Name, s.Stud_Email, a.Adm_ID, a.Adm_Name, Mod_On
							FROM adm_stud
							INNER JOIN admin as a ON (adm_stud.Adm_ID=a.Adm_ID)
							INNER JOIN student as s ON (adm_stud.Stud_ID=s.Stud_ID)";
					
					if($result = $conn->query($sql)){
						if($result->num_rows > 0) {
							while ($row = $result->fetch_assoc()) { ?>
								<tr>					
								<td><?php echo $row['Stud_ID']; ?></td>
								<td><?php echo $row['Stud_Name']; ?></td>
								<td><?php echo $row['Stud_Email']; ?></td>
								<td><?php echo $row['Adm_Name']; ?></td>
								<td class="w3-center">
									<?php $date = $row['Mod_On'];
									echo date("j/n/Y g:i:s A", strtotime($date)); ?>
								</td>
								<form action="student-update.php" method="post">
									<input type="text" name="stud_id" value="<?php echo $row["Stud_ID"]; ?>" hidden />
									<td><button type="submit" name="update">Update</button></td>
								</form>
								<form action="delete.php" method="POST">
									<input type="text" name="stud_id" value="<?php echo $row["Stud_ID"]; ?>" hidden />
									<td><button type="submit" name="delete">Delete</button></td>	
								</form>
								</tr>
								<?php
							}
						}
					}
					else {
						echo $conn->error;
					}
				?>
				<tr>
					<form action="" method="POST">
						<td><input class="w3-input" type="text" name="stud_id" placeholder="Add student ID"/></td>
						<td><input class="w3-input" type="text" name="stud_name" placeholder="Add student name" /></td>
						<td><input class="w3-button w3-light-grey w3-border " type="submit" name="add" value="Add" /></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</form>
				</tr>
			</tbody>
		</table>
	</div>
</body>
</html>