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
	// {{{ protected function getJavaScriptClass()

	/**
	 * Gets the name of the JavaScript class to instantiate for this
	 * confirmation button 
	 *
	 * @return string the name of the JavaScript class to instantiate for this
	 *                 confirmation button. 'SwatConfirmationButton'.
	 */
	protected function getJavaScriptClass()
	{
		return 'SwatConfirmationButton';
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
		$javascript = parent::getInlineJavaScript();
		$javascript.= sprintf("\n%s_obj.setConfirmationMessage(%s);",
			$this->id, SwatString::quoteJavaScriptString(
				$this->confirmation_message));

		return $javascript;
	}

	// }}}
}

?>
