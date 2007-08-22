<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatCellRendererContainer.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * A tile in a {@link SwatTileView}
 *
 * @package   Swat
 * @copyright 2007 silverorange
 * @lisence   http://www.gnu.org/copyleft/lesser.html LGPL Lisence 2.1
 * @see       SwatTileView
 */
class SwatTile extends SwatCellRendererContainer
{
	// {{{ private properties

	/**
	 * Messages affixed to this tile
	 *
	 * @var array
	 */
	private  $messages = array();

	// }}}
	// {{{ public function display()

	/**
	 * Displays this tile
	 *
	 * Each data row is displayed between div tags in the tile
	 */
	public function display($data)
	{
		if (!$this->visible)
			return;

		$div_tag = new SwatHtmlTag('div');
		$div_tag->class = $this->getCSSClassString();
		$div_tag->open();

		foreach ($this->renderers as $renderer) {
			$this->renderers->applyMappingsToRenderer($renderer, $data);
			$renderer->render();
		}

		$div_tag->close();
	}

	// }}}
	// {{{ public function init()

	/**
	 * Initializes this tile
	 *
	 * This initializes the tile contained in the tile view
	 */
	public function init()
	{
		foreach ($this->renderers as $renderer)
			$renderer->init();
	}

	// }}}
	// {{{ public function process()

	/**
	 * Processes this tile
	 *
	 * Processes each renderer contained in the tile
	 */
	public function process()
	{
		foreach ($this->renderers as $renderer)
			$renderer->process();
	}

	// }}}
	// {{{ public function getMessages()

	/**
	 * Gathers all messages from this tile
	 *
	 * @return array an array of {@link SwatMessage} objects.
	 */
	public function getMessages()
	{
		$messages = $this->messages;

		foreach ($this->renderers->renderers as $renderer)
			$messages = array_merge($messages, $renderer->getMessages());

		return $messages;
	}

	// }}}
	// {{{ public function addMessages()

	/**
	 * Adds a message to this tile
	 *
	 * @param SwatMessage the message to add.
	 *
	 * @see SwatMessage
	 */
	public function addMessage(SwatMessage $message)
	{
		$this->messages[] = $message;
	}

	// }}}
	// {{{ public function hasMessage()

	/**
	 * Gets wheter or not this tile has any messages
	 *
	 * @return boolean true if this tile has one or more messages and 
	 *						false if it does not.
	 */
	public function hasMessage()
	{
		$has_message = false;

		foreach ($this->renderers->renderers as $renderer){
			if ($renderer->hasMessage()){
				$has_message = true;
				break;
			}
		}
		
		return $has_message;
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this tile
	 *
	 * CSS classes are added to this tile in the following order:
	 *
	 * 1. hard-coded CSS classes from tile subclasses,
	 * 2. user-specified CSS classes on this tile,
	 * 3. the inheritance classes of the first cell renderer in this tile,
	 * 4. hard-coded CSS classes from the first cell renderer in this tile,
	 * 5. hard-coded data-specific CSS classes from the first cell renderer in
	 *    this tile if this tile has data mappings applied,
	 * 6. user-specified CSS classes on the first cell renderer in this tile.
	 *
	 * @return array the array of CSS classes that are applied to this tile.
	 *
	 * @see SwatCellRenderer::getInheritanceCSSClassNames()
	 * @see SwatCellRenderer::getBaseCSSClassNames()
	 * @see SwatUIObject::getCSSClassNames()
	 */
	protected function getCSSClassNames()
	{
		// base classes
		$classes = $this->getBaseCSSClassNames();

		// user-specified classes
		$classes = array_merge($classes, $this->classes);

		$first_renderer = $this->renderers->getFirst();
		if ($first_renderer !== null) {
			// renderer inheritance classes
			$classes = array_merge($classes,
				$first_renderer->getInheritanceCSSClassNames());

			// renderer base classes
			$classes = array_merge($classes,
				$first_renderer->getBaseCSSClassNames());

			// renderer data specific classes
			if ($this->renderers->mappingsApplied())
				$classes = array_merge($classes,
					$first_renderer->getDataSpecificCSSClassNames());

			// renderer user-specified classes
			$classes = array_merge($classes, $first_renderer->classes);
		}
		return $classes;
	}

	// }}}
	// {{{ protected function getBaseCSSClassNames()

	/** 
	 * Gets the base CSS class names of this tile
	 *
	 * This is the recommended place for column subclasses to add extra hard-
	 * coded CSS classes.
	 *
	 * @return array the array of base CSS class names for this tile.
	 */
	protected function getBaseCSSClassNames()
	{
		return array('swat-tile');
	}

	// }}}
}
?>
