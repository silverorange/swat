<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatButton.php';
require_once 'Swat/SwatYUI.php';

/**
 * A button widget with a javascript confirmation dialog
 *
 * This widget displays as an XHTML form submit button, so it should be used
 * within {@link SwatForm}.
 *
 * @package   Swat
 * @copyright 2004-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatConfirmationButton extends SwatButton
{
	// {{{ public properties

	/**
	 * Confirmation message to display when this button is clicked
	 *
	 * The default value is assigned in the constructor so that it is properly
	 * translated.
	 *
	 * @var string
	 */
	public $confirmation_message = '';

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new confirmation button widget
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$yui = new SwatYUI(array('event'));
		$this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());
		$this->addJavaScript(
			'packages/swat/javascript/swat-confirmation-button.js',
			Swat::PACKAGE_ID);

		$this->confirmation_message =
			Swat::_('Are you sure you wish to continue?');
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this button
	 *
	 * Outputs an XHTML input tag.
	 */
	public function display()
	{
		parent::display();
		$this->displayInlineJavaScript($this->getInlineJavaScript());
	}

	// }}}
	// {{{ protected function getInlineJavaScript()

	/**
	 * Gets the inline JavaScript required for this control
	 *
	 * @return stirng the inline JavaScript required for this control.
	 */
	protected function getInlineJavaScript()
	{
		$javascript = sprintf("var %s = new SwatConfirmationButton('%s');",
			$this->id, $this->id);

		$message = $this->confirmation_message;

		// NOTE: Most of the following escaping is required to prevent XSS
		//       attacks.

		// escape escape characters
		$message = str_replace('\\', '\\\\', $message); 

		// escape single quotes
		$message = str_replace("'", "\'", $message);

		// convert newlines
		$message = str_replace("\n", '\n', $message);

		// break closing script tags
		$message = preg_replace('/<\/(script)([^>]*)?>/ui', "</\\1' + '\\2>",
			$message);

		// escape CDATA closing triads
		$message = str_replace(']]>', "' +\n//]]>\n']]>' +\n//<![CDATA[\n'",
			$message);

		$javascript.= sprintf("\n%s.setMessage('%s');\n",
			$this->id, $message);

		return $javascript;
	}

	// }}}
}

?>
