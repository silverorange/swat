<?
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatContainer.php');
require_once('Swat/SwatHtmlTag.php');

/**
 * A container to use around control widgets in a form.
 * Adds a label and space to output messages.
 */
class SwatFormField extends SwatContainer {

	/**
	 * @var string The visible name for this field, or null.
	 */
	public $title = null;

	/**
	 * @var string CSS class to use on the HTML div tag.
	 */
	public $class = 'swat-form-field';

	/**
	 * @var string CSS class to use on outer HTML div when an error message is displayed.
	 */
	public $error_class = 'swat-form-field-error';

	/**
	 * @var string CSS class to use on the HTML div where the error message is displayed.
	 */
	public $errormsg_class = 'swat-form-field-errormsg';

	public function display() {
		$first_child = $this->getChild(0);

		if ($first_child == NULL)
			return;

		$error_messages = $this->gatherErrorMessages();
		
		$divtag = new SwatHtmlTag('div');
		$divtag->class = (count($error_messages) > 0) ? $this->error_class : $this->class;

		$divtag->open();

		if ($this->title != null) {
			$labeltag = new SwatHtmlTag('label');
			$labeltag->for = $first_child->name;
			$labeltag->open();
			echo $this->title, ':';

			if (($first_child instanceof SwatControl) && $first_child->required)
				echo '<span class="required">*</span>';

			$labeltag->close();
		}

		foreach ($this->children as &$child)
			$child->display();

		if (count($error_messages) > 0) {
			$errordivtag = new SwatHtmlTag('div');
			$errordivtag->class = $this->errormsg_class;
			
			$errordivtag->open();

			foreach ($error_messages as &$err)
				echo $err->message, '<br />';

			$errordivtag->close();
		}

		$divtag->close();
	}
}

?>
