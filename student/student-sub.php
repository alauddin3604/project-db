<?php
require '../connection.php';
session_start();
$msg = "";

if (isset($_SESSION['stud_id'])) {
	$stud_id = $_SESSION['stud_id'];
}
else {
	header('location: ../index.php');
}

if (isset($_POST['register'])) {
	$sub_code = $_POST['sub_code'];

	$sql = "INSERT INTO stud_sub VALUES ('$stud_id', '$sub_code')";

	if (!$conn->query($sql)) {
		$msg = '<div class="w3-panel w3-pale-red w3-display-container">
		<span onclick="this.parentElement.style.display=\'none\'"
		class="w3-button w3-large w3-display-topright">&times;</span>
		<h3>Unsuccessful!</h3>
		<p>'.$conn->error.'</p>
		</div>';
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Register subject</title>
	<link rel="stylesheet" href="../css/w3.css">
	<style>
		table {
			table-layout: fixed;
		}
		td {
			height: 30px;
		}
	</style>
</head>
<body>
	<div class="w3-container">
		<div class="w3-bar w3-light-grey">
			<a href="home.php" class="w3-bar-item w3-button w3-black">Subject</a>
			
			<a href="../logout.php" class="w3-bar-item w3-button w3-right">Log Out</a>
		</div>
		<br>
		<a href="home.php"><button>Subject list</button></a>
		<h4>Register subject below</h4>
		<table class="w3-table w3-bordered">
			<tr>
				<th style="width: 5%">No</th>
				<th>Lecturer</th>
				<th style="width: 50%">Subject</th>
				<th>Register</th>
			</tr>
			<?php
				$sql1 = "SELECT workload.*, l.Lect_ID, l.Lect_Name, s.Sub_Code, s.Sub_Name FROM workload
						INNER JOIN lecturer AS l ON (workload.Lect_ID = l.Lect_ID)
						INNER JOIN subject AS s ON (workload.Sub_Code = s.Sub_Code)";
				

				$result1 = $conn->query($sql1);

				if($result1->num_rows > 0){
					$i = 1;
					foreach ($result1 as $row) { ?>
						<tr style="text-align: center">
							<td><?php echo $i; ?></td>
							<td><?php echo $row['Lect_Name']; ?></td>
							<td><?php echo $row["Sub_Name"]; ?></td>
							<td>
								<?php 
								$sub = $row['Sub_Code'];
								$sql2 = "SELECT * FROM stud_sub WHERE Stud_ID = '$stud_id' AND Sub_Code = '$sub'";
								$result2 = $conn->query($sql2);
								if(!$result2) die($conn->error);
								if ($result2->num_rows > 0) echo "Already registered";
								else {
								?>
								<form action="" method="POST">
									<input type="text" name="sub_code" value="<?php echo $row['Sub_Code']?>" hidden>
									<input class="w3-button w3-light-grey" type="submit" name="register" value="Register">
								</form>
							
							</td>
						</tr>
						<?php
								}
						$i++;
					}
				}
			?>
		</table>
		<p><?php echo $msg; ?></p>
	</div>
	<script>    
	if(typeof window.history.pushState == 'function') {
		window.history.pushState({}, "Hide", "http://localhost:8012/project-db/student/student-sub.php");
	}
	</script>
</body>
</html>