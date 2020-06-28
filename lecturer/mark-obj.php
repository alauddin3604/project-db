<?php
session_start();
require '../connection.php';
$msg = '';

// If no lecturer is login, go to login page
if (!isset($_SESSION['lecturer_id']))
{
	header('location: ../index.php');
}

if (isset($_SESSION['workload_id']) && isset($_SESSION['subject_code']))
{
	$subject_code = $_SESSION['subject_code'];
	$q = 'SELECT * FROM subjects WHERE subject_code = ?';
	$stmt = $conn->prepare($q);
	$stmt->bind_param('s', $subject_code);
	if (!$stmt->execute())
	{
		echo $conn->error;
	}
	else
	{
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		$subject_name = $row['subject_name'];
	}

	$workload_id = $_SESSION['workload_id'];

	// List the students' name, id and their mark that take the subject
	$sql = 'SELECT s.student_name, ss.student_id, m.mark
			FROM stud_sub ss
			INNER JOIN students s ON ss.student_id = s.student_id
			LEFT JOIN mark_objective m ON (m.workload_id = ss.workload_id) AND (m.student_id = ss.student_id)
			WHERE ss.workload_id = ?';

	$stmt = $conn->prepare($sql);
	$stmt->bind_param('i', $workload_id);
	$stmt->execute();
	$resultList = $stmt->get_result();
	//$row = $resultList->fetch_assoc();
	//echo $row['mark'];
	// End of list

	// Get students who pass or fail - 1
	$sql = 'SELECT COUNT(*) FROM quiz_objective WHERE workload_id = ?';
	$stmt = $conn->prepare($sql);
	$stmt->bind_param('i', $workload_id);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_array();
	$totalQuestion = $row[0];
	
	$passMark = $totalQuestion;
	$fullMark = $passMark * 2;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Quiz Objective</title>
	<link rel="stylesheet" href="../css/w3.css">
	<link rel="stylesheet" href="../css/style.css">
</head>
<body>
	<div class="w3-container">
		<div class="w3-bar">
			<a href="home.php" class="w3-bar-item w3-button w3-light-grey">Home</a>
			<a href="../logout.php" class="w3-bar-item w3-button w3-right">Log Out</a>
		</div>
		<p>Subject Name: <?php echo $subject_name; ?></p>
		<p>Subject Code: <?php echo $subject_code; ?></p>
		<br>
		<br>
		<table class="w3-table w3-bordered">
			<tr>
				<th>No</th>
				<th>Student Name</th>
				<th>Student ID</th>
				<th>Result Quiz Objective</th>
			</tr>
			<?php
			$i = 1;
			if($resultList->num_rows > 0)
			{
				while ($row = $resultList->fetch_assoc())
				{
					echo '<tr>';
					echo '<td>' . $i . '</td>';
					echo "<td>$row[student_name]</td>";
					echo '<td>' . $row['student_id'] . '</td>';
					if (is_null($row['mark']))
						echo '<td></td>';
					else
						echo "<td>$row[mark] / $fullMark</td>";
					echo '</tr>';
				}
			}
			?>
			</tbody>
		</table>
		<?php
		// Get students who pass or fail - 2
		$numPass = 0;
		$numFail = 0;
		$sql = 'SELECT mark FROM mark_objective WHERE workload_id = ?';
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('i', $workload_id);
		if ($stmt->execute())
		{
			$resultPass = $stmt->get_result();
			if ($resultPass->num_rows > 0)
			{
				foreach ($resultPass as $row)
				{
					$mark = $row['mark'];
					if ($mark >= $passMark)
					{
						$numPass++;
					}
					else
					{
						$numFail++;
					}
				}
				echo 'Number of students pass: ' . $numPass . '. Number of students fail: ' . $numFail;
			}
		}
		?>
	</div>
</body>
</html>