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
echo $student_id.'<br>';
if (isset($_POST['submit']))
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
		echo 'Success';
		header('location: home.php');
	}
	else
		echo 'Error'.$conn->error;
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
		
	</div>
</body>
</html>