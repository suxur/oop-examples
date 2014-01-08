<?php

	class Model
	{
		protected $_table;

		/** @var Database $db */
		protected $db;

		/**
		 * Class Constructor
		 * -----------------------------------------------------------
		 */
		public function __construct($db)
		{
			$this->db = $db;
		}

		/**
		 * Get
		 * -----------------------------------------------------------
		 * This method gets either a single row by the 'id' or 'get_by' method
		 * or multiple results by the 'id' or 'get_by' method.
		 *
		 * @param null   $id
		 * @param bool   $single
		 * @param string $columns
		 *
		 * @return array|bool
		 */
		public function get($id = null, $single = false, $columns = "*")
		{
			$data = array();

			$sql = "SELECT " . $columns . " FROM " . $this->_table;

			if ($id != null || $single == true) {
				if ($id != null) {
					$where = "WHERE id = " . $id . " LIMIT 1";
					$sql = $sql . " " . $where;
				} elseif ($single == true) {
					$limit = 'LIMIT 1';
					$sql = $sql . " " . $limit;
				}

				if ($this->db->queryf($sql, true)) {
					$data = $this->db->sarray;
				} else {
					$this->db->sendErrorAlert();
				}
			} else {
				if ($this->db->query($sql)) {
					while ($this->db->fetch(true)) {
						$data[] = $this->db->sarray;
					}
				} else {
					$this->db->sendErrorAlert();
				}
			}

			return $data;
		}

		/**
		 *  Save
		 * -----------------------------------------------------------
		 *
		 * @param      $data
		 * @param null $id
		 *
		 * @return bool
		 */
		public function save($data, $id = null)
		{
			$i       = 1;
			$values  = null;
			$columns = null;



			if ($id === null) {
				// Insert
				foreach ($data as $key => $value) {
					if ($i == count($data)) {
						$columns .= $key;
						$values .= "'" . trim(addslashes($value)) . "'";
					} else {
						$columns .= $key . ", ";
						$values .= "'" . trim(addslashes($value)) . "', ";
					}

					$i++;
				}

				$sql = "INSERT INTO " . $this->_table . " (" . $columns . ") VALUES(" . $values . ")";
			} else {
				// Update
				foreach ($data as $key => $value) {
					if ($i == count($data)) {
						$columns .= $key . " = '" . trim(addslashes($value)) . "'";
					} else {
						$columns .= $key . " = '" . trim(addslashes($value)) . "', ";
					}

					$i++;
				}

				$sql = "UPDATE " . $this->_table . " SET " . $columns . " WHERE id = " . $id;
			}

			if (!$this->db->query($sql)) {
				$this->db->sendErrorAlert();
				return false;
			}

			return true;
		}

		/**
		 * Delete
		 * -----------------------------------------------------------
		 * This method checks to make sure an 'id' is passed
		 * and then deletes the entry associated with the id.
		 *
		 * @param $id
		 *
		 * @return bool
		 */
		public function delete($id)
		{
			if (!$id) {
				return false;
			}

			$sql = "DELETE FROM " . $this->_table . " WHERE id = " . $id;

			if (!$this->db->query($sql)) {
				$this->db->sendErrorAlert();
				return false;
			}

			return true;
		}
	}

	/* End of file Model.class.php */
	/* Location: ./includes/autoload/Model.class.php */