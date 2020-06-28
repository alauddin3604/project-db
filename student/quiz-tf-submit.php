<?php
date_default_timezone_set('Asia/Kuala_Lumpur');
session_start();
require '../connection.php';

# Check session
if (!isset($_SESSION['student_id']))
{
	header('location: ../');
} 
$student_id = $_SESSION['student_id'];
$workload_id = $_SESSION['workload_id'];

if (isset($_POST['submit']))
{
	/* Check if student has take that subject*/
	$sql = 'SELECT * FROM mark_truefalse WHERE student_id = ? AND workload_id = ?';
	$stmt = $conn->prepare($sql);
	$stmt->bind_param('si', $student_id, $workload_id);
	$stmt->execute();
	$result = $stmt->get_result();
	if ($result->num_rows > 0) // Means student has took the quiz
	{
		$row = $result->fetch_assoc();
		if ($row['mark'] != 0 || $row['mark'] != null) // Check if his/her mark isn't 0
		{
			// echo 'took';
			header('location: home.php?err=1');
		}
		else // If 0, update the mark
		{
			$sql = 'SELECT *
					FROM quiz_truefalse
					WHERE workload_id = ?';
			$stmt = $conn->prepare($sql);
			$stmt->bind_param('i', $workload_id);
			$stmt->execute();
			$result = $stmt->get_result();
		
			foreach($_POST['answer'] as $option_num => $option_val)
			{
				if ($option_val == 'true')
					$ans[$option_num] = 1;
				else
					$ans[$option_num] = 0;
				echo $option_num." ".$ans[$option_num]."<br>";	
			}

			$totalMark = 0;
			$i = 1;
			while($row = $result->fetch_array()){
				echo 'ans['.$i.'] = '.$ans[$i];
				if ($ans[$i] == $row['answer']) {
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
			echo $date;
			$sql = 'UPDATE mark_truefalse SET mark = ?, date_submit = ?
					WHERE student_id = ? AND workload_id = ?';
			$stmt = $conn->prepare($sql);
			$stmt->bind_param('issi', $totalMark, $date, $student_id, $workload_id);
			if ($stmt->execute())
			{
				//echo 'mark updated';
				header('location: quiz-tf.php?mark=' . $totalMark);
			}
			else
				echo 'Error'.$conn->error;
		}
	}
	else // Student hasn't take the quiz yet
	{
		$sql = 'SELECT *
				FROM quiz_truefalse
				WHERE workload_id = ?';
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('i', $workload_id);
		$stmt->execute();
		$result = $stmt->get_result();
		
		foreach($_POST['answer'] as $option_num => $option_val)
		{
			if ($option_val == 'true')
				$ans[$option_num] = 1;
			else
				$ans[$option_num] = 0;
			echo $option_num." ".$ans[$option_num]."<br>";
			
		}

		$totalMark = 0;
		$i = 1;
		while($row = $result->fetch_array()){
			echo 'ans['.$i.'] = '.$ans[$i];
			if ($ans[$i] == $row['answer']) {
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
		echo $date;
		$sql = 'INSERT INTO mark_truefalse (student_id, workload_id, mark, date_submit)
				VALUES (?, ?, ?, ?)';
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('siis', $student_id, $workload_id, $totalMark, $date);
		if ($stmt->execute())
		{
			//echo 'inserted';
			header('location: quiz-tf.php?mark=' . $totalMark);
		}
		else
			echo 'Error'.$conn->error;
	}
}
