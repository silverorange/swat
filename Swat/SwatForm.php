<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/exceptions/SwatException.php';
require_once 'Swat/exceptions/SwatInvalidCharacterEncodingException.php';
require_once 'Swat/exceptions/SwatInvalidTypeException.php';
require_once 'Swat/SwatDisplayableContainer.php';
require_once 'Swat/SwatMessageDisplay.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatString.php';
require_once 'Swat/SwatYUI.php';

/**
 * A form widget which can contain other widgets
 *
 * SwatForms are very useful for processing widgets. For most widgets, if they
 * are not inside a SwatForm they will not be able to be processed properly.
 *
 * With Swat's default style, SwatForm widgets have no visible margins, padding
 * or borders.
 *
 * @package   Swat
 * @copyright 2004-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatForm extends SwatDisplayableContainer
{
	// {{{ constants

	const METHOD_POST = 'post';
	const METHOD_GET  = 'get';

	const PROCESS_FIELD = '_swat_form_process';
	const HIDDEN_FIELD = '_swat_form_hidden_fields';
	const AUTHENTICATION_TOKEN_FIELD = '_swat_form_authentication_token';
	const SERIALIZED_PREFIX = '_swat_form_serialized_';

	const ENCODING_FIELD        = '_swat_form_character_encoding';
	const ENCODING_ENTITY_VALUE = '&auml;&trade;&reg;';
	const ENCODING_UTF8_VALUE   = "\xc3\xa4\xe2\x84\xa2\xc2\xae";
	const ENCODING_8BIT_VALUE   = "\xe4\x99\xae";

	// }}}
	// {{{ public properties

	/**
	 * The action attribute of the HTML form tag
	 *
	 * @var string
	 */
	public $action = '#';

	/**
	 * Encoding type of the form
	 *
	 * Used for multipart forms for file uploads.
	 *
	 * @var string
	 */
	public $encoding_type = null;

	/**
	 * A list of character set encodings the server can process
	 *
	 * The client will use this list to pick an appropriate character set for
	 * form data sent to the server.
	 *
	 * The list is formatted according to the HTML form element's
	 * {@link http://www.w3.org/TR/html401/interact/forms.html#adef-accept-charset accept-charset}
	 * attribute syntax.
	 *
	 * By default, the only accepted character set is UTF-8.
	 *
	 * @var string
	 */
	public $accept_charset = 'utf-8';

	/**
	 * Whether or not to automatically focus the a default SwatControl when
	 * this form loads
	 *
	 * Autofocusing is good for applications or pages that are keyboard driven
	 * -- such as data entry forms -- as it immediatly places the focus on the
	 * form.
	 *
	 * @var boolean
	 */
	public $autofocus = false;

	/**
	 * A reference to the default control to focus when the form loads
	 *
	 * If this is not set then it defaults to the first SwatControl
	 * in the form.
	 *
	 * @var SwatControl
	 */
	public $default_focused_control = null;

	/**
	 * A reference to the button that was clicked to submit the form,
	 * or null if the button is not set.
	 *
	 * You usually do not want to explicitly set this in your code because
	 * other parts of Swat set this property automatically.
	 *
	 * @var SwatButton
	 */
	public $button = null;

	/**
	 * Whether or not to use auto-complete for elements in this form
	 *
	 * Autocomplete is a browser-specific extension that is enabled by default.
	 * If the browser is autocompleting fields it shouldn't (e.g. login info
	 * on a non-login page) the behavior may be turned off by setting this to
	 * false. In Swat, this currently only solves the problem for users with
	 * JavaScript.
	 *
	 * @var boolean
	 */
	public $autocomplete = true;

	/**
	 * The default value to use for signature salt
	 *
	 * If this value is not null, all newly instantiated forms will call the
	 * {@link SwatForm::setSalt()} method with this value as the <i>$salt</i>
	 * parameter.
	 *
	 * @var string
	 */
	public static $default_salt = null;

	// }}}
	// {{{ protected properties

	/**
	 * Hidden form fields
	 *
	 * An array of the form:
	 *    name => value
	 * where all the values are passed as hidden fields in this form.
	 *
	 * @var array
	 *
	 * @see SwatForm::addHiddenField()
	 * @see SwatForm::getHiddenField()
	 */
	protected $hidden_fields = array();

	/**
	 * The value to use when salting serialized data signatures
	 *
	 * @var string
	 */
	protected $salt = null;

	/**
	 * The default encoding to assume for 8-bit content submitted from clients
	 *
	 * Form data is automatically converted from this encoding to UTF-8 when the
	 * form is processed.
	 *
	 * By default, 'windows-1252' is used. This also handles ISO 8859-1 content
	 * as Windows-1252 is a superset of ISO 8859-1.
	 *
	 * @var string
	 *
	 * @see SwatForm::setDefault8BitEncoding()
	 * @see SwatForm::set8BitEncoding()
	 */
	protected static $default_8bit_encoding = 'windows-1252';

	/**
	 * The encoding to assume for 8-bit content submitted from clients
	 *
	 * Form data is automatically converted from this encoding to UTF-8 when the
	 * form is processed.
	 *
	 * @var string
	 *
	 * @see SwatForm::set8BitEncoding()
	 */
	protected $_8bit_encoding = null;

	/**
	 * The default URI at which a Connection: close header may be sent to the
	 * browser
	 *
	 * The Connection: close header may be used to work around
	 * {@link https://bugs.webkit.org/show_bug.cgi?id=5760 Webkit bug #5760}.
	 *
	 * By default, no Connection: close URI is specified. In this case, no
	 * workaround is attempted when SwatForm forms are submitted.
	 *
	 * @var string
	 *
	 * @see SwatForm::setDefaultConnectionCloseUri()
	 * @see SwatForm::setConnectionCloseUri()
	 */
	protected static $default_connection_close_uri = null;

	/**
	 * URI at which a Connection: close header may be sent to the browser
	 *
	 * This may be used to work around
	 * {@link https://bugs.webkit.org/show_bug.cgi?id=5760 Webkit bug #5760}.
	 *
	 * @var string
	 *
	 * @see SwatForm::setConnectionCloseUri()
	 */
	protected $connection_close_uri = null;

	// }}}
	// {{{ private properties

	/**
	 * The method to use for this form
	 *
	 * Is one of SwatForm::METHOD_* constants.
	 *
	 * @var string
	 */
	private $method = self::METHOD_POST;

	/**
	 * The token value used to prevent cross-site request forgeries
	 *
	 * If this value is not null, all submitted forms may be checked to see if
	 * they are authenticated with this token value.
	 *
	 * @var string
	 *
	 * @see SwatForm::setAuthenticationToken()
	 * @see SwatForm::clearAuthenticationToken()
	 * @see SwatForm::isAuthenticated()
	 */
	private static $authentication_token = null;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new form
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		if (self::$default_salt !== null) {
			$this->setSalt(self::$default_salt);
		}

		if (self::$default_8bit_encoding !== null) {
			$this->set8BitEncoding(self::$default_8bit_encoding);
		}

		if (self::$default_connection_close_uri !== null) {
			$this->setConnectionCloseUri(self::$default_connection_close_uri);
		}

		$this->requires_id = true;

		$this->addJavaScript('packages/swat/javascript/swat-form.js');
	}

	// }}}
	// {{{ public function setMethod()

	/**
	 * Sets the HTTP method this form uses to send data
	 *
	 * @param string $method a method constant. Must be one of
	 *                        SwatForm::METHOD_* otherwise an error is thrown.
	 *
	 * @throws SwatException
	 */
	public function setMethod($method)
	{
		$valid_methods = array(self::METHOD_POST, self::METHOD_GET);

		if (!in_array($method, $valid_methods))
			throw new SwatException("‘{$method}’ is not a valid form method.");

		$this->method = $method;
	}

	// }}}
	// {{{ public function getMethod()

	/**
	 * Gets the HTTP method this form uses to send data
	 *
	 * @return string a method constant.
	 */
	public function getMethod()
	{
		return $this->method;
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this form
	 *
	 * Outputs the HTML form tag and calls the display() method on each child
	 * widget of this form. Then, after all the child widgets are displayed,
	 * displays all hidden fields.
	 *
	 * This method also adds a hidden field called 'process' that is given
	 * the unique identifier of this form as a value.
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		SwatWidget::display();

		$this->addHiddenField(self::PROCESS_FIELD, $this->id);

		$form_tag = $this->getFormTag();

		$form_tag->open();
		$this->displayChildren();
		$this->displayHiddenFields();
		$form_tag->close();

		if ($this->connection_close_uri != '') {
			$yui = new SwatYUI(array('event'));
			$this->html_head_entry_set->addEntrySet(
				$yui->getHtmlHeadEntrySet());
		}

		Swat::displayInlineJavaScript($this->getInlineJavaScript());
	}

	// }}}
	// {{{ public function process()

	/**
	 * Processes this form
	 *
	 * If this form has been submitted then calls the process() method on
	 * each child widget. Then processes hidden form fields.
	 *
	 * This form is only marked as processed if it was submitted by the user.
	 *
	 * @return true if this form was actually submitted, false otherwise.
	 *
	 * @see SwatContainer::process()
	 */
	public function process()
	{
		$this->processed = $this->isSubmitted();

		if ($this->processed) {
			$this->processEncoding();
			$this->processHiddenFields();

			foreach ($this->children as $child)
				if ($child !== null && !$child->isProcessed())
					$child->process();
		}
	}

	// }}}
	// {{{ public function addHiddenField()

	/**
	 * Adds a hidden form field
	 *
	 * Adds a form field to this form that is not shown to the user. Hidden
	 * form fields are outputted as <i>type="hidden"</i> input tags. Values are
	 * serialized before being output so the value can be either a primitive
	 * type or an object. Unserialization happens automatically when
	 * {@link SwatForm::getHiddenField()} is used to retrieve the value. For
	 * non-array and non-object types, the value is also stored as an
	 * unserialized value that can be retrieved without using
	 * SwatForm::getHiddenField().
	 *
	 * @param string $name the name of the field.
	 * @param mixed $value the value of the field, either a string or an array.
	 *
	 * @see SwatForm::getHiddenField()
	 *
	 * @throws SwatInvalidTypeException if an attempt is made to add a value
	 *                                  of type 'resource'.
	 */
	public function addHiddenField($name, $value)
	{
		if (is_resource($value))
			throw new SwatInvalidTypeException(
				'Cannot add a hidden field of type ‘resource’ to a SwatForm.',
				0, $value);

		$this->hidden_fields[$name] = $value;
	}

	// }}}
	// {{{ public function getHiddenField()

	/**
	 * Gets the value of a hidden form field
	 *
	 * @param string $name the name of the field whose value to get.
	 *
	 * @return mixed the value of the field. The type of the field is preserved
	 *                from the call to {@link SwatForm::addHiddenField()}. If
	 *                the field does not exist, null is returned.
	 *
	 * @throws SwatInvalidSerializedDataException if the serialized form data
	 *                                            does not match the signature
	 *                                            data.
	 *
	 * @see SwatForm::addHiddenField()
	 */
	public function getHiddenField($name)
	{
		$data = null;

		if (isset($this->hidden_fields[$name])) {
			// Get value of a hidden field we've already unserialized after
			// processing this form.
			$data = $this->hidden_fields[$name];
		} elseif (!$this->processed && $this->isSubmitted()) {
			// Otherwise, make sure this form was processed and get hidden
			// field from raw form data.
			$raw_data = $this->getFormData();
			$serialized_field_name = self::SERIALIZED_PREFIX.$name;
			if (isset($raw_data[$serialized_field_name])) {
				$data = $this->unserializeHiddenField(
					$raw_data[$serialized_field_name]);
			}
		}

		return $data;
	}

	// }}}
	// {{{ public function clearHiddenFields()

	/**
	 * Clears all hidden fields
	 */
	public function clearHiddenFields()
	{
		$this->hidden_fields = array();
	}

	// }}}
	// {{{ public function addWithField()

	/**
	 * Adds a widget within a new SwatFormField
	 *
	 * This is a convenience method that does the following:
	 * - creates a new SwatFormField,
	 * - adds the widget as a child of the form field,
	 * - and then adds the SwatFormField to this form.
	 *
	 * @param SwatWidget $widget a reference to a widget to add.
	 * @param string $title the visible title of the form field.
	 */
	public function addWithField(SwatWidget $widget, $title)
	{
		require_once 'Swat/SwatFormField.php';
		$field = new SwatFormField();
		$field->add($widget);
		$field->title = $title;
		$this->add($field);
	}

	// }}}
	// {{{ public function &getFormData()

	/**
	 * Returns the super-global array with this form's data
	 *
	 * Returns a reference to the super-global array containing this
	 * form's data. The array is chosen based on this form's method.
	 *
	 * @return array a reference to the super-global array containing this
	 *                form's data.
	 */
	public function &getFormData()
	{
		$data = null;

		switch ($this->method) {
		case self::METHOD_POST:
			$data = &$_POST;
			break;
		case self::METHOD_GET:
			$data = &$_GET;
			break;
		}

		return $data;
	}

	// }}}
	// {{{ public function isSubmitted()

	/**
	 * Whether or not this form was submitted on the previous page request
	 *
	 * This method may be called before or after the SwatForm::process() method.
	 * and is thus sometimes more useful than SwatForm::isProcessed() which
	 * only returns a meaningful value after SwatForm::process() is called.
	 *
	 * @return boolean true if this form was submitted on the previous page
	 *                  request and false if it was not.
	 */
	public function isSubmitted()
	{
		$raw_data = $this->getFormData();

		return (isset($raw_data[self::PROCESS_FIELD]) &&
			$raw_data[self::PROCESS_FIELD] == $this->id);
	}

	// }}}
	// {{{ public function isAuthenticated()

	/**
	 * Whether or not this form is authenticated
	 *
	 * This can be used to catch cross-site request forgeries if the
	 * {@link SwatForm::setAuthenticationToken()} method was previously called.
	 *
	 * If form authentication is used, processing should only be performed on
	 * authenticated forms. An unauthenticated form may be a malicious
	 * request.
	 *
	 * @return boolean true if this form is authenticated or if this form does
	 *                  not use authentication. False if this form is
	 *                  not authenticated.
	 */
	public function isAuthenticated()
	{
		/*
		 * If this form was not submitted, consider it authenticated. Processing
		 * should be safe on forms that were not submitted.
		 */
		if (!$this->isSubmitted())
			return true;

		$raw_data = $this->getFormData();

		$token = null;
		if (isset($raw_data[self::AUTHENTICATION_TOKEN_FIELD]))
			$token = SwatString::signedUnserialize(
				$raw_data[self::AUTHENTICATION_TOKEN_FIELD], $this->salt);

		/*
		 * If this form's authentication token is set, the token in submitted
		 * data must match.
		 */
		return (self::$authentication_token === null ||
			self::$authentication_token === $token);
	}

	// }}}
	// {{{ public function setSalt()

	/**
	 * Sets the salt value to use when salting signature data
	 *
	 * @param string $salt the value to use when salting signature data.
	 */
	public function setSalt($salt)
	{
		$this->salt = (string)$salt;
	}

	// }}}
	// {{{ public function getSalt()

	/**
	 * Gets the salt value to use when salting signature data
	 *
	 * {@link SwatInputControl} widgets may want ot use this value for salting
	 * their own data. This can be done using:
	 *
	 * <code>
	 * $salt = $this->getForm()->getSalt();
	 * </code>
	 *
	 * @return string the value to use when salting signature data.
	 */
	public function getSalt()
	{
		return $this->salt;
	}

	// }}}
	// {{{ public function set8BitEncoding()

	/**
	 * Sets the encoding to assume for 8-bit content submitted from clients
	 *
	 * Form data is automatically converted from this character encoding to
	 * UTF-8 when the form is processed.
	 *
	 * @param string $encoding the encoding to use. If null is specified, no
	 *                          character encoding conversion is performed.
	 *
	 * @see SwatForm::setDefault8BitEncoding()
	 */
	public function set8BitEncoding($encoding)
	{
		$this->_8bit_encoding = $encoding;
	}

	// }}}
	// {{{ public function setConnectionCloseUri()

	/**
	 * Sets the URI at which a Connection: close header may be sent to the
	 * browser
	 *
	 * The Connection: close header may be used to work around
	 * {@link https://bugs.webkit.org/show_bug.cgi?id=5760 Webkit bug #5760}.
	 *
	 * @param string $connection_close_uri the URI to use. If null is specified,
	 *                                      no workarounds will be performed
	 *                                      for Safari.
	 *
	 * @see SwatForm::setDefaultConnectionCloseUri()
	 */
	public function setConnectionCloseUri($connection_close_uri)
	{
		$this->connection_close_uri = $connection_close_uri;
	}

	// }}}
	// {{{ public static function setDefault8BitEncoding()

	/**
	 * Sets the default encoding to assume for 8-bit content submitted from
	 * clients
	 *
	 * Form data is automatically converted from this character encoding to
	 * UTF-8 when the form is processed.
	 *
	 * @param string $encoding the encoding to use. If null is specified, no
	 *                          character encoding conversion is performed.
	 *
	 * @see SwatForm::set8BitEncoding()
	 */
	public static function setDefault8BitEncoding($encoding)
	{
		self::$default_8bit_encoding = $encoding;
	}

	// }}}
	// {{{ public static function setDefaultConnectionCloseUri()

	/**
	 * Sets the default URI at which a Connection: close header may be sent to
	 * the browser
	 *
	 * The Connection: close header may be used to work around
	 * {@link https://bugs.webkit.org/show_bug.cgi?id=5760 Webkit bug #5760}.
	 *
	 * @param string $connection_close_uri the URI to use. If null is specified,
	 *                                      no workarounds will be performed
	 *                                      for Safari. The null behavior is
	 *                                      the default behavior.
	 *
	 * @see SwatForm::setConnectionCloseUri()
	 */
	public static function setDefaultConnectionCloseUri($connection_close_uri)
	{
		self::$default_connection_close_uri = $connection_close_uri;
	}

	// }}}
	// {{{ public static function setAuthenticationToken()

	/**
	 * Sets the token value used to prevent cross-site request forgeries
	 *
	 * After the authentication token is set, when any form is processed, the
	 * the submitted form data must contain this token.
	 *
	 * For the safest results, this token should be taken from an active
	 * session. For usability reasons, the same token should be used for the
	 * same user over multiple requests. The token should be unique to a user's
	 * session and should be difficult to guess.
	 *
	 * @param string $token the value used to prevent cross-site request
	 *                       forgeries.
	 */
	public static function setAuthenticationToken($token)
	{
		self::$authentication_token = (string)$token;
	}

	// }}}
	// {{{ public static function clearAuthenticationToken()

	/**
	 * Clears the token value used to prevent cross-site request forgeries
	 *
	 * After this method is called, no cross-site request forgery detection can
	 * be performed, and all forms will be considered authenticated. This is
	 * acceptable if a user's session is ending and the threat of cross-site
	 * request forgeries is gone.
	 */
	public static function clearAuthenticationToken()
	{
		self::$authentication_token = null;
	}

	// }}}
	// {{{ protected function processHiddenFields()

	/**
	 * Checks submitted form data for hidden fields
	 *
	 * Checks submitted form data for hidden fields. If hidden fields are
	 * found, properly re-adds them to this form.
	 *
	 * @throws SwatInvalidSerializedDataException if the serialized form data
	 *                                            does not match the signature
	 *                                            data.
	 */
	protected function processHiddenFields()
	{
		$raw_data = $this->getFormData();

		$serialized_field_name = self::HIDDEN_FIELD;
		if (isset($raw_data[$serialized_field_name])) {
			$fields = SwatString::signedUnserialize(
				$raw_data[$serialized_field_name], $this->salt);
		} else {
			return;
		}

		foreach ($fields as $name) {
			$serialized_field_name = self::SERIALIZED_PREFIX.$name;
			if (isset($raw_data[$serialized_field_name])) {
				$this->hidden_fields[$name] = $this->unserializeHiddenField(
					$raw_data[$serialized_field_name]);
			}
		}
	}

	// }}}
	// {{{ protected function processEncoding()

	/**
	 * Detects 8-bit character encoding in form data and converts data to UTF-8
	 *
	 * Conversion is only performed if this form's 8-bit encoding is set. This
	 * form's 8-bit encoding may be set automatically if the SwatForm default
	 * 8-bit encoding is set.
	 *
	 * This algorithm is adapted from a blog post at
	 * {@link http://blogs.sun.com/shankar/entry/how_to_handle_utf_8}.
	 *
	 * @throws SwatException if an 8-bit encoding is set and the form data is
	 *                       neither 8-bit nor UTF-8.
	 */
	protected function processEncoding()
	{
		$raw_data = &$this->getFormData();

		if ($this->_8bit_encoding !== null &&
			isset($raw_data[self::ENCODING_FIELD])) {

			$value = $raw_data[self::ENCODING_FIELD];

			if ($value === self::ENCODING_8BIT_VALUE) {

				// convert from our 8-bit encoding to utf-8
				foreach ($raw_data as $key => &$value) {
					if (!mb_check_encoding($value, $this->_8bit_encoding)) {
						// The 8-bit data is not valid for the 8-bit character
						// encoding. This can happen, for example, if robots
						// submit the 8-bit detection value and then submit
						// UTF-8 encoded values.
						throw new SwatInvalidCharacterEncodingException(
							sprintf(
								"Form submitted with 8-bit encoding but form ".
								"data is not valid %s. If this form data is ".
								"expected, use the ".
								"SwatForm::set8BitEncoding() method to set an ".
								"appropriate 8-bit encoding value.\n\nForm ".
								"data: \n%s",
								$this->_8bit_encoding,
								file_get_contents('php://input')
							)
						);
					}

					$value = iconv($this->_8bit_encoding, 'utf-8', $value);
				}

				foreach ($_FILES as &$file) {
					$filename = $file['name'];
					if (!mb_check_encoding($filename, $this->_8bit_encoding)) {
						// The 8-bit data is not valid for the 8-bit character
						// encoding. This can happen, for example, if robots
						// submit the 8-bit detection value and then submit
						// UTF-8 encoded values.
						throw new SwatInvalidCharacterEncodingException(
							sprintf(
								"Form submitted with 8-bit encoding but form ".
								"data is not valid %s. If this form data is ".
								"expected, use the ".
								"SwatForm::set8BitEncoding() method to set an ".
								"appropriate 8-bit encoding value.\n\nForm ".
								"data: \n%s",
								$this->_8bit_encoding,
								file_get_contents('php://input')
							)
						);
					}
					$file['name'] = iconv(
						$this->_8bit_encoding,
						'utf-8',
						$filename
					);
				}

			} elseif ($value !== self::ENCODING_UTF8_VALUE) {
				// it's not 8-bit or UTF-8. Time to panic!
				throw new SwatInvalidCharacterEncodingException(
					"Unknown form data character encoding. Form data: \n".
					file_get_contents('php://input'));
			}
		}
	}

	// }}}
	// {{{ protected function notifyOfAdd()

	/**
	 * Notifies this widget that a widget was added
	 *
	 * If any of the widgets in the added subtree are file entry widgets then
	 * set this form's encoding accordingly.
	 *
	 * @param SwatWidget $widget the widget that has been added.
	 *
	 * @see SwatContainer::notifyOfAdd()
	 */
	protected function notifyOfAdd($widget)
	{
		if (class_exists('SwatFileEntry')) {

			if ($widget instanceof SwatFileEntry) {
				$this->encoding_type = 'multipart/form-data';
			} elseif ($widget instanceof SwatUIParent) {
				$descendants = $widget->getDescendants();
				foreach ($descendants as $sub_widget) {
					if ($sub_widget instanceof SwatFileEntry) {
						$this->encoding_type = 'multipart/form-data';
						break;
					}
				}
			}
		}
	}

	// }}}
	// {{{ protected function displayHiddenFields()

	/**
	 * Displays hidden form fields
	 *
	 * Displays hiden form fields as <input type="hidden" /> XHTML elements.
	 * This method automatically handles array type values so they will be
	 * returned correctly as arrays.
	 *
	 * This methods also generates an array of hidden field names and passes
	 * them as hidden fields.
	 *
	 * If an authentication token is set on this form to prevent cross-site
	 * request forgeries, the token is displayed in a hidden field.
	 */
	protected function displayHiddenFields()
	{
		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'hidden';

		echo '<div class="swat-hidden">';

		if ($this->_8bit_encoding !== null) {
			// The character encoding detection field is intentionally not using
			// SwatHtmlTag to avoid minimizing entities.
			echo '<input type="hidden" ',
				'name="', self::ENCODING_FIELD, '" ',
				'value="', self::ENCODING_ENTITY_VALUE, '" />';
		}

		foreach ($this->hidden_fields as $name => $value) {
			// display unserialized value for primative types
			if ($value !== null && !is_array($value) && !is_object($value)) {

				// SwatHtmlTag uses SwatString::minimizeEntities(), which
				// prevents double-escaping entities. For hidden form-fields,
				// we want data to be returned exactly as it was specified. This
				// necessitates double-escaping to ensure any entities that were
				// specified in the hidden field value are returned correctly.
				$escaped_value = htmlspecialchars($value, ENT_COMPAT, 'UTF-8');

				$input_tag->name = $name;
				$input_tag->value = $escaped_value;
				$input_tag->display();
			}

			// display serialized value
			$serialized_data = $this->serializeHiddenField($value);

			// SwatHtmlTag uses SwatString::minimizeEntities(), which prevents
			// double-escaping entities. For hidden form-fields, we want data
			// to be returned exactly as it was specified. This  necessitates
			// double-escaping to ensure any entities that were specified in
			// the hidden field value are returned correctly.
			$escaped_serialized_data = htmlspecialchars($serialized_data,
				ENT_COMPAT, 'UTF-8');

			$input_tag->name = self::SERIALIZED_PREFIX.$name;
			$input_tag->value = $escaped_serialized_data;
			$input_tag->display();
		}

		// display hidden field names
		if (count($this->hidden_fields) > 0) {
			// array of field names
			$serialized_data = SwatString::signedSerialize(
				array_keys($this->hidden_fields), $this->salt);

			$input_tag->name = self::HIDDEN_FIELD;
			$input_tag->value = $serialized_data;
			$input_tag->display();
		}

		// display authentication token
		if (self::$authentication_token !== null) {
			$serialized_data = SwatString::signedSerialize(
				self::$authentication_token, $this->salt);

			$input_tag = new SwatHtmlTag('input');
			$input_tag->type = 'hidden';
			$input_tag->name = self::AUTHENTICATION_TOKEN_FIELD;
			$input_tag->value = $serialized_data;
			$input_tag->display();
		}

		echo '</div>';
	}

	// }}}
	// {{{ protected fucntion getFormTag()

	/**
	 * Gets the XHTML form tag used to display this form
	 *
	 * @return SwatHtmlTag the XHTML form tag used to display this form.
	 */
	protected function getFormTag()
	{
		$form_tag = new SwatHtmlTag('form');

		$form_tag->addAttributes(
			array(
				'id'             => $this->id,
				'method'         => $this->method,
				'enctype'        => $this->encoding_type,
				'accept-charset' => $this->accept_charset,
				'action'         => $this->action,
				'class'          => $this->getCSSClassString(),
			)
		);

		// we're going to validate data on the server, so turn off HTML5
		// form validation
		$form_tag->novalidate = 'novalidate';

		return $form_tag;
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this form
	 *
	 * @return array the array of CSS classes that are applied to this form.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-form');
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
	// {{{ protected function getInlineJavaScript()

	/**
	 * Gets inline JavaScript required for this form
	 *
	 * Right now, this JavaScript focuses the first SwatControl in the form.
	 *
	 * @return string inline JavaScript required for this form.
	 */
	protected function getInlineJavaScript()
	{
		$javascript = sprintf("var %s_obj = new %s(%s, %s);",
			$this->id,
			$this->getJavaScriptClass(),
			SwatString::quoteJavaScriptString($this->id),
			SwatString::quoteJavaScriptString($this->connection_close_uri));

		if ($this->autofocus) {
			$focusable = true;
			if ($this->default_focused_control === null) {
				$control = $this->getFirstDescendant('SwatControl');
				if ($control === null) {
					$focusable = false;
				} else {
					$focus_id = $control->getFocusableHtmlId();
					if ($focus_id === null)
						$focusable = false;
				}
			} else {
				$focus_id =
					$this->default_focused_control->getFocusableHtmlId();

				if ($focus_id === null)
					$focusable = false;
			}

			if ($focusable)
				$javascript.=
					"\n{$this->id}_obj.setDefaultFocus('{$focus_id}');";
		}

		if (!$this->autocomplete) {
			$javascript.=
				"\n{$this->id}_obj.setAutocomplete(false);";
		}

		return $javascript;
	}

	// }}}
	// {{{ protected function getJavaScriptClass()

	/**
	 * Gets the name of the JavaScript class to instantiate for this form
	 *
	 * Sub-classes of this class may want to return a sub-class of the default
	 * JavaScript form class.
	 *
	 * @return string the name of the JavaScript class to instantiate for this
	 *                 form . Defaults to 'SwatForm'.
	 */
	protected function getJavaScriptClass()
	{
		return 'SwatForm';
	}

	// }}}
	// {{{ protected function serializeHiddenField()

	/**
	 * Serializes a hidden field value into a string safe for including in
	 * form data
	 *
	 * @param mixed $value the hidden field value to serialize.
	 *
	 * @return string the hidden field value serialized for safely including in
	 *                 form data.
	 */
	protected function serializeHiddenField($value)
	{
		$value = SwatString::signedSerialize($value, $this->salt);

		// escape special characters that confuse browsers (mostly IE;
		// null characters confuse all browsers)
		$value = str_replace('\\', '\\\\', $value);
		$value = str_replace("\x00", '\x00', $value);
		$value = str_replace("\x0a", '\x0a', $value);
		$value = str_replace("\x0d", '\x0d', $value);

		return $value;
	}

	// }}}
	// {{{ protected function unserializeHiddenField()

	/**
	 * Unserializes a hidden field value that was serialized using
	 * {@link SwatForm::serializeHiddenField()}
	 *
	 * @param string $value the hidden field value to unserialize.
	 *
	 * @return mixed the unserialized value.
	 *
	 * @throws SwatInvalidSerializedDataException if the serialized form data
	 *                                            does not match the signature
	 *                                            data.
	 */
	protected function unserializeHiddenField($value)
	{
		// unescape special characters (see serializeHiddenField())
		$value = str_replace('\x00', "\x00", $value);
		$value = str_replace('\x0a', "\x0a", $value);
		$value = str_replace('\x0d', "\x0d", $value);
		$value = str_replace('\\\\', '\\', $value);

		$value = SwatString::signedUnserialize($value, $this->salt);

		return $value;
	}

	// }}}
}

?>
