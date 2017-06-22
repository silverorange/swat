<?php

/**
 * A control for recording a rating out of a variable number of values
 *
 * @package   Swat
 * @copyright 2007-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatRating extends SwatInputControl
{
	// {{{ public properties

	/**
	 * The value of this rating control
	 *
	 * @var integer
	 */
	public $value = null;

	/**
	 * The maximum value of this rating control
	 *
	 * @var integer
	 */
	public $maximum_value = 5;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new rating control
	 *
	 * @param string $id optional. A non-visible unique id for this rating
	 *                    control.
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->requires_id = true;

		$yui = new SwatYUI(array('dom', 'animation'));
		$this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());

		$this->addJavaScript('packages/swat/javascript/swat-rating.js');
		$this->addStyleSheet('packages/swat/styles/swat-rating.css');
	}

	// }}}
	// {{{ public function init()

	/**
	 * Initializes this rating control
	 */
	public function init()
	{
		parent::init();

		$flydown = $this->getCompositeWidget('flydown');
		$flydown->addOptionsByArray($this->getRatings());
	}

	// }}}
	// {{{ public function process()

	/**
	 * Processes this rating control
	 */
	public function process()
	{
		parent::process();

		$flydown = $this->getCompositeWidget('flydown');
		if ($flydown->value == '') {
			$this->value = null;
		} else {
			$this->value = (integer)$flydown->value;
		}
	}

	// }}}
	//  {{{ public function display()

	/**
	 * Displays this rating control
	 */
	public function display()
	{
		parent::display();

		if (!$this->visible)
			return;

		$flydown = $this->getCompositeWidget('flydown');
		$flydown->value = (string)$this->value;

		$div = new SwatHtmlTag('div');
		$div->id = $this->id;
		$div->class = $this->getCSSClassString();
		if (!$this->isSensitive()) {
			$div->class.= ' swat-insensitive';
		}
		$div->open();
		$flydown->display();
		$div->close();

		Swat::displayInlineJavaScript($this->getInlineJavaScript());
	}

	// }}}
	// {{{ protected function getRatings()

	protected function getRatings()
	{
		$ratings = array();

		for ($i = 1; $i <= $this->maximum_value; $i++) {
			$ratings[$i] = sprintf('%s/%s', $i, $this->maximum_value);
		}

		return $ratings;
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this rating control
	 *
	 * @return array the array of CSS classes that are applied to this rating
	 *                control.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-rating');
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
	// {{{ protected function getInlineJavaScript()

	/**
	 * Gets the inline JavaScript for this rating control
	 *
	 * @return string the inline JavaScript required for this rating control.
	 */
	protected function getInlineJavaScript()
	{
		$quoted_string = SwatString::quoteJavaScriptString($this->id);
		return sprintf('var %s_obj = new SwatRating(%s, %s);',
			$this->id, $quoted_string, intval($this->maximum_value));
	}

	// }}}
	// {{{ protected function createCompositeWidgets()

	/**
	 * Creates the composite flydown used by this rating control
	 *
	 * @see SwatWidget::createCompositeWidgets()
	 */
	protected function createCompositeWidgets()
	{
		$flydown = new SwatFlydown();
		$flydown->id = $this->id.'_flydown';
		$flydown->serialize_values = false;
		$this->addCompositeWidget($flydown, 'flydown');
	}

	// }}}
}

?>
