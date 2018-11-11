<?php
/**
 * User: Leo
 * Date: 2018-11-11
 */

class ServiceManager {
	
	private $db = null;
	private $offer = null;
	
	/**
	 * __construct
	 * @param PdoConnection $db
	 * @return void
	 */
	public function __construct($db) {
		$this->db = $db;
	}
	
	/**
	 * getDbConnection
	 * @return PdoConnection
	 */
	public function getDbConnection() {
		return $this->db;
	}
	
	/**
	 * getOfferService
	 * @return Offer
	 */
	public function getOfferService() {
		if ( ! isset($this->offer) ) {
			$this->offer = new Offer($this->db);
		}
		return $this->offer;
	}
	
}
