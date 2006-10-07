<?php

require_once 'Swat/SwatDisplayableContainer.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'YUI/YUI.php';

/**
 * A container to show and hide child widgets
 *
 * @package   Swat
 * @copyright 2004-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDisclosure extends SwatDisplayableContainer
{
	// {{{ public properties

	/**
	 * A visible title for the label shown beside the disclosure triangle
	 *
	 * @var string
	 */
	public $title = null;

	/**
	 * The initial state of the disclosure
	 *
	 * @var boolean
	 */
	public $open = true;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new disclosure container
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);
		$this->requires_id = true;

		$yui = new YUI('animation');
		$this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());

		$this->addJavaScript('packages/swat/javascript/swat-disclosure.js',
			Swat::PACKAGE_ID);

		$this->addStyleSheet('packages/swat/styles/swat-disclosure.css',
			Swat::PACKAGE_ID);
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this disclosure container
	 *
	 * Creates appropriate divs and outputs closed or opened based on the
	 * initial state.
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		$control_div = new SwatHtmlTag('div');
		$control_div->id = $this->id;
		$control_div->class = $this->getCSSClassString();

		$anchor = new SwatHtmlTag('a');
		$anchor->class = 'swat-disclosure-anchor';
		$anchor->href = sprintf('javascript:%s_obj.toggle();', $this->id);
		$anchor->setContent($this->title);

		$input = new SwatHtmlTag('input');
		$input->type = 'hidden';
		$input->value = ($this->open) ? 'opened' : 'closed';
		$input->id = $this->id.'_input';

		$img = new SwatHtmlTag('img');

		if ($this->open) {
			$img->src = 'packages/swat/images/swat-disclosure-open.png';
			$img->alt = Swat::_('close');
		} else {
			$img->src = 'packages/swat/images/swat-disclosure-closed.png';
			$img->alt = Swat::_('open');
		}

		$img->width = 16;
		$img->height = 16;
		$img->id = $this->id.'_img';
		$img->class = 'swat-disclosure-image';

		$container_div = new SwatHtmlTag('div');
		$container_div->class = 'swat-disclosure-container';

		$control_div->open();
		$anchor->open();
		$input->display();
		$img->display();
		echo ' ';
		$anchor->displayContent();
		$anchor->close();

		$container_div->open();
		$this->displayChildren();
		$container_div->close();

		$this->displayJavaScript();

		$control_div->close();
	}

	// }}}
	// {{{ protected function displayJavaScript()

	/**
	 * Outputs disclosure specific JavaScript
	 */
	protected function displayJavaScript()
	{
		echo '<script type="text/javascript">';
		echo "//<![CDATA[\n";
		echo "var {$this->id}_obj = new SwatDisclosure('{$this->id}');\n";
		echo "//]]>";
		echo '</script>';
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this disclosure
	 *
	 * @return array the array of CSS classes that are applied to this
	 *                disclosure.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-disclosure');

		if ($this->open)
			$classes[] = 'swat-disclosure-control-opened';
		else
			$classes[] = 'swat-disclosure-control-closed';

		$classes = array_merge($classes, $this->classes);
		return $classes;
	}

	// }}}
}

?>
