<?php
require '../connection.php';
session_start();

if (isset($_SESSION['stud_id'])) {
	$stud_id = $_SESSION['stud_id'];
	$q = "SELECT Stud_ID, Stud_Name FROM student WHERE Stud_ID = '$stud_id'";
	if(!$result = $conn->query($q)) {
		echo $conn->error;
	}
	else {
		$row = $result->fetch_assoc();
		$stud_name = $row['Stud_Name'];
	}
}
else {
	header('location: ../index.php');
}

# SQL to list registered subjects
$sql = "SELECT stud_sub.*, sb.Sub_Code, sb.Sub_Name, l.Lect_ID, l.Lect_Name, w.Lect_ID, w.Sub_Code
		FROM stud_sub
		INNER JOIN workload w ON (stud_sub.Sub_Code=w.Sub_Code) AND (stud_sub.Lect_ID=w.Lect_ID)
		INNER JOIN subject sb ON (w.Sub_Code=sb.Sub_Code)
		INNER JOIN lecturer l ON (w.Lect_ID=l.Lect_ID)
		WHERE stud_sub.Stud_ID = '$stud_id'";

if(!$result = $conn->query($sql)) die($conn->error); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Home: Student</title>
	<link rel="stylesheet" href="../css/w3.css">
</head>
<body>
	<div class="w3-container">
		<div class="w3-bar">
			<a href="student-sub.php" class="w3-bar-item w3-button w3-light-grey">Register Subject</a>
			<a href="../logout.php" class="w3-bar-item w3-button w3-right">Log Out</a>
		</div>
		<p>Current Session: <?php echo $stud_id; ?></p>
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
				<td><?php echo $row['Lect_Name']; ?></td>
				<td><?php echo $row['Sub_Name']; ?></td>
				<td></td>
				<td><button>View</button></td>
				<td></td>
				<td><button>View</button></td>
				</tr>
			<?php
			}
			?>		
		</table>
	</div>
</body>
</html>