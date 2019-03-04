<?php

/**
 * A currency cell renderer
 *
 * @package   Swat
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatMoneyCellRenderer extends SwatCellRenderer
{
    // {{{ public properties

    /**
     * Optional locale for currency format
     *
     * @var string
     */
    public $locale = null;

    /**
     * Monetary value
     *
     * @var float
     */
    public $value;

    /**
     * If {@link SwatMoneyCellRenderer::$international} is false, whether to
     * render the international currency symbol
     *
     * If true, displays the international currency symbol. Use of this property
     * is discouraged in favour of using the
     * {@link SwatMoneyCellRenderer::$international} property. Using this
     * property can render strings that are incorrect for the given locale.
     * Using the international property always renders strings correctly for the
     * specified locale.
     *
     * If {@link SwatMoneyCellRenderer::$international} is true, this property
     * has no effect.
     *
     * @var boolean
     */
    public $display_currency = false;

    /**
     * Whether or not to render the currency value using the international
     * format for the specified locale
     *
     * This uses the international currency symbol of the specified locale
     * instead of the national symbol. For example, the locale en_CA would
     * render $10 as CAD 10.00.
     *
     * @var boolean
     */
    public $international = false;

    /**
     * Number of decimal places to display
     *
     * If set to null, the default number of decimal places for the specified
     * locale is used.
     *
     * @var integer
     */
    public $decimal_places = null;

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
    // {{{ public function __construct()

    /**
     * Creates a money cell renderer
     */
    public function __construct()
    {
        parent::__construct();

        $this->addStyleSheet(
            'packages/swat/styles/swat-money-cell-renderer.css'
        );
    }

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

        if ($this->value === null && $this->null_display_value !== null) {
            $span_tag = new SwatHtmlTag('span');
            $span_tag->class = 'swat-none';
            $span_tag->setContent($this->null_display_value);
            $span_tag->display();
        } else {
            parent::render();

            $locale = SwatI18NLocale::get($this->locale);
            $format = $this->getCurrencyFormat();

            echo SwatString::minimizeEntities(
                $locale->formatCurrency(
                    $this->value,
                    $this->international,
                    $format
                )
            );

            if (!$this->international && $this->display_currency) {
                echo '&nbsp;',
                    SwatString::minimizeEntities(
                        $locale->getInternationalCurrencySymbol()
                    );
            }
        }
    }

    // }}}
    // {{{ protected function getCurrencyFormat()

    /**
     * Gets currency format to use when rendering
     *
     * @see SwatMoneyCellRenderer::render()
     */
    protected function getCurrencyFormat()
    {
        $format = array('fractional_digits' => $this->decimal_places);

        return $format;
    }

    // }}}
}
