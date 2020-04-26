<?php
require '../connection.php';
session_start();
$msg = '';

if (isset($_SESSION['student_id']))
{
	$student_id = $_SESSION['student_id'];
	$q = 'SELECT student_id, student_name FROM students WHERE student_id = ?';
	$stmt = $conn->prepare($q);
	$stmt->bind_param('s', $student_id);
	if(!$stmt->execute())
	{
		echo $conn->error;
	}
	else
	{
		$row = $stmt->get_result()->fetch_assoc();
		$student_name = $row['student_name'];
	}
}
else
{
	header('location: ../index.php');
}

if (isset($_POST['register'])) // Student registers subject
{
	$subject_code = $_POST['subject_code'];
	$lecturer_id = $_POST['lecturer_id'];

	$sql = 'INSERT INTO stud_sub VALUES (?,?,?)';

	$stmt = $conn->prepare($sql);
	$stmt->bind_param('ssi', $student_id, $subject_code, $lecturer_id);
	if (!$stmt->execute())
	{
		$msg = '<p class="error">*ERROR! '.$conn->error.'</p>';
	}
	else
	{
		$msg = '<p class="success">Done registered ' . $subject_code . '</p>';
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Register subject</title>
	<link rel="stylesheet" href="../css/w3.css">
	<link rel="stylesheet" href="../css/style.css">
	<style>
		table {
			table-layout: fixed;
		}
		td {
			height: 30px;
		}
	</style>
</head>
<body>
	<div class="w3-container">
		<div class="w3-bar">
			<a href="home.php" class="w3-bar-item w3-button w3-light-grey">Subject List</a>
			<a href="../logout.php" class="w3-bar-item w3-button w3-right">Log Out</a>
		</div>
		<p>Current Session: <?php echo $student_id .', '.$student_name; ?></p>
		<h4>Register subject below</h4>
		<table class="w3-table w3-bordered">
			<tr>
				<th style="width: 5%">No</th>
				<th>Lecturer</th>
				<th style="width: 50%">Subject</th>
				<th>Register</th>
			</tr>
			<?php
				$sql1 = 'SELECT workloads.*, l.lecturer_id, l.lecturer_name, s.subject_code, s.subject_name FROM workloads
						INNER JOIN lecturers AS l ON (workloads.lecturer_id = l.lecturer_id)
						INNER JOIN subjects AS s ON (workloads.subject_code = s.subject_code)
						ORDER BY s.subject_name';
				
				$stmt1 = $conn->prepare($sql1);
				if (!$stmt1->execute()) {
					die($conn->error);
				}		
				$result1 = $stmt1->get_result();

				if($result1->num_rows > 0) {
					$i = 1;
					foreach ($result1 as $row) { ?>
						<tr style="text-align: center">
							<td><?php echo $i; ?></td>
							<td><?php echo $row['lecturer_name']; ?></td>
							<td><?php echo $row["subject_name"]; ?></td>
							<td> <!-- Display message if student has registered the subject -->
								<?php 
								$sub = $row['subject_code'];
								$sql2 = 'SELECT * FROM stud_sub WHERE student_id = ? AND subject_code = ?';
								$stmt2 = $conn->prepare($sql2);
								$stmt2->bind_param('ss', $student_id, $sub);
								if (!$stmt2->execute()) die($conn->error);
								$result2 = $stmt2->get_result();
								if ($result2->num_rows > 0) {
									echo '<button class="w3-button" disabled>Already registered</button>'; // If row is present, disable register button
								}
								else {
								?>
								<form action="" method="POST">
									<input type="text" name="subject_code" value="<?php echo $row['subject_code']?>" hidden>
									<input type="text" name="lecturer_id" value="<?php echo $row['lecturer_id']?>" hidden>
									<input class="w3-button w3-light-grey" type="submit" name="register" value="Register">
								</form>
							</td>
						</tr>
						<?php
								}
						$i++;
					}
				}
			?>
		</table>
		<?php echo $msg; ?>
	</div>
</body>
</html>