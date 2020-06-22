<?php
date_default_timezone_set('Asia/Kuala_Lumpur');
session_start();
require '../connection.php';

# Check session
if (isset($_SESSION['student_id']))
{
	$student_id = $_SESSION['student_id'];
	$workload_id = $_SESSION['workload_id'];
}
else
{
	header('location: ../');
}

if (isset($_POST['submit']))
{
	$sql = 'SELECT *
			FROM quiz_objective
			WHERE workload_id = ?';
	$stmt = $conn->prepare($sql);
	$stmt->bind_param('i', $workload_id);
	$stmt->execute();
	$result = $stmt->get_result();
	
	foreach($_POST['answer'] as $i => $answer_val)
	{
		$answer[$i] = $answer_val; // Store the answers in an array
	}

	$totalMark = 0;
	$i = 1;
	while($row = $result->fetch_array()){
		echo 'ans[' . $i . '] = ' . $answer[$i];
		if ($answer[$i] == $row['answer']) {
			echo 'Right';
			$totalMark = $totalMark + 2;
		}
		else
			echo 'False';
		echo '<br>';
		++$i;
	}
	echo $totalMark;

	$date =date('Y-m-d H:i:s');

	$sql = 'INSERT INTO mark_objective (student_id, workload_id, mark, date_submit)
			VALUES (?, ?, ?, ?)';
	$stmt = $conn->prepare($sql);
	$stmt->bind_param('siis', $student_id, $workload_id, $totalMark, $date);
	if ($stmt->execute())
	{
		echo 'Success';
		
	}
	else
		echo 'Error' . $conn->error;
}