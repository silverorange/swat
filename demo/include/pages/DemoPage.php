<?php

require_once 'Swat/SwatNavBar.php';
require_once 'Swat/SwatUI.php';
require_once '../include/DemoMenu.php';
require_once '../include/DemoDocumentationMenu.php';
require_once 'Site/pages/SitePage.php';
require_once 'Site/SiteApplication.php';
/**
 * A page in the Swat Demo Application
 *
 * Demo application pages can set widget properties that are not expressable
 * in SwatML.
 *
 * @package   SwatDemo
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class DemoPage extends SitePage
{
	// {{{ protected properties

	protected $ui = null;
	protected $navbar = null;
	protected $documentation_menu = null;

	protected $start_time = 0;
	
	protected $demo;

	// }}}
	// {{{ public function __construct()
	
	public function __construct($app)
	{
		parent::__construct($app);

		$this->navbar = new SwatNavBar();
		$this->ui = new SwatUI();
		$this->ui->mapClassPrefixToPath('Demo', '../include/');
	}

	// }}}
	// {{{ public function init()

	public function init()
	{
		$this->start_time = microtime(true);

		$this->demo = get_class($this);
		
		$this->ui->loadFromXML('../include/pages/'.strtolower($this->demo).'.xml');

		$this->initUI();

		$this->ui->init();

		$this->navbar->createEntry($this->app->title, 'index.php');
		$this->navbar->createEntry($this->demo);

		$this->documentation_menu = $this->getDocumentationMenu();
	}

	// }}}
	// {{{ public function initUI()

	/**
	 * Allows subclasses to modify the SwatUI object before it is displayed
	 */
	public function initUI()
	{
	}

	// }}}
	// {{{ pulbic function process

	public function process()
	{
		$this->ui->process();
	}

	// }}}
	// {{{ public function build()

	public function build()
	{
		parent::build();

		$this->layout->data->title = $this->demo.' | '.$this->app->title;

		$this->layout->addHtmlHeadEntrySet(
			$this->ui->getRoot()->getHtmlHeadEntries());

		$this->layout->data->source_code =
			str_replace("\t", '    ', htmlspecialchars(implode('',
				file('../include/pages/'.strtolower($this->demo).'.xml')), ENT_COMPAT, 'UTF-8'));

		$this->layout->startCapture('ui');
		$this->ui->displayTidy();
		$this->layout->endCapture();

		$this->layout->startCapture('menu');
		$this->menu = new DemoMenu();
		$this->menu->display();
		$this->layout->endCapture();

		$this->layout->startCapture('documentation_menu');
		$this->documentation_menu->display();
		$this->layout->endCapture();

		$this->layout->data->execution_time = round(microtime(true) - $this->start_time, 4);

		$this->layout->startCapture('navbar');
		$this->navbar->display();
		$this->layout->endCapture();
	}

	// }}}
	// {{{ protected function getDocumentationMenu()

	protected function getDocumentationMenu()
	{
		switch ($this->demo) {
		case 'Calendar':
			$entries = array('SwatCalendar');
			break;

		case 'ChangeOrder':
			$entries = array('SwatChangeOrder');
			break;

		case 'Checkbox':
			$entries = array(
				'SwatCheckbox',
				'SwatCheckboxList',
				'SwatCheckboxTree'
				);
			break;

		case 'ColorEntry':
			$entries = array(
				'SwatColorEntry',
				'SwatSimpleColorEntry'
				);
			break;

		case 'DateEntry':
			$entries = array(
				'SwatDateEntry',
				'SwatTimeEntry'
				);
			break;

		case 'DetailsView':
			$entries = array(
				'SwatDetailsView',
				'SwatDetailsViewField',
				'SwatDetailsViewVerticalField',
				'SwatBooleanCellRenderer',
				'SwatDateCellRenderer',
				'SwatImageCellRenderer',
				'SwatMoneyCellRenderer',
				'SwatTextCellRenderer'
				);
			break;

		case 'Disclosure':
			$entries = array('SwatDisclosure');
			break;

		case 'Entry':
			$entries = array(
				'SwatEntry',
				'SwatEmailEntry',
				'SwatIntegerEntry',
				'SwatFloatEntry',
				'SwatMoneyEntry'
				);
			break;

		case 'Fieldset':
			$entries = array('SwatFieldset');
			break;

		case 'FileEntry':
			$entries = array('SwatFileEntry');
			break;

		case 'Flydown':
			$entries = array(
				'SwatFlydown',
				'SwatFlydownTree',
				'SwatCascadeFlydown',
				'SwatOption'
				);
			break;

		case 'Frame':
			$entries = array('SwatFrame');
			break;

		case 'MessageDisplay':
			$entries = array('SwatMessageDisplay');
			break;

		case 'Pagination':
			$entries = array('SwatPagination');
			break;

		case 'PasswordEntry':
			$entries = array(
				'SwatPasswordEntry',
				'SwatConfirmPasswordEntry'
				);
			break;

		case 'RadioList':
			$entries = array('SwatRadioList');
			break;

		case 'StringDemo':
			$entries = array('SwatString');
			break;

		case 'TableView':
			$entries = array(
				'SwatTableView',
				'SwatTableStore',
				'SwatTableViewColumn',
				'SwatTableViewCheckboxColumn',
				'SwatCheckboxCellRenderer',
				'SwatBooleanCellRenderer',
				'SwatDateCellRenderer',
				'SwatImageCellRenderer',
				'SwatMoneyCellRenderer',
				'SwatTextCellRenderer',
				'SwatActions',
				'SwatActionItem'
				);
			break;

		case 'Textarea':
			$entries = array(
				'SwatTextarea',
				'SwatTextareaEditor'
				);
			break;

		case 'TimeZoneEntry':
			$entries = array('SwatTimeZoneEntry');
			break;

		case 'ToolLink':
			$entries = array('SwatToolLink');
			break;

		case 'YesNoFlydown':
			$entries = array('SwatYesNoFlydown');
			break;

		default:
			$entries = array();
			break;
		}

		return new DemoDocumentationMenu($entries);
	}

	// }}}
	// {{{ protected function createLayout()

	protected function createLayout()
	{
		return new SiteLayout($this->app, '../include/layouts/xhtml/default.php');
	}

	// }}}
}

?>
