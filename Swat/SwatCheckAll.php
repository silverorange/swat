<?php

require_once 'Swat/SwatControl.php';

/**
 * A "check all" JavaScript checkbox
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatCheckAll extends SwatControl
{
	/**
	 * Controller
	 *
	 * A reference to the {@link SwatObject} linked to the SwatCheckList.
	 *
	 * @var SwatObject
	 */
	public $controller = null;

	/**
	 * Title
	 *
	 * Optional text to display next to the checkbox, by default "Check All".
	 * The default title gets set in init().
	 *
	 * @var string
	 */
	public $title = null;

	/**
	 * Creates a new check-all widget
	 *
	 * Sets the widget title to a default value.
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->requires_id = true;

		$this->title = Swat::_('Check All');
		$this->addJavaScript('swat/javascript/swat-check-all.js');
	}

	/**
	 * Displays this check-all widget
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		if ($this->controller === null)
			throw new SwatException('A controller referencing '.
				'the SwatObject containing the checklist must be set.');

		$div_tag = new SwatHtmlTag('div');
		$div_tag->class = 'swat-check-all';
		$div_tag->open();

		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'checkbox';
		$input_tag->id = $this->id;

		$label_tag = new SwatHtmlTag('label');
		$label_tag->for = $this->id;

		$label_tag->open();
		$input_tag->display();
		echo $this->title;
		$label_tag->close();

		$div_tag->close();

		$this->displayJavaScript();
	}

	/**
	 * Displays the JavaScript for this check-all widget
	 */
	private function displayJavaScript()
	{
		echo '<script type="text/javascript">';
		echo "//<![CDATA[\n";

		echo "new SwatCheckAll('{$this->id}', {$this->controller->id});\n";

		echo "\n//]]>";
		echo '</script>';
	}
}

?>
