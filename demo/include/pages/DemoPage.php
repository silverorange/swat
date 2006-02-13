<?php

require_once 'Swat/SwatPage.php';
require_once 'Swat/SwatNavBar.php';
require_once 'Swat/SwatUI.php';
require_once '../include/DemoMenu.php';
require_once '../include/DemoDocumentationMenu.php';

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
class DemoPage extends SwatPage
{
	protected $ui = null;
	protected $navbar = null;
	protected $documentation_menu = null;

	protected $start_time = 0;
	
	protected $demo;
	
	public function __construct($app)
	{
		parent::__construct($app);

		$this->navbar = new SwatNavBar();
		$this->ui = new SwatUI();
		$this->ui->mapClassPrefixToPath('Demo', '../include/');
	}

	public function init()
	{
		$this->start_time = microtime(true);

		$this->demo = SwatApplication::initVar('demo', 'Main',
			SwatApplication::VAR_GET);

		// simple security
		$this->demo = basename($this->demo);
		
		$this->ui->loadFromXML('../include/pages/'.strtolower($this->demo).'.xml');

		$this->initUI();

		$this->ui->init();

		$this->navbar->createEntry($this->app->title, 'index.php');
		$this->navbar->createEntry($this->demo);

		$this->documentation_menu = $this->getDocumentationMenu();
	}

	/**
	 * Allows subclasses to modify the SwatUI object before it is displayed
	 */
	public function initUI()
	{
	}

	public function process()
	{
		$this->ui->process();
	}

	public function build()
	{
		$this->layout->title = $this->demo.' | '.$this->app->title;

		ob_start();
		$this->ui->getRoot()->displayHtmlHeadEntries();
		$this->layout->html_head_entries = ob_get_clean();
		
		$this->layout->source_code =
			str_replace("\t", '    ', htmlspecialchars(implode('',
				file('../include/pages/'.strtolower($this->demo).'.xml')), ENT_COMPAT, 'UTF-8'));

		ob_start();
		$this->ui->displayTidy();
		$this->layout->ui = ob_get_clean();

		ob_start();
		$this->menu = new DemoMenu();
		$this->menu->display();
		$this->layout->menu = ob_get_clean();

		ob_start();
		$this->documentation_menu->display();
		$this->layout->documentation_menu = ob_get_clean();

		$this->layout->execution_time = round(microtime(true) - $this->start_time, 4);

		ob_start();
		$this->navbar->display();
		$this->layout->navbar = ob_get_clean();
	}

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
}

?>
