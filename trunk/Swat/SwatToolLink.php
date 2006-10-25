<?php

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/exceptions/SwatUndefinedStockTypeException.php';

/**
 * A a tool link in the widget tree
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatToolLink extends SwatControl
{
	// {{{ public properties

	/**
	 * The href attribute in the XHTML anchor tag
	 *
	 * Optionally uses vsprintf() syntax, for example:
	 * <code>
	 * $tool_link->link = 'MySection/MyPage/%s?id=%s';
	 * </code>
	 *
	 * @var string
	 *
	 * @see SwatToolLink::$value
	 */
	public $link = '';

	/**
	 * The title of this link
	 *
	 * @var string
	 */
	public $title = '';

	/**
	 * A value or array of values to substitute into the link of this cell
	 *
	 * The value property may be specified either as an array of values or as
	 * a single value. If an array is passed, a call to vsprintf() is done
	 * on the {@link SwatToolLink::$link} property. If the value is a string
	 * a single sprintf() call is made.
	 *
	 * @var mixed
	 *
	 * @see SwatToolLink::$link
	 */
	public $value = null;

	/**
	 * The stock id of this tool link
	 *
	 * Specifying a stock id initializes this tool link with a set of
	 * stock values.
	 *
	 * @var string
	 *
	 * @see SwatToolLink::setFromStock()
	 */
	public $stock_id = null;

	/**
	 * Access key for this link
	 *
	 * Specifying an access key makes this tool link keyboard-accessible.
	 *
	 * @var string
	 */
	public $access_key = null;

	// }}}
	// {{{ protected properties

	/**
	 * A CSS class set by the stock_id of this tool link 
	 *
	 * @var string
	 */
	protected $stock_class = null;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new toollink
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->addStyleSheet('packages/swat/styles/swat-tool-link.css',
			Swat::PACKAGE_ID);

		$this->addTangoAttribution();
	}

	// }}}
	// {{{ public function init()

	/**
	 * Initializes this widget
	 *
	 * Loads properties from stock if $stock_id is set.
	 *
	 * @see SwatWidget::init()
	 */
	public function init()
	{
		parent::init();

		if ($this->stock_id !== null) 
			$this->setFromStock($this->stock_id, false);
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this tool link
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		if ($this->isSensitive()) {
			$anchor_tag = new SwatHtmlTag('a');
			$anchor_tag->id = $this->id;
			$anchor_tag->class = $this->getCSSClassString();

			if ($this->value === null)
				$anchor_tag->href = $this->link;
			elseif (is_array($this->value))
				$anchor_tag->href = vsprintf($this->link, $this->value);
			else
				$anchor_tag->href = sprintf($this->link, $this->value);

			$anchor_tag->accesskey = $this->access_key;
			$anchor_tag->setContent($this->title);
			$anchor_tag->display();
		} else {
			$span_tag = new SwatHtmlTag('span');
			$span_tag->id = $this->id;
			$span_tag->class = $this->getCSSClassString();
			$span_tag->setContent($this->title);
			$span_tag->display();
		}
	}

	// }}}
	// {{{ public function setFromStock()

	/**
	 * Sets the values of this tool link to a stock type
	 *
	 * Valid stock type ids are:
	 *
	 * - create
	 * - add
	 * - edit
	 * - delete
	 * - preview
	 * - change-order
	 *
	 * @param string $stock_id the identifier of the stock type to use.
	 * @param boolean $overwrite_properties whether to overwrite properties if
	 *                                       they are already set.
	 *
	 * @throws SwatUndefinedStockTypeException
	 */
	public function setFromStock($stock_id, $overwrite_properties = true)
	{
		switch ($stock_id) {
		case 'create':
			$title = Swat::_('Create');
			$class = 'swat-tool-link-create';
			break;

		case 'add':
			$title = Swat::_('Add');
			$class = 'swat-tool-link-add';
			break;

		case 'edit':
			$title = Swat::_('Edit');
			$class = 'swat-tool-link-edit';
			break;

		case 'delete':
			$title = Swat::_('Delete');
			$class = 'swat-tool-link-delete';
			break;

		case 'preview':
			$title = Swat::_('Preview');
			$class = 'swat-tool-link-preview';
			break;

		case 'change-order':
			$title = Swat::_('Change Order');
			$class = 'swat-tool-link-change-order';
			break;

		case 'help':
			$title = Swat::_('Help');
			$class = 'swat-tool-link-help';
			break;

		case 'print':
			$title = Swat::_('Print');
			$class = 'swat-tool-link-print';
			break;

		case 'email':
			$title = Swat::_('Email');
			$class = 'swat-tool-link-email';
			break;

		default:
			throw new SwatUndefinedStockTypeException(
				"Stock type with id of '{$stock_id}' not found.",
				0, $stock_id);
		}
		
		if ($overwrite_properties || ($this->title === null))
			$this->title = $title;

		$this->stock_class = $class;
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this tool link 
	 *
	 * @return array the array of CSS classes that are applied to this tool
	 *                link.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-tool-link');

		if (!$this->isSensitive())
			$classes[] = 'swat-tool-link-insensitive';

		if ($this->stock_class !== null)
			$classes[] = $this->stock_class;

		$classes = array_merge($classes, $this->classes);

		return $classes;
	}

	// }}}
}

?>
