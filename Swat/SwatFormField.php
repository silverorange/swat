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
	 * @var string Class to use on the HTML div tag.
	 */
	public $class = 'swat-form-field';

	public function display() {
		$child =& $this->getChild();

		if ($child == null)
			return;

		$divtag = new SwatHtmlTag('div');
		$divtag->class = $this->class;

		$divtag->open();

		if ($this->title != null) {
			$labeltag = new SwatHtmlTag('label');
			$labeltag->for = $child->name;
			$labeltag->open();
			echo $this->title;

			if (($child instanceof SwatControl) && $child->required)
				echo '<span class="required">*</span>';

			$labeltag->close();
			$child->display();

		} else {
			$child->display();
		}

		$divtag->close();
	}
}

?>
