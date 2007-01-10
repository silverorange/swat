<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Date.php';
require_once 'Swat/SwatInputControl.php';
require_once 'Swat/SwatFlydown.php';
require_once 'Swat/SwatCascadeFlydown.php';
require_once 'Swat/SwatState.php';

/**
 * A time-zone selection widget
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTimeZoneEntry extends SwatInputControl implements SwatState
{
	// {{{ public properties

	/**
	 * Time-zone identifier
	 *
	 * The id of the selected time-zone.
	 *
	 * @var string
	 */
	public $value = null;

	// }}}
	// {{{ private properties

	/**
	 * Time-zone areas available for this time-zone entry widget
	 *
	 * This is an array of flydown options. Areas are usually continents.
	 *
	 * @var array
	 */
	private $areas = array();

	/**
	 * Time-zone regions available for this time-zone entry widget
	 *
	 * This is an array of flydown options. Regions are usually cities.
	 *
	 * @var array
	 */
	private $regions = array();

	/**
	 * A reference to the internal areas flydown
	 *
	 * This contains time-zone continents.
	 *
	 * @var SwatFlydown
	 */
	private $areas_flydown;

	/**
	 * A reference to the internal regions flydown
	 *
	 * This contains time-zone cities.
	 *
	 * @var SwatCascadeFlydown
	 */
	private $regions_flydown;

	/**
	 * An internal flag that is set to true when embedded widgets have been
	 * created
	 *
	 * @var boolean
	 *
	 * @see SwatTimeZoneEntry::createEmbeddedWidgets()
	 */
	 */
	private $widgets_created = false;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new time-zone selector widget
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		static $area_whitelist = array('Africa', 'America', 'Antarctica',
			'Arctic', 'Asia', 'Atlantic', 'Australia', 'Europe',
			'Indian', 'Pacific');

		$time_zone_list = $this->parseAreaWhitelist($area_whitelist);
		$this->setAreas($time_zone_list);

		$this->addJavaScript('packages/swat/javascript/swat-cascade.js',
			Swat::PACKAGE_ID);
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this time-zone entry widget
	 *
	 * Outputs a cascading list of time-zones.
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		$this->createEmbeddedWidgets();

		$div_tag = new SwatHtmlTag('div');
		$div_tag->id = $this->id;
		$div_tag->class = $this->getCSSClassString();
		$div_tag->open();

		$this->areas_flydown->addOptionsByArray($this->areas);
		$this->areas_flydown->value = $this->getArea($this->value);
		$this->areas_flydown->display();

		$this->regions_flydown->options = $this->regions;
		$this->regions_flydown->value = $this->getRegion($this->value);
		$this->regions_flydown->display();

		$div_tag->close();
	}

	// }}}
	// {{{ public function process()

	/**
	 * Processes this time-zone entry widget
	 *
	 * If any validation type errors occur, an error message is attached to
	 * this entry widget.
	 */
	public function process()
	{
		parent::process();

		$this->createEmbeddedWidgets();

		$this->areas_flydown->process();
		$this->regions_flydown->process();

		if ($this->areas_flydown->value === null ||
			$this->regions_flydown->value === null)
			$this->value = null;
		else
			$this->value = $this->areas_flydown->value.'/'.
				$this->regions_flydown->value;

		if (!$this->required && $this->value === null) {
			return;

		} elseif ($this->value === null) {
			$message = Swat::_('The %s field is required.');
			$this->addMessage(new SwatMessage($message, SwatMessage::ERROR));
			
		} elseif (!isset($GLOBALS['_DATE_TIMEZONE_DATA'][$this->value])) {
			$message = Swat::_('The %s field is an invalid time-zone.');
			$this->addMessage(new SwatMessage($message, SwatMessage::ERROR));
			
		}
	}

	// }}}
	// {{{ public function getState()

	/**
	 * Gets the current state of this time-zone entry widget
	 *
	 * @return string the current state of this time-zone entry widget.
	 *
	 * @see SwatState::getState()
	 */
	public function getState()
	{
		return $this->value;
	}

	// }}}
	// {{{ public function setState()

	/**
	 * Sets the current state of this time-zone entry widget
	 *
	 * @param string $state the new state of this time-zone entry widget.
	 *
	 * @see SwatState::setState()
	 */
	public function setState($state)
	{
		$this->value = $state;
	}

	// }}}
	// {{{ public function getHtmlHeadEntrySet()

	/**
	 * Gets the SwatHtmlHeadEntry objects needed by this date entry 
	 *
	 * @return SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects needed by
	 *                               this date entry.
	 *
	 * @see SwatUIObject::getHtmlHeadEntrySet()
	 */
	public function getHtmlHeadEntrySet()
	{
		$set = parent::getHtmlHeadEntrySet();
		$this->createEmbeddedWidgets();

		$set->addEntrySet($this->areas_flydown->getHtmlHeadEntrySet());
		$set->addEntrySet($this->regions_flydown->getHtmlHeadEntrySet());

		return $set;
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this time zone entry
	 * widget 
	 *
	 * @return array the array of CSS classes that are applied to this time
	 *                zone entry widget.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-time-zone-entry');
		$classes = array_merge($classes, $this->classes);
		return $classes;
	}

	// }}}
	// {{{ private function createEmbeddedWidgets()

	/**
	 * Creates all internal widgets required for this time-zone entry
	 */
	private function createEmbeddedWidgets()
	{ 
		if (!$this->widgets_created) {
			$this->areas_flydown = new SwatFlydown($this->id.'_areas');
			$this->areas_flydown->addOptionsByArray($this->areas);
			$this->areas_flydown->show_blank = false;
			$this->areas_flydown->parent = $this;

			$this->regions_flydown =
				new SwatCascadeFlydown($this->id.'_regions');

			$this->regions_flydown->show_blank = true;
			$this->regions_flydown->blank_value = null;
			$this->regions_flydown->cascade_from = $this->areas_flydown;
			$this->regions_flydown->width = '15em';
			$this->regions_flydown->parent = $this;

			$this->widgets_created = true;
		}
	}

	// }}}
	// {{{ private function parseAreaWhitelist()

	/**
	 * Parses a whitelist of valid areas
	 *
	 * Filters the full list of areas down to a select list and returns a
	 * tree-structured array of areas, regions, and subregions.
	 *
	 * @param array area_whitelist an array of valid area names.
	 *
	 * @return array a tree-structured array of areas regions and subregions
	 *                inside the specified whitelist of areas.
	 */
	private function parseAreaWhitelist($area_whitelist)
	{
		$regions = array();

		foreach ($GLOBALS['_DATE_TIMEZONE_DATA'] as $key => $obj) {
			$tok = strtok($key, '/');
			$current = &$regions;

			if (!in_array($tok, $area_whitelist))
				continue;

			while ($tok !== false) {
				if (!isset($current[$tok]))
					$current[$tok] = array();

				$current = &$current[$tok];
				$tok = strtok('/');
			}
		}

		return $regions;
	}

	// }}}
	// {{{ private function setAreas()

	/**
	 * Sets areas
	 *
	 * Builds the class variable array $areas.
	 *
	 * @param array $time_zone_list a tree structured array of areas, regions,
	 *                               and subregions.
	 */
	private function setAreas($time_zone_list)
	{
		ksort($time_zone_list);

		foreach ($time_zone_list as $name => $subregions) {
			$this->areas[$name] = $name;
			$this->regions[$name] = array();

			$this->setRegions($subregions, $name);
		}
	}

	// }}}
	// {{{ private function setRegions()

	/**
	 * Sets regions
	 *
	 * Builds the class variable array $regions.
	 *
	 * @param array $time_zone_list a tree structured array of areas, regions,
	 *                               and subregions.
	 * @param string $area the region's area.
	 * @param string $prefix a list of parent regions appended to sub-regions.
	 */
	private function setRegions($time_zone_list, $area, $prefix = '')
	{
		ksort($time_zone_list);

		foreach ($time_zone_list as $name => $subregions) {

			if (count($subregions))
				$this->setRegions($subregions, $area, $prefix.$name.'/');
			else {
				$title = $prefix.$name;

				if (isset($GLOBALS['_DATE_TIMEZONE_DATA'][$area.'/'.$title]))
					$title.= ' ('.$GLOBALS['_DATE_TIMEZONE_DATA'][$area.'/'.$title]['shortname'].')';

				$this->regions[$area][] =
					new SwatOption($name, str_replace('_', ' ', $title));
			}
		}
	}

	// }}}
	// {{{ private function getArea()

	/**
	 * Gets an area from a time-zone identifier
	 *
	 * Returns the area part of a full time-zone.
	 *
	 * @param string $time_zone the time-zone identifier to get the area from.
	 *
	 * @return string an area name.
	 */
	private function getArea($time_zone)
	{
		if ($time_zone === null)
			return null;

		return substr($time_zone, 0, strpos($time_zone, '/'));
	}

	// }}}
	// {{{ private function getRegion()

	/**
	 * Gets a region from a time-zone identifier
	 *
	 * Returns the region part of a full time-zone.
	 *
	 * @param string $time_zone the time-zone identifier to get the
	 *                           region from.
	 *
	 * @return string a region name.
	 */
	private function getRegion($time_zone)
	{
		if ($time_zone === null)
			return null;

		return substr($time_zone, strpos($time_zone, '/') + 1);
	}

	// }}}
}

?>
