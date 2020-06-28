<?php
session_start();
require '../connection.php';
include '../message.php';
date_default_timezone_set('Asia/Kuala_Lumpur');
$msg = '';

# Check session
if (!isset($_SESSION['admin_id']))
	header('location: ../index.php');
	
if(isset($_POST['add'])) // Add new data
{
	if (empty($_POST['admin_id']) || empty($_POST['admin_name']))
	{
		$msg = $validationMsg; // Return error
	}
	else
	{
		$admin_id = $conn->real_escape_string($_POST['admin_id']);
		$admin_name = strtoupper($conn->real_escape_string($_POST['admin_name'])); // Convert to uppercase

		$sql = 'SELECT admin_id FROM admins WHERE admin_id = ?';
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('i', $admin_id);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result->num_rows > 0)
			$msg = $duplicateMsg; // Return error
		else
		{
			$log_status = 0;
			$admin_password = password_hash($admin_id, PASSWORD_DEFAULT);
			$sql = 'INSERT INTO admins VALUES (?, ?, ?, ?)';
			$stmt = $conn->prepare($sql);
			$stmt->bind_param('issi', $admin_id, $admin_name, $admin_password, $log_status);
			if ($stmt->execute())
				$msg = $addMsg; // Return success
			else
				$msg = $errorMsg; // Return error
		}
	}
}

if (isset($_POST['update'])) // Update data
{
	$current_admin_id = $_POST['current_admin_id']; // This is used if want to change the ID
	$admin_id =  $_POST['adm_id'];
	$admin_name =  $_POST['adm_name'];
	
	$sql = "UPDATE admins SET admin_id = ?, admin_name = ? WHERE admin_id = ?";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param('isi', $admin_id, $admin_name, $current_admin_id);
	if ($stmt->execute())
	{
		if ($_SESSION['admin_id'] == $current_admin_id)
			$_SESSION['admin_id'] = $admin_id;
		$msg = $updateMsg; // Return success
	}
	else
		$msg = $errorMsg;
}

if (isset($_POST['delete'])) // Delete data
{
	$admin_id = $_POST['adm_id'];

	$sql = 'SELECT a.admin_id, b.admin_id, c.admin_id
			FROM adm_lect a, adm_stud b, adm_sub c
			WHERE  a.admin_id = ? OR b.admin_id = ? OR c.admin_id = ?';
	
	$stmt = $conn->prepare($sql);
	$stmt->bind_param('iii', $admin_id, $admin_id, $admin_id);
	$stmt->execute();
	$result = $stmt->get_result();
		
	if ($result->num_rows > 0)
		$msg = $usedMsg;
	else
	{
		$sql = 'DELETE FROM admins WHERE admin_id = ?';

		$stmt = $conn->prepare($sql);
		$stmt->bind_param('i', $admin_id);
		if ($stmt->execute())
			$msg = $deleteMsg;
		else
			$msg = $errorMsg;
	}
}

$q = 'SELECT admin_id, admin_name FROM admins WHERE admin_id = ?';
$stmt = $conn->prepare($q);
$stmt->bind_param('i', $_SESSION['admin_id']);
if(!$stmt->execute())
{
	echo $conn->error;
}
else
{
	$result = $stmt->get_result();
	$row = $result->fetch_assoc();
	$session_name = $row['admin_name'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Register Admin</title>
	<link rel="stylesheet" href="../css/w3.css">
	<link rel="stylesheet" href="../css/style.css">
	<style>
		input::-webkit-outer-spin-button,
		input::-webkit-inner-spin-button
		{
			-webkit-appearance: none;
			margin: 0;
		}
	</style>
</head>
<body>
	<div class="w3-container">
		<div class="w3-bar w3-dark-grey">
			<a href="home.php" class="w3-bar-item w3-button">Home</a>
			<a href="admin.php" class="w3-bar-item w3-button w3-black">Admin</a>
			<a href="student.php" class="w3-bar-item w3-button">Student</a>
			<a href="lecturer.php" class="w3-bar-item w3-button">Lecturer</a>
			<a href="subject.php" class="w3-bar-item w3-button">Subject</a>
			<a href="workload.php" class="w3-bar-item w3-button">Workload</a>
			<a href="../logout.php" class="w3-bar-item w3-button w3-right">Log Out</a>
		</div>
		<p>Current session: <?php echo $_SESSION['admin_id'].", ".$session_name ?></p>
		<br>
		<?php echo $msg; ?>
		<br>
		<table class="w3-table w3-bordered">
			<tr>
				<th>Admin ID</th>
				<th>Admin Name</th>
				<th>Update</th>
				<th>Delete</th>
			</tr>
			<?php
			$sql = 'SELECT * FROM admins';
			$result = $conn->query($sql);
			
			if ($result->num_rows > 0) {
				while ($row = $result->fetch_assoc()) { ?>
					<tr style="text-align: center">
					
					<td><?php echo $row['admin_id']; ?></td>
					<td><?php echo $row['admin_name']; ?></td>
					<td><button class="w3-button w3-round w3-light-grey update" data-id="<?php echo $row['admin_id'] ?>" data-name="<?php echo $row['admin_name'] ?>">Update</button></td>
					<?php if ($row['admin_id'] != $_SESSION['admin_id']) { ?> <!-- The admin cannot delete their own ID -->
					<td><button class="w3-button w3-round w3-light-grey delete" data-id="<?php echo $row['admin_id'] ?>" data-name="<?php echo $row['admin_name'] ?>">Delete</button></td>
					<?php } else { echo "<td></td>"; }?>
					</tr>
					<?php
				}
			} ?> <!-- To display each result from table -->		
			<tr>
				<form action="" method="POST">
					<td><input class="w3-input" type="text" name="admin_id" placeholder="Admin ID" /></td>
					<td><input class="w3-input" type="text" name="admin_name" placeholder="Admin name" /></td>
					<td><input class="w3-button w3-round w3-light-grey" type="submit" id="btn-add" name="add" value="Add" /></td>
					<td></td>
				</form>
			</tr>		
		</table>
	</div>
	<!-- Update popup box -->
	<div id="onUpdate" class="w3-modal">
		<div class="w3-modal-content w3-card-4" style="max-width:400px">
			<div class="w3-center"><br>
				<span class="w3-button w3-xlarge w3-transparent w3-display-topright" title="Close Modal">×</span>
			</div>
			<form class="w3-container" action="" method="POST">
				<div class="w3-section">
					<input type="text" id="curr_adm_id" name="current_admin_id" hidden>
					<label><b>Admin ID</b></label>
					<input class="w3-input w3-border w3-margin-bottom" type="text" id="adm_id" name="adm_id" required>
					<label><b>Admin Name</b></label>
					<input class="w3-input w3-border" type="text" id="adm_name" name="adm_name" required>			
				</div>
				<div class="w3-container w3-border-top w3-padding-16">
				<button class="w3-button w3-block w3-dark-grey" type="submit" name="update">Save</button>
				</div>
			</form>
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
					<label><b>Admin ID</b></label>
					<input class="w3-input w3-border w3-margin-bottom" type="text" id="del_id" name="adm_id" readonly>
					<label><b>Admin Name</b></label>
					<input class="w3-input w3-border" type="text" id="del_name" name="adm_name" readonly>
				</div>
				<div class="w3-container w3-border-top w3-padding-16">
					<button onclick="document.getElementById('onDelete').style.display='none'" type="button" class="w3-button w3-red w3-right w3-padding">Cancel</button>
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
				$("#curr_adm_id").val(id);
				$("#adm_id").val(id);
				$("#adm_name").val(name);
			});

			$('.delete').click(function() {
				var id = $(this).data('id');
				var name = $(this).data('name');
				$('#onDelete').css('display', 'block');
				$('#del_id').val(id);
				$('#del_name').val(name);
			});

			$("span[title='Close Modal']").click(function() {
				$('#onUpdate').css('display', 'none');
				$('#onDelete').css('display', 'none');
			});
		});
	</script>
</body>
</html>