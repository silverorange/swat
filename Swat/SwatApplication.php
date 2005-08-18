<?php

require_once 'Swat/SwatObject.php';
require_once 'Swat/SwatLayout.php';

/**
 * Base class for a web application
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatApplication extends SwatObject
{
	// {{{ global variable type constants

	const VAR_POST    = 1;
	const VAR_GET     = 2;
	const VAR_REQUEST = 4;
	const VAR_COOKIE  = 8;
	const VAR_SERVER  = 16;
	const VAR_SESSION = 32;
	const VAR_FILES   = 64;
	const VAR_ENV     = 128;

	// }}}
	// {{{ public properties

	/**
	 * A unique identifier for this application
	 *
	 * @var string
	 */
	public $id;

	/**
	 * The current page of this application
	 *
	 * @var SwatPage
	 */
	protected $page = null;
	
	// }}}
	// {{{ protected properties

	/**
	 * The base value for all of this application's anchor hrefs
	 *
	 * @var string
	 */
	protected $base_href = null;

	/**
	 * The uri of the current page request
	 *
	 * @var string
	 */
	protected $uri = null;

	// }}}
	// {{{ private properties

	/**
	 * Whether init() has been run on this->page
	 *
	 * @var boolean
	 */
	private $page_initialized = false;

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
	// {{{ public function init()

	/**
	 * Initializes this application
	 *
	 * Subclasses should implement all application level initialization here
	 * and call whichever SwatApplication::init* methods are necessary.
	 */
	public function init()
	{
		$this->initBaseHref();
		$this->initPage();
	}

	// }}}
	// {{{ protected function initBaseHref()

	/**
	 * Initializes the base href
	 */
	protected function initBaseHref($prefix_length = 0, $secure = false)
	{
		$this->uri = $_SERVER['REQUEST_URI'];

		$uri_array = explode('/', $this->uri);

		$base_uri = implode('/',
			array_slice($uri_array, 0, $prefix_length + 1)).'/';

		$protocol = ($secure) ? 'https://' : 'http://';

		$this->base_href = $protocol.$this->getServerName().$base_uri;
	}

	// }}}
	// {{{ protected function initPage()

	/**
	 * Initializes the page
	 */
	protected function initPage()
	{
		if ($this->page === null)
			$this->page = $this->resolvePage();

		$this->page->init();

		$this->page_initialized = true;
	}

	// }}}
	// {{{ public function getPage()

	/**
	 * Gets the current page
	 */
	public function getPage()
	{
		return $this->page;
	}

	// }}}
	// {{{ public function setPage()

	/**
	 * Sets the current page
	 *
	 * If a page object is provided, the current page is set to the provided
	 * page replacing any previous page. This can be useful to process one page
	 * then load another page to process and display.
	 *
	 * If no page object is provided a default page is chosen based on
	 * application specific code. Subclasses should implement logic here to
	 * decide which page sub-class to instantiate.
	 *
	 * @param SwatPage the page to load as a replacement of the current page.
	 *
	 * @throws SwatException
	 *
	 * @see SwatPage
	 */
	public function setPage($page)
	{
		if ($page instanceof SwatPage)
			$this->page = $page;
		else
			throw new SwatException(__CLASS__.': provided page must be '.
				'an instance of SwatPage');

		if ($this->page_initialized)
			$this->page->init();
	}

	// }}}
	// {{{ protected function resolvePage()

	/**
	 * Resolves a page for this application
	 *
	 * This method is called if no {@link SwatPage} is provided to the
	 * {@link SwatApplication::setPage()} method.
	 */
	protected function resolvePage()
	{
		return new SwatPage($this);
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
		return $this->base_href;
	}

	// }}}
	// {{{ public function getUri()

	/**
	 * Gets the URI of the current page request
	 *
	 * @return string the URI of the current page request.
	 */
	public function getUri()
	{
		return $this->uri;
	}

	// }}}
	// {{{ protected function getServerName()

	/**
	 * Gets the servername
	 *
	 * @return string the servername
	 */
	protected function getServerName()
	{
		return $_SERVER['HTTP_HOST'];
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
	// {{{ public static function initVar()

	/**
	 * Initializes a variable
	 *
	 * Static convenience method to initialize a local variable with a value 
	 * from one of the PHP global arrays.
	 *
	 * @param $name string the name of the variable to lookup.
	 *
	 * @param $types integer a bitwise combination of self::VAR_*
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
			$types = self::VAR_POST | self::VAR_GET;

		if (($types & self::VAR_POST) != 0
			&& isset($_POST[$name]))
				$var = $_POST[$name];

		elseif (($types & self::VAR_GET) != 0
			&& isset($_GET[$name]))
				$var = $_GET[$name];

		elseif (($types & self::VAR_REQUEST) != 0
			&& isset($_REQUEST[$var]))
				$var = $_REQUEST[$var];
				
		elseif (($types & self::VAR_COOKIE) != 0
			&& isset($_COOKIE[$var]))
				$var = $_COOKIE[$var];
				
		elseif (($types & self::VAR_SERVER) != 0
			&& isset($_SERVER[$var]))
				$var = $_SERVER[$var];
				
		elseif (($types & self::VAR_SESSION) != 0
			&& isset($_SESSION[$var]))
				$var = $_SESSION[$var];
				
		elseif (($types & self::VAR_FILES) != 0
			&& isset($_FILES[$var]))
				$var = $_FILES[$var];
				
		elseif (($types & self::VAR_ENV != 0)
			&& isset($_ENV[$var]))
				$var = $_ENV[$var];

		return $var;
	}

	// }}}
}

?>
