<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatDisclosure.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * A frame-like container to show and hide child widgets
 *
 * @package   Swat
 * @copyright 2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatFrameDisclosure extends SwatDisclosure
{
	// {{{ public function __construct()

	/**
	 * Creates a new frame disclosure container
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->addStyleSheet('packages/swat/styles/swat-frame-disclosure.css',
			Swat::PACKAGE_ID);
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this frame disclosure container
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

		$header_tag = new SwatHtmlTag('h2');
		$header_tag->class = 'swat-frame-title';

		$control_div = $this->getControlDivTag();
		$span_tag = $this->getSpanTag();
		$input_tag = $this->getInputTag();
		$container_div = $this->getContainerDivTag();
		$container_div->class.= ' swat-frame-contents';
		$animate_div = $this->getAnimateDivTag();

		$control_div->open();

		$header_tag->open();
		$span_tag->display();
		$header_tag->close();

		$input_tag->display();

		$container_div->open();
		$animate_div->open();
		$this->displayChildren();
		$animate_div->close();
		$container_div->close();

		Swat::displayInlineJavaScript($this->getInlineJavascript());

		$control_div->close();
	}

	// }}}
	// {{{ protected function getSpanTag()

	protected function getSpanTag()
	{
		$span_tag = parent::getSpanTag();
		$span_tag->class = null;
		return $span_tag;
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
		return 'SwatFrameDisclosure';
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
		$classes = array();
		$classes[] = 'swat-frame';
		$classes[] = 'swat-disclosure-control-opened';
		$classes[] = 'swat-frame-disclosure';
		$classes = array_merge($classes, $this->classes);
		return $classes;
	}

	// }}}
}

?>
