<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'JQuery/JQuery.php';
require_once 'Swat/SwatDisplayableContainer.php';
require_once 'Swat/SwatTitleable.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * Fieldset tag container
 *
 * An HTML fieldset tag with an optional HTML legend title.
 *
 * @package   Swat
 * @copyright 2004-2015 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatFieldset extends SwatDisplayableContainer implements SwatTitleable
{
	// {{{ public properties

	/**
	 * Fieldset title
	 *
	 * A visible title for this fieldset, or null.
	 *
	 * @var string
	 */
	public $title = null;

	/**
	 * Optional content type for the title
	 *
	 * Default text/plain, use text/xml for XHTML fragments.
	 *
	 * @var string
	 */
	public $title_content_type = 'text/plain';

	/**
	 * Access key
	 *
	 * Access key for this fieldset legend, for keyboard nagivation.
	 *
	 * @var string
	 */
	public $access_key = null;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new fieldset
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->requires_id = true;

		// JavaScript for IE peekaboo hack
		$jquery = new JQuery();
		$this->html_head_entry_set->addEntrySet(
			$jquery->getHtmlHeadEntrySet()
		);
		$this->addJavaScript('packages/swat/javascript/swat-fieldset.js');
	}

	// }}}
	// {{{ public function getTitle()

	/**
	 * Gets the title of this fieldset
	 *
	 * Implements the {SwatTitleable::getTitle()} interface.
	 *
	 * @return the title of this fieldset.
	 */
	public function getTitle()
	{
		return $this->title;
	}

	// }}}
	// {{{ public function getTitleContentType()

	/**
	 * Gets the title content-type of this fieldset
	 *
	 * Implements the {@link SwatTitleable::getTitleContentType()} interface.
	 *
	 * @return string the title content-type of this fieldset.
	 */
	public function getTitleContentType()
	{
		return $this->title_content_type;
	}

	// }}}
	// {{{ public function display()

	public function display()
	{
		if (!$this->visible)
			return;

		SwatWidget::display();

		$fieldset_tag = new SwatHtmlTag('fieldset');
		$fieldset_tag->id = $this->id;
		$fieldset_tag->class = $this->getCSSClassString();
		$fieldset_tag->open();

		if ($this->title !== null) {
			$legend_tag = new SwatHtmlTag('legend');

			if ($this->access_key != '')
				$legend_tag->accesskey = $this->access_key;

			$legend_tag->setContent($this->title, $this->title_content_type);
			$legend_tag->display();
		}

		$this->displayChildren();

		$fieldset_tag->close();
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this fieldset
	 *
	 * @return array the array of CSS classes that are applied to this fieldset.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-fieldset');
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
}

?>
