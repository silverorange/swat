<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/exceptions/SwatException.php';
require_once 'Swat/exceptions/SwatInvalidTypeException.php';
require_once 'Swat/SwatDisplayableContainer.php';
require_once 'Swat/SwatMessageDisplay.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatString.php';

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
 * @copyright 2004-2007 silverorange
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
	 * other parts of Swat set this proprety automatically.
	 *
	 * @var SwatButton
	 */
	public $button = null;

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

	// }}}
	// {{{ private properties

	/**
	 * Whether or not this form was authenticated
	 *
	 * @var boolean
	 *
	 * @see SwatForm::isAuthenticated()
	 */
	private $authenticated = false;

	/**
	 * The method to use for this form
	 *
	 * Is one of SwatForm::METHOD_* constants.
	 *
	 * @var string
	 */
	private $method = SwatForm::METHOD_POST;

	/**
	 * The token value used to prevent cross-site request forgeries
	 *
	 * If this value is not null, all submitted forms will be authenticated
	 * with this token value.
	 *
	 * @var string
	 *
	 * @see SwatForm::setAuthenticationToken()
	 * @see SwatForm::clearAuthenticationToken()
	 * @see SwatForm::processAuthenticationToken()
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

		if (self::$default_salt !== null)
			$this->setSalt(self::$default_salt);

		$this->requires_id = true;

		$this->addJavaScript('packages/swat/javascript/swat-form.js',
			Swat::PACKAGE_ID);
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
		$valid_methods = array(SwatForm::METHOD_POST, SwatForm::METHOD_GET);

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

		$this->addHiddenField(self::PROCESS_FIELD, $this->id);

		$form_tag = new SwatHtmlTag('form');
		$form_tag->id = $this->id;
		$form_tag->method = $this->method;
		$form_tag->enctype = $this->encoding_type;
		$form_tag->action = $this->action;
		$form_tag->class = $this->getCSSClassString();

		$form_tag->open();
		$this->displayChildren();
		$this->displayHiddenFields();
		$form_tag->close();

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
			// always process authentication token first
			$this->processAuthenticationToken();
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

		// get value of a hidden field we've already unserialized after
		// processing this form
		if (isset($this->hidden_fields[$name])) {
			$data = $this->hidden_fields[$name];

		// otherwise, make sure this form was processed and get hidden field
		// from raw form data
		} elseif (!$this->processed && $this->isSubmitted()) {
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
		case SwatForm::METHOD_POST:
			$data = &$_POST;
			break;
		case SwatForm::METHOD_GET:
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
	 * This method may becalled before or after the SwatForm::process() method.
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
	 * If form authentication is used, data should only be saved from
	 * authenticated forms. An unauthenticated form may be a malicious
	 * request.
	 *
	 * @return boolean true if this form is authenticated or if this form does
	 *                  not use authentication. False if this form is
	 *                  not authenticated.
	 */
	public function isAuthenticated()
	{
		return $this->authenticated;
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
	// {{{ public static function setAuthenticationToken()

	/**
	 * Sets the token value used to prevent cross-site request forgeries
	 *
	 * After the authentication token is set, when any form is processed, the
	 * the submitted form data must contain this token.
	 *
	 * For the safest results, this token should be taken from an active
	 * session. The same token should be used for the same user over
	 * multiple requests. The token should be unique to a user's session and
	 * should be difficult to guess.
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
	 * After this method is called, when any form is processed, no cross-site
	 * request forgery checks will be made. This is acceptable if a user's
	 * session is ending and the threat of cross-site request forgeries is
	 * gone.
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
	// {{{ protected function processAuthenticationToken()

	/**
	 * Checks the authentication token against submitted form data
	 *
	 * This catches cross-site request forgeries if the
	 * {@link SwatForm::setAuthenticationToken()} method was previously called.
	 *
	 * If an authentication token mismatch is detected, a message is added
	 * to this form.
	 */
	protected function processAuthenticationToken()
	{
		$raw_data = $this->getFormData();

		$token = null;
		if (isset($raw_data[self::AUTHENTICATION_TOKEN_FIELD]))
			$token = SwatString::signedUnserialize(
				$raw_data[self::AUTHENTICATION_TOKEN_FIELD], $this->salt);

		/*
		 * If this form's authentication token is set, the token in submitted
		 * data must match.
		 */
		$this->authenticated = 
			(self::$authentication_token === null ||
			self::$authentication_token === $token);
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
	 * them as hidden fields as well.
	 *
	 * If an authentication token is set on this form to prevent cross-site
	 * request forgeries, the token is displayed in a hidden field as well.
	 */
	protected function displayHiddenFields()
	{
		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'hidden';

		echo '<div class="swat-input-hidden">';

		foreach ($this->hidden_fields as $name => $value) {
			// display unserialized value for primative types
			if ($value !== null && !is_array($value) && !is_object($value)) {
				$input_tag->name = $name;
				$input_tag->value = $value;
				$input_tag->display();
			}

			// display serialized value
			$serialized_data = $this->serializeHiddenField($value);
			$input_tag->name = self::SERIALIZED_PREFIX.$name;
			$input_tag->value = $serialized_data;
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
		$javascript = "var {$this->id}_obj = new SwatForm('{$this->id}');";

		if ($this->autofocus) {
			$focusable = true;
			if ($this->default_focused_control === null) {
				$control = $this->getFirstDescendant('SwatControl');
				if ($control === null || $control->id === null)
					$focusable = false;
				else
					$focus_id = $control->id;
			} else {
				if ($this->default_focused_control->id === null)
					$focusable = false;
				else
					$focus_id = $this->default_focused_control->id;
			}

			if ($focusable)
				$javascript.=
					"\n{$this->id}_obj.setDefaultFocus('{$focus_id}');";
		}

		return $javascript;
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
		$value = str_replace('\\\\', '\\',   $value);

		$value = SwatString::signedUnserialize($value, $this->salt);

		return $value;
	}

	// }}}
}

?>
