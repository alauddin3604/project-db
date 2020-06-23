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
	/* Check if student has take that subject*/
	$sql = 'SELECT * FROM mark_objective WHERE student_id = ? AND workload_id = ?';
	$stmt = $conn->prepare($sql);
	$stmt->bind_param('si', $student_id, $workload_id);
	$stmt->execute();
	$result = $stmt->get_result();
	if ($result->num_rows > 0) // Means student has took the quiz
	{
		$row = $result->fetch_assoc();
		if ($row['mark'] != 0 || $row['mark'] != null) // Check if his/her mark isn't 0
		{
			header('location: home.php?err=1');
		}
		else // If 0, proceed to quiz submission
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

			$sql = 'UPDATE mark_objective SET mark = ?, date_submit = ?
					WHERE student_id = ? AND workload_id = ?';
			$stmt = $conn->prepare($sql);
			$stmt->bind_param('issi', $totalMark, $date, $student_id, $workload_id);
			if ($stmt->execute())
			{
				// echo 'Success';
				header('location: quiz-obj.php?mark=' . $totalMark);
			}
			else
				echo 'Error' . $conn->error;
		}
	}
	else // Student hasn't take the quiz yet
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
			// echo 'Success';
			header('location: quiz-obj.php?mark=' . $totalMark);
		}
		else
			echo 'Error' . $conn->error;
	}
}