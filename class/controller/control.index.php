<?php
/**
 * User: Leo
 * Date: 2018-11-11
 */

Class IndexController extends BaseController {
	
	private $offerService = null;
	
	/**
	 * init
	 * @return void
	 */
	public function init() {
		parent::init();
		$this->setOutType(BaseController::VIEW_OUTTYPE_LAYOUT);
		$this->offerService = $this->sm->getOfferService();
	}

	/**
	 * indexAction
	 * @return void
	 */
	public function indexAction() {
		$params = array();
		$params['offers'] = $this->offerService->getOffers();
		$tempData = $this->renderTemplate('home', $params);
		$this->setOutData($tempData);
	}

}
