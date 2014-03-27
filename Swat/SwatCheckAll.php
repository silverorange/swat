<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatCheckbox.php';
require_once 'Swat/SwatYUI.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * A "check all" JavaScript powered checkbox
 *
 * @package   Swat
 * @copyright 2005-2014 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatCheckAll extends SwatCheckbox
{
	// {{{ public properties

	/**
	 * Optional checkbox label title
	 *
	 * Defaults to "Check All".
	 *
	 * @var string
	 */
	public $title;

	/**
	 * Optional content type for title
	 *
	 * Defaults to text/plain, use text/xml for XHTML fragments.
	 *
	 * @var string
	 */
	public $content_type = 'text/plain';

	/**
	 * Count for all items when displaying an extended-all checkbox
	 *
	 * When the check-all checkbox has been checked, an additional
	 * checkbox will appear allowing the user to specify that they wish to
	 * select all possible items. This is useful in cases where pagination
	 * makes selecting all possible items impossible.
	 *
	 * @var integer
	 */
	public $extended_count = 0;

	/**
	 * Count for all visible items when displaying an extended-all checkbox
	 *
	 * @var integer
	 */
	public $visible_count = 0;

	/**
	 * Optional extended-all checkbox unit.
	 *
	 * Used for displaying a "check-all" message. Defaults to "items".
	 *
	 * @var string
	 */
	public $unit;

	// }}}
	// {{{ public function __construct()

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
		$this->title = Swat::_('Select All');
		$yui = new SwatYUI(array('event'));
		$this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());
		$this->addJavaScript('packages/swat/javascript/swat-check-all.js');
	}

	// }}}
	// {{{ public function isExtendedSelected()

	/**
	 * Whether or not the extended-checkbox was checked
	 *
	 * @return boolean Whether or not the extended-checkbox was checked
	 */
	public function isExtendedSelected()
	{
		return $this->getCompositeWidget('extended_checkbox')->value;
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this check-all widget
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		$div_tag = new SwatHtmlTag('div');
		$div_tag->id = $this->id;
		$div_tag->class = $this->getCSSClassString();
		$div_tag->open();

		$label_tag = new SwatHtmlTag('label');
		$label_tag->for = $this->id.'_value';
		$label_tag->open();

		$old_id = $this->id;
		$this->id.= '_value';
		parent::display();
		$this->id = $old_id;

		$span_tag = new SwatHtmlTag('span');
		$span_tag->class = 'swat-check-all-title';
		$span_tag->setContent($this->title, $this->content_type);
		$span_tag->display();

		$label_tag->close();

		if ($this->extended_count > $this->visible_count) {
			$div_tag = new SwatHtmlTag('div');
			$div_tag->id = $this->id.'_extended';
			$div_tag->class = 'swat-hidden swat-extended-check-all';
			$div_tag->open();
			echo $this->getExtendedTitle();
			$div_tag->close();
		}

		$div_tag->close();

		Swat::displayInlineJavaScript($this->getInlineJavaScript());
	}

	// }}}
	// {{{ protected function getExtendedTitle()

	protected function getExtendedTitle()
	{
		$locale = SwatI18NLocale::get();
		$entity = ($this->unit === null) ? Swat::_('items') : $this->unit;

		$checkbox = $this->getCompositeWidget('extended_checkbox');
		$checkbox->tabindex = $this->tab_index;

		ob_start();
		$label_tag = new SwatHtmlTag('label');
		$label_tag->for = $checkbox->id;
		$label_tag->setContent(sprintf(Swat::_('select all %s %s'),
			$locale->formatNumber($this->extended_count), $entity));

		$label_tag->open();
		$checkbox->display();
		$label_tag->displayContent();
		$label_tag->close();
		$checkbox_display = ob_get_clean();

		$title = Swat::_('All %s %s on this page are selected. (%s)');

		return sprintf($title,
			$locale->formatNumber($this->visible_count),
			$entity,
			$checkbox_display);
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this check-all widget
	 *
	 * @return array the array of CSS classes that are applied to this
	 *               check-all widget.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-check-all');
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
	// {{{ protected function getInlineJavaScript()

	/**
	 * Gets the inline JavaScript for this check-all widget
	 *
	 * @return string the inline JavaScript for this check-all widget.
	 */
	protected function getInlineJavaScript()
	{
		return sprintf("var %s_obj = new SwatCheckAll('%s');",
			$this->id, $this->id);

	}

	// }}}
	// {{{ protected function createCompositeWidgets()

	protected function createCompositeWidgets()
	{
		$extended_checkbox = new SwatCheckbox();
		$this->addCompositeWidget($extended_checkbox, 'extended_checkbox');
	}

	// }}}
}

?>
