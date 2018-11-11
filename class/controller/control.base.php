<?php
/**
 * User: Leo
 * Date: 2018-11-11
 */

class BaseController {
	
	const VIEW_OUTTYPE_REDIR = 1;
	const VIEW_OUTTYPE_PLAIN = 2;
	const VIEW_OUTTYPE_JSON = 3;
	const VIEW_OUTTYPE_LAYOUT = 4;
	
	protected $sm = null;
	protected $db = null;
	protected $router = null;
	
	protected $globals = array();

	protected $layout = 'global';
	protected $header = '';
	protected $outType = self::VIEW_OUTTYPE_LAYOUT;
	protected $outData = '';
	
	/**
	 * __construct
	 * @param ServiceManager $sm
	 * @param Router $router
	 * @return void
	 */
	public function __construct($sm, $router) {
		$this->sm = $sm;
		$this->router = $router;
		$this->db = $sm->getDbConnection();
	}
	
	/**
	 * init
	 * @return void
	 */
	public function init() {
	}

	/**
	 * addHeader
	 * @param string $str
	 * @return void
	 */
	public function addHeader($str) {

		if ( $this->header ) {
			$this->header .= "\n";
		}
		$this->header .= $str;

	}
	
	/**
	 * setLayout
	 * @param string $layout
	 * @return void
	 */
	public function setLayout($layout) {
		$this->layout = $layout;
	}
	
	/**
	 * setOutType
	 * @param const $outType
	 * @return void
	 */
	public function setOutType($outType) {
		$this->outType = $outType;
	}
	
	/**
	 * setOutData
	 * @param mixed $outData
	 * @return void
	 */
	public function setOutData($outData) {
		$this->outData = $outData;
	}
	
	/**
	 * setParams
	 * @param string $key
	 * @param mixed $val
	 * @return void
	 */
	public function setParams($key, $val) {
		$this->globals[$key] = $val;
	}
	
	/**
	 * getParams
	 * @param string $key
	 * @return mixed
	 */
	public function getParams($key = null) {
		if ( $key ) {
			if ( isset($this->globals[$key]) ) {
				return $this->globals[$key];
			}
		} else {
			return $this->globals;
		}
		return null;
	}
	
	/**
	 * addJsHeader
	 * @param string $str
	 * @return void
	 */
	public function addJsHeader($str) {
		if ( isset($this->globals['js_header']) ) {
			$this->globals['js_header'] .= "\r\n".$str;
		} else {
			$this->globals['js_header'] = $str;
		}
	}
	
	/**
	 * addCssHeader
	 * @param string $str
	 * @return void
	 */
	public function addCssHeader($str) {
		if ( isset($this->globals['css_header']) ) {
			$this->globals['css_header'] .= "\r\n".$str;
		} else {
			$this->globals['css_header'] = $str;
		}
	}
	
	/**
	 * renderTemplate
	 * @param string $tpl_file_name
	 * @param array $params
	 * @param boolean $collect
	 * @return void
	 */
	public function renderTemplate($tpl_file_name, $params = array(), $collect = true) {

		$tpl_file_name = '../tpl/'.$tpl_file_name.'.tpl';
		
		if ( ! file_exists($tpl_file_name) ) {
			throw new Exception('View not found: ' . $tpl_file_name);
			return false;
		}
		
		if ( $params ) {
			extract($params, EXTR_SKIP);
		}
		
		if ( $collect ) {
			ob_start();
			require($tpl_file_name);
			$output = ob_get_clean();
			return $output;
		} else {
			require($tpl_file_name);
		}
	}

	/**
	 * display
	 * @return void
	 */
	public function display() {

		if ( $this->outType == self::VIEW_OUTTYPE_REDIR ) {
		} else if ( $this->outType == self::VIEW_OUTTYPE_PLAIN ) {
			echo $this->outData;
		} else if ( $this->outType == self::VIEW_OUTTYPE_JSON ) {
			header('Content-Type: application/json');
			echo json_encode($this->outData);
		} else {
			$this->renderTemplate($this->layout, $this->getParams(), false);
		}

	}
	
}
