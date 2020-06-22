<?php
session_start();
require '../connection.php';
include '../message.php';
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

# SQL to list registered subjects
$sql = "SELECT stud_sub.*, sb.subject_name, l.lecturer_name, w.workload_id, t.mark AS mark_tf, o.mark AS mark_obj
		FROM stud_sub
		JOIN workloads w ON (stud_sub.workload_id = w.workload_id)
		LEFT JOIN mark_truefalse t ON (stud_sub.workload_id = t.workload_id) AND (stud_sub.student_id = t.student_id)
		LEFT JOIN mark_objective o ON (stud_sub.workload_id = o.workload_id) AND (stud_sub.student_id = o.student_id)
		INNER JOIN subjects sb ON (w.subject_code = sb.subject_code)
		INNER JOIN lecturers l ON (w.lecturer_id = l.lecturer_id)
		WHERE stud_sub.student_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $student_id);
$stmt->execute();
$resultList = $stmt->get_result();

if (isset($_POST['view']))
{
	if (isset($_POST['mark-tf']))
	{
		if ($_POST['mark-tf'] == null || $_POST['mark-tf'] == 0) // If mark is 0 or haven't take the quiz yet
		{
			$_SESSION['workload_id'] = $_POST['workload_id'];
			header('location: quiz-tf.php');
		}
		else
		{
			$msg = $alreadyTookQuizMsg;
			unset($_POST);
		}
	}
	
	if (isset($_POST['mark-obj']))
	{
		if ($_POST['mark-obj'] == null || $_POST['mark-obj'] == 0)
		{
			$_SESSION['workload_id'] = $_POST['workload_id'];
			header('location: quiz-obj.php');
		}
		else
		{
			$msg = $alreadyTookQuizMsg;
		}
	}
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Home: Student</title>
	<link rel="stylesheet" href="../css/w3.css">
	<link rel="stylesheet" href="../css/style.css">
</head>
<body>
	<div class="w3-container">
		<div class="w3-bar">
			<a href="student-sub.php" class="w3-bar-item w3-button w3-light-grey">Register Subject</a>
			<a href="../logout.php" class="w3-bar-item w3-button w3-right">Log Out</a>
		</div>
		<p>Current Session: <?php echo $student_id . ', '.$student_name; ?></p>
		<h4>Subject list</h4>
		<?php echo $msg; ?>
		<table class="w3-table w3-bordered">
		<tr>
			<th>No</th>
			<th>Lecturer</th>
			<th>Subject</th>
			<th>Mark T/F</th>
			<th>Quiz T/F</th>
			<th>Mark<br>Objective</th>
			<th>Quiz Objective</th>
		</tr>
		<?php
			$i = 1;
			foreach ($resultList as $row) { ?>
				<tr>
				<td><?php echo $i; ?></td>
				<td><?php echo $row['lecturer_name']; ?></td>
				<td><?php echo $row['subject_name']; ?></td>
				<td><?php echo $row['mark_tf'] ?></td>
				<form action="" method="POST">
					<input type="text" name="mark-tf" value="<?php echo $row['mark_tf']; ?>" hidden>
					<input type="text" name="workload_id" value="<?php echo $row['workload_id']; ?>" hidden>
					<td><button class="w3-button w3-round w3-light-grey" type="submit" name="view">View</button></td>
				</form>
				<td><?php echo $row['mark_obj'] ?></td>
				<form action="" method="POST">
					<input type="text" name="mark-obj" value="<?php echo $row['mark_obj'] ?>" hidden>
					<input type="text" name="workload_id" value="<?php echo $row['workload_id'] ?>" hidden>
					<td><button class="w3-button w3-round w3-light-grey" type="submit" name="view">View</button></td>
				</form>		
				</tr>
			<?php
			$i++;
			}
			?>		
		</table>
	</div>
</body>
</html>