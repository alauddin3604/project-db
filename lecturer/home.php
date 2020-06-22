<?php
session_start();
require '../connection.php';
$msg = '';

if (isset($_SESSION['lecturer_id']))
	$lecturer_id = $_SESSION['lecturer_id'];
else
	header('location: ../index.php');

if (isset($_POST['edit-tf']))
{
	$_SESSION['subject_code'] = $_POST['subject_code'];
	header('location: quiz-tf.php');
}
if (isset($_POST['view-marks-tf']))
{
	$_SESSION['subject_code'] = $_POST['subject_code'];
	$_SESSION['workload_id'] = $_POST['workload_id'];
	header('location: mark-tf.php');
}
if (isset($_POST['edit-obj']))
{
	$_SESSION['workload_id'] = $_POST['workload_id'];
	$_SESSION['subject_code'] = $_POST['subject_code'];
	header('location: quiz-obj.php');
}
if (isset($_POST['view-marks-obj']))
{
	$_SESSION['subject_code'] = $_POST['subject_code'];
	$_SESSION['workload_id'] = $_POST['workload_id'];
	header('location: mark-obj.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Home</title>
	<link rel="stylesheet" href="../css/w3.css">
	<link rel="stylesheet" href="../css/style.css">
</head>
<body>
	<div class="w3-container">
		<div class="w3-bar">
			<a href="../logout.php" class="w3-bar-item w3-button w3-right">Log Out</a>
		</div>
		<h3>Your subject list</h3>
		<br>
		<br>
		<table class="w3-table w3-bordered">
			<tr>
				<th>No</th>
				<th>Subject</th>
				<th>Quiz True/False</th>
				<th>Quiz Objective</th>
			</tr>
			<?php
				$i = 1;
				$sql = 'SELECT s.subject_code, s.subject_name, w.*
				FROM workloads AS w
				INNER JOIN subjects AS s ON s.subject_code = w.subject_code
				WHERE w.lecturer_id = ' . $lecturer_id;
				if ($result = $conn->query($sql))
				{
					if ($result->num_rows > 0) {
						while ($row = $result->fetch_assoc()) { ?>
							<tr style="text-align: center">
							<td><?php echo $i; ?></td>
							<td><?php echo $row['subject_name']; ?></td>
							<form action="" method="POST">
								<input type="text" name="workload_id" value="<?php echo $row['workload_id']; ?>" hidden>
								<input type="text" name="subject_code" value="<?php echo $row['subject_code']; ?>" hidden>
								<td>
									<button class="w3-button w3-round w3-light-grey" type="submit" name="view-marks-tf">Results</button>
									<button class="w3-button w3-round w3-light-grey" type="submit" name="edit-tf">Create/View Quiz</button>
								</td>
							</form>
							<form action="" method="POST">
								<input type="text" name="workload_id" value="<?php echo $row['workload_id']; ?>" hidden>
								<input type="text" name="subject_code" value="<?php echo $row['subject_code']; ?>" hidden>
								<td>
									<button class="w3-button w3-round w3-light-grey" type="submit" name="view-marks-obj">Results</button>
									<button class="w3-button w3-round w3-light-grey" type="submit" name="edit-obj">Create/View Quiz</button>
								</td>			
							</form>	
							</tr>
							<?php
							$i++;
						}
					}
				}
				else
				{
					echo $conn->error;
				}				
			?>
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
					<input type="text" id="current_lecturer" name="current_lecturer_id" hidden>
					<label><b>Lecturer ID</b></label>
					<input class="w3-input w3-border w3-margin-bottom" type="text" id="lect_id" name="lecturer_id" required>
					<label><b>Lecturer Name</b></label>
					<input class="w3-input w3-border" type="text" id="lect_name" name="lecturer_name" required>
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
					<label><b>Lecturer ID</b></label>
					<input class="w3-input w3-border w3-margin-bottom" type="text" id="del-lect_id" name="lect_id" readonly>
					<label><b>Lecturer Name</b></label>
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
			document.getElementById('current_lecturer').value = lect_id;
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