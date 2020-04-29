<?php
session_start();
require '../connection.php';

# Check session
if (!isset($_SESSION['student_id']))
{
	header('location: ../');
}

$subject_code = $_GET['q'];

$sql = 'SELECT * FROM subjects WHERE subject_code = ?';
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $subject_code);
if ($stmt->execute())
{
	$row = $stmt->get_result()->fetch_assoc();
	$subject_name = $row['subject_name'];
}
else
{
	die($conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Quiz</title>
	<link rel="stylesheet" href="../css/w3.css">
	<link rel="stylesheet" href="../css/style.css">
</head>
<body>
	<div class="w3-container">
		<div class="w3-bar">
			<a href="home.php" class="w3-bar-item w3-button w3-light-grey">Registered Subject List</a>
			<a href="../logout.php" class="w3-bar-item w3-button w3-right">Log Out</a>
		</div>
		<p>Subject Name: <?php echo $subject_name; ?></p>
		<p>Subject Code: <?php echo $subject_code; ?></p>
		<table>
			<tr>
				<th>No</th>
				<th>Question</th>
				<th>True or False</th>
				<th>Confirm your answer</th>
			</tr>
		</table>
	</div>
</body>
</html>