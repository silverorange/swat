<?php

require_once 'DemoPage.php';
require_once 'Swat/SwatDate.php';

/**
 * A demo using a details view
 *
 * @package   SwatDemo
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class DetailsView extends DemoPage
{
	// {{{ public function initUI()

	public function initUI()
	{
		$details_view = $this->ui->getWidget('details_view');
        
		$fruit = new FruitObject();
     
		$fruit->align        = 'middle';
		$fruit->title        = 'Apple';   
		$fruit->image        = 'images/apple.png';
		$fruit->image_width  = 28;
		$fruit->image_height = 28;
		$fruit->makes_jam    = true;
		$fruit->makes_pie    = true;
		$fruit->harvest_date = '2005-10-31';
		$fruit->cost         = .5;
		$fruit->text         =
			"Curabitur semper risus non turpis accumsan luctus. Nam libero ".
			"erat, consequat sed, commodo quis, mollis eu, ligula. Curabitur ".
			"imperdiet ultricies nisl. Suspendisse ac justo nec nisl ".
			"sollicitudin vestibulum. Curabitur luctus pharetra mi. Mauris ".
			"gravida luctus ante. Donec bibendum massa a risus. Sed et quam. ".
			"Nam eu libero vel arcu semper placerat. Proin tristique quam at ".
			"est. Donec vel sem.";

		$details_view->data = $fruit;
	}

	// }}}
}

/**
 * A demo using a details view
 *
 * @package   SwatDemo
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class FruitObject
{
	// {{{ public properties

	public $align = '';
	public $image = '';
	public $image_width = 0;
	public $image_height = 0;
	public $title = '';
	public $color = '';
	public $makes_jam = false;
	public $makes_pie = false;
	public $harvest_date = null;
	public $cost = 0;
	public $text = '';

	// }}}
}

?>
