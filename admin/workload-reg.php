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

$query1 = "SELECT Lect_ID, Lect_Name FROM lecturer ORDER BY Lect_Name";
if (!$res1 = $conn->query($query1))
	die($conn->error);

$query2 = "SELECT Sub_Code, Sub_Name FROM subject ORDER BY Sub_Name";
if (!$res2 = $conn->query($query2))
	die($conn->error);

if(isset($_GET['add']))
{
	$lect_id = $_GET['lecturer'];
	$sub_code = $_GET['subject'];

	$sql = "SELECT * FROM workload WHERE Lect_ID='$lect_id' AND Sub_Code='$sub_code'";

	if ($result = $conn->query($sql)) {
		if ($result->num_rows > 0) {
			$msg = '<div class="w3-panel w3-pale-red w3-border">
			<h3>Unsuccessful!</h3>
			<p>The lecturer has already assigned with that subject!</p>
			</div>';
		}
		else {
			$sql = "INSERT INTO workload VALUES ('$lect_id', '$sub_code')";
			if ($conn->query($sql)) {
				$msg = '<div class="w3-panel w3-pale-green w3-border">
				<h3>Success!</h3>
				<p>New data is successfully recorded.</p>
				</div>';
			} else {
				echo "Error: " . $conn->error; 
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
	<title>Register Workload</title>
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
			<a href="admin-reg.php" class="w3-bar-item w3-button">Admin</a>
			<a href="student-reg.php" class="w3-bar-item w3-button">Student</a>
			<a href="lecturer-reg.php" class="w3-bar-item w3-button">Lecturer</a>
			<a href="subject-reg.php" class="w3-bar-item w3-button">Subject</a>
			<a href="workload-reg.php" class="w3-bar-item w3-button w3-black">Workload</a>
			<a href="../logout.php" class="w3-bar-item w3-button w3-right">Log Out</a>
		</div>
		<p>Current session: <?php echo $adm_id.", ".$adm_name ?></p>
		<br>
		<br>
		<table class="w3-table w3-bordered">
			<tr>
				<th>No</th>
				<th>Lecturer</th>
				<th>Subject</th>
				<th>Update</th>
				<th>Delete</th>
			</tr>
			<?php
				$sql = "SELECT l.Lect_ID, l.Lect_Name, s.Sub_Code, s.Sub_Name
				FROM workload
				INNER JOIN lecturer as l ON (workload.Lect_ID=l.Lect_ID)
				INNER JOIN subject as s ON (workload.Sub_Code=s.Sub_Code)";
				$result = $conn->query($sql);
				$i = 1;
				if($result->num_rows > 0){
					while ($row = $result->fetch_assoc()) { ?>
						<tr>
							<td><?php echo $i; $i++; ?></td>
							<td><?php echo $row['Lect_Name']; ?></td>
							<td><?php echo $row['Sub_Name']; ?></td>
							<td><a href="workload-update.php?id=<?php echo $row["Adm_ID"]; ?>"><button>Update</button></a></td>
							<td><a href="delete.php?adm_id=<?php echo $row["Adm_ID"]; ?>"><button>Delete</button></a></td>
						</tr>
						<?php
					}
				}
			?>
			<tr>
				<form action="workload-reg.php" method="get">
					<td></td>
					<td>
						<select class="w3-select" name="lecturer">
							<option value="" disabled selected>Choose lecturer</option>
							<?php while($row1 = $res1->fetch_array()):;?>
							<option value="<?php echo $row1[0];?>"><?php echo $row1[1];?></option>
							<?php endwhile; ?>
						</select>
					</td>
					<td>
						<select class="w3-select" name="subject">
							<option value="" disabled selected>Choose subject</option>
							<?php while($row2 = $res2->fetch_array()):;?>
							<option value="<?php echo $row2[0];?>"><?php echo $row2[1];?></option>
							<?php endwhile; ?>
						</select>
					</td>
					<td><input class="w3-button w3-light-grey w3-border" type="submit" name="add" value="Add" /></td>
					<td></td>
				</form>
			</tr>
		</table>
		<?php echo $msg; ?>
	</div>
	<script>    
	if(typeof window.history.pushState == 'function') {
		window.history.pushState({}, "Hide", "http://localhost:8012/project-db/admin/workload-reg.php");
	}
	</script>
</body>
</html>