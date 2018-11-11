<?php
/**
 * User: Leo
 * Date: 2018-11-11
 */

Class Offer {
	
	private $db = null;
	
	/**
	 * __construct
	 * @param PdoConnection $db
	 * @return void
	 */
	public function __construct($db) {
		$this->db = $db;
	}
	
	/**
	 * getOffers
	 * @param int $sort
	 * @return array
	 */
	public function getOffers($sort = 0) {
		
		$sql = "SELECT * FROM `tb_offer`";
		switch ( $sort ) {
			case 1:
				$sql .= " ORDER BY `name` ASC";
				break;
			case 2:
				$sql .= " ORDER BY `name` DESC";
				break;
			case 3:
				$sql .= " ORDER BY `cash_back` ASC";
				break;
			case 4:
				$sql .= " ORDER BY `cash_back` DESC";
				break;
			default:
				break;
		}
		return $this->db->fetchAll($sql);
	}
	
	/**
	 * importData import data into tb_offer from c51.json
	 * @return int
	 */
	public function importData() {
		
		$json = file_get_contents('../doc/c51.json');
		$offers = json_decode($json, true);
		
		if ( $offers && $offers['offers'] ) {
			
			$sql = "TRUNCATE TABLE `tb_offer`";
			$this->db->query($sql);
		
			foreach ( $offers['offers'] as $row ) {
				
				$offer_id = $row['offer_id'];
				$name = $row['name'];
				$image_url = $row['image_url'];
				$cash_back = $row['cash_back'];
	
				$offer_id = intval($offer_id);
				$name = $this->db->quote($name);
				$image_url = $this->db->quote($image_url);
				$cash_back = $this->db->quote($cash_back);
				$sql = "INSERT INTO `tb_offer`
						( `offer_id`, `name`, `image_url`, `cash_back` )
						VALUES( $offer_id, $name, $image_url, $cash_back )";
				$this->db->query($sql);
			
			}
			
			return count($offers['offers']);
		
		}
		
		return 0;
	}
		
}
