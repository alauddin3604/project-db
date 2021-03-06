<?php
session_start();
require '../connection.php';

// Check session
if (isset($_SESSION['student_id']))
{
	$student_id = $_SESSION['student_id'];
}
else
{
	header('location: ../');
}

if (isset($_SESSION['workload_id']))
{
	$workload_id = $_SESSION['workload_id'];
	
	// To get the subject's name
	$sql = 'SELECT w.*, s.*
			FROM workloads w
			INNER JOIN subjects s ON s.subject_code = w.subject_code
			WHERE workload_id = ?';
	$stmt = $conn->prepare($sql);
	$stmt->bind_param('i', $workload_id);
	if ($stmt->execute())
	{
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		$subject_code = $row['subject_code'];
		$subject_name = $row['subject_name'];
	}
	else
	{
		die($conn->error);
	}

	// To list the questions
	$sql = 'SELECT q.* 
			FROM quiz_objective q
			WHERE workload_id = ?';
	$stmt = $conn->prepare($sql);
	$stmt->bind_param('i', $workload_id);
	if ($stmt->execute())
	{
		$result = $stmt->get_result();
	}
	else
	{
		die('FAILED: ' . $conn->error);
	}	
}
else
{
	header('location: home.php');
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
		<form action="quiz-obj-submit.php" method="POST">
		<table class="w3-table w3-bordered">
			<tr>
				<th>No</th>
				<th>Question</th>
				<th>Option A</th>
				<th>Option B</th>
				<th>Option C</th>
				<th>Option D</th>
				<th>Your Answer</th>
			</tr>
			<?php
			$i = 1;
			if($result->num_rows > 0)
			{
				while ($row = $result->fetch_assoc())
				{ ?>
					<tr>				
					<td><?php echo $i ?></td>
					<td><?php echo $row['question'] ?></td>
					<td><?php echo $row['option_a'] ?></td>
					<td><?php echo $row['option_b'] ?></td>
					<td><?php echo $row['option_c'] ?></td>
					<td><?php echo $row['option_d'] ?></td>
					<td><input class="w3-input" type="text" name="answer[<?php echo $i ?>]"></td>	
					</tr>
					<?php
					$i++;
				}
			} ?>
		</table>
		<br>
		<input class="w3-button w3-round w3-light-grey w3-right" type="submit" name="submit" onclick="return confirm('Are you sure you want to submit your answers?')" value="Submit">
		</form>
		<?php
		if (isset($_GET['mark']))
			echo "Thank you! Your mark is $_GET[mark]";
		?>
	</div>
</body>
</html>