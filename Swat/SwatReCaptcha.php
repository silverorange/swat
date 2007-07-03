<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'ReCaptcha/ReCaptcha.php';
require_once 'Swat/SwatMessage.php';
require_once 'Swat/SwatInputControl.php';

/**
 * A widget used to display and validate ReCaptcha's
 *
 * @package   Swat
 * @copyright 2007 silverorange
 * @lisence   http://www.gnu.org/copyleft/lesser.html LGPL Lisence 2.1
 */
class SwatReCaptcha extends SwatInputControl
{
	// {{{ private properties

	/**
	 * Public Key
	 *
	 * The public key obtained from the ReCaptcha website used to communicate
	 * with their servers.
	 *
	 * @var string
	 */
	public $public_key = null;
	
	/**
	 * Private Key
	 *
	 * The private key obtained from the ReCaptcha website used to communicate
	 * with their servers.
	 *
	 * @var string
	 */
	public $private_key = null;

	/**
	 * If you are displaying a page to the user over SSL, be sure to set this 
	 * to true so an error dialog doesn't come up in the user's browser.
	 *
	 * @var boolean
	 */ 
	public $secure = false;

	// }}}
	// {{{ public function display()

	/**
	 * Displays this ReCaptcha widget
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		// passing null for second paramater becasue we display our own messages
		ReCaptcha::display($this->public_key, null, $this->secure);
	}

	// }}}
	// {{{ public function process()

	/**
	 * Processes this ReCaptcha Widget
	 *
	 * If the user entered an incorrect response a message is displayed and 
	 * validation is halted.
	 */
	public function process()
	{
		$form = $this->getForm();
		$data = $form->getFormData();

		$resp = ReCaptcha::validate($this->private_key,
											$_SERVER["REMOTE_ADDR"],	
											$data["recaptcha_challenge_field"],
											$data["recaptcha_response_field"]);

		if (!$resp->is_valid){
			// do not validate
			$message = new SwatMessage(Swat::_('The words you entered did not '.
											'match the words displayed, '.
											'please try again.'),
											SwatMessage::ERROR);
			$this->addMessage($message);
		}
	}

	// }}}
}
