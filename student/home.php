<?php
session_start();
require '../connection.php';

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
$sql = "SELECT stud_sub.*, sb.*, l.lecturer_id, l.lecturer_name, w.lecturer_id, w.subject_code
		FROM stud_sub
		INNER JOIN workloads w ON (stud_sub.subject_code = w.subject_code) AND (stud_sub.lecturer_id = w.lecturer_id)
		INNER JOIN subjects sb ON (w.subject_code = sb.subject_code)
		INNER JOIN lecturers l ON (w.lecturer_id = l.lecturer_id)
		WHERE stud_sub.student_id = '$student_id'";

if(!$result = $conn->query($sql)) die($conn->error); 
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
		<table class="w3-table w3-bordered">
		<tr>
			<th>No</th>
			<th>Lecturer</th>
			<th>Subject</th>
			<th>Mark T/F</th>
			<th>Quiz T/F</th>
			<th>Mark Subjective</th>
			<th>Quiz Subjective</th>
		</tr>
		<?php
			$i = 1;
			foreach ($result as $row) { ?>
				<tr>
				<td><?php echo $i; ?></td>
				<td><?php echo $row['lecturer_name']; ?></td>
				<td><?php echo $row['subject_name']; ?></td>
				<td></td>
				<td><a href="quiz-tf.php?q=<?php echo $row['subject_code']; ?>"><button>View</button></a></td>
				<td></td>
				<td><a href="quiz-subjective.php?q=<?php echo $row['subject_code']; ?>"><button>View</button></a></td>
				</tr>
			<?php
			$i++;
			}
			?>		
		</table>
	</div>
</body>
</html>