<?php
session_start();
require '../connection.php';
include '../message.php';
date_default_timezone_set('Asia/Kuala_Lumpur');
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
	$msg = $errorMsg;
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
	$lecturer_name = strtoupper($_POST['lect_name']);
	$lecturer_password = password_hash($lecturer_id, PASSWORD_DEFAULT);
	$date = date("Y-m-d H:i:s");

	$sql = 'SELECT * FROM lecturers WHERE lecturer_id = ?';
	$stmt = $conn->prepare($sql);
	$stmt->bind_param('i', $lecturer_id);

	if ($stmt->execute())
	{
		$result = $stmt->get_result();
		if ($result->num_rows > 0)
			$msg = $duplicateMsg;
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
				if($stmt->execute())
					$msg = $addMsg;
				else
					$msg = 'ERROR1';
			}
			else
				$msg = 'ERROR2';
		}
	}
}

if(isset($_POST['update'])) // Update data
{
	$current_lecturer_id = $_POST['current_lecturer_id'];
	$lecturer_id = $conn->real_escape_string($_POST['lecturer_id']);

	if ($current_lecturer_id != $lecturer_id)
	{
		# Check if entered ID is duplicate
		$sql = "SELECT lecturer_id FROM lecturers WHERE lecturer_id = ?";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('i', $lecturer_id);
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
		$lecturer_name = $conn->real_escape_string($_POST['lecturer_name']);
		$mod_on = date('Y-m-d H:i:s');

		$sql = 'SELECT lecturer_id, log_status FROM lecturers WHERE lecturer_id = ?'; // Check if user doesn't login for the first time
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('s', $current_lecturer_id);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();

		if ($row['log_status'] == 0)
		{
			$lecturer_password = password_hash($lecturer_id, PASSWORD_DEFAULT);
			$q1 = 'UPDATE lecturers SET lecturer_id = ?, lecturer_name = ?, lecturer_password = ? WHERE lecturer_id = ?';
			$stmt = $conn->prepare($q1);
			$stmt->bind_param('issi', $lecturer_id, $lecturer_name, $lecturer_password, $current_lecturer_id);

			if (!$stmt->execute())
			{
				$msg = $errorMsg;
			}
		}
		else
		{
			$q1 = 'UPDATE lecturers SET lecturer_id = ?, lecturer_name = ? WHERE lecturer_id = ?';
			$stmt = $conn->prepare($q1);
			$stmt->bind_param('isi', $lecturer_id, $lecturer_name, $current_lecturer_id);

			if (!$stmt->execute())
			{
				die($conn->error);
			}
		}
		
		$q2 = 'UPDATE adm_lect SET admin_id = ?, modified_on = ? WHERE lecturer_id = ?'; // Update to adm_lect table
		$stmt = $conn->prepare($q2);
		$stmt->bind_param('isi', $session_id, $mod_on, $lecturer_id);

		if ($stmt->execute())
		{
				$msg = $updateMsg;
		}
		else
		{
			die($conn->error);
		}
	}
}

if (isset($_POST['delete'])) // Delete data
{
	$lecturer_id = $_POST['lect_id'];

	$sql = 'SELECT subject_code FROM workloads WHERE lecturer_id = ?'; // Check if lecturer has assigned subject(s)

	$stmt = $conn->prepare($sql);
	$stmt->bind_param('i', $lecturer_id);
	if ($stmt->execute())
	{
		$result = $stmt->get_result();
		if ($result->num_rows > 0)
		{
			$msg = $usedMsg;
		}
		else
		{
			$sql = 'DELETE FROM lecturers WHERE lecturer_id = ?';
			$stmt = $conn->prepare($sql);
			$stmt->bind_param('i', $lecturer_id);
			
			if ($stmt->execute())
			{
				$msg = $deleteMsg;
			}
			else
			{
				die($conn->error);
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
	<link rel="stylesheet" href="../css/style.css">
</head>
<body>
	<div class="w3-container">
		<div class="w3-bar w3-dark-grey">
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
		<?php echo $msg; ?>
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
							echo date("j/n/Y<\b\\r>g:i:s A", strtotime($date)); ?></td>
							<td><button class="w3-button w3-round w3-light-grey update" data-id="<?php echo $row['lecturer_id'] ?>" data-name="<?php echo $row['lecturer_name'] ?>">Update</button></td>
							<td><button class="w3-button w3-round w3-light-grey delete" data-id="<?php echo $row['lecturer_id'] ?>" data-name="<?php echo $row['lecturer_name'] ?>">Delete</button></td>				
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
					<td><input class="w3-input" type="text" name="lect_id" placeholder="Add lecturer ID" required></td>
					<td><input class="w3-input" type="text" name="lect_name" placeholder="Add lecturer name" required></td>
					<td><input class="w3-button w3-round w3-light-grey" type="submit" name="add" value="Add" required></td>
					<td></td>
					<td></td>
					<td></td>
				</form>
			</tr>
		</table>
	</div>
	<!-- Update popup box -->
	<div id="onUpdate" class="w3-modal">
		<div class="w3-modal-content w3-card-4" style="max-width:600px">
  			<div class="w3-center"><br>
				<span class="w3-button w3-xlarge w3-transparent w3-display-topright" title="Close Modal">×</span>
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
			<div class="w3-container w3-border-top w3-padding-16">
				<button type="button" class="w3-button w3-red w3-right w3-padding cancel">Cancel</button>
			</div>
		</div>
	</div>
	<!-- Delete popup box -->
	<div id="onDelete" class="w3-modal">
		<div class="w3-modal-content w3-card-4" style="max-width:600px">
			<div class="w3-center"><br>
				<span class="w3-button w3-xlarge w3-transparent w3-display-topright" title="Close Modal">×</span>
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
					<button type="button" class="w3-button w3-red w3-right w3-padding cancel">Cancel</button>
					<button class="w3-button w3-green w3-right w3-padding" type="submit" name="delete">Confirm</button>
				</div>
			</form>
		</div>
	</div>
	<script src="../js/jquery-3.4.1.min.js"></script>
	<script>
		$(document).ready(function() {
			$(".update").click(function() {
				var id = $(this).data("id");
				var name = $(this).data("name");
				$("#onUpdate").css("display", "block");
				$("#current_lecturer").val(id);
				$("#lect_id").val(id);
				$("#lect_name").val(name);
			});

			$(".delete").click(function() {
				var id = $(this).data("id");
				var name = $(this).data("name");
				$('#onDelete').css('display', 'block');
				$("#del-lect_id").val(id);
				$("#del-lect_name").val(name);
			});

			$("span[title='Close Modal']").click(function() {
				$("#onUpdate").css("display", "none");
				$("#onDelete").css("display", "none");
			});

			$(".cancel").click(function() {
				$("#onUpdate").css("display", "none");
				$("#onDelete").css("display", "none");
			});
		});
	</script>
</body>
</html>