<?php
date_default_timezone_set('Asia/Kuala_Lumpur');
session_start();
require '../connection.php';
include '../message.php';
$msg = '';

if (isset($_SESSION['lecturer_id']))
	$lecturer_id = $_SESSION['lecturer_id'];
else
	header('location: ../index.php');

if (isset($_SESSION['subject_code']) && isset($_SESSION['workload_id']))
{
	$subject_code = $_SESSION['subject_code'];
	$workload_id = $_SESSION['workload_id'];
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
}

// Add question
if (isset($_POST['add']))
{
	$question = $_POST['question'];
	$option_a = $conn->real_escape_string($_POST['option_a']);
	$option_b = $conn->real_escape_string($_POST['option_b']);
	$option_c = $conn->real_escape_string($_POST['option_c']);
	$option_d = $conn->real_escape_string($_POST['option_d']);
	$answer = $conn->real_escape_string($_POST['answer']);

	$date = date('Y-m-d H:i:s');

	$query = 'INSERT INTO quiz_objective (question, option_a, option_b, option_c, option_d, answer, workload_id, modified_on)
			VALUES (?, ?, ?, ?, ?, ?, ?, ?)';

	if ($stmt = $conn->prepare($query))
	{
		$stmt->bind_param('ssssssis', $question, $option_a, $option_b, $option_c, $option_d, $answer, $workload_id, $date);
		if (!$stmt->execute())
		{
			echo 'Failed to execute: ' . $conn->error;
		}
	}
	else
	{
		echo 'Error: '. $conn->error;
	}
}

// Update question
if (isset($_POST['update']))
{
	$id = $_POST['id']; // workload_id
	$question = $conn->real_escape_string($_POST['question']);
	$option_a = $conn->real_escape_string($_POST['option_a']);
	$option_b = $conn->real_escape_string($_POST['option_b']);
	$option_c = $conn->real_escape_string($_POST['option_c']);
	$option_d = $conn->real_escape_string($_POST['option_d']);
	$answer = $conn->real_escape_string($_POST['answer']);
	$date = date('Y-m-d H:i:s');

	$sql = 'UPDATE quiz_objective
			SET question = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, answer = ?, modified_on = ?
			WHERE id = ?';
	$stmt = $conn->prepare($sql);
	$stmt->bind_param('sssssssi', $question, $option_a, $option_b, $option_c, $option_d, $answer, $date, $id);

	if ($stmt->execute())
		$msg = $updateMsg;
	else
		die($conn->error);
}

// Delete question
if (isset($_POST['delete']))
{
	$quiz_id = $_POST['del_quiz_id'];
	
	$sql = 'DELETE FROM quiz_objective WHERE id = ?';
	$stmt = $conn->prepare($sql);
	$stmt->bind_param('i', $quiz_id);
	if ($stmt->execute())
		$msg = $deleteMsg;
	else
		die($conn->error);
}


$getWorkload = 'SELECT workload_id FROM workloads WHERE lecturer_id = ? AND subject_code = ?';
$stmtWork = $conn->prepare($getWorkload);
$stmtWork->bind_param('is', $lecturer_id, $subject_code);
if ($stmtWork->execute())
{	
	$resultWork = $stmtWork->get_result();
	$rowWork = $resultWork->fetch_assoc();
	$workload_id = $rowWork['workload_id'];
	$sql = 'SELECT q.*, l.lecturer_name
			FROM quiz_objective AS q
			INNER JOIN workloads w ON q.workload_id = w.workload_id
			INNER JOIN lecturers l ON w.lecturer_id = l.lecturer_id
			WHERE q.workload_id = ?';
	if(!($stmt = $conn->prepare($sql)))
	{
		echo 'Error prepare: ' . $conn->error;
	}
	else		
	{
		$stmt->bind_param('i', $workload_id);
		if (!$stmt->execute())
			die('FAILED: '.$conn->error);
		$result = $stmt->get_result();
	}
}
else
	echo $conn->error;

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
			<a href="../logout.php" class="w3-bar-item w3-button w3-dark-grey w3-right">Log Out</a>
		</div>
		<p>Subject Name: <?php echo $subject_name ?></p>
		<p>Subject Code: <?php echo $subject_code ?></p>
		<br>
		<?php echo $msg ?>
		<br>
		<table class="w3-table w3-bordered">
			<tr>
				<th>No</th>
				<th>Question</th>
				<th>Answer A</th>
				<th>Answer B</th>
				<th>Answer C</th>
				<th>Answer D</th>
				<th>Correct Answer</th>
				<th>Modified By</th>
				<th>Modified On</th>
				<th>Update</th>
				<th>Delete</th>
			</tr>
			<?php
			$i = 1;
			if ($result->num_rows > 0)
			{
				while ($row = $result->fetch_assoc())
				{ ?>				
					<tr>					
					<td><?php echo $i; ?></td>
					<td><?php echo $row['question']; ?></td>
					<td><?php echo $row['option_a']; ?></td>
					<td><?php echo $row['option_b']; ?></td>
					<td><?php echo $row['option_c']; ?></td>
					<td><?php echo $row['option_d']; ?></td>
					<td><?php echo $row['answer']; ?></td>
					<td><?php echo $row['lecturer_name']; ?></td>
					<td><?php $date = $row['modified_on'];
					echo date("j/m/Y g:i:s A", strtotime($date)) ?></td>
					<td>
						<button class="w3-button w3-round w3-light-grey" onclick="onUpdate(<?php echo $row['id'] ?>, '<?php echo $row['question'] ?>', '<?php echo $row['option_a'] ?>',
						'<?php echo $row['option_b'] ?>', '<?php echo $row['option_c'] ?>', '<?php echo $row['option_d'] ?>','<?php echo $row['answer'] ?>')">Update</button></td>
					<td><button class="w3-button w3-round w3-light-grey" onclick="onDelete(<?php echo $row['id'] ?>, '<?php echo $row['question'] ?>')">Delete</button></td>
					</tr>
					<?php
					$i++;
				}
			}
			?>
				<tr>
					<form action="" method="POST">
						<td></td>
						<td><textarea class="w3-input" type="text" name="question" placeholder="Add question"></textarea></td>
						<td><input class="w3-input" type="text" name="option_a"></td>
						<td><input class="w3-input" type="text" name="option_b"></td>
						<td><input class="w3-input" type="text" name="option_c"></td>
						<td><input class="w3-input" type="text" name="option_d"></td>
						<td><input class="w3-input" type="text" name="answer"></td>
						<td></td>
						<td></td>
						<td></td>
						<td><input class="w3-button w3-round w3-light-grey" type="submit" name="add" value="Add"></td>
					</form>
				</tr>
			</tbody>
		</table>
	</div>
	<!-- Update popup box -->
	<div id="onUpdate" class="w3-modal">
		<div class="w3-modal-content w3-card-4" style="max-width:600px">
			<div class="w3-center"><br>
				<span onclick="document.getElementById('onUpdate').style.display='none'" class="w3-button w3-xlarge w3-transparent w3-display-topright" title="Close Modal">×</span>
			</div>
			<form class="w3-container" action="" method="POST">
				<div class="w3-section">
					<input type="text" id="update_id" name="id" hidden>
					<label><b>Question</b></label>
					<input class="w3-input w3-border w3-margin-bottom" type="text" id="update_question" name="question" required>
					<label><b>Option A</b></label>
					<input class="w3-input w3-border w3-margin-bottom" type="text" id="option_a" name="option_a" required>
					<label><b>Option B</b></label>
					<input class="w3-input w3-border w3-margin-bottom" type="text" id="option_b" name="option_b" required>
					<label><b>Option C</b></label>
					<input class="w3-input w3-border w3-margin-bottom" type="text" id="option_c" name="option_c" required>
					<label><b>Option D</b></label>
					<input class="w3-input w3-border w3-margin-bottom" type="text" id="option_d" name="option_d" required>
					<label><b>Correct Answer</b></label>
					<input class="w3-input w3-border w3-margin-bottom" type="text" id="update_answer" name="answer" required>
					<button class="w3-button w3-block w3-dark-grey w3-section w3-padding" type="submit" name="update">Save</button>
				</div>
			</form>
			<div class="w3-container w3-border-top w3-padding-16 w3-light-grey">
				<button onclick="document.getElementById('onUpdate').style.display='none'" type="button" class="w3-button w3-red w3-right w3-padding">Cancel</button>
			</div>
		</div>
	</div>
	<!-- Delete popup box -->
	<div id="onDelete" class="w3-modal">
		<div class="w3-modal-content w3-card-4" style="max-width:600px">
			<div class="w3-center"><br>
				<span onclick="document.getElementById('onDelete').style.display='none'" class="w3-button w3-xlarge w3-transparent w3-display-topright" title="Close Modal">×</span>
			</div>
			<form class="w3-container" action="" method="POST">
				<input type="text" id="del_quizz_id" name="del_quiz_id" hidden>
				<div class="w3-section">
					<p>Are you sure you want to delete this question? </p>
					<label><b>Question</b></label>
					<input class="w3-input w3-border w3-margin-bottom" type="text" id="del_question" readonly>
				</div>
			
				<div class="w3-container w3-border-top w3-padding-16">
					<button onclick="document.getElementById('onDelete').style.display='none'" type="button" class="w3-button w3-red w3-right w3-padding">Cancel</button>
					<button class="w3-button w3-green w3-right w3-padding" type="submit" name="delete">Confirm</button>
				</div>
		</form>
		</div>
	</div>
	<script>
		function onUpdate(quiz_id, question, option_a, option_b, option_c, option_d, answer)
		{
			document.getElementById('onUpdate').style.display='block';
			document.getElementById('update_id').value = quiz_id;
			document.getElementById('update_question').value = question;
			document.getElementById('option_a').value = option_a;
			document.getElementById('option_b').value = option_b;
			document.getElementById('option_c').value = option_c;
			document.getElementById('option_d').value = option_d;
			document.getElementById('update_answer').value = answer;
		}
		function onDelete(quizz_id, question)
		{
			document.getElementById('onDelete').style.display='block';
			document.getElementById("del_quizz_id").value = quizz_id;
			document.getElementById("del_question").value = question;
		}
	</script>
</body>
</html>