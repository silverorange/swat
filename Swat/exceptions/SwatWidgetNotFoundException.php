<?php

/**
 * Thrown when a widget is not found.
 *
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatWidgetNotFoundException extends SwatException
{
    /**
     * The widget id that was searched for.
     *
     * @var string
     */
    protected $widget_id;

    /**
     * Creates a new widget not found exception.
     *
     * @param string $message   the message of the exception
     * @param int    $code      the code of the exception
     * @param string $widget_id the widget id that was searched for
     */
    public function __construct($message = null, $code = 0, $widget_id = null)
    {
        parent::__construct($message, $code);
        $this->widget_id = $widget_id;
    }

    /**
     * Gets the widget id that was searched for.
     *
     * @return string the widget id that was searched for
     */
    public function getWidgetId()
    {
        return $this->widget_id;
    }
}
