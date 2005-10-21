<?php

require_once 'Swat/SwatFlydown.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatState.php';

/**
 * A radio list selection widget
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatRadioList extends SwatFlydown implements SwatState
{
	/**
	 * Creates a new radiolist
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct();
		$this->show_blank=false;
	}
	
	/**
	 * Displays this radio list
	 */
	public function display()
	{
		if (!$this->visible)
			return;
		$options = $this->getOptions();
		
		// Empty string XHTML option value is assumed to be null
		// when processing.
		if ($this->show_blank)
			$options = array_merge(
				array(new SwatFlydownOption(0, $this->blank_title)),
				$options);
		
		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'radio';
		$input_tag->name = $this->id;
		
		if ($this->onchange !== null)
			$input_tag->onchange = $this->onchange;

		$label_tag = new SwatHtmlTag('label');
		$label_tag->class = 'swat-control';

		if (count($options)) {
			foreach ($options as $flydown_option) {	
				if ($flydown_option instanceof SwatFlydownDivider) {
					//ignore these for now TODO: make dividers work with radiolists
				} else {
					$input_tag->value = (string)$flydown_option->value;
					$input_tag->removeAttribute('checked');
	
					if ((string)$this->value === (string)$flydown_option->value)
						$input_tag->checked = "checked";
	
					$input_tag->id = $this->id.'_'.$input_tag->value;
					$input_tag->display();
	
					$label_tag->for = $this->id.'_'.$input_tag->value;
					$label_tag->content = $flydown_option->title;
	
					$label_tag->display();
				
					echo '<br />';
				}
			}
		}
	}
}

?>
