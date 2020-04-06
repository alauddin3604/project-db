<?php
require '../connection.php';

if(isset($_POST['del-admin'])) // Delete admin
{
	
	$adm_id = $_POST['adm_id'];

		$sql = "SELECT a.Adm_ID, b.Adm_ID, c.Adm_ID
				FROM adm_lect a, adm_stud b, adm_sub c
				WHERE  a.Adm_ID = '$adm_id' OR b.Adm_ID = '$adm_id' OR c.Adm_ID = '$adm_id'";
	
	
		if (!$result = $conn->query($sql)) {
			echo $conn->error;
		}
		else {
			if ($result->num_rows > 0) {
				echo "<script>
						alert('Unsuccessful! Admin data is being used at other tables.');
						window.location.href = document.referrer;
					</script>";
			}
			else {
				$sql = "DELETE FROM admin WHERE Adm_ID = '$adm_id'";

				if ($conn->query($sql)) {
					echo "<script>
						alert('Data is deleted successfully.');
						window.location.href = document.referrer;
					</script>";
				}
			}
		}
	
}
else if (isset($_GET['code'])) { // Delete subject
	$sub_code = $_GET['code'];

	$sql = "SELECT Sub_Code FROM workload 
			WHERE Sub_Code = '$sub_code'";

	if ($result = $conn->query($sql)) {
		if ($result->num_rows > 0) {
			echo "<script>
				alert('Unsuccessful! Admin data is being used at other tables.');
				window.location.href = document.referrer;
			</script>";
		}
		else {
			echo "delete";
		}
	}
}
else if (isset($_GET['stud_id'])) { // Delete student data
	$stud_id = $_GET['stud_id'];

	$sql = "SELECT Stud_ID FROM stud_sub WHERE Stud_ID = '$stud_id'";

	if ($result = $conn->query($sql)) {
		if ($resulr->num_rows > 0) {
			echo "<script>
			alert('Unsuccessful! Admin data is being used at other tables.');
			window.location.href = document.referrer;
			</script>";
		}
		else {
			$sql = "DELETE FROM student WHERE Stud_ID = '$stud_id'";

			if ($conn->query($sql)) {
				echo "<script>
				alert('Data is deleted successfully.');
				window.location.href = document.referrer;
				</script>";
			}
			else {
				echo "<script>
				alert('Unsuccessful! ". $conn->error .".');
				window.location.href = document.referrer;
				</script>";
			}
		}
	}
}
else if (isset($_GET['lect_id'])) { // Delete lecturer data
	$lect_id = $_GET['lect_id'];

	$sql = "SELECT Sub_Code FROM workload 
			WHERE Lect_ID = '$lect_id'";

	if ($result = $conn->query($sql)) {
		if ($result->num_rows > 0) {
			echo "<script>
				alert('Unsuccessful! Admin data is being used at other tables.');
				window.location.href = document.referrer;
			</script>";
		}
		else {
			$sql = "DELETE FROM lecturer WHERE Lect_ID = '$lect_id'";
			
			if ($conn->query($sql)) {
				echo "<script>
				alert('Data is deleted successfully.');
				window.location.href = document.referrer;
				</script>";
			}
			else {
				echo "<script>
				alert('Unsuccessful! ". $conn->error .".');
				window.location.href = document.referrer;
				</script>";
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
	<title>Delete</title>
</head>
<body>
	
</body>
</html>