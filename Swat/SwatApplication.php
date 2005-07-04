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
	// {{{ global variable type constants

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

	// }}}
	// {{{ public properties

	/**
	 * A unique identifier for this application
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Whether this site is secure (behind SSL) or not
	 *
	 * @var boolean
	 */
	public $secure = false;

	/**
	 * Whether this site is a live or stage copy
	 *
	 * @var boolean
	 */
	public $live = false;

	// }}}
	// {{{ protected properties

	/**
	 * The number of elements of the raw URI that comprise the base
	 *
	 * This value changes between live and stage sites.
	 *
	 * @var integer
	 */
	protected $base_uri_length = 0;

	/**
	 * The raw URI of the current page request of this application
	 *
	 * @var string
	 */
	protected $uri = null;
	
	/**
	 * The base part of the raw URI of the current page request of this
	 * application
	 *
	 * Ends with a trailing '/' character.
	 *
	 * @var string
	 */
	protected $base_uri = null;
	
	/**
	 * The base value for all of this application's anchor hrefs
	 *
	 * @var string
	 */
	protected $base_href = null;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new Swat application
	 *
	 * @param String $id a unique identifier for this application.
	 */
	public function __construct($id)
	{
		$this->id = $id;
	}

	// }}}
	// {{{ public function getUri()

	/**
	 * Gets the raw URI of the current page request for this application
	 *
	 * @return string the raw URI of this page request.
	 */
	public function getUri()
	{
		if ($this->uri === null)
			$this->uri = $_SERVER['REQUEST_URI'];

		return $this->uri;
	}

	// }}}
	// {{{ public function getBaseUri()

	/**
	 * Gets the base part of the request URI
	 *
	 * The base of the request URI is returned with a trailing '/' character.
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

	// }}}
	// {{{ public function getBaseHref()

	/**
	 * Gets the base value for all application anchor hrefs
	 *
	 * @return string the base value for all application anchor hrefs.
	 */
	public function getBaseHref()
	{
		if ($this->base_href === null) {
			$uri_scheme = ($this->secure) ? 'https://' : 'http://';

			$server_name = ($this->live) ?
				$_SERVER['SERVER_NAME'] : $_SERVER['HTTP_HOST'];
				
			$this->base_href = $uri_scheme.$server_name.$this->getBaseUri();
		}

		return $this->base_href;
	}

	// }}}
	// {{{ public function relocate()

	/**
	 * Relocates to another URI
	 *
	 * Calls the PHP header() function to relocate this application to another
	 * URI. This function does not return and in fact calls the PHP exit()
	 * function just to be sure execution does not continue.
	 *
	 * @param string $uri the URI to relocate to.
	 */
	public function relocate($uri)
	{
		if (substr($uri, 0, 1) != '/' && strpos($uri, '://') === false)
			$uri = $this->getBaseHref().$uri;

		header('Location: '.$uri);
		exit();
	}

	// }}}
	// {{{ abstract public function init()

	/**
	 * Initializes this application
	 *
	 * Subclasses should implement all application level initialization here.
	 */
	abstract public function init();

	// }}}
	// {{{ abstract public function getPage()

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

	// }}}
	// {{{ public static function initVar()

	/**
	 * Initializes a variable
	 *
	 * Static convenience method to initialize a local variable with a value 
	 * from one of the PHP global arrays.
	 *
	 * @param $name string the name of the variable to lookup.
	 *
	 * @param $types integer a bitwise combination of SwatApplication::VAR_*
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

	// }}}
}

?>
