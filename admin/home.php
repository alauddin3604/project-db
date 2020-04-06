<?php
require '../connection.php';
session_start();

if (isset($_SESSION['session_id']))
	$session_id = $_SESSION['session_id'];
else
	header('location: ../index.php');

$sql = "SELECT Adm_Name FROM admin WHERE Adm_ID = $session_id";

if ($result = $conn->query($sql)) {
	$row = $result->fetch_assoc();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
	<link rel="stylesheet" href="../css/w3.css">
	<style>
		.align-center {
			text-align: center;
			margin-top: 5em;
		}
	</style>
</head>
<body>
	
	<br>
	<div class="align-center">
		<p>Current session: <?php echo $session_id . ", " . ucwords(strtolower($row['Adm_Name'])); ?></p>
		<p>What you want to do?</p>
		<a href="admin.php"><button>Register Admin</button></a>
		<a href="student.php"><button>Register Student</button></a>
		<a href="lecturer.php"><button>Register Lecturer</button></a>
		<a href="subject.php"><button>Register Subject</button></a>
		<a href="workload.php"><button>Register Workload</button></a><br>
		<p><a href="../logout.php"><button>Logout</button></a></p>
	</div>
	
</body>
</html>