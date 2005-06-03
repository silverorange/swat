<?php

require_once('Swat/SwatObject.php');

/**
 * Base class for a web application
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
abstract class SwatApplication extends SwatObject {

	/**
	 * Application id
	 *
	 * @var string
	 */
	public $id;

	/**
	 * URI (read-only)
	 *
	 * The URI of the current request.
	 * Set by {@link SwatApplication::initUriVars}.
	 *
	 * @var string
	 */
	public $uri;

	/**
	 * Base URI (read-only)
	 *
	 * The URI part of the basehref. 
	 * Set by {@link SwatApplication::initUriVars}.
	 *
	 * @var string
	 */
	public $baseuri;

	/**
	 * Base-Href (read-only)
	 *
	 * Set by SwatApplication::initUriVars().
	 *
	 * @var string
	 */
	public $basehref;

	/**
	 * @param String $id Unique id of the application.
	 */
	function __construct($id) {
		$this->id = $id;
	}

	protected function initUriVars($prefix_length = 0) {
		$this->uri = $_SERVER['REQUEST_URI'];

		$uri_array = explode('/', $this->uri);
		$this->baseuri = implode('/',
			array_slice($uri_array, 0, $prefix_length + 1)).'/';

		// TODO: Once we have a SITE_LIVE equivalent, we should use HTTP_HOST
		//       on stage and SERVER_NAME on live.
		$this->basehref = 'http://'.$_SERVER['HTTP_HOST'].$this->baseuri;
	}

	/**
	 * Initialize the application
	 *
	 * Subclasses should implement all application level initialization here.
	 */
	abstract public function init();

	/**
	 * Get the page object
	 *
	 * Subclasses should implement logic here to decide which page sub-class to
	 * instantiate, then return a {@link SwatPage} descenedant.
	 *
	 * @return SwatPage A sub-class of {@link SwatPage} is returned.
	 */
	abstract public function getPage();

	/**
	 * Relocate
	 *
	 * Relocate to another URL. This function does not return.
	 * @param string $url The URL to relocate to.
	 */
	function relocate($url) {
		if (substr($url, 0, 1) != '/' && strpos($url, '://') === FALSE)
			$url = $this->basehref.$url;

		header('Location: '.$url);
		exit();
	}


	const VAR_POST    = 1;
	const VAR_GET     = 2;
	/*
	const VAR_REQUEST = 4;
	const VAR_COOKIE  = 8;
	const VAR_SERVER  = 16;
	const VAR_SESSION = 32;
	const VAR_FILES   = 64;
	const VAR_ENV     = 128;
	*/
	
	/**
	 * Initialize a variable
	 *
	 * Static convenience method to initialize a local variable with a value 
	 * from one of the PHP global arrays.
	 *
	 * @param $name string The name of the variable to lookup.
	 *
	 * @param $types int Bitwise combination of SwatApplication::VAR_*
	 *        constansts.
	 *
	 * @param $default mixed Value to return if variable is not found in
	 *        the global arrays.
	 *
	 * @return mixed The value of the variable.
	 */
	public static function initVar($name, $default = null, $types = 0) {
		
		$var = $default;
		
		if ($types == 0)
			$types = SwatApplication::VAR_POST | SwatApplication::VAR_GET;
	
		if (($types & SwatApplication::VAR_POST != 0)
			&& isset($_POST[$name]))
				$var = $_POST[$name];

		elseif (($types & SwatApplication::VAR_GET != 0) 
			&& isset($_GET[$name]))
				$var = $_GET[$name];

		/*
		elseif (($types & SwatApplication::VAR_REQUEST)
			&& isset($_REQUEST[$var]))
				$var = $_REQUEST[$var];
				
		elseif (($types & SwatApplication::VAR_COOKIE)
			&& isset($_COOKIE[$var]))
				$var = $_COOKIE[$var];
				
		elseif (($types & SwatApplication::VAR_SERVER)
			&& isset($_SERVER[$var]))
				$var = $_SERVER[$var];
				
		elseif (($types & SwatApplication::VAR_SESSION)
			&& isset($_SESSION[$var]))
				$var = $_SESSION[$var];
				
		elseif (($types & SwatApplication::VAR_FILES)
			&& isset($_FILES[$var]))
				$var = $_FILES[$var];
				
		elseif (($types & SwatApplication::VAR_ENV)
			&& isset($_ENV[$var]))
				$var = $_ENV[$var];
		*/
		
		return $var;
	}

}

?>
