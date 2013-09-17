<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatEntry.php';

/**
 * A URI entry widget
 *
 * Automatically verifies that the value of the widget is a valid URI.
 *
 * URI validation based on regexp by Diego Perini.
 * See {@link https://gist.github.com/dperini/729294}. 
 *
 * @package   Swat
 * @copyright 2005-2013 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatUriEntry extends SwatEntry
{
	// {{{ public properties

	/**
	 * Whether or not to require the scheme for the URI
	 *
	 * If no scheme is specified, the default scheme will be prepended.
	 *
	 * @var boolean
	 */
	public $scheme_required = true;

	/**
	 * Default scheme to use if $scheme_required is false and the URI
	 * isn't valid.
	 *
	 * @var string
	 */
	public $default_scheme = 'http';

	/**
	 * Valid schemes
	 *
	 * @var array
	 */
	public $valid_schemes = array('http', 'https', 'ftp');

	// }}}
	// {{{ public function process()

	/**
	 * Processes this URI entry
	 *
	 * Ensures this URI is formatted correctly. If the URI is not formatted
	 * correctly, adds an error message to this widget.
	 */
	public function process()
	{
		parent::process();

		if ($this->value === null)
			return;

		$this->value = trim($this->value);

		if ($this->value == '') {
			$this->value = null;
			return;
		}

		if (!$this->validateUri($this->value)) {
			if ($this->validateUri($this->default_scheme.'://'.$this->value)) {
				if ($this->scheme_required) {
					$this->addMessage($this->getValidationMessage(
						'scheme-required'));
				} else {
					$this->value = $this->default_scheme.'://'.$this->value;
				}
			} else {
				$this->addMessage($this->getValidationMessage(
					'invalid-uri'));
			}
		}
	}

	// }}}
	// {{{ protected function validateUri()

	/**
	 * Validates a URI
	 *
	 * @param string $value the URI to validate.
	 *
	 * @return boolean true if <code>$value</code> is a valid URI and
	 *                 false if it is not.
	 */
	protected function validateUri($value)
	{
		$regexp = '_^'.
			// scheme 
			'(?:(?:'.implode('|', $this->valid_schemes).')://)'.
			// user:pass authentication
			'(?:\S+(?::\S*)?@)?'.
			// host name
			'(?:(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)'.
			// domain name
			'(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)'.
			// TLD identifier
			'*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,}))'.
			// port number
			'(?::\d{2,5})?'.
			// resource path
			'(?:/[^\s]*)'.
			'?$_iuS';

		return preg_match($regexp, $value);
	}

	// }}}
	// {{{ protected function getValidationMessage()

	/**
	 * Gets a validation message for this entry
	 *
	 * Can be used by sub-classes to change the validation messages.
	 *
	 * @param string $id the string identifier of the validation message.
	 *
	 * @return SwatMessage the validation message.
	 */
	protected function getValidationMessage($id)
	{
		switch ($id) {
		case 'scheme-required':
			$text = sprintf(Swat::_('“%s” must include a prefix (i.e. %s).'),
				$this->value,
				$this->default_scheme);
			break;
		case 'invalid-uri':
			$text = sprintf(
				Swat::_('“%s” is not a properly formatted address.'),
				$this->value);
			break;
		default:
			return parent::getValidationMessage($id);
		}

		$message = new SwatMessage($text, 'error');
		return $message;
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this entry
	 *
	 * @return array the array of CSS classes that are applied to this
	 *                entry.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-uri-entry');
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
}

?>
