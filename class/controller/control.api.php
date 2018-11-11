<?php
/**
 * User: Leo
 * Date: 2018-11-11
 */

Class ApiController extends BaseController {
	
	private $offerService = null;
	
	/**
	 * init
	 * @return void
	 */
	public function init() {
		parent::init();
		$this->setOutType(BaseController::VIEW_OUTTYPE_JSON);
		$this->offerService = $this->sm->getOfferService();
	}

	/**
	 * getOffersAction
	 * @return void
	 */
	public function getOffersAction() {
		
		$sort = $this->router->getParameter('sort') ? : 0;
		$sort = intval($sort);
		
		$offers = $this->offerService->getOffers($sort);

		$outData = array('batch_id' => 0, 'offers' => $offers);
		$this->setOutData($outData);
	}
	
	/**
	 * importAction
	 * @return void
	 */
	public function importAction() {
				
		$count = $this->offerService->importData();

		$outData = array('count' => $count);
		$this->setOutData($outData);
	}

}
