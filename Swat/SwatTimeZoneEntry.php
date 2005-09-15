<?php

require_once 'Date.php';
require_once 'Swat/SwatFlydown.php';
require_once 'Swat/SwatCascadeFlydown.php';
require_once 'Swat/SwatState.php';

/**
 * A time-zone selection widget
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTimeZoneEntry extends SwatControl implements SwatState
{
	/**
	 * Time-zone identifier
	 *
	 * The id of the selected time-zone.
	 *
	 * @var string
	 */
	public $value = null;

	/**
	 * Whether this entry widget is required or not
	 *
	 * Must have a non-empty value when processed.
	 *
	 * @var boolean
	 */
	public $required = false;

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
	 * Creates a new time-zone selector widget
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$area_whitelist = array('Africa', 'America', 'Antarctica',
			'Arctic', 'Asia', 'Atlantic', 'Australia', 'Europe',
			'Indian', 'Pacific');

		$time_zone_list = $this->parseAreaWhitelist($area_whitelist);
		$this->setAreas($time_zone_list);

		$this->addJavaScript('swat/javascript/swat-cascade.js');
	}

	/**
	 * Displays this time-zone entry widget
	 *
	 * Outputs a cascading list of time-zones.
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		$areas = new SwatFlydown();
		$areas->addOptionsByArray($this->areas);
		$areas->show_blank = false;
		$areas->name = $this->id.'_areas';
		$areas->id = $this->id.'_areas';
		$areas->value = $this->getArea($this->value);
		$areas->display();

		$regions = new SwatCascadeFlydown();
		$regions->options = $this->regions;
		$regions->name = $this->id.'_regions';
		$regions->id = $this->id.'_regions';
		$regions->show_blank = true;
		$regions->cascade_from = $areas;
		$regions->width = '15em';
		$regions->value = $this->getRegion($this->value);
		$regions->display();
	}

	/**
	 * Processes this time-zone entry widget
	 *
	 * If any validation type errors occur, an error message is attached to
	 * this entry widget.
	 */
	public function process()
	{
		if (strlen($_POST[$this->id.'_areas']) || strlen($_POST[$this->id.'_regions']))
			$this->value = $_POST[$this->id.'_areas'].'/'.$_POST[$this->id.'_regions'];
		else
			$this->value = null;

		if (!$this->required && $this->value === null) {
			return;

		} elseif ($this->value === null) {
			$msg = Swat::_('The %s field is required.');
			$this->addMessage(new SwatMessage($msg, SwatMessage::ERROR));
			
		} elseif (!isset($GLOBALS['_DATE_TIMEZONE_DATA'][$this->value])) {
			$msg = Swat::_('The %s field is an invalid time-zone.');
			$this->addMessage(new SwatMessage($msg, SwatMessage::ERROR));
			
		}
	}
	
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

	/**
	 * Parses a whitelist of valid areas
	 *
	 * Filters the full list of areas down to a select list and returns a
	 * tree-structured array of areas, regions, and subregions.
	 *
	 * @param array area_whitelist an array of valid area names.
	 **/
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

	/**
	 * Sets areas
	 *
	 * Builds the class variable array $areas.
	 *
	 * @param $time_zone_list array a tree structured array of areas, regions,
	 * 		  and subregions
	 **/
	private function setAreas($time_zone_list)
	{
		ksort($time_zone_list);

		foreach ($time_zone_list as $name => $subregions) {
			$this->areas[$name] = $name;
			$this->regions[$name] = array();

			$this->setRegions($subregions, $name);
		}
	}

	/**
	 * Sets regions
	 *
	 * Builds the class variable array $regions.
	 *
	 * @param $time_zone_list array a tree structured array of areas, regions,
	 * 		  and subregions.
	 * @param $area string the region's area.
	 * @param $prefix string a list of parent regions appended to sub-regions.
	 **/
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
					new SwatFlydownOption($name, str_replace('_', ' ', $title));
			}
		}
	}

	/**
	 * Gets an area from a time-zone identifier
	 *
	 * Returns the area part of a full time-zone.
	 *
	 * @return string an area name.
	 **/
	private function getArea($time_zone)
	{
		if ($time_zone === null)
			return null;

		return substr($time_zone, 0, strpos($time_zone, '/'));
	}

	/**
	 * Gets a region from a time-zone identifier
	 *
	 * Returns the region part of a full time-zone.
	 *
	 * @return string a region name.
	 **/
	private function getRegion($time_zone)
	{
		if ($time_zone === null)
			return null;

		return substr($time_zone, strpos($time_zone, '/') + 1);
	}
}

?>
