<?php
require '../connection.php';
include '../message.php';
session_start();
date_default_timezone_set('Asia/Kuala_Lumpur');
$msg = '';

if (isset($_SESSION['admin_id']))
	$session_id = $_SESSION['admin_id'];
else
	header('location: ../index.php');

$q = 'SELECT admin_id, admin_name FROM admins WHERE admin_id=?';
$stmt = $conn->prepare($q);
$stmt->bind_param('i', $session_id);
if (!$stmt->execute())
{
	echo $conn->error;
}
else
{
	$row = $stmt->get_result()->fetch_assoc();
	$session_name = $row['admin_name'];
}

$query1 = 'SELECT lecturer_id, lecturer_name FROM lecturers ORDER BY lecturer_name'; // To list all lecturers in dropdown
$stmt1 = $conn->prepare($query1);
if ($stmt1->execute())
{
	$res1 = $stmt1->get_result();
}

$query2 = 'SELECT * FROM subjects ORDER BY subject_name'; // To list all subjects in dropwdown
$stmt2 = $conn->prepare($query2);
if ($stmt2->execute())
{
	$res2 = $stmt2->get_result();
}

if(isset($_POST['add'])) // Add workload
{
	if (empty($_POST['lecturer']) || empty($_POST['subject']))
		$msg = $validationMsg; // Return error
	else
	{
		$lecturer_id = $_POST['lecturer'];
		$subject_code = $_POST['subject'];
		$sql = 'SELECT * FROM workloads WHERE lecturer_id=? AND subject_code=?';
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('is', $lecturer_id, $subject_code);
		$stmt->execute();
		$result = $stmt->get_result();
	
		if ($result->num_rows > 0)
			$msg = $lecturerMsg; // Return error
		else
		{
			$sql = 'INSERT INTO workloads (lecturer_id, subject_code) VALUES (?, ?)';
			$stmt = $conn->prepare($sql);
			$stmt->bind_param('is', $lecturer_id, $subject_code);
			if ($stmt->execute())
				$msg = $addMsg; // Return success
			else
				$msg = $errorMsg; // Return error
		}
	}
}

if (isset($_POST['update'])) // Update data
{
	if (!isset($_POST['subject_code']))
	{
		$msg = $validationMsg;
	}
	else
	{
		$lecturer_id = $conn->real_escape_string($_POST['lecturer_id']);
		$subject_code = $conn->real_escape_string($_POST['subject_code']);
		$current_subject_code = htmlspecialchars(($_POST['current_subject_code']));

		$sql = 'SELECT * FROM workloads WHERE lecturer_id = ? AND subject_code = ?';

		$stmt = $conn->prepare($sql);
		$stmt->bind_param('is', $lecturer_id, $subject_code);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result->num_rows > 0)
		{
			$msg = $lecturerMsg;
		}
		else 
		{
			$sql = 'UPDATE workloads SET subject_code = ?
			WHERE lecturer_id = ? AND subject_code = ?';

			$stmt = $conn->prepare($sql);
			$stmt->bind_param('sis', $subject_code, $lecturer_id, $current_subject_code);
			
			if ($stmt->execute())
			{
				$msg = '<div class="w3-panel w3-green w3-display-container">
				<span onclick="this.parentElement.style.display=\'none\'"
				class="w3-button w3-display-topright">&times;</span>
				<h3>Success!</h3>
				<p>The data is successfully updated.</p>	
				</div>';
			}
			else
			{
				echo 'Error: ' . $conn->error; 
			}
		}
	}	
}

if (isset($_POST['delete'])) // Delete data
{
	$workload_id = $_POST['workload_id'];
	
	# Check if the lecturer has made a quiz
	$sql = 'SELECT question, answer
			FROM quiz_truefalse
			WHERE workload_id = ?
			UNION
			SELECT question, answer
			FROM quiz_objective
			WHERE workload_id = ?';

	$stmt->prepare($sql);
	$stmt->bind_param('ii', $workload_id, $workload_id);
	if ($stmt->execute())
	{
		$result = $stmt->get_result();
		if ($result->num_rows > 0)
			$msg = $quizMadeMsg;
		else
		{
			$sql = 'DELETE FROM workloads WHERE workload_id = ?';
			$stmt = $conn->prepare($sql);
			$stmt->bind_param('i', $workload_id);

			if ($stmt->execute())
				$msg = $deleteMsg;
			else
				die($conn->error);
		}	
	}
}

// List all assigned subjects
$sql = 'SELECT l.*, s.*, workloads.*
FROM workloads
INNER JOIN lecturers as l ON (workloads.lecturer_id = l.lecturer_id)
INNER JOIN subjects as s ON (workloads.subject_code = s.subject_code)';
$result_list = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Register Workload</title>
	<link rel="stylesheet" href="../css/w3.css">
	<link rel="stylesheet" href="../css/style.css">
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
		<div class="w3-bar w3-dark-grey">
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
		<?php echo $msg; ?>
		<br>
		<table class="w3-table w3-bordered">
			<tr>
				<th>No</th>
				<th>Lecturer</th>
				<th>Subject</th>
				<th>Update</th>
				<th>Delete</th>
			</tr>
			<?php # List all assigned subjects
				$i = 1;
				if($result_list->num_rows > 0){
					while ($row = $result_list->fetch_assoc()) { ?>
						<tr>
							<td><?php echo $i; $i++; ?></td>
							<td><?php echo $row['lecturer_name']; ?></td>
							<td><?php echo $row['subject_name']; ?></td>
							<td><button class="w3-button w3-round w3-light-grey" onclick="onUpdate('<?php echo $row['lecturer_id']; ?>', '<?php echo $row['lecturer_name']; ?>', '<?php echo $row['subject_code']; ?>', '<?php echo $row['subject_name']; ?>')">Update</button></td>
							<td><button class="w3-button w3-round w3-light-grey" onclick="onDelete(<?php echo $row['workload_id']; ?>, '<?php echo $row['lecturer_name']; ?>', '<?php echo $row['subject_name']; ?>')">Delete</button></td>
						</tr>
						<?php
					}
				}
			?>
			<tr>
				<form action="" method="post">
					<td></td>
					<td>
						<select class="w3-select" name="lecturer" required>
							<option value="" disabled selected>Choose lecturer</option>
							<?php while($row1 = $res1->fetch_assoc()):;?>
							<option value="<?php echo $row1['lecturer_id'];?>"><?php echo $row1['lecturer_name'];?></option>
							<?php endwhile; ?>
						</select>
					</td>
					<td>
						<select class="w3-select" name="subject" required>
							<option value="" disabled selected>Choose subject</option>
							<?php while($row2 = $res2->fetch_array()):;?>
							<option value="<?php echo $row2[0];?>"><?php echo $row2[1];?></option>
							<?php endwhile; ?>
						</select>
					</td>
					<td><input class="w3-button w3-round w3-light-grey" type="submit" name="add" value="Add" /></td>
					<td></td>
				</form>
			</tr>
		</table>
	</div>
	<!-- Update popup box -->
	<div id="onUpdate" class="w3-modal">
		<div class="w3-modal-content w3-card-4" style="max-width:600px">
			<div class="w3-center"><br>
				<span onclick="document.getElementById('onUpdate').style.display='none'" class="w3-button w3-xlarge w3-transparent w3-display-topright" title="Close Modal">×</span>
			</div>
			<form class="w3-container" action="" method="POST" autocomplete="off">
				<div class="w3-section">
					<input type="text" id="curr_sub_code" name="current_subject_code" hidden> <!-- To post the current subject code -->
					<input type="text" id="curr_lect_id" name="lecturer_id" hidden>
					<label><b>Lecturer</b></label>
					<input class="w3-input w3-border w3-margin-bottom" type="text" id="prev_lect" readonly>
					<label><b  id="prev_sub"></b></label>
					<select class="w3-select w3-border" name="subject_code" required>
						<option value="" disabled selected>Choose subject</option>
						<?php 
						$res2->data_seek(0);
						while($row2 = $res2->fetch_array()):;?>
						<option value="<?php echo $row2[0];?>"><?php echo $row2[1];?></option>
						<?php endwhile; ?>
					</select>
					<button class="w3-button w3-block w3-dark-grey w3-section w3-padding" type="submit" name="update">Save</button>
				</div>
			</form>
			<div class="w3-container w3-border-top w3-padding-16">
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
					<input type="text" id="del_workload_id" name="workload_id" hidden>
					<p>Are you sure you want to delete this data? </p>
					<label><b>Lecturer Name</b></label>
					<input class="w3-input w3-border w3-margin-bottom" type="text" id="del_lect_name" readonly>
					<label><b>Subject Name</b></label>
					<input class="w3-input w3-border" type="text" id="del_subject_name" readonly>
				</div>
			
				<div class="w3-container w3-border-top w3-padding-16">
					<button onclick="document.getElementById('onDelete').style.display='none'" type="button" class="w3-button w3-red w3-right w3-padding">Cancel</button>
					<button class="w3-button w3-green w3-right w3-padding" type="submit" name="delete">Confirm</button>
				</div>
			</form>
		</div>
	</div>
	<script>
		function onUpdate(lect_id, lect_name, sub_code, sub_name) {
			document.getElementById('onUpdate').style.display='block';
			document.getElementById('curr_sub_code').value = sub_code;
			document.getElementById('curr_lect_id').value = lect_id;
			document.getElementById('prev_lect').value = lect_name;
			document.getElementById('prev_sub').innerHTML = 'Assign new subject<br>(Current subject: ' + sub_name + ')';
		}
		function onDelete(workload_id, lecturer_name, subject_name) {
			document.getElementById('onDelete').style.display='block';
			document.getElementById("del_workload_id").value = workload_id;
			document.getElementById("del_lect_name").value = lecturer_name;
			document.getElementById("del_subject_name").value = subject_name;
		}
	</script>
</body>
</html>