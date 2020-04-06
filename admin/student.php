<?php
require '../connection.php';
session_start();
date_default_timezone_set('Asia/Kuala_Lumpur');
$msg = "";

if (isset($_SESSION['session_id']))
	$session_id = $_SESSION['session_id'];
else
	header('location: ../index.php');

$q = "SELECT Adm_ID, Adm_Name FROM admin WHERE Adm_ID = '$session_id'";
if(!$result = $conn->query($q)) {
	echo $conn->error;
}
else {
	$row = $result->fetch_assoc();
	$session_name = $row['Adm_Name'];
}

if (isset($_POST['add'])) { // Add new data
	$stud_id = $_POST['stud_id'];
	$stud_name = $_POST['stud_name'];
	$stud_email = $stud_id."@siswa.uthm.edu.my";
	$stud_pass = password_hash($stud_id, PASSWORD_DEFAULT);
	$stud_log = 0;

	$mod_on = date("Y-m-d H:i:s");
	
	$sql1 = "INSERT INTO student VALUES('$stud_id', '$stud_name', '$stud_email', '$stud_pass', '$stud_log')";
	$sql2 = "INSERT INTO adm_stud VALUES('$session_id', '$stud_id', '$mod_on')";

	if ($conn->query($sql1)) {
		if($conn->query(($sql2))) {
			$msg = '<div class="w3-panel w3-pale-green w3-display-container">
			<span onclick="this.parentElement.style.display=\'none\'"
			class="w3-button w3-large w3-display-topright">&times;</span>
			<h3>Success!</h3>
			<p>Data is updated successfully.</p>
			</div>';
		}
		else {
			echo $conn->error;
		}
	 }
	 else {
		echo $conn->error;
	 }
}

if(isset($_POST['update'])) { // Update data
	$stud_id = $_POST['stud_id'];
	$stud_name = $_POST['stud_name'];
	$stud_email = $stud_id.'@siswa.uthm.edu.my';

	$mod_on = date('Y-m-d H:i:s');

	$q1 = "UPDATE student
			SET Stud_ID = '$stud_id', Stud_Name = '$stud_name', Stud_Email = '$stud_email'
			WHERE Stud_ID = '$stud_id'";

	$q2 = "UPDATE adm_stud 
			SET Adm_ID = '$session_id', Mod_On = '$mod_on'
			WHERE Stud_ID = '$stud_id'";

	$result = $conn->query($q1);

	if(!$result) {
		die($conn->error);
	}
	else {
		if ($conn->query($q2)) {
			$msg = '<div class="w3-panel w3-pale-green w3-display-container">
			<span onclick="this.parentElement.style.display=\'none\'"
			class="w3-button w3-large w3-display-topright">&times;</span>
			<h3>Success!</h3>
			<p>Data is updated successfully.</p>
			</div>';
		}
		else {
			$msg = '<div class="w3-panel w3-pale-red w3-display-container">
			<span onclick="this.parentElement.style.display=\'none\'"
			class="w3-button w3-large w3-display-topright">&times;</span>
			<h3>Unsuccessful!</h3>
			<p>'.$conn->error.'</p>
			</div>';
		}
	}
}

if (isset($_POST['delete'])) { // Delete student data
	$stud_id = $_POST['stud_id'];

	$sql = "SELECT Stud_ID FROM stud_sub WHERE Stud_ID = '$stud_id'";

	if ($result = $conn->query($sql)) {
		if ($result->num_rows > 0) {
			$msg = '<div class="w3-panel w3-pale-red w3-display-container">
			<span onclick="this.parentElement.style.display=\'none\'"
			class="w3-button w3-large w3-display-topright">&times;</span>
			<h3>Unsuccessful!</h3>
			<p>Student has already registered some subjects</p>
			</div>';
		}
		else {
			$sql = "DELETE FROM student WHERE Stud_ID = '$stud_id'";

			if ($conn->query($sql)) {
				$msg = '<div class="w3-panel w3-pale-green w3-display-container">
				<span onclick="this.parentElement.style.display=\'none\'"
				class="w3-button w3-large w3-display-topright">&times;</span>
				<h3>Success!</h3>
				<p>Data is deleted successfully.</p>
				</div>';
			}
			else {
				$msg = '<div class="w3-panel w3-pale-red w3-display-container">
				<span onclick="this.parentElement.style.display=\'none\'"
				class="w3-button w3-large w3-display-topright">&times;</span>
				<h3>Unsuccessful!</h3>
				<p>'.$conn->error.'</p>
				</div>';
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
			<a href="admin.php" class="w3-bar-item w3-button">Admin</a>
			<a href="student.php" class="w3-bar-item w3-button w3-black">Student</a>
			<a href="lecturer.php" class="w3-bar-item w3-button">Lecturer</a>
			<a href="subject.php" class="w3-bar-item w3-button">Subject</a>
			<a href="workload.php" class="w3-bar-item w3-button">Workload</a>
			<a href="../logout.php" class="w3-bar-item w3-button w3-right">Log Out</a>
		</div>
		<p>Current session: <?php echo $session_id.", ".$session_name ?></p>
		<br>
		<br>
		<table class="w3-table w3-bordered">
			<tr>
					<th>Student ID</th>
					<th>Student Name</th>
					<th>Student Email</th>
					<th>Modified By</th>
					<th>Modified On</th>
					<th>Update</th>
					<th>Delete</th>
			</tr>
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
							<td><button onclick="onUpdate('<?php echo $row['Stud_ID']; ?>', '<?php echo $row['Stud_Name']; ?>')">Update</button></td>
							<td><button onclick="onDelete('<?php echo $row['Stud_ID']; ?>', '<?php echo $row['Stud_Name']; ?>')">Delete</button></td>
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
		<p><?php echo $msg; ?></p>
	</div>
	<!-- Update popup box -->
	<div id="onUpdate" class="w3-modal">
		<div class="w3-modal-content w3-card-4" style="max-width:600px">
			<div class="w3-center"><br>
				<span onclick="document.getElementById('onUpdate').style.display='none'" class="w3-button w3-xlarge w3-transparent w3-display-topright" title="Close Modal">×</span>
			</div>
			<form class="w3-container" action="" method="POST">
				<div class="w3-section">
					<label><b>Student ID</b></label>
					<input class="w3-input w3-border w3-margin-bottom" type="text" id="stud_id" name="stud_id" required>
					<label><b>Student Name</b></label>
					<input class="w3-input w3-border" type="text" id="stud_name" name="stud_name" required>
					<button class="w3-button w3-block w3-dark-grey w3-section w3-padding" type="submit" name="update">Save</button>
				</div>
			</form>
			<div class="w3-container w3-border-top w3-padding-16 w3-light-grey">
				<button onclick="document.getElementById('onUpdate').style.display='none'" type="button" class="w3-button w3-red w3-right w3-padding">Cancel</button>
			</div>
		</div>
	</div>
	<!-- Delete popup box -->
	<div id="onDelete" class="w3-modal">
		<div class="w3-modal-content w3-card-4" style="max-width:600px">
			<div class="w3-center"><br>
				<span onclick="document.getElementById('onDelete').style.display='none'" class="w3-button w3-xlarge w3-transparent w3-display-topright" title="Close Modal">×</span>
			</div>
			<form class="w3-container" action="" method="POST">
				<div class="w3-section">
					<p>Are you sure you want to delete this data? </p>
					<label><b>Student ID</b></label>
					<input class="w3-input w3-border w3-margin-bottom" type="text" id="id" name="stud_id" readonly>
					<label><b>Student Name</b></label>
					<input class="w3-input w3-border" type="text" id="name" name="stud_name" readonly>
				</div>
			
				<div class="w3-container w3-border-top w3-padding-16">
					<button onclick="document.getElementById('onDelete').style.display='none'" type="button" class="w3-button w3-red w3-right w3-padding">Cancel</button>
					<button class="w3-button w3-green w3-right w3-padding" type="submit" name="delete">Confirm</button>
				</div>
		</form>
		</div>
	</div>
	<script>
		function onUpdate(stud_id, stud_name) {
			document.getElementById('onUpdate').style.display='block';
			document.getElementById("stud_id").value = stud_id;
			document.getElementById("stud_name").value = stud_name;
		}
		function onDelete(stud_id, stud_name) {
			document.getElementById('onDelete').style.display='block';
			document.getElementById("id").value = stud_id;
			document.getElementById("name").value = stud_name;
		}
	</script>
</body>
</html>