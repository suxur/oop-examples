<?php

	class TierII extends Model
	{
		protected $_table = "tier_2_reports";

		/**
		 * Class Constructor
		 * -----------------------------------------------------------
		 * this injects database dependency
		 *
		 * @internal param $db
		 */
		public function __construct($db)
		{
			parent::__construct($db);
		}

		/**
		 * Get Tier II By Facility 
		 * -----------------------------------------------------------
		 * this method returns tier II information related to a
		 * facility using the facility id.
		 *
         * @param $id
         * @param string $columns
         * @return array|null
         */
		public function getTierIIByFacility($id, $columns = "*")
		{
			$data = array();

			$sql = "SELECT " . $columns . " FROM " . $this->_table . " AS t INNER JOIN facilities AS f ON f.id = t.fid WHERE t.id = " . $id;

			if ($this->db->queryf($sql)) {
				if ($this->db->num_rows() == 1) {
					$data = $this->db->sarray;
				}
			} else {
				$this->db->sendErrorAlert();
			}

			return $data;
		}
	}

	/* End of file TierII.class.php */
	/* Location: ./includes/autoload/TierII.class.php */