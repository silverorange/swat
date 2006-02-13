<?php

require_once 'Swat/SwatControl.php';

/**
 * A "check all" JavaScript powered checkbox
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatCheckAll extends SwatControl
{
	/**
	 * Optional text to display next to the checkbox, by default "Check All".
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

		$div_tag = new SwatHtmlTag('div');
		$div_tag->class = 'swat-check-all';
		$div_tag->open();

		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'checkbox';
		$input_tag->id = $this->id;

		$label_tag = new SwatHtmlTag('label');
		$label_tag->for = $this->id;
		$label_tag->setContent($this->title);

		$label_tag->open();
		$input_tag->display();
		$label_tag->displayContent();
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

		printf("var %s_obj = new SwatCheckAll('%s');\n",
			$this->id, $this->id);

		echo "\n//]]>";
		echo '</script>';
	}
}

?>
