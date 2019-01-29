<?php

/**
 * A numeric cell renderer
 *
 * @package   Swat
 * @copyright 2006-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatNumericCellRenderer extends SwatCellRenderer
{
	// {{{ public properties

	/**
	 * Value can be either a float or an integer
	 *
	 * @var float
	 */
	public $value;

	/**
	 * Number of digits to display after the decimal point
	 *
	 * If null, the native number of digits displayed by PHP is used. The native
	 * number of digits could be a relatively large number of digits for uneven
	 * fractions.
	 *
	 * @var integer
	 */
	public $precision = null;

	/**
	 * What to display when value is null.
	 *
	 * If set to null, the default behaviour is for the value to be passed to
	 * formatCurrency(). If not null, display this in a span instead.
	 *
	 * @var string
	 */
	public $null_display_value = null;

	// }}}
	// {{{ public function render()

	/**
	 * Renders the contents of this cell
	 *
	 * @see SwatCellRenderer::render()
	 */
	public function render()
	{
		if (!$this->visible) {
			return;
		}

		parent::render();

		if ($this->value === null && $this->null_display_value !== null) {
			$this->renderNullValue();
		} else {
			echo $this->getDisplayValue();
		}
	}

	// }}}
	// {{{ protected function renderNullValue()

	protected function renderNullValue()
	{
		$span_tag = new SwatHtmlTag('span');
		$span_tag->class = 'swat-none';
		$span_tag->setContent($this->null_display_value);
		$span_tag->display();
	}

	// }}}
	// {{{ protected function getDisplayValue()

	public function getDisplayValue()
	{
		$value = $this->value;

		if (is_numeric($this->value)) {
			$locale = SwatI18NLocale::get();
			$value = $locale->formatNumber($this->value, $this->precision);
		}

		return $value;
	}

	// }}}
}
