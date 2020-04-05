<?php
$conn = new mysqli("localhost", "root", "", "project_db");
if ($conn->connect_errno) {
    die('Failed to connect to MySQL: ' . $conn->connect_error);
}

?>