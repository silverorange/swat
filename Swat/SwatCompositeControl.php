<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatControl.php';
require_once 'Swat/exceptions/SwatDuplicateIdException.php';
require_once 'Swat/exceptions/SwatWidgetNotFoundException.php';

/**
 * A control that can be composed of multiple internal embedded widgets
 *
 * Extending SwatCompositeControl is the easiest way to build a complicated
 * control from multiple widgets. The main methods used to do this are
 * {@link SwatCompositeControl::createEmbeddedWidgets()},
 * {@link SwatCompositeControl::embedWidget()} and
 * {@link SwatCompositeControl::getEmbeddedWidget()}.
 *
 * Developers should implement the <i>createEmbeddedWidgets()</i> method by
 * creating embedded widgets and adding them to this composite control by
 * calling <i>embedWidget()</i>. As long as the parent implemtations of init()
 * and process() are called, nothing further needs to be done for those methods.
 * For the display() method, developers can use the <i>getEmbeddedWidget()</i>
 * method to retrieve an embedded widget for display. Embedded widgets are
 * <i>not</i> displayed by the default implementation of display().
 *
 * In keeping with object-oriented composition theory, none of the embedded
 * widgets are publically accessible. Methods could be added to make embedded
 * widgets available publically, but in that case it would be better to just
 * extend {@link SwatContainer}.
 *
 * @package   Swat
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatCompositeControl extends SwatControl
{
	// {{{ protected properties

	/**
	 * Embedded widgets of this composite control
	 *
	 * Array is of the form 'key' => widget.
	 *
	 * @var array
	 */
	protected $embedded_widgets = array();

	// }}}
	// {{{ private properties

	/**
	 * Whether or not embedded widgets have been created
	 *
	 * This flag is used by
	 * {@link SwatCompositeControl::confirmEmbeddedWidgets()} to ensure embedded
	 * widgets are only created once.
	 *
	 * @var boolean
	 */
	private $embedded_widgets_created = false;

	// }}}
	// {{{ public function init()

	/**
	 * Initialized this composite control
	 *
	 * Embedded widgets of this composite control are automatically initialized
	 * as well.
	 */
	public function init()
	{
		parent::init();

		$this->confirmEmbeddedWidgets();
		foreach ($this->embedded_widgets as $widget)
			$widget->init();
	}

	// }}}
	// {{{ public function process()

	/**
	 * Processes this composite control
	 *
	 * Embedded widgets of this composite control are automatically processed
	 * as well.
	 */
	public function process()
	{
		parent::process();

		$this->confirmEmbeddedWidgets();
		foreach ($this->embedded_widgets as $widget)
			if (!$widget->isProcessed())
				$widget->process();
	}

	// }}}
	// {{{ public function getHtmlHeadEntrySet()

	/**
	 * Gets the SwatHtmlHeadEntry objects needed by this composite control
	 *
	 * @return SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects needed by
	 *                               this composite control.
	 *
	 * @see SwatUIObject::getHtmlHeadEntrySet()
	 */
	public function getHtmlHeadEntrySet()
	{
		$set = parent::getHtmlHeadEntrySet();

		foreach ($this->embedded_widgets as $widget)
			$set->addEntrySet($widget->getHtmlHeadEntrySet());

		return $set;
	}

	// }}}
	// {{{ protected function embedWidget()

	/**
	 * Embeds a widget in this composite control
	 *
	 * @param SwatWidget $widget the widget to embed.
	 * @param string $key a key identifying the widget so it may be retrieved
	 *                     later. The key does not have to be the widget's id
	 *                     but the key does have to be unique within this
	 *                     control relative to the keys of other embedded
	 *                     widgets.
	 *
	 * @throws SwatDuplicateIdException if an embedded widget with the
	 *                                   specified key is already embedded in
	 *                                   this composite control.
	 */
	protected function embedWidget(SwatWidget $widget, $key)
	{
		if (array_key_exists($key, $this->embedded_widgets))
			throw new SwatDuplicateIdException(sprintf(
				"An embedded widget with the key '%s' already exists in this ".
				"composite widget.", $key), 0, $key);

		if ($widget->parent !== null)
			throw new SwatException('Cannot embed a widget that already has '.
				'a parent.');

		$this->embedded_widgets[$key] = $widget;
		$widget->parent = $this;
	}

	// }}}
	// {{{ protected function getEmbeddedWidget()

	/**
	 * Gets an embedded widget in this composite control by its key
	 *
	 * This is used by other methods to retrieve a specific embedded widget. It
	 * is recommended to use this method rather than accessing the protected
	 * array of embedded widgets because this method ensures embedded widgets
	 * are created before trying to retrieve the specified widget.
	 *
	 * @param string $key the key of the embedded widget to get.
	 *
	 * @return SwatWidget the specified embedded widget.
	 *
	 * @throws SwatWidgetNotFoundException if no embedded widget with the
	 *                                     specified key exists in this
	 *                                     composite control.
	 */
	protected function getEmbeddedWidget($key)
	{
		$this->confirmEmbeddedWidgets();

		if (!array_key_exists($key, $this->embedded_widgets))
			throw new SwatWidgetNotFoundException(sprintf(
				"Embedded widget with key of '%s' not found in %s. Make sure ".
				"the widget was created and added to this composite control.",
				$key, get_class($this)), 0, $key);

		return $this->embedded_widgets[$key];
	}

	// }}}
	// {{{ abstract protected function createEmbeddedWidgets()

	/**
	 * Creates embedded widgets used by this composite control
	 *
	 * Created widgets should be added to this control in this method using
	 * {@link SwatCompositeControl::embedWidget()}.
	 */
	abstract protected function createEmbeddedWidgets();

	// }}}
	// {{{ protected final function confirmEmbeddedWidgets()

	/**
	 * Confirms embedded widgets have been created
	 *
	 * Widgets are only created once. This method may be called multiple times
	 * in different places to ensure embedded widgets are available. In general,
	 * it is best to call this method before attempting to use embedded widgets.
	 *
	 * This method is called by the default implementations of init(),
	 * process() and is called any time
	 * {@link SwatCompositeControl::getEmbeddedWidget()} is used so it rarely
	 * needs to be called manually.
	 */
	protected final function confirmEmbeddedWidgets()
	{
		if (!$this->embedded_widgets_created) {
			$this->createEmbeddedWidgets();
			$this->embedded_widgets_created = true;
		}
	}

	// }}}
}

?>
