<?php

require_once 'Swat/SwatCellRenderer.php';

/**
 * A cell renderer for a boolean value
 *
 * @package   Swat
 * @copyright 2004-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatBooleanCellRenderer extends SwatCellRenderer
{
	/**
	 * Value of this cell
	 *
	 * The boolean value to display in this cell.
	 *
	 * @var boolean
	 */
	public $value;

	/**
	 * Optional content to display for a true value.
	 *
	 * @var string
	 */
	public $true_content = null;

	/**
	 * Optional content to display for a false value.
	 *
	 * @var string
	 */
	public $false_content = null;

	/**
	 * Optional content type
	 *
	 * Default text/plain, use text/xml for XHTML fragments.
	 *
	 * @var string
	 */
	public $content_type = null;

	/**
	 * The stock id of this SwatBooleanCellRenderer
	 *
	 * Specifying a stock id initializes this boolean cell renderer with a set of
	 * stock values.
	 *
	 * @var string
	 *
	 * @see SwatBooleanCellRenderer::setFromStock()
	 */
	public $stock_id = null;

	/**
	 * Sets the values of this boolean cell renderer to a stock type
	 *
	 * Valid stock type ids are:
	 *
	 * - check-only
	 * - yes-no
	 *
	 * @param string $stock_id the identifier of the stock type to use.
	 * @param boolean $overwrite_properties whether to overwrite properties if
	 *                                       they are already set.
	 *
	 * @throws SwatUndefinedStockTypeException
	 */
	public function setFromStock($stock_id, $overwrite_properties = true)
	{
		$content_type = 'text/plain';

		switch ($stock_id) {
		case 'yes-no':
			$false_content = 'No';
			$true_content = 'Yes';
			break;

		case 'check-only':
			$content_type = 'text/xml';
			$false_content = '&nbsp;';

			ob_start();
			$this->displayCheck();
			$true_content = ob_get_clean();
			break;

		default:
			throw new SwatUndefinedStockTypeException(
				"Stock type with id of '{$stock_id}' not found.",
				0, $stock_id);
		}

		if ($overwrite_properties || ($this->false_content === null))
			$this->false_content = $false_content;
		
		if ($overwrite_properties || ($this->true_content === null))
			$this->true_content = $true_content;

		if ($overwrite_properties || ($this->content_type === null))
			$this->content_type = $content_type;
	}

	/**
	 * Renders the contents of this cell
	 *
	 * @see SwatCellRenderer::render()
	 */
	public function render()
	{
		if ($this->stock_id === null)
			$this->setFromStock('check-only', false);
		else
			$this->setFromStock($this->stock_id, false);

		if ($this->content_type = null)
			$this->content_type = 'text/plain';

		if ((boolean)$this->value)
			$this->renderTrue();
		else
			$this->renderFalse();
	}

	protected function renderTrue()
	{
		if ($this->content_type === 'text/plain')
			echo SwatString::minimizeEntities($this->true_content);
		else
			echo $this->true_content;
	}

	protected function renderFalse()
	{
		if ($this->content_type === 'text/plain')
			echo SwatString::minimizeEntities($this->false_content);
		else
			echo $this->false_content;
	}

	protected function displayCheck()
	{
		$image_tag = new SwatHtmlTag('img');
		$image_tag->src = 'swat/images/check.png';
		$image_tag->alt = Swat::_('Yes');
		$image_tag->height = '14';
		$image_tag->width = '14';
		$image_tag->display();
	}
}

?>
