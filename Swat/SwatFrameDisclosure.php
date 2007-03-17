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
		$anchor = $this->getAnchorTag();
		$input = $this->getInputTag();
		$img = $this->getImgTag();
		$animate_div = $this->getAnimateDivTag();

		$container_div = $this->getContainerDivTag();
		$container_div->class.= ' swat-frame-contents';

		$control_div->open();
		$header_tag->open();
		$anchor->open();
		$input->display();
		$img->display();
		echo ' ';
		$anchor->displayContent();
		$anchor->close();
		$header_tag->close();

		$container_div->open();
		$animate_div->open();
		$this->displayChildren();
		$animate_div->close();
		$container_div->close();

		Swat::displayInlineJavaScript($this->getInlineJavascript());

		$control_div->close();
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
