<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatDisplayableContainer.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatYUI.php';

/**
 * A container to show and hide child widgets
 *
 * @package   Swat
 * @copyright 2004-2016 silverorange
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
	public $title = '';

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

		$yui = new SwatYUI(array('dom', 'animation'));
		$this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());

		$this->addJavaScript('packages/swat/javascript/swat-disclosure.js');
		$this->addStyleSheet('packages/swat/styles/swat-disclosure.css');
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this disclosure container
	 *
	 * Creates appropriate divs and outputs closed or opened based on the
	 * initial state.
	 *
	 * The disclosure is always displayed as opened in case the user has
	 * JavaScript turned off.
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		SwatWidget::display();

		$control_div = $this->getControlDivTag();
		$span = $this->getSpanTag();
		$input = $this->getInputTag();
		$container_div = $this->getContainerDivTag();
		$animate_div = $this->getAnimateDivTag();
		$padding_div = $this->getPaddingDivTag();

		$control_div->open();

		$span->display();
		$input->display();

		$container_div->open();
		$animate_div->open();
		$padding_div->open();
		$this->displayChildren();
		$padding_div->close();
		$animate_div->close();
		$container_div->close();

		Swat::displayInlineJavaScript($this->getInlineJavaScript());

		$control_div->close();
	}

	// }}}
	// {{{ protected function getControlDivTag()

	protected function getControlDivTag()
	{
		$div = new SwatHtmlTag('div');
		$div->id = $this->id;
		$div->class = $this->getCSSClassString();

		return $div;
	}

	// }}}
	// {{{ protected function getContainerDivTag()

	protected function getContainerDivTag()
	{
		$div = new SwatHtmlTag('div');
		$div->class = 'swat-disclosure-container';

		return $div;
	}

	// }}}
	// {{{ protected function getAnimateDivTag()

	protected function getAnimateDivTag()
	{
		$div = new SwatHtmlTag('div');

		return $div;
	}

	// }}}
	// {{{ protected function getPaddingDivTag()

	protected function getPaddingDivTag()
	{
		$div = new SwatHtmlTag('div');
		$div->class = 'swat-disclosure-padding-container';

		return $div;
	}

	// }}}
	// {{{ protected function getInputTag()

	protected function getInputTag()
	{
		$input = new SwatHtmlTag('input');
		$input->type = 'hidden';
		// initial value is blank, value is set by JavaScript
		$input->value = '';
		$input->id = $this->id.'_input';

		return $input;
	}

	// }}}
	// {{{ protected function getSpanTag()

	protected function getSpanTag()
	{
		$title = strval($this->title);

		$span = new SwatHtmlTag('span');
		$span->class = 'swat-disclosure-span';
		$span->setContent($title);

		return $span;
	}

	// }}}
	// {{{ protected function getJavaScriptClass()

	/**
	 * Gets the name of the JavaScript class to instantiate for this disclosure
	 *
	 * Sub-classes of this class may want to return a sub-class of the default
	 * JavaScript disclosure class.
	 *
	 * @return string the name of the JavaScript class to instantiate for this
	 *                 disclosure. Defaults to 'SwatDisclosure'.
	 */
	protected function getJavaScriptClass()
	{
		return 'SwatDisclosure';
	}

	// }}}
	// {{{ protected function getInlineJavaScript()

	/**
	 * Gets disclosure specific inline JavaScript
	 *
	 * @return string disclosure specific inline JavaScript.
	 */
	protected function getInlineJavaScript()
	{
		$open = ($this->open) ? 'true' : 'false';
		return sprintf("var %s_obj = new %s('%s', %s);",
			$this->id, $this->getJavaScriptClass(), $this->id, $open);
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
		$classes = array(
			'swat-disclosure',
			// always display open in case JavaScript is turned off
			'swat-disclosure-control-opened',
		);

		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
}

?>
