<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

/**
 * A single line search entry widget
 *
 * @package   Swat
 * @copyright 2007-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatSearchEntry extends SwatEntry
{
	// {{{ public properties

	/**
	 * An XHTML name for this search entry widget
	 *
	 * The name is used as the XHTML form element name. This is useful for
	 * HTTP GET forms where the input name is displayed in the request URI.
	 * If the name is not specified, the widget id is used as the name.
	 *
	 * @var string
	 */
	public $name;

	// }}}
	// {{{ public function __construct()

	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->requires_id = true;

		$yui = new SwatYUI(array('dom', 'event'));
		$this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());
		$this->addJavaScript('packages/swat/javascript/swat-search-entry.js');
		$this->addStyleSheet('packages/swat/styles/swat-search-entry.css');
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this search entry
	 *
	 * Outputs an appropriate XHTML tag and JavaScript.
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		parent::display();

		Swat::displayInlineJavaScript($this->getInlineJavaScript());
	}

	// }}}
	// {{{ protected function getInlineJavaScript()

	/**
	 * Gets the inline JavaScript for this entry to function
	 *
	 * The inline JavaScript creates an instance of the
	 * SwatSearchEntry widget with the name $this->id_'obj'.
	 *
	 * @return srting the inline JavaScript required for this control to
	 *					function
	 */
	protected function getInlineJavaScript()
	{
		return "var {$this->id}_obj = new SwatSearchEntry('{$this->id}');";
	}

	// }}}
	// {{{ protected function getInputTag()

	protected function getInputTag()
	{
		$tag = parent::getInputTag();

		if ($this->name !== null)
			$tag->name = $this->name;

		return $tag;
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this entry
	 *
	 * @return array the array of CSS classes that are applied to this
	 *                entry.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-search-entry');
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
	// {{{ protected function getRawValue()

	/**
	 * Gets the raw value entered by the user before processing
	 *
	 * @return string the raw value entred by the user before processing or
	 *                 null if no value was entered by the user.
	 */
	protected function getRawValue()
	{
		$value = null;

		if ($this->name === null) {
			$value = parent::getRawValue();
		} else {
			$data = &$this->getForm()->getFormData();
			$id = $this->name;
			if (isset($data[$id]) && $data[$id] != '') {
				$value = $data[$id];
			}
		}

		return $value;
	}

	// }}}
	// {{{ protected function hasRawValue()

	/**
	 * Gets whether or not a value was submitted by the user for this entry
	 *
	 * Note: Users can submit a value of nothing and this method will return
	 * true. This method only returns false if no data was submitted at all.
	 *
	 * @return boolean true if a value was submitted by the user for this entry
	 *                  and false if no value was submitted by the user.
	 */
	protected function hasRawValue()
	{
		$has_value = false;

		if ($this->name === null) {
			$has_value = parent::hasRawValue();
		} else {
			$data = &$this->getForm()->getFormData();
			$id = $this->name;
			if (isset($data[$id])) {
				$has_value = true;
			}
		}

		return $has_value;
	}

	// }}}
}

?>
