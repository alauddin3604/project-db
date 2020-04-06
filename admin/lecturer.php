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

// Add new data
if(isset($_POST['add']))
{
	$lect_id = $_POST['lect_id'];
	$lect_name = $_POST['lect_name'];
	$hashedPassword = password_hash($lect_id, PASSWORD_DEFAULT);
	$date = date("Y-m-d H:i:s");

	$sql = "SELECT * FROM lecturer WHERE Lect_ID = '$lect_id'";

	if ($res = $conn->query($sql)) {
		if ($res->num_rows > 0) {
			$msg = '<div class="w3-panel w3-pale-red w3-display-container">
			<span onclick="this.parentElement.style.display=\'none\'"
			class="w3-button w3-large w3-display-topright">&times;</span>
			<h3>Unsuccessful!</h3>
			<p>The lecturer ID has already registered!</p>
			</div>';
		}
		else {
			$sql1 = "INSERT INTO lecturer VALUES('$lect_id', '$lect_name', '$hashedPassword', 0)";
			$sql2 = "INSERT INTO adm_lect VALUES('$session_id', '$lect_id', '$date')";
			if($conn->query($sql1)) {
				if($conn->query($sql2)) {
					$msg = '<div class="w3-panel w3-pale-green w3-border">
					<h3>Success!</h3>
					<p>New data is successfully recorded.</p>
					</div>';
				}
				else {
					$msg = '<div class="w3-panel w3-pale-red w3-border">
					<h3>Unsuccessful!</h3>
					<p>Error: '.$conn->error.'</p>
					</div>';
				}
			}
			else {
				$msg = '<div class="w3-panel w3-pale-red w3-border">
				<h3>Unsuccessful!</h3>
				<p>Error: '.$conn->error.'</p>
				</div>';;
			}
		}
	}
}

if(isset($_POST['update'])) { // Update data
	$lect_id = $_POST['lect_id'];
	$lect_name = $_POST['lect_name'];

	$mod_on = date('Y-m-d H:i:s');

	$q1 = "UPDATE lecturer
			SET Lect_ID = '$lect_id', Lect_Name = '$lect_name'
			WHERE Lect_ID = '$lect_id'";

	$q2 = "UPDATE adm_lect 
			SET Adm_ID = '$session_id', Mod_On = '$mod_on'
			WHERE Lect_ID = '$lect_id'";

	if(!$result = $conn->query($q1)) {
		$msg = '<div class="w3-panel w3-pale-red w3-display-container">
		<span onclick="this.parentElement.style.display=\'none\'"
		class="w3-button w3-large w3-display-topright">&times;</span>
		<h3>Unsuccessful!</h3>
		<p>'.$conn->error.'</p>
		</div>';
	}
	else {
		if (!$conn->query($q2)) {
			$msg = '<div class="w3-panel w3-pale-red w3-display-container">
			<span onclick="this.parentElement.style.display=\'none\'"
			class="w3-button w3-large w3-display-topright">&times;</span>
			<h3>Unsuccessful!</h3>
			<p>'.$conn->error.'</p>
			</div>';
		}
		else {
			$msg = '<div class="w3-panel w3-pale-green w3-display-container">
			<span onclick="this.parentElement.style.display=\'none\'"
			class="w3-button w3-large w3-display-topright">&times;</span>
			<h3>Success!</h3>
			<p>Data is updated successfully.</p>
			</div>';
		}
	}
}

if (isset($_POST['delete'])) { // Delete lecturer data
	$lect_id = $_POST['lect_id'];

	$sql = "SELECT Sub_Code FROM workload 
			WHERE Lect_ID = '$lect_id'";

	if ($result = $conn->query($sql)) {
		if ($result->num_rows > 0) {
			$msg = '<div class="w3-panel w3-pale-red w3-display-container">
			<span onclick="this.parentElement.style.display=\'none\'"
			class="w3-button w3-large w3-display-topright">&times;</span>
			<h3>Unsuccessful!</h3>
			<p>This lecturer has assigned with subject(s)!</p>
			</div>';
		}
		else {
			$sql = "DELETE FROM lecturer WHERE Lect_ID = '$lect_id'";
			
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
	<title>Register Lecturer</title>
	<link rel="stylesheet" href="../css/w3.css">
</head>
<body>
	<div class="w3-container">
		<div class="w3-bar w3-light-grey">
			<a href="home.php" class="w3-bar-item w3-button">Home</a>
			<a href="admin.php" class="w3-bar-item w3-button">Admin</a>
			<a href="student.php" class="w3-bar-item w3-button">Student</a>
			<a href="lecturer.php" class="w3-bar-item w3-button w3-black">Lecturer</a>
			<a href="subject.php" class="w3-bar-item w3-button">Subject</a>
			<a href="workload.php" class="w3-bar-item w3-button">Workload</a>
			<a href="../logout.php" class="w3-bar-item w3-button w3-right">Log Out</a>
		</div>
		<p>Current session: <?php echo $session_id.", ".$session_name ?></p>
		<br>
		<br>
		<table class="w3-table w3-bordered">
			<tr>
				<th>Lecturer ID</th>
				<th>Lecturer Name</th>
				<th>Modified By</th>
				<th>Modified On</th>
				<th>Update</th>
				<th>Delete</th>
			</tr>
			<?php
				$sql = "SELECT l.Lect_ID, l.Lect_Name, a.Adm_ID, a.Adm_Name, Mod_On
				FROM adm_lect
				INNER JOIN admin as a ON (adm_lect.Adm_ID=a.Adm_ID)
				INNER JOIN lecturer as l ON (adm_lect.Lect_ID=l.Lect_ID)";
				if ($result = $conn->query($sql))
				{
					if ($result->num_rows > 0) {
						while ($row = $result->fetch_assoc()) { ?>
							<tr style="text-align: center">
							<td><?php echo $row['Lect_ID']; ?></td>
							<td><?php echo $row['Lect_Name']; ?></td>
							<td><?php echo $row['Adm_Name']; ?></td>
							<td><?php $date = $row['Mod_On'];
							echo date("j/n/Y g:i:s A", strtotime($date)); ?></td>
							<td><button onclick="onUpdate(<?php echo $row['Lect_ID']; ?>, '<?php echo $row['Lect_Name']; ?>')">Update</button></td>
							<td><button onclick="onDelete('<?php echo $row['Lect_ID']; ?>', '<?php echo $row['Lect_Name']; ?>')">Delete</button></td>
											
							</tr>
							<?php
						}
					}
				}
				else
				{
					echo $conn->error;
				}				
			?>
			<tr>
				<form action="" method="POST">
					<td><input class="w3-input" type="text" name="lect_id" placeholder="Add lecturer ID"/></td>
					<td><input class="w3-input" type="text" name="lect_name" placeholder="Add lecturer name" /></td>
					<td><input class="w3-button w3-light-grey w3-border " type="submit" name="add" value="Add" /></td>
					<td></td>
					<td></td>
					<td></td>
				</form>
			</tr>
		</table>
		<p><?php echo $msg; ?></p>
	</div>
	<div id="id01" class="w3-modal">
		<div class="w3-modal-content w3-card-4" style="max-width:600px">
  			<div class="w3-center"><br>
				<span onclick="document.getElementById('id01').style.display='none'" class="w3-button w3-xlarge w3-transparent w3-display-topright" title="Close Modal">×</span>
			</div>
			<form class="w3-container" action="" method="POST">
				<div class="w3-section">
					<label><b>Lecturer ID</b></label>
					<input class="w3-input w3-border w3-margin-bottom" type="text" id="lect_id" name="lect_id" required>
					<label><b>Lecturer Name</b></label>
					<input class="w3-input w3-border" type="text" id="lect_name" name="lect_name" required>
					<button class="w3-button w3-block w3-dark-grey w3-section w3-padding" type="submit" name="update">Save</button>
				</div>
			</form>
			<div class="w3-container w3-border-top w3-padding-16 w3-light-grey">
				<button onclick="document.getElementById('id01').style.display='none'" type="button" class="w3-button w3-red">Cancel</button>
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
					<input class="w3-input w3-border w3-margin-bottom" type="text" id="del-lect_id" name="lect_id" readonly>
					<label><b>Student Name</b></label>
					<input class="w3-input w3-border" type="text" id="del-lect_name" name="lect_name" readonly>
				</div>
			
				<div class="w3-container w3-border-top w3-padding-16">
					<button onclick="document.getElementById('onDelete').style.display='none'" type="button" class="w3-button w3-red w3-right w3-padding">Cancel</button>
					<button class="w3-button w3-green w3-right w3-padding" type="submit" name="delete">Confirm</button>
				</div>
		</form>
		</div>
	</div>
	<script>
		function onUpdate(lect_id, lect_name) {
			document.getElementById('onUpdate').style.display='block';
			document.getElementById("lect_id").value = lect_id;
			document.getElementById("lect_name").value = lect_name;
		}
		function onDelete(lect_id, lect_name) {
			document.getElementById('onDelete').style.display='block';
			document.getElementById("del-lect_id").value = lect_id;
			document.getElementById("del-lect_name").value = lect_name;
		}
	</script>
</body>
</html>