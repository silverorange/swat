<?php

require_once 'Swat/exceptions/SwatException.php';
require_once 'Swat/SwatTreeNode.php';
require_once 'Swat/SwatFlydownOption.php';

/**
 * A tree node for a flydown
 *
 * Contains a flydown option that has a value and a title.
 *
 * @package   Swat
 * @copyright 2005 silverorange
 * @license
 */
class SwatTreeFlydownNode extends SwatTreeNode
{
	/**
	 * The flydown option for this node
	 *
	 * @var SwatFlydownOption
	 */
	protected $flydown_option;

	/**
	 * Creates a new tree flydown node
	 *
	 * This method is overloaded to accept either a value-title pair or a new
	 * {@link SwatFlydownOption} object. Example usage:
	 *
	 * <code>
	 * // using an already existing flydown option
	 * $option = new SwatFlydownOption(1, 'Apples');
	 * $node1 = new SwatTreeFlydownNode($option);
	 *
	 * // creating a new flydown option
	 * $node2 = new SwatTreeFlydown(2, 'Oranges');
	 * </code>
	 *
	 * @param mixed $param1 either a {@link SwatFlydownOption} object or an
	 *                       integer or string representing the value of a new
	 *                       flydown option.
	 * @param mixed $param2 if a SwatFlydownOption object is passed in for
	 *                       parameter one, this parameter must be ommitted.
	 *                       Otherwise, this is a string title for a new
	 *                       flydown option.
	 *
	 * @throws SwatException
	 */
	public function __construct($param1, $param2 = null)
	{
		if ($param2 === null && $param1 instanceof SwatFlydownOption)
			$this->flydown_option = $param1;
		elseif ($param2 === null)
			throw new SwatException('First parameter must be a '.
				'SwatFlydownOption or second parameter must be specified.');
		else
			$this->flydown_option = new SwatFlydownOption($param1, $param2);
	}

	/**
	 * Gets the flydown option for this node
	 *
	 * @return SwatFlydownOption the flydown option for this node.
	 */
	public function getFlydownOption()
	{
		return $this->flydown_option;
	}
}

?>
