<?php
include_once 'class.db.php';

class User extends DB
{
	public $err_msg = '*Invalid ID or password!';

	public function __construct()
	{
		parent::__construct();
	}

	public function student_login($id, $password)
	{
		$query = "SELECT Stud_ID, Stud_Pass, Stud_Log FROM student WHERE Stud_ID = ?";
	
		$stmt = $this->db->prepare($query);
		$stmt->bind_param('s', $id);
		$stmt->execute();
		$result = $stmt->get_result();

		if ($result->num_rows > 0)
		{
			$row = $result->fetch_assoc();
			if (password_verify($password, $row['Stud_Pass']))
			{
				$_SESSION['stud_id'] = $id;
				if ($row['Stud_Log'] == false)
				{
					$stmt = $this->db->prepare("UPDATE student SET Stud_Log = 1 WHERE Stud_ID = ?");
					$stmt->bind_param('s', $id);
					$stmt->execute();
					header('location: pass-update.php');
				}
				else
				{
					header('location: student/home.php'); // Redirect to home if user has already update their password
				}
			}
			else { return $this->err_msg; }
		}
		else { return $this->err_msg; }
	}

	public function lecturer_login($id, $password)
	{
		$query = "SELECT Lect_ID, Lect_Pass, Lect_Log FROM lecturer WHERE Lect_ID = '$id'";

		$stmt = $this->db->prepare($query);
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$result = $stmt->get_result();
	
		if($result->num_rows > 0)
		{
			$row = $result->fetch_assoc();
			if (password_verify($password, $row['Lect_Pass']))
			{
				$_SESSION['lect_id'] = $id;
				if ($row['Lect_Log'] == false)
				{
					$stmt = $this->db->prepare("UPDATE lecturer SET Lect_Log = 1 WHERE Lect_ID = '$id'");
					$stmt->bind_param('i', $id);
					$stmt->execute();
					header('location: pass-update.php');
				}
				else
				{
					header('location: lecturer/home.php');
				}
			}
			else { return $this->err_msg; }
		}
		else { return $this->err_msg; }
	}

	public function admin_login($id, $password)
	{
		$query = "SELECT Adm_ID, Adm_Pass, Adm_Log FROM admin WHERE Adm_ID = ?";

		$stmt = $this->db->prepare($query);
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$result = $stmt->get_result();
			
		if ($result->num_rows > 0)
		{
			$row = $result->fetch_assoc();			
			if (password_verify($password, $row['Adm_Pass']))
			{
				$_SESSION['session_id'] = $id;
				if ($row['Adm_Log'] == false)
				{
					$stmt = $this->db->prepare("UPDATE admin SET Adm_Log = 1 WHERE Adm_ID = '$id'");
					$stmt->bind_param('i', $id);
					$stmt->execute();
					header('location: pass-update.php');
				}
				else
				{
					header('location: admin/home.php');
				}
			}
			else { return $this->err_msg; }
		}
		else { return $this->err_msg; }
	}
}

?>