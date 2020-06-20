<?php
class DB
{
	private $servername = 'localhost';
	private $username   = 'root';
	private $password   = '';
	private $dbname     = 'project_db';

	protected $db;

	public function __construct()
	{
		if (!isset($this->db))
		{
			$this->db = new mysqli($this->servername, $this->username, $this->password, $this->dbname);

			if (!$this->db)
			{
				echo 'Cannot connect to database.';
				exit;
			}
		}
		return $this->db;
	}
}

?>