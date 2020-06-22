<?php
require '../connection.php';
include '../message.php';
session_start();
date_default_timezone_set('Asia/Kuala_Lumpur');

// Messages to display
$msg = '';

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

if (isset($_POST['add'])) // Add new data
{
	$student_id = $conn->real_escape_string($_POST['student_id']);

	$sql = 'SELECT student_id FROM students WHERE student_id = ?';

	$stmt = $conn->prepare($sql);
	$stmt->bind_param('s', $student_id);
	$stmt->execute();
	$result = $stmt->get_result();

	if ($result->num_rows > 0)
	{
		$msg = $duplicateMsg; // Return error
	}
	else
	{
		$student_name = strtoupper($conn->real_escape_string($_POST['student_name']));
		$student_email = $student_id.'@siswa.uthm.edu.my';
		$student_password = password_hash($student_id, PASSWORD_DEFAULT);
		$log_status = 0;

		$mod_on = date('Y-m-d H:i:s');
		
		$sql1 = 'INSERT INTO students VALUES(?,?,?,?,?)';
		$sql2 = 'INSERT INTO adm_stud VALUES(?, ?, ?)';

		$stmt = $conn->prepare($sql1);
		$stmt->bind_param('ssssi', $student_id, $student_name, $student_email, $student_password, $log_status);
		
		if ($stmt->execute())
		{
			$stmt = $conn->prepare($sql2);
			$stmt->bind_param('iss', $session_id, $student_id, $mod_on);
			if($stmt->execute())
			{
				$msg = $addMsg;
			}
			else
			{
				$msg = '<p class="error">*1 '.$conn->error.'</p>';
			}
		}
		else
		{
			$msg = $errorMsg;
		}
	}
	
}

if(isset($_POST['update'])) // Update data
{
	$current_student_id = htmlspecialchars($_POST['current_student_id']);
	$student_id = htmlspecialchars($_POST['student_id']);

	if ($current_student_id != $student_id)
	{
		# Check if entered ID is duplicate
		$sql = "SELECT student_id FROM students WHERE student_id = ?";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('s', $student_id);
		$stmt->execute();
		$result = $stmt->get_result();

		if ($result->num_rows > 0)
		{
			$msg = $duplicateMsg; // Return error
		}
		else
		{
			goto update;
		}
	}
	else
	{
		update:
		$student_name = strtoupper(htmlspecialchars($_POST['student_name']));
		$student_email = $student_id . '@siswa.uthm.edu.my';
		$mod_on = date('Y-m-d H:i:s');

		$sql = 'SELECT student_id, log_status FROM students WHERE student_id = ?';
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('s', $current_student_id);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();

		if ($row['log_status'] == 0) // Check if user doesn't login for the first time
		{
			$student_password = password_hash($student_id, PASSWORD_DEFAULT);
			$q1 = 'UPDATE students SET student_id=?, student_name=?, student_email=?, student_password=? WHERE student_id=?';
			$stmt = $conn->prepare($q1);
			$stmt->bind_param('sssss', $student_id, $student_name, $student_email, $student_password, $current_student_id);

			if (!$stmt->execute())
			{
				$msg = $errorMsg;
			}
		}
		else
		{
			$q1 = "UPDATE students SET student_id=?, student_name=?, student_email=? WHERE student_id=?";
			$stmt = $conn->prepare($q1);
			$stmt->bind_param('ssss', $student_id, $student_name, $student_email, $current_student_id);

			if (!$stmt->execute())
			{
				die($conn->error);
			}
		}

		$q2 = "UPDATE adm_stud SET admin_id = ?, modified_on = ? WHERE student_id = ?";
		$stmt = $conn->prepare($q2);
		$stmt->bind_param('iss', $session_id, $mod_on, $stud_id);

		if ($stmt->execute())
		{
			$msg = $updateMsg;
		}
		else
		{
			$msg = $errorMsg;
		}
	}
}

if (isset($_POST['delete'])) // Delete student data
{
	$student_id = htmlspecialchars($_POST['student_id']);

	$sql = "SELECT student_id FROM stud_sub WHERE student_id = ?"; // Check if student has registered subject
	$stmt = $conn->prepare($sql);
	$stmt->bind_param('s', $student_id);
	$stmt->execute();
	$result = $stmt->get_result();
	if ($result->num_rows > 0)
	{
		$msg = $studentMsg;
	}
	else
	{
		$sql = "DELETE FROM students WHERE student_id = ?";
		$stmt->prepare($sql);
		$stmt->bind_param('s', $student_id);
		if ($stmt->execute()) {
			$msg = $deleteMsg;
		}
		else {
			$msg = '<p class="error">*ERROR! '.$conn->error.'';
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
	<link rel="stylesheet" href="../css/style.css">
</head>
<body>
	<div class="w3-container">
		<div class="w3-bar w3-dark-grey">
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
		<?php echo $msg ?>
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
			$sql = 'SELECT s.student_id, s.student_name, s.student_email, a.admin_id, a.admin_name, modified_on
					FROM adm_stud
					INNER JOIN admins as a ON (adm_stud.admin_id = a.admin_id)
					INNER JOIN students as s ON (adm_stud.student_id = s.student_id)';
				
			$stmt = $conn->prepare($sql);
			$stmt->execute();
			$result = $stmt->get_result();
			if($result->num_rows > 0) {
				while ($row = $result->fetch_assoc()) { ?>
					<tr>					
					<td><?php echo $row['student_id']; ?></td>
					<td><?php echo $row['student_name']; ?></td>
					<td><?php echo $row['student_email']; ?></td>
					<td><?php echo $row['admin_name']; ?></td>
					<td>
						<?php $date = $row['modified_on'];
						echo date("j/n/Y<\b\\r>g:i:s A", strtotime($date)); ?>
					</td>
					<td><button class="w3-button w3-round w3-light-grey" onclick="onUpdate('<?php echo $row['student_id']; ?>', '<?php echo $row['student_name']; ?>')">Update</button></td>
					<td><button class="w3-button w3-round w3-light-grey" onclick="onDelete('<?php echo $row['student_id']; ?>', '<?php echo $row['student_name']; ?>')">Delete</button></td>
					</tr>
					<?php
				}
			}
			?>
				<tr>
					<form action="" method="POST">
						<td><input class="w3-input" type="text" name="student_id" placeholder="Add student ID"/></td>
						<td><input class="w3-input" type="text" name="student_name" placeholder="Add student name" /></td>
						<td><input class="w3-button w3-round w3-light-grey" type="submit" name="add" value="Add" /></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</form>
				</tr>
			</tbody>
		</table>
	</div>
	<!-- Update popup box -->
	<div id="onUpdate" class="w3-modal">
		<div class="w3-modal-content w3-card-4" style="max-width:600px">
			<div class="w3-center"><br>
				<span onclick="document.getElementById('onUpdate').style.display='none'" class="w3-button w3-xlarge w3-transparent w3-display-topright" title="Close Modal">×</span>
			</div>
			<form class="w3-container" action="" method="POST">
				<div class="w3-section">
					<input type="text" id="curr_stud_id" name="current_student_id" hidden>
					<label><b>Student ID</b></label>
					<input class="w3-input w3-border w3-margin-bottom" type="text" id="stud_id" name="student_id" required>
					<label><b>Student Name</b></label>
					<input class="w3-input w3-border" type="text" id="stud_name" name="student_name" required>
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
					<p>Are you sure you want to delete this data? </p>
					<label><b>Student ID</b></label>
					<input class="w3-input w3-border w3-margin-bottom" type="text" id="id" name="student_id" readonly>
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
			document.getElementById('curr_stud_id').value = stud_id;
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