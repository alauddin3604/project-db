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
		$id = strtoupper($id);
		$query = 'SELECT student_id, student_password, log_status FROM students WHERE student_id = ?';
	
		$stmt = $this->db->prepare($query);
		$stmt->bind_param('s', $id);
		$stmt->execute();
		$result = $stmt->get_result();

		if ($result->num_rows > 0)
		{
			$row = $result->fetch_assoc();
			if (password_verify($password, $row['student_password']))
			{
				$_SESSION['student_id'] = $id;
				if ($row['log_status'] == false)
				{
					$stmt = $this->db->prepare('UPDATE students SET log_status = 1 WHERE student_id = ?');
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
		$query = 'SELECT lecturer_id, lecturer_password, log_status FROM lecturers WHERE lecturer_id = ?';

		$stmt = $this->db->prepare($query);
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$result = $stmt->get_result();
	
		if($result->num_rows > 0)
		{
			$row = $result->fetch_assoc();
			if (password_verify($password, $row['lecturer_password']))
			{
				$_SESSION['lecturer_id'] = $id;
				if ($row['log_status'] == false)
				{
					$stmt = $this->db->prepare('UPDATE lecturers SET log_status = 1 WHERE lecturer_id = ?');
					$stmt->bind_param('i', $id);
					$stmt->execute();
					header('location: pass-update.php');
				}
				else
				{
					header('location: lecturer/home.php'); // Redirect to home if user has already update their password
				}
			}
			else { return $this->err_msg; }
		}
		else { return $this->err_msg; }
	}

	public function admin_login($id, $password)
	{
		$query = 'SELECT admin_id, admin_password, log_status FROM admins WHERE admin_id=?';

		$stmt = $this->db->prepare($query);
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$result = $stmt->get_result();
			
		if ($result->num_rows > 0)
		{
			$row = $result->fetch_assoc();			
			if (password_verify($password, $row['admin_password']))
			{
				$_SESSION['admin_id'] = $id;
				if ($row['log_status'] == false)
				{
					$stmt = $this->db->prepare('UPDATE admins SET log_status=1 WHERE admin_id=?');
					$stmt->bind_param('i', $id);
					$stmt->execute();
					header('location: pass-update.php');
				}
				else
				{
					header('location: admin/home.php'); // Redirect to home if user has already update their password
				}
			}
			else { return $this->err_msg; }
		}
		else { return $this->err_msg; }
	}
}

?>