<?php

require_once 'Swat/SwatButton.php';

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
		$this->displayJavaScript();
	}

	// }}}
	// {{{ private function displayJavaScript()

	/**
	 * Outputs the JavaScript required for this control
	 */
	private function displayJavaScript()
	{
		echo '<script type="text/javascript">';

		printf("%s = new SwatConfirmationButton('%s');\n", $this->id, $this->id);

		$message = str_replace("'", "''", $this->confirmation_message);
		$message = str_replace("\n", '\n', $this->confirmation_message);

		printf("%s.setMessage('%s');\n", $this->id, $message);

		echo '</script>';
	}

	// }}}
}

?>
