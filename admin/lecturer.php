<?php
session_start();
require '../connection.php';
date_default_timezone_set('Asia/Kuala_Lumpur');
$msg = "";

if (isset($_SESSION['admin_id']))
	$session_id = $_SESSION['admin_id'];
else
	header('location: ../index.php');

$q = 'SELECT admin_id, admin_name FROM admins WHERE admin_id = ?';
$stmt = $conn->prepare($q);
$stmt->bind_param('i', $session_id);
if (!$stmt->execute())
{
	echo $conn->error;
}
else
{
	$result = $stmt->get_result();
	$row = $result->fetch_assoc();
	$session_name = $row['admin_name'];
}

// Add new data
if(isset($_POST['add']))
{
	$lecturer_id = $_POST['lect_id'];
	$lecturer_name = $_POST['lect_name'];
	$lecturer_password = password_hash($lecturer_id, PASSWORD_DEFAULT);
	$date = date("Y-m-d H:i:s");

	$sql = 'SELECT * FROM lecturers WHERE lecturer_id = ?';
	$stmt = $conn->prepare($sql);
	$stmt->bind_param('i', $lecturer_id);

	if ($stmt->execute())
	{
		$result = $stmt->get_result();
		if ($result->num_rows > 0)
		{
			$msg = '<p style="color: red;">The lecturer ID has already registered!</p>';
		}
		else
		{
			$sql1 = 'INSERT INTO lecturers VALUES(?,?,?, 0)';
			$sql2 = 'INSERT INTO adm_lect VALUES(?,?,?)';

			$stmt = $conn->prepare($sql1);
			$stmt->bind_param('iss', $lecturer_id, $lecturer_name, $lecturer_password);
			if($stmt->execute())
			{
				$stmt = $conn->prepare($sql2);
				$stmt->bind_param('iis', $session_id, $lecturer_id, $date);
				if($conn->query($sql2))
				{
					$msg = '<p style="color: green;">New data is successfully recorded!</p>';
				}
				else
				{
					$msg = '<p style="color: red;">*ERROR! '. $conn->error .'</p>';
				}
			}
			else
			{
				$msg = '<p style="color: red;">*ERROR! '. $conn->error .'</p>';
			}
		}
	}
}

if(isset($_POST['update'])) // Update data
{
	$current_lecturer_id = $_POST['current_lecturer_id'];
	$lecturer_id = $conn->real_escape_string($_POST['lecturer_id']);
	$lecturer_name = $conn->real_escape_string($_POST['lecturer_name']);

	$mod_on = date('Y-m-d H:i:s');

	$q1 = 'UPDATE lecturers
			SET lecturer_id = ?, lecture_name = ?
			WHERE lecturer_id = ?';

	$q2 = 'UPDATE adm_lect 
			SET admin_id = ?, modified_on = ?
			WHERE lecturer_id = ?';

	$stmt = $conn->prepare($q1);
	$stmt->bind_param('isi', $lecturer_id, $lecturer_name, $current_lecturer_id);

	if($stmt->execute())
	{
		$stmt = $conn->prepare($q2);
		$stmt->bind_param('isi', $session_id, $mod_on, $lecturer_id);
		if (!$stmt->execute())
		{
			$msg = '<p style="color: red;">*ERROR! '. $conn->error .'</p>';
		}
		else
		{
			$msg = '<p style="color: green;">Data is updated successfully!</p>';
		}
	}
	else
	{
		$msg = '<p style="color: red;">*ERROR! '. $conn->error .'</p>';
	}
}

if (isset($_POST['delete'])) // Delete data
{
	$lecturer_id = $_POST['lect_id'];

	$sql = 'SELECT subject_code FROM workloads WHERE lecturer_id = ?';

	$stmt = $conn->prepare($sql);
	$stmt->bind_param('i', $lecturer_id);
	if ($stmt->execute())
	{
		$result = $stmt->get_result();
		if ($result->num_rows > 0)
		{
			$msg = '<p style="color: red;">*ERROR! This lecturer has assigned with subject(s)!</p>';
		}
		else
		{
			$sql = 'DELETE FROM lecturers WHERE lecturer_id = ?';
			$stmt = $conn->prepare($sql);
			$stmt->bind_param('i', $lecturer_id);
			
			if ($stmt->execute())
			{
				$msg = '<p style="color: green;">*Data is successfully deleted!</p>';
			}
			else
			{
				$msg = "<p style='color: red;'>*ERROR! $conn->error</p>";
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
				$sql = 'SELECT l.lecturer_id, l.lecturer_name, a.admin_id, a.admin_name, modified_on
				FROM adm_lect
				INNER JOIN admins AS a ON (adm_lect.admin_id = a.admin_id)
				INNER JOIN lecturers AS l ON (adm_lect.lecturer_id = l.lecturer_id)';
				if ($result = $conn->query($sql))
				{
					if ($result->num_rows > 0) {
						while ($row = $result->fetch_assoc()) { ?>
							<tr style="text-align: center">
							<td><?php echo $row['lecturer_id']; ?></td>
							<td><?php echo $row['lecturer_name']; ?></td>
							<td><?php echo $row['admin_name']; ?></td>
							<td><?php $date = $row['modified_on'];
							echo date("j/n/Y g:i:s A", strtotime($date)); ?></td>
							<td><button onclick="onUpdate(<?php echo $row['lecturer_id']; ?>, '<?php echo $row['lecturer_name']; ?>')">Update</button></td>
							<td><button onclick="onDelete('<?php echo $row['lecturer_id']; ?>', '<?php echo $row['lecturer_name']; ?>')">Delete</button></td>				
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