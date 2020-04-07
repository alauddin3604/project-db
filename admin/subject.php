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
if (!$result = $conn->query($q)) {
	echo $conn->error;
}
else {
	$row = $result->fetch_assoc();
	$session_name = $row['Adm_Name'];
}

if (isset($_POST['add'])) {// Add new data
	$sub_code = $_POST['sub_code'];

	$sql = "SELECT * FROM subject WHERE Sub_Code = '$sub_code'";

	if ($result = $conn->query($sql)) {
		if ($result->num_rows > 0) {
			$msg = '<div class="w3-panel w3-pale-red w3-display-container">
			<span onclick="this.parentElement.style.display=\'none\'"
			class="w3-button w3-large w3-display-topright">&times;</span>
			<h3>Unsuccessful!</h3>
			<p>The subject code has already registered!</p>
			</div>';
		}
		else {
			$sub_name = $_POST['sub_name'];

			$date = date("Y-m-d H:i:s");

			$sql1 = "INSERT INTO subject VALUES('$sub_code', '$sub_name')";
			$sql2 = "INSERT INTO adm_sub VALUES('$session_id', '$sub_code', '$date')";
			if($conn->query($sql1)) {
				if($conn->query($sql2)) {
					$msg = '<div class="w3-panel w3-pale-green w3-display-container w3-border">
					<span onclick="this.parentElement.style.display=\'none\'"
					class="w3-button w3-large w3-display-topright">&times;</span>
					<h3>Success!</h3>
					<p>The subject is successfully registered!</p>
					</div>';
				}
				else {
					$msg = '<div class="w3-panel w3-pale-red w3-display-container w3-border">
					<span onclick="this.parentElement.style.display=\'none\'"
					class="w3-button w3-large w3-display-topright">&times;</span>
					<h3>Unsuccessful!</h3>
					<p>The lecturer ID has already registered!</p>
					</div>';
				}
			}
			else {
				$msg = '<div class="w3-panel w3-pale-red w3-display-container">
				<span onclick="this.parentElement.style.display=\'none\'"
				class="w3-button w3-large w3-display-topright">&times;</span>
				<h3>Unsuccessful!</h3>
				<p>The lecturer ID has already registered!</p>
				</div>';
			}
		}
	}	
}

if(isset($_POST['update'])) { // Update data
	$sub_code = $_POST['sub_code'];
	$sub_name = $_POST['sub_name'];
	$curr_sub_code = $_POST['curr_sub_code'];

	$mod_on = date('Y-m-d H:i:s');

	$q1 = "UPDATE subject
			SET Sub_Code = '$sub_code', Sub_Name = '$sub_name'
			WHERE Sub_Code = '$curr_sub_code'";

	$q2 = "UPDATE adm_sub 
			SET Adm_ID = '$session_id', Mod_On = '$mod_on'
			WHERE Sub_Code = '$sub_code'";

	if(!$conn->query($q1)) {
		$msg = '<div class="w3-panel w3-pale-red w3-display-container w3-border">
		<span onclick="this.parentElement.style.display=\'none\'"
		class="w3-button w3-large w3-display-topright">&times;</span>
		<h3>Unsuccessful!</h3>
		<p>'.$conn->error.'</p>
		</div>';
	}
	else {
		if ($conn->query($q2)) {
			$msg = '<div class="w3-panel w3-pale-green w3-display-container w3-border">
			<span onclick="this.parentElement.style.display=\'none\'"
			class="w3-button w3-large w3-display-topright">&times;</span>
			<h3>Success!</h3>
			<p>Data is updated successfully.</p>
			</div>';
		}
		else {
			$msg = '<div class="w3-panel w3-pale-red w3-display-container w3-border">
			<span onclick="this.parentElement.style.display=\'none\'"
			class="w3-button w3-large w3-display-topright">&times;</span>
			<h3>Unsuccessful!</h3>
			<p>'.$conn->error.'</p>
			</div>';
		}
		
	}
}

if (isset($_POST['delete'])) { // Delete data
	$sub_code = $_POST['sub_code'];

	$sql = "SELECT Sub_Code FROM workload 
			WHERE Sub_Code = '$sub_code'";

	if ($result = $conn->query($sql)) {
		if ($result->num_rows > 0) {
			$msg = '<div class="w3-panel w3-pale-red w3-display-container w3-border">
			<span onclick="this.parentElement.style.display=\'none\'"
			class="w3-button w3-large w3-display-topright">&times;</span>
			<h3>Unsuccessful!</h3>
			<p>This subject has been assigned to lecturer(s)</p>
			</div>';
		}
		else {
			$sql = "DELETE FROM subject WHERE Sub_Code = '$sub_code'";

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
	<title>Subject Registration</title>
	<link rel="stylesheet" href="../css/w3.css">
</head>
<body>
	<div class="w3-container">
		<div class="w3-bar w3-light-grey">
			<a href="home.php" class="w3-bar-item w3-button">Home</a>
			<a href="admin.php" class="w3-bar-item w3-button">Admin</a>
			<a href="student.php" class="w3-bar-item w3-button">Student</a>
			<a href="lecturer.php" class="w3-bar-item w3-button">Lecturer</a>
			<a href="subject.php" class="w3-bar-item w3-button w3-black">Subject</a>
			<a href="workload.php" class="w3-bar-item w3-button">Workload</a>
			<a href="../logout.php" class="w3-bar-item w3-button w3-right">Log Out</a>
		</div>
		<p>Current session: <?php echo $session_id.", ".$session_name ?></p>
		<br>
		<br>
		<table class="w3-table w3-bordered">
			<tr>
				<th>Subject Code</th>
				<th>Subject Name</th>
				<th>Modified By</th>
				<th>Modified On</th>
				<th>Update</th>
				<th>Delete</th>
			</tr>
			<?php
				$sql = "SELECT s.Sub_Code, s.Sub_Name, a.Adm_ID, a.Adm_Name, Mod_On
				FROM adm_sub
				INNER JOIN admin as a ON (adm_sub.Adm_ID=a.Adm_ID)
				INNER JOIN subject as s ON (adm_sub.Sub_Code=s.Sub_Code)
				ORDER BY s.Sub_Code";
				if($result = $conn->query($sql))
				{
					if($result->num_rows > 0){
						while ($row = $result->fetch_assoc()) { ?>
							<tr style="text-align: center">
							<td><?php echo $row['Sub_Code']; ?></td>
							<td><?php echo $row['Sub_Name']; ?></td>
							<td><?php echo $row['Adm_Name']; ?></td>
							<td><?php $date = $row['Mod_On'];
							echo date("j/n/Y g:i:s A", strtotime($date)); ?></td>
							<td><button onclick="onUpdate('<?php echo $row['Sub_Code']; ?>', '<?php echo $row['Sub_Name']; ?>')">Update</button></td>
							<td><button onclick="onDelete('<?php echo $row['Sub_Code']; ?>', '<?php echo $row['Sub_Name']; ?>')">Delete</button></td>
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
					<td><input class="w3-input" type="text" name="sub_code" placeholder="Add subject code"/></td>
					<td><input class="w3-input" type="text" name="sub_name" placeholder="Add subject name" /></td>
					<td><input class="w3-button w3-light-grey w3-border" type="submit" name="add" value="Add"></td>
					<td></td>
					<td></td>
					<td></td>
				</form>
			</tr>
		</table>
		<p><?php echo $msg; ?></p>
	</div>
	<!-- Update popup box -->
	<div id="onUpdate" class="w3-modal">
		<div class="w3-modal-content w3-card-4" style="max-width:600px">
			<div class="w3-center"><br>
				<span onclick="document.getElementById('onUpdate').style.display='none'" class="w3-button w3-xlarge w3-transparent w3-display-topright" title="Close Modal">×</span>
			</div>
			<form class="w3-container" action="" method="POST" autocomplete="off">
				<div class="w3-section">
					<input type="text" id="curr_sub_code" name="curr_sub_code" hidden>
					<label><b>Subject Code</b></label>
					<input class="w3-input w3-border w3-margin-bottom" type="text" id="sub_code" name="sub_code" required>
					<label><b>Subject Name</b></label>
					<input class="w3-input w3-border" type="text" id="sub_name" name="sub_name" required>
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
					<label><b>Subject Code</b></label>
					<input class="w3-input w3-border w3-margin-bottom" type="text" id="del-sub_code" name="sub_code" readonly>
					<label><b>Subject Name</b></label>
					<input class="w3-input w3-border" type="text" id="del-sub_name" name="sub_name" readonly>
				</div>
			
				<div class="w3-container w3-border-top w3-padding-16">
					<button onclick="document.getElementById('onDelete').style.display='none'" type="button" class="w3-button w3-red w3-right w3-padding">Cancel</button>
					<button class="w3-button w3-green w3-right w3-padding" type="submit" name="delete">Confirm</button>
				</div>
			</form>
		</div>
	</div>
	<script>
		function onUpdate(sub_code, sub_name) {
			document.getElementById('onUpdate').style.display='block';
			document.getElementById("curr_sub_code").value = sub_code;
			document.getElementById("sub_code").value = sub_code;
			document.getElementById("sub_name").value = sub_name;
		}
		function onDelete(sub_code, sub_name) {
			document.getElementById('onDelete').style.display='block';
			document.getElementById("del-sub_code").value = sub_code;
			document.getElementById("del-sub_name").value = sub_name;
		}
	</script>
</body>
</html>