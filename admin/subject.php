<?php
require '../connection.php';
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

if (isset($_POST['add'])) // Add new data
{
	$sub_code = $conn->real_escape_string($_POST['sub_code']);

	$sql = 'SELECT * FROM subjects WHERE subject_code=?';
	$stmt = $conn->prepare($sql);
	$stmt->bind_param('s', $sub_code);
	$stmt->execute();
	$result = $stmt->get_result();

	if ($result->num_rows > 0)
	{
		$msg = '<p style="color: red;">*ERROR! Duplicate subject code!</p>';
	}
	else
	{
		$sub_name = $_POST['sub_name'];

		$date = date('Y-m-d H:i:s');

		$sql1 = 'INSERT INTO subjects VALUES(?, ?)';
		$stmt = $conn->prepare($sql1);
		$stmt->bind_param('ss', $sub_code, $sub_name);
		
		if ($stmt->execute())
		{
			$sql2 = "INSERT INTO adm_sub VALUES(?,?,?)";
			$stmt = $conn->prepare($sql2);
			$stmt->bind_param('iss', $session_id, $sub_code, $date);
			if ($stmt->execute())
			{
				$msg = '<p style="color: green;">The subject is successfully registered!</p>';
			}
			else
			{
				$msg = '<p style="color: red;">*ERROR! '.$conn->error.'</p>';
			}
		}
		else
		{
			$msg = '<p style="color: red;">*ERROR! '.$conn->error.'</p>';
		}
	}	
}

if(isset($_POST['update'])) // Update data
{
	$sub_code = $conn->real_escape_string($_POST['sub_code']);
	$sub_name = $conn->real_escape_string($_POST['sub_name']);
	$curr_sub_code = htmlspecialchars($_POST['curr_sub_code']);

	$mod_on = date('Y-m-d H:i:s');

	$q1 = 'UPDATE subjects
			SET subject_code=?, subject_name=?
			WHERE subject_code=?';

	$q2 = 'UPDATE adm_sub 
			SET admin_id=?, modified_on=?
			WHERE subject_code=?';

	$stmt = $conn->prepare($q1);
	$stmt->bind_param('sss', $sub_code, $sub_name, $curr_sub_code);
	
	if(!$stmt->execute())
	{
		$msg = '<p style="color: red;">*ERROR! '.$conn->error.'</p>';
	}
	else
	{
		$stmt = $conn->prepare($q2);
		$stmt->bind_param('iss', $session_id, $mod_on, $sub_code);
		if ($stmt->execute())
		{
			$msg = '<p style="color: green;">Data is updated successfully.</p>';
		}
		else
		{
			$msg = '<p style="color: red;">*ERROR! '.$conn->error.'</p>';
		}	
	}
}

if (isset($_POST['delete'])) // Delete data
{
	$subject_code = $conn->real_escape_string($_POST['sub_code']);

	$sql = 'SELECT subject_code FROM workloads WHERE subject_code = ?';
	$stmt = $conn->prepare($sql);
	$stmt->bind_param('s', $subject_code);
	if ($stmt->execute())
	{
		$result = $stmt->get_result();
		if ($result->num_rows > 0)
		{
			$msg = '<p style="color: red;">*ERROR! The subject has assigned to lecturer(s).</p>';
		}
		else
		{
			$sql = 'DELETE FROM subjects WHERE subject_code = ?';
			$stmt = $conn->prepare($sql);
			$stmt->bind_param('s', $subject_code);

			if ($stmt->execute())
			{
				$msg = '<p style="color: green;">Data is deleted successfully.</p>';
			}
			else
			{
				$msg = '<p style="color: red;">*ERROR! '.$conn->error.'</p>';
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
				$sql = 'SELECT s.subject_code, s.subject_name, a.admin_id, a.admin_name, modified_on
				FROM adm_sub
				INNER JOIN admins as a ON (adm_sub.admin_id = a.admin_id)
				INNER JOIN subjects as s ON (adm_sub.subject_code = s.subject_code)
				ORDER BY s.subject_code';
				if($result = $conn->query($sql))
				{
					if($result->num_rows > 0){
						while ($row = $result->fetch_assoc()) { ?>
							<tr style="text-align: center">
							<td><?php echo $row['subject_code']; ?></td>
							<td><?php echo $row['subject_name']; ?></td>
							<td><?php echo $row['admin_name']; ?></td>
							<td><?php $date = $row['modified_on'];
							echo date("j/n/Y g:i:s A", strtotime($date)); ?></td>
							<td><button onclick="onUpdate('<?php echo $row['subject_code']; ?>', '<?php echo $row['subject_name']; ?>')">Update</button></td>
							<td><button onclick="onDelete('<?php echo $row['subject_code']; ?>', '<?php echo $row['subject_name']; ?>')">Delete</button></td>
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