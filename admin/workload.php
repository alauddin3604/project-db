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

$query1 = "SELECT Lect_ID, Lect_Name FROM lecturer ORDER BY Lect_Name";
if (!$res1 = $conn->query($query1))
	die($conn->error);

$query2 = "SELECT Sub_Code, Sub_Name FROM subject ORDER BY Sub_Name";
if (!$res2 = $conn->query($query2))
	die($conn->error);

if(isset($_POST['add']))
{
	$lect_id = $_POST['lecturer'];
	$sub_code = $_POST['subject'];

	$sql = "SELECT * FROM workload WHERE Lect_ID='$lect_id' AND Sub_Code='$sub_code'";

	if ($result = $conn->query($sql)) {
		if ($result->num_rows > 0) {
			$msg = '<div class="w3-panel w3-pale-red w3-display-container w3-border">
			<span onclick="this.parentElement.style.display=\'none\'"
			class="w3-button w3-large w3-display-topright">&times;</span>
			<h3>Unsuccessful!</h3>
			<p>The lecturer has already assigned that subject!</p>
			</div>';
		}
		else {
			$sql = "INSERT INTO workload VALUES ('$lect_id', '$sub_code')";
			if ($conn->query($sql)) {
				$msg ='<div class="w3-panel w3-pale-green w3-display-container w3-border">
				<span onclick="this.parentElement.style.display=\'none\'"
				class="w3-button w3-large w3-display-topright">&times;</span>
				<h3>Successful!</h3>
				<p>New data is successfully recorded!</p>
				</div>';
			} else {
				echo "Error: " . $conn->error; 
			}
		}
	}
}

if (isset($_POST['update'])) { // Update data
	if (!isset($_POST['lecturer']) && !isset($_POST['subject'])) {
		$msg = '<div class="w3-panel w3-pale-red w3-display-container w3-border">
		<span onclick="this.parentElement.style.display=\'none\'"
		class="w3-button w3-large w3-display-topright">&times;</span>
		<h3>Unsuccessful!</h3>
		<p>You haven\'t select any option!</p>
		</div>';
	}
	else {
		$lect_id = $_POST['lecturer'];
		$sub_code = $_POST['subject'];

		$sql = "SELECT * FROM workload WHERE Lect_ID='$lect_id' AND Sub_Code='$sub_code'";

		if ($result = $conn->query($sql)) {
			if ($result->num_rows > 0) {
				$msg = '<div class="w3-panel w3-pale-red w3-display-container w3-border">
				<span onclick="this.parentElement.style.display=\'none\'"
				class="w3-button w3-large w3-display-topright">&times;</span>
				<h3>Unsuccessful!</h3>
				<p>The lecturer has already assigned that subject!</p>
				</div>';
			}
			else {
				$sql = "INSERT INTO workload VALUES ('$lect_id', '$sub_code')";
				if ($conn->query($sql)) {
					$msg ='<div class="w3-panel w3-pale-green w3-display-container w3-border">
					<span onclick="this.parentElement.style.display=\'none\'"
					class="w3-button w3-large w3-display-topright">&times;</span>
					<h3>Successful!</h3>
					<p>New data is successfully recorded!</p>
					</div>';
				} else {
					echo "Error: " . $conn->error; 
				}
			}
		}
	}
	
}

if (isset($_POST['delete'])) { // Delete data
	$sub_code = $_POST['sub_code'];
	$lect_id = $_POST['lect_id'];

	$sql = "DELETE FROM workload WHERE Sub_Code = '$sub_code' AND Lect_ID = '$lect_id'";

	if ($conn->query($sql)) {
		$msg = '<div class="w3-panel w3-pale-green w3-display-container w3-border">
		<span onclick="this.parentElement.style.display=\'none\'"
		class="w3-button w3-large w3-display-topright">&times;</span>
		<h3>Success!</h3>
		<p>Data is deleted successfully.</p>
		</div>';
	}
	else {
		die($conn->error);
	}
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Register Workload</title>
	<link rel="stylesheet" href="../css/w3.css">
	<style>
		input::-webkit-outer-spin-button,
		input::-webkit-inner-spin-button {
			-webkit-appearance: none;
			margin: 0;
		}
	</style>
</head>
<body>
	<div class="w3-container">
		<div class="w3-bar w3-light-grey">
			<a href="home.php" class="w3-bar-item w3-button">Home</a>
			<a href="admin.php" class="w3-bar-item w3-button">Admin</a>
			<a href="student.php" class="w3-bar-item w3-button">Student</a>
			<a href="lecturer.php" class="w3-bar-item w3-button">Lecturer</a>
			<a href="subject.php" class="w3-bar-item w3-button">Subject</a>
			<a href="workload.php" class="w3-bar-item w3-button w3-black">Workload</a>
			<a href="../logout.php" class="w3-bar-item w3-button w3-right">Log Out</a>
		</div>
		<p>Current session: <?php echo $session_id.", ".$session_name ?></p>
		<br>
		<br>
		<table class="w3-table w3-bordered">
			<tr>
				<th>No</th>
				<th>Lecturer</th>
				<th>Subject</th>
				<th>Update</th>
				<th>Delete</th>
			</tr>
			<?php
				$sql = "SELECT l.Lect_ID, l.Lect_Name, s.Sub_Code, s.Sub_Name
				FROM workload
				INNER JOIN lecturer as l ON (workload.Lect_ID=l.Lect_ID)
				INNER JOIN subject as s ON (workload.Sub_Code=s.Sub_Code)";
				$result = $conn->query($sql);
				$i = 1;
				if($result->num_rows > 0){
					while ($row = $result->fetch_assoc()) { ?>
						<tr>
							<td><?php echo $i; $i++; ?></td>
							<td><?php echo $row['Lect_Name']; ?></td>
							<td><?php echo $row['Sub_Name']; ?></td>
							<td><button onclick="onUpdate('<?php echo $row['Lect_Name']; ?>', '<?php echo $row['Sub_Code']; ?>', '<?php echo $row['Sub_Name']; ?>')">Update</button></td>
							<td><button onclick="onDelete('<?php echo $row['Lect_ID']; ?>', '<?php echo $row['Sub_Code']; ?>', '<?php echo $row['Lect_Name']; ?>', '<?php echo $row['Sub_Name']; ?>')">Delete</button></td>
						</tr>
						<?php
					}
				}
			?>
			<tr>
				<form action="" method="post">
					<td></td>
					<td>
						<select class="w3-select" name="lecturer">
							<option value="" disabled selected>Choose lecturer</option>
							<?php while($row1 = $res1->fetch_assoc()):;?>
							<option value="<?php echo $row1['Lect_ID'];?>"><?php echo $row1['Lect_Name'];?></option>
							<?php endwhile; ?>
						</select>
					</td>
					<td>
						<select class="w3-select" name="subject">
							<option value="" disabled selected>Choose subject</option>
							<?php while($row2 = $res2->fetch_array()):;?>
							<option value="<?php echo $row2[0];?>"><?php echo $row2[1];?></option>
							<?php endwhile; ?>
						</select>
					</td>
					<td><input class="w3-button w3-light-grey w3-border" type="submit" name="add" value="Add" /></td>
					<td></td>
				</form>
			</tr>
		</table>
		<?php echo $msg; ?>
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
					<label><b>Lecturer</b></label>
					<select class="w3-select w3-border" name="lecturer">
						<option id="prev_lect" value="" disabled></option>
						<?php 
						$res1->data_seek(0);
						while($row1 = $res1->fetch_assoc()) {?>
						<option value="<?php echo $row1['Lect_ID'];?>"><?php echo $row1['Lect_Name'];?></option>
						<?php } ?>
					</select>
					<label><b>Subject</b></label>
					<select class="w3-select w3-border" name="subject">
						<option id="prev_sub" value="" disabled></option>
						<?php 
						$res2->data_seek(0);
						while($row2 = $res2->fetch_array()):;?>
						<option value="<?php echo $row2[0];?>"><?php echo $row2[1];?></option>
						<?php endwhile; ?>
					</select>
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
					<input type="text" id="code" name="sub_code" hidden>
					<input type="text" id="lect_id" name="lect_id" hidden>
					<p>Are you sure you want to delete this data? </p>
					<label><b>Lecturer Name</b></label>
					<input class="w3-input w3-border w3-margin-bottom" type="text" id="id" name="lect_name" readonly>
					<label><b>Subject Name</b></label>
					<input class="w3-input w3-border" type="text" id="name" name="sub_name" readonly>
				</div>
			
				<div class="w3-container w3-border-top w3-padding-16">
					<button onclick="document.getElementById('onDelete').style.display='none'" type="button" class="w3-button w3-red w3-right w3-padding">Cancel</button>
					<button class="w3-button w3-green w3-right w3-padding" type="submit" name="delete">Confirm</button>
				</div>
		</form>
		</div>
	</div>
	<script>
		function onUpdate(lect_name, sub_code, sub_name) {
			document.getElementById('onUpdate').style.display='block';
			document.getElementById('prev_lect').innerHTML = lect_name;
			document.getElementById('prev_sub').innerHTML = sub_name;
		}
		function onDelete(lect_id, sub_code, stud_id, stud_name) {
			document.getElementById('onDelete').style.display='block';
			document.getElementById("lect_id").value = lect_id;
			document.getElementById("code").value = sub_code;
			document.getElementById("id").value = stud_id;
			document.getElementById("name").value = stud_name;
		}
	</script>
</body>
</html>