<?php

	class Company
	{
		/** @var Database $db */
		protected $db;

		/** @var  Auth $userAuth */
		protected $auth;

		private $_table = "companies";
		private $_usersTable = "users";
		private $_ordersTable = "orders";
		private $_loginsTable = "companies_logins";

		/**
		 * Constructor
		 * -------------------------------------------------------------
		 *
		 * @param $db
		 * @param $auth
		 */
		public function __construct($db, $auth)
		{
			$this->db   = $db;
			$this->auth = $auth;
		}

		/**
		 * Create Company
		 * -------------------------------------------------------------
		 * this method will create a new company if a company
		 * does not already exist with the same information
		 *
		 * @param $data
		 * @return int
		 */
		public function createCompany($data)
		{
			// more strict compare for duplicates
			if (($profile = $this->compareCompany($data)) !== false) {
				return $profile['id'];
			}

			$cid     = 0;
			$columns = "";
			$values  = "";

			foreach ($data as $key => $value) {
				$columns .= $key . ", ";
				$values .= "'" . addslashes($value) . "', ";
			}

			$sql = "INSERT INTO " . $this->_table . "(" . $columns . " createdDate, createdBy) VALUES(" . $values . time() . ", " . $this->auth->id . ")";

			if ($this->db->query($sql)) {
				$cid = $this->db->insert_id();
			} else {
				$this->db->showError();
			}

			return $cid;
		}

		/**
		 * Update Company
		 * -------------------------------------------------------------
		 * this method updates a pre-existing company
		 * with new data
		 *
		 * @param $id
		 * @param $data
		 * @return bool
		 */
		public function updateCompany($id, $data)
		{
			$values = array();

			foreach ($data as $key => $value) {
				$values[] = $key . " = '" . addslashes($value) . "'";
			}

			$sql = "UPDATE " . $this->_table . " SET " . implode(", ", $values) . " WHERE id = " . $id;

			if (!$this->db->query($sql)) {
				$this->db->showError();
				return false;
			}

			return true;
		}

		/**
		 * Compare Company
		 * -------------------------------------------------------------
		 * this method checks for duplicate companies
		 *
		 * @param $data
		 * @return bool|null
		 */
		private function compareCompany($data)
		{
			$values = array();

			foreach ($data as $key => $value) {
				if ($value != "" && $key != "address" && $key != "county" && $key != "altPhone") {
					$values[] = $key . " LIKE '" . $value . "'";
				}
			}

			$sql = "SELECT id FROM " . $this->_table . " WHERE " . implode(" AND ", $values);

			if ($this->db->queryf($sql)) {
				if ($this->db->num_rows() > 0) {
					$this->db->sarray = stripRecur($this->db->sarray);
					return $this->db->sarray;
				} else {
					return false;
				}
			}

			$this->db->kill();

			return false;
		}

		/**
		 * Get Company
		 * -------------------------------------------------------------
		 *
		 * @param $id
		 * @param string $columns
		 * @return array|null
		 */
		public function getCompany($id, $columns = "*")
		{
			$company = array();

			$sql = "SELECT " . $columns . " FROM " . $this->_table . " WHERE id = " . $id;

			if ($this->db->queryf($sql) && $this->db->num_rows() == 1) {
				$company = $this->db->sarray;
			}

			return $company;
		}

		/**
		 * Get Companies
		 * -------------------------------------------------------------
		 *
		 * @param string $columns
		 * @return array
		 */
		public function getCompanies($columns = "*")
		{
			$companies = array();

			$sql = "SELECT " . $columns . " FROM " . $this->_table;

			if ($this->db->query($sql) && $this->db->num_rows() > 0) {
				while ($this->db->fetch(true)) {
					$companies[] = $this->db->sarray;
				}
			}

			return $companies;
		}

		/**
		 * Get Company's Users
		 * -------------------------------------------------------------
		 *
		 * @param $id
		 * @param string $columns
		 * @return array
		 */
		public function getCompanyUsers($id, $columns = "*")
		{
			$users = array();

			$sql = "SELECT " . $columns . " FROM " . $this->_usersTable . " WHERE cid = " . $id;

			if ($this->db->query($sql) && $this->db->num_rows() > 0) {
				while ($this->db->fetch(true)) {
					$users[] = $this->db->sarray;
				}
			}

			return $users;
		}

		/**
		 * Get Company's Orders
		 * -------------------------------------------------------------
		 *
		 * @param $id
		 * @param string $columns
		 * @return array
		 */
		public function getCompanyOrders($id, $columns = "*")
		{
			$orders = array();

			$sql = "SELECT " . $columns . " FROM " . $this->_ordersTable . " WHERE cid = " . $id;

			if ($this->db->query($sql) && $this->db->num_rows() > 0) {
				while ($this->db->fetch(true)) {
					$orders[] = $this->db->sarray;
				}
			}

			return $orders;
		}

		/**
		 * Create Company Tier II Login
		 * -------------------------------------------------------------
		 *
		 * @param $cid
		 * @param $data
		 *
		 * @return int
		 */
		public function createLogin($cid, $data)
		{
			$columns = "";
			$values  = "";

			foreach ($data as $key => $value) {
				$columns .= $key . ", ";
				$values .= "'" . addslashes($value) . "', ";
			}

			$sql = "INSERT INTO " . $this->_loginsTable . "(" . $columns . " cid) VALUES(" . $values . $cid .  ")";

			if ($this->db->query($sql)) {
				$cid = $this->db->insert_id();
			} else {
				$this->db->showError();
			}

			return $cid;
		}

		/**
		 * Get Company's Tier II Logins
		 * -------------------------------------------------------------
		 *
		 * @param        $id
		 * @param string $columns
		 *
		 * @return array
		 */
		public function getCompanyLogins($id, $columns = "*")
		{
			$logins = array();

			$sql = "SELECT " . $columns . " FROM " . $this->_loginsTable . " WHERE cid = " . $id . " ORDER BY state ASC";

			if ($this->db->query($sql) && $this->db->num_rows() > 0) {
				while ($this->db->fetch(true)) {
					$logins[] = $this->db->sarray;
				}
			}

			if (!empty($logins)) {
				foreach ($logins as $key => $value) {
					if (empty($value['t2id'])) {
						$logins['set1'][$key]['state'] = $value['state'];
						$logins['set1'][$key]['username'] = $value['username'];
						$logins['set1'][$key]['password'] = $value['password'];
					} else {
						$logins['set2'][$key]['state'] = $value['state'];
						$logins['set2'][$key]['t2id'] = $value['t2id'];
					}
				}
			}

			return $logins;
		}
	}

	/* End of file Company.class.php */
	/* Location: ./includes/autoload/Company.class.php */