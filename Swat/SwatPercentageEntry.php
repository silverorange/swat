<?php

require_once 'Swat/SwatEntry.php';

class SwatPercentageEntry extends SwatFloatEntry
{


	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->size = 5;
	}

	public function process()
	{
		
		parent::process();

		if (($this->value >= 0) and ($this->value <= 100))
			$this->value = $this->value / 100;
		else {
			$message = Swat::_('Please use a number between 0 and 100');
			$this->addMessage(new SwatMessage($message, SwatMessage::ERROR));
		}
		$this->value = $this->value;
	}

	protected function getDisplayValue()
	{
		if (is_float($this->value) and ($this->value >= 0) and ($this->value <= 100))
			return ($this->value * 100).'%';
	}
	protected function getNumericValue()
	{
		$value = trim($this->value);
		$value = str_replace('%','',$this->value);
		return SwatString::toFloat($value);
	}

}
?>	
