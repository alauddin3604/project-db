<?php
require '../connection.php';
session_start();
date_default_timezone_set('Asia/Kuala_Lumpur');
$msg = "";

if (isset($_SESSION['session_id']))
	$session_id = $_SESSION['session_id'];
else
	header('location: ../index.php');

$q = "SELECT Adm_ID, Adm_Name FROM admin WHERE Adm_ID = '$session_id'";
if(!$result = $conn->query($q)) {
	echo $conn->error;
}
else {
	$row = $result->fetch_assoc();
	$session_name = $row['Adm_Name'];
}

if(isset($_POST['add'])) // Add new data
{
	if (empty($_POST['adm_id']) || empty($_POST['adm_name'])) {
		$msg = '<div class="w3-panel w3-pale-red w3-display-container">
		<span onclick="this.parentElement.style.display=\'none\'"
		class="w3-button w3-large w3-display-topright">&times;</span>
		<h3>Unsuccessful!</h3>
		<p>Please fill all the fields!</p>
		</div>';
	}
	else {
		$id = $_POST['adm_id'];
		$name = $_POST['adm_name'];
		$pass = $id;

		$hashedPassword = password_hash($pass, PASSWORD_DEFAULT);
		$sql = "INSERT INTO admin VALUES('$id', '$name', '$hashedPassword', 0)";

		if ($conn->query($sql)) {
			$msg = '<div class="w3-panel w3-pale-green w3-display-container">
			<span onclick="this.parentElement.style.display=\'none\'"
			class="w3-button w3-large w3-display-topright">&times;</span>
			<h3>Success!</h3>
			<p>New data is successfully recorded.</p>
			</div>';
		} else {
			$msg = '<div class="w3-panel w3-pale-red w3-display-container">
			<span onclick="this.parentElement.style.display=\'none\'"
			class="w3-button w3-large w3-display-topright">&times;</span>
			<h3>Unsuccessful!</h3>
			<p>'.$conn->error.'</p>
			</div>';
		}
	}
}

if(isset($_POST['delete'])) {// Delete admin
	$adm_id = $_POST['adm_id'];

	$sql = "SELECT a.Adm_ID, b.Adm_ID, c.Adm_ID
			FROM adm_lect a, adm_stud b, adm_sub c
			WHERE  a.Adm_ID = '$adm_id' OR b.Adm_ID = '$adm_id' OR c.Adm_ID = '$adm_id'";
	
	
	if (!$result = $conn->query($sql)) {
		echo $conn->error;
	}
	else {
		if ($result->num_rows > 0) {
			$msg = '<div class="w3-panel w3-pale-red w3-display-container">
			<span onclick="this.parentElement.style.display=\'none\'"
			class="w3-button w3-large w3-display-topright">&times;</span>
			<h3>Unsuccessful!</h3>
			<p>The admin data is currently used in other tables.</p>
			</div>';
		}
		else {
			$sql = "DELETE FROM admin WHERE Adm_ID = '$adm_id'";

			if ($conn->query($sql)) {
				$msg = '<div class="w3-panel w3-pale-green w3-display-container">
				<span onclick="this.parentElement.style.display=\'none\'"
				class="w3-button w3-large w3-display-topright">&times;</span>
				<h3>Successful!</h3>
				<p>Data is deleted successfully.</p>
				</div>';
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
	<title>Register Admin</title>
	<link rel="stylesheet" href="../css/w3.css">
	<style>
		input::-webkit-outer-spin-button,
		input::-webkit-inner-spin-button {
			-webkit-appearance: none;
			margin: 0;
		}
	</style>
</head>
<body>
	<div class="w3-container">
		<div class="w3-bar w3-light-grey">
			<a href="home.php" class="w3-bar-item w3-button">Home</a>
			<a href="admin-reg.php" class="w3-bar-item w3-button w3-black">Admin</a>
			<a href="student-reg.php" class="w3-bar-item w3-button">Student</a>
			<a href="lecturer-reg.php" class="w3-bar-item w3-button">Lecturer</a>
			<a href="subject-reg.php" class="w3-bar-item w3-button">Subject</a>
			<a href="workload-reg.php" class="w3-bar-item w3-button">Workload</a>
			<a href="../logout.php" class="w3-bar-item w3-button w3-right">Log Out</a>
		</div>
		<p>Current session: <?php echo $session_id.", ".$session_name ?></p>
		<br>
		<br>
		<table class="w3-table w3-bordered">
			<tr>
				<th>Admin ID</th>
				<th>Admin Name</th>
				<th>Update</th>
				<th>Delete</th>
			</tr>
			<?php
				$sql = "SELECT * FROM admin";
				$result = $conn->query($sql);
				
				if($result->num_rows > 0){
					while ($row = $result->fetch_assoc()) { ?>
						<tr style="text-align: center">
						
						<td><?php echo $row['Adm_ID']; ?></td>
						<td><?php echo $row['Adm_Name']; ?></td>
						<td><a href="student-update.php?id=<?php echo $row["Adm_ID"]; ?>"><button>Update</button></a></td>
						<?php if ($row['Adm_ID'] != $session_id) { ?>
							<form action="" method="POST">
								<input type="text" name="adm_id" value="<?php echo $row["Adm_ID"]; ?>" hidden />
								<td><button type="submit" name="delete">Delete</button></td>
							</form>				
							<?php } else { echo "<td></td>"; }?>
						</tr>
						<?php
					}
				}
			?>
			<tr>
				<form action="" method="POST">
					<td><input class="w3-input" type="text" name="adm_id" placeholder="Admin ID" /></td>
					<td><input class="w3-input" type="text" name="adm_name" placeholder="Admin name" /></td>
					<td><input class="w3-button w3-light-grey w3-border " type="submit" name="add" value="Add" /></td>
					<td></td>
				</form>
			</tr>
		</table>
		<p><?php echo $msg; ?></p>
	</div>
</body>
</html>