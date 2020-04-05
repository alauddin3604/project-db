<?php
require '../connection.php';
session_start();
date_default_timezone_set('Asia/Kuala_Lumpur');
$msg = "";

if (isset($_SESSION['adm_id']))
	$adm_id = $_SESSION['adm_id'];
else
	header('location: ../index.php');

$q = "SELECT Adm_ID, Adm_Name FROM admin WHERE Adm_ID = '$adm_id'";
if(!$result = $conn->query($q)) {
	echo $conn->error;
}
else {
	$row = $result->fetch_array(MYSQLI_ASSOC);
	$adm_name = $row['Adm_Name'];
}

// Add new data
if(isset($_POST['add']))
{
	$lect_id = $_POST['lect_id'];
	$lect_name = $_POST['lect_name'];
	$hashedPassword = password_hash($lect_id, PASSWORD_DEFAULT);
	$date = date("Y-m-d H:i:s");

	$sql = "SELECT * FROM lecturer WHERE Lect_ID = '$lect_id'";

	if ($res = $conn->query($sql)) {
		if ($res->num_rows > 0) {
			$msg = '<div class="w3-panel w3-pale-red w3-display-container">
			<span onclick="this.parentElement.style.display=\'none\'"
			class="w3-button w3-large w3-display-topright">&times;</span>
			<h3>Unsuccessful!</h3>
			<p>The lecturer ID has already registered!</p>
			</div>';
		}
		else {
			$sql1 = "INSERT INTO lecturer VALUES('$lect_id', '$lect_name', '$hashedPassword', 0)";
			$sql2 = "INSERT INTO adm_lect VALUES('$adm_id', '$lect_id', '$date')";
			if($conn->query($sql1)) {
				if($conn->query($sql2)) {
					$msg = '<div class="w3-panel w3-pale-green w3-border">
					<h3>Success!</h3>
					<p>New data is successfully recorded.</p>
					</div>';
				}
				else {
					$msg = '<div class="w3-panel w3-pale-red w3-border">
					<h3>Unsuccessful!</h3>
					<p>Error: '.$conn->error.'</p>
					</div>';
				}
			}
			else {
				$msg = '<div class="w3-panel w3-pale-red w3-border">
				<h3>Unsuccessful!</h3>
				<p>Error: '.$conn->error.'</p>
				</div>';;
			}
		}
	}
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Register Lecturer</title>
	<link rel="stylesheet" href="../css/w3.css">
</head>
<body>
	<div class="w3-container">
		<div class="w3-bar w3-light-grey">
			<a href="home.php" class="w3-bar-item w3-button">Home</a>
			<a href="admin-reg.php" class="w3-bar-item w3-button">Admin</a>
			<a href="student-reg.php" class="w3-bar-item w3-button">Student</a>
			<a href="lecturer-reg.php" class="w3-bar-item w3-button w3-black">Lecturer</a>
			<a href="subject-reg.php" class="w3-bar-item w3-button">Subject</a>
			<a href="workload-reg.php" class="w3-bar-item w3-button">Workload</a>
			<a href="../logout.php" class="w3-bar-item w3-button w3-right">Log Out</a>
		</div>
		<p>Current session: <?php echo $adm_id.", ".$adm_name ?></p>
		<br>
		<br>
		<table class="w3-table w3-bordered">
			<tr>
				<th>Lecturer ID</th>
				<th>Lecturer Name</th>
				<th>Modified By</th>
				<th>Modified On</th>
				<th>Update</th>
				<th>Delete</th>
			</tr>
			<?php
				$sql = "SELECT l.Lect_ID, l.Lect_Name, a.Adm_ID, a.Adm_Name, Mod_On
				FROM adm_lect
				INNER JOIN admin as a ON (adm_lect.Adm_ID=a.Adm_ID)
				INNER JOIN lecturer as l ON (adm_lect.Lect_ID=l.Lect_ID)";
				if($result = $conn->query($sql))
				{
					if($result->num_rows > 0){
						while ($row = $result->fetch_array(MYSQLI_ASSOC)) { ?>
							<tr style="text-align: center">
							<td><?php echo $row['Lect_ID']; ?></td>
							<td><?php echo $row['Lect_Name']; ?></td>
							<td><?php echo $row['Adm_Name']; ?></td>
							<td><?php echo $row['Mod_On']; ?></td>
							<td><a href="lecturer-update.php?lect_id=<?php echo $row["Lect_ID"]; ?>"><button>Update</button></a></td>
							<td><a href="delete.php?lect_id=<?php echo $row["Lect_ID"]; ?>"><button>Delete</button></a></td>					
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
					<td><input class="w3-input" type="text" name="lect_id" placeholder="Add lecturer ID"/></td>
					<td><input class="w3-input" type="text" name="lect_name" placeholder="Add lecturer name" /></td>
					<td><input class="w3-button w3-light-grey w3-border " type="submit" name="add" value="Add" /></td>
					<td></td>
					<td></td>
					<td></td>
				</form>
			</tr>
		</table>
		<?php echo $msg; ?>
	</div>
	<script>    
	if(typeof window.history.pushState == 'function') {
		window.history.pushState({}, "Hide", "http://localhost:8012/project-db/admin/lecturer-reg.php");
	}
	</script>
</body>
</html>