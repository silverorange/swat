<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatFlydown.php';
require_once 'Swat/SwatYUI.php';

/**
 * A cascading flydown (aka combo-box) selection widget
 *
 * The term cascading refers to the fact that this flydown's contents are
 * updated dynamically based on the selected value of another flydown.
 *
 * The value of the other SwatFlydown cascades to this SwatCascadeFlydown.
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatCascadeFlydown extends SwatFlydown
{
	// {{{ public properties

	/**
	 * Flydown options
	 *
	 * An array of parents and {@link SwatOption}s for the flydown. Each parent
	 * value is associated to an array of possible child values, in the form:
	 *
	 * <code>
	 * array(
	 *     parent_value1 => array(SwatOption1, SwatOption2),
	 *     parent_value2 => array(SwatOption3, SwatOption4),
	 * );
	 * </code>
	 *
	 * @var array
	 */
	public $options = array();

	/**
	 * Cascade from
	 *
	 * A reference to the {@link SwatWidget} that this item cascades from.
	 *
	 * @var SwatWidget
	 */
	public $cascade_from = null;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new calendar
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->requires_id = true;

		$yui = new SwatYUI(array('event'));
		$this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());

		$this->addJavaScript('packages/swat/javascript/swat-cascade.js',
			Swat::PACKAGE_ID);
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this cascading flydown
	 *
	 * {@link SwatFlydown::$show_blank} is set to false here.
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		parent::display();
		Swat::displayInlineJavaScript($this->getInlineJavaScript());
	}

	// }}}
	// {{{ public function addOption()

	/**
	 * Adds an option to this option control
	 *
	 * @param mixed $parent the value of the parent value frome which this
	 *                       option cascades.
	 *
	 * @param mixed|SwatOption $value either a value for the option, or a
	 *                                 {@link SwatOption} object. If a
	 *                                 SwatOption is used, the <i>$title</i>
	 *                                 and <i>$content_type</i> parameters of
	 *                                 this method call are ignored.
	 * @param string $title the title of the added option. Ignored if the
	 *                       <i>$value</i> parameter is a SwatOption object.
	 * @param string $content_type optional. The content type of the title. If
	 *                              not specified, defaults to 'text/plain'.
	 *                              Ignored if the <i>$value</i> parameter is
	 *                              a SwatOption object.
	 */
	public function addOption($parent, $value, $title = '', $content_type = 'text/plain')
	{
		if ($value instanceof SwatOption)
			$option = $value;
		else
			$option = new SwatOption($value, $title, $content_type);

		$this->options[$parent][] = $option;
	}

	// }}}
	// {{{ public function addOptionsByArray()

	/**
	 * Adds options to this option control using an associative array
	 *
	 * @param array $options an array of options. Keys are option
	 *                        parent values. Values are an associative array of
	 *                        title, value pairs.
	 * @param string $content_type optional. The content type of the option
	 *                              titles. If not specified, defaults to
	 *                              'text/plain'.
	 */
	public function addOptionsByArray(array $options,
		$content_type = 'text/plain')
	{
		foreach ($options as $parent => $child_options)
			foreach ($child_options as $value => $title)
				$this->addOption($parent, $value, $title,
					$content_type);
	}

	// }}}
	// {{{ protected function getOptions()

	/**
	 * Gets the options of this flydown as a flat array
	 *
	 * For the cascading flydown, the array returned
	 *
	 * The array is of the form:
	 *    value => title
	 *
	 * @return array the options of this flydown as a flat array.
	 *
	 * @see SwatFlydown::getOptions()
	 */
	protected function &getOptions()
	{
		$ret = array();

		$parent_value = $this->cascade_from->value;
		if ($parent_value === null) {
			if ($this->cascade_from->show_blank) {
				// select the blank option on the cascade from
				$ret[] = new SwatOption('', '&nbsp;');
				$ret[] = new SwatOption('', '&nbsp;');
				return $ret;
			} else {
				// select the first option on the cascade from
				$first_value = reset($this->cascade_from->options)->value;
				$option_array = $this->options[$first_value];
			}
		} else
			$option_array = $this->options[$parent_value];

		if ($this->show_blank && count($option_array) > 1) {
			$ret[] = new SwatOption(null, Swat::_('choose one ...'));
		}

		$ret = array_merge($ret, $option_array);
		return $ret;
	}

	// }}}
	// {{{ protected function getInlineJavaScript()

	/**
	 * Gets the inline JavaScript that makes this control work
	 *
	 * @return string the inline JavaScript that makes this control work.
	 */
	protected function getInlineJavaScript()
	{
		$javascript = sprintf("var %s_cascade = new SwatCascade('%s', '%s');",
			$this->id,
			$this->cascade_from->id,
			$this->id);

		$salt = $this->getForm()->getSalt();

		foreach($this->options as $parent => $options) {
			$parent = SwatString::signedSerialize($parent, $salt);

			if ($this->show_blank && count($options) > 0)
				$javascript.= sprintf(
					"\n%s_cascade.addChild('%s', '%s', '%s');",
					$this->id,
					$parent,
					SwatString::signedSerialize(null, $salt),
					Swat::_('choose one …'));

			foreach ($options as $option) {
				$selected = ($option->value === $this->value) ?
					'true' : 'false';

				$title = $option->title;
				$title = str_replace("\n", '\n', $title);
				$title = str_replace("'", "\\'", $title);

				$value = SwatString::signedSerialize($option->value, $salt);

				$javascript.= sprintf(
					"\n%s_cascade.addChild('%s', '%s', '%s', %s);",
					$this->id,
					$parent,
					$value,
					$title,
					$selected);
			}
		}

		$javascript.= sprintf("\n%s_cascade.init();",
			$this->id);

		return $javascript;
	}

	// }}}
}

?>
