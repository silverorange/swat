<?php

require_once 'Swat/SwatObject.php';

/**
 * Base class for a web application
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatApplication extends SwatObject
{
	/**
	 * A unique identifier for this application
	 *
	 * @var string
	 */
	public $id;

	/**
	 * The number of elements of the raw URI that comprise the base
	 *
	 * This value changes between live and stage sites.
	 *
	 * @var int
	 */
	protected $base_uri_length = 0;

	/**
	 * The raw URI of this page request
	 *
	 * @var string
	 */
	protected $uri = null;
	
	/**
	 * The base part of the raw URI of this page request
	 *
	 * Ends with a trailing '/' character.
	 *
	 * @var string
	 */
	protected $base_uri = null;
	
	/**
	 * The base value for all application anchor hrefs
	 *
	 * @var string
	 */
	protected $base_href = null;
	
	/**
	 * Creates a new Swat application
	 *
	 * @param String $id a unique identifier for this application.
	 */
	public function __construct($id)
	{
		$this->id = $id;
	}

	/**
	 * Gets the raw URI of this page request
	 *
	 * @return string the raw URI of this page request.
	 */
	public function getUri()
	{
		if ($this->uri === null)
			$this->uri = $_SERVER['REQUEST_URI'];

		return $this->uri;
	}

	/**
	 * Gets the base part of the request URI
	 *
	 * The base or the request URI is returned with a trailing '/' character.
	 *
	 * @return string the base part of the request URI.
	 *
	 * @see SwatApplication::getBaseHref()
	 */
	public function getBaseUri()
	{
		if ($this->base_uri === null) {
			$uri_array = explode('/', $this->getUri());
			$this->base_uri = implode('/',
				array_slice($uri_array, 0, $this->base_uri_length + 1)).'/';
		}

		return $this->base_uri;
	}

	/**
	 * Gets the base value for all application anchor hrefs
	 *
	 * @return string the base value for all application anchor hrefs.
	 */
	public function getBaseHref()
	{
		/*
		 * TODO: Once we have a SITE_LIVE equivalent, we should use HTTP_HOST
		 *       on stage and SERVER_NAME on live.
		 * TODO: This also needs to be updated to support https.
		 */
		if ($this->base_href === null)
			$this->base_href =
				'http://'.$_SERVER['HTTP_HOST'].$this->getBaseUri();

		return $this->base_href;
	}

	/**
	 * Relocates to another URL
	 *
	 * Calls the PHP header() function to relocate this application to another
	 * URL. This function does not return and in fact calls the PHP exit()
	 * function just to be sure execution does not continue.
	 *
	 * @param string $url the URL to relocate to.
	 */
	public function relocate($url)
	{
		if (substr($url, 0, 1) != '/' && strpos($url, '://') === false)
			$url = $this->getBaseHref().$url;

		header('Location: '.$url);
		exit();
	}

	/**
	 * Initializes this application
	 *
	 * Subclasses should implement all application level initialization here.
	 */
	abstract public function init();

	/**
	 * Gets the page object
	 *
	 * Subclasses should implement logic here to decide which page sub-class to
	 * instantiate, then return a {@link SwatPage} descenedant.
	 *
	 * @return SwatPage A sub-class of {@link SwatPage} is returned.
	 *
	 * @see SwatPage
	 */
	abstract public function getPage();

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
	 * Initializes a variable
	 *
	 * Static convenience method to initialize a local variable with a value 
	 * from one of the PHP global arrays.
	 *
	 * @param $name string the name of the variable to lookup.
	 *
	 * @param $types int a bitwise combination of SwatApplication::VAR_*
	 *                    constants.
	 *
	 * @param $default mixed the value to return if variable is not found in
	 *                        the super-global arrays.
	 *
	 * @return mixed the value of the variable.
	 */
	public static function initVar($name, $default = null, $types = 0)
	{
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
