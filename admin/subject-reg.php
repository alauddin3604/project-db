<?php
require '../connection.php';
session_start();
date_default_timezone_set('Asia/Kuala_Lumpur');

if (isset($_SESSION['adm_id']))
	$adm_id = $_SESSION['adm_id'];
else
	header('location: ../index.php');

$q = "SELECT Adm_ID, Adm_Name FROM admin WHERE Adm_ID = '$adm_id'";
if(!$result = $conn->query($q)) {
	echo $conn->error;
}
else {
	$row = $result->fetch_assoc();
	$adm_name = $row['Adm_Name'];
}

if(isset($_POST['add']))
{
	$sub_code = $_POST['sub_code'];
	$sub_name = $_POST['sub_name'];

	$date = date("Y-m-d H:i:s");

	$sql1 = "INSERT INTO subject VALUES('$sub_code', '$sub_name')";
	$sql2 = "INSERT INTO adm_sub VALUES('$adm_id', '$sub_code', '$date')";
	if($conn->query($sql1))
	{
		if(!$conn->query($sql2))
			echo "Error: " . $sql2 . ": " . $conn->error;
		else
			echo "<script>alert('New data is successfully recorded');</script>";
	}
	else
	{
		echo "Error: " . $sql1 . ": " . $conn->error;
	}
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Subject Registration</title>
	<link rel="stylesheet" href="../css/w3.css">
</head>
<body>
	<div class="w3-container">
		<div class="w3-bar w3-light-grey">
			<a href="home.php" class="w3-bar-item w3-button">Home</a>
			<a href="admin-reg.php" class="w3-bar-item w3-button">Admin</a>
			<a href="student-reg.php" class="w3-bar-item w3-button">Student</a>
			<a href="lecturer-reg.php" class="w3-bar-item w3-button">Lecturer</a>
			<a href="subject-reg.php" class="w3-bar-item w3-button w3-black">Subject</a>
			<a href="workload-reg.php" class="w3-bar-item w3-button">Workload</a>
			<a href="../logout.php" class="w3-bar-item w3-button w3-right">Log Out</a>
		</div>
		<p>Current session: <?php echo $adm_id.", ".$adm_name ?></p>
		<br>
		<br>
		<table class="w3-table w3-bordered">
			<tr>
				<th>Subject Code</th>
				<th>Subject Name</th>
				<th>Modified By</th>
				<th>Modified On</th>
				<th>Update</th>
				<th>Delete</th>
			</tr>
			<?php
				$sql = "SELECT s.Sub_Code, s.Sub_Name, a.Adm_ID, a.Adm_Name, Mod_On
				FROM adm_sub
				INNER JOIN admin as a ON (adm_sub.Adm_ID=a.Adm_ID)
				INNER JOIN subject as s ON (adm_sub.Sub_Code=s.Sub_Code)
				ORDER BY s.Sub_Code";
				if($result = $conn->query($sql))
				{
					if(mysqli_num_rows($result) > 0){
						while ($row = $result->fetch_array(MYSQLI_ASSOC)) { ?>
							<tr style="text-align: center">
							<td><?php echo $row['Sub_Code']; ?></td>
							<td><?php echo $row['Sub_Name']; ?></td>
							<td><?php echo $row['Adm_Name']; ?></td>
							<td><?php $date = $row['Mod_On'];
							echo date("j/n/Y g:i:s A", strtotime($date)); ?></td>
							<td><a href="subject-update.php?code=<?php echo $row["Sub_Code"]; ?>"><button>Update</button></a></td>
							<td><a href="delete.php?code=<?php echo $row["Sub_Code"]; ?>"><button>Delete</button></a></td>
							</tr>
							<?php
						}
					}
				}
				else
				{
					echo $conn->error;
				}				
			?>
			<tr>
				<form action="" method="POST">
					<td><input class="w3-input" type="text" name="sub_code" placeholder="Add subject code"/></td>
					<td><input class="w3-input" type="text" name="sub_name" placeholder="Add subject name" /></td>
					<td><input class="w3-button w3-light-grey w3-border" type="submit" name="add" value="Add"></td>
					<td></td>
					<td></td>
					<td></td>
				</form>
			</tr>
		</table>
	</div>

	<script>    
	if(typeof window.history.pushState == 'function') {
		window.history.pushState({}, "Hide", "http://localhost:8012/project-db/admin/subject-reg.php");
	}
	</script>
</body>
</html>