<?php

require_once 'DemoPage.php';
require_once 'Swat/SwatString.php';
require_once '../include/DemoMenu.php';

/**
 * Several SwatString tests
 *
 * This tests the various static methods of SwatString and displays the test
 * results in a SwatContentBlock.
 *
 * @package   SwatDemo
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class StringDemo extends DemoPage
{
	private $strings = array(
		'Suspendisse potenti. Cras varius diam. Fusce mollis pharetra sapien. Curabitur vel tellus vel nisi luctus tempus.',
		'Nullam consequat metus porttitor libero. Integer rhoncus. Phasellus tortor.',
		'Quisque quis nulla.',
		'Nullam tempus pede a neque. Aliquam ligula. Nullam ligula. In tempus blandit ipsum. Integer sit amet lacus.',
		'Donec laoreet, tellus non tempor cursus, justo augue laoreet sapien, vitae facilisis tortor tellus ut augue.',
		'Pellentesque iaculis egestas nibh.',
		'Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae;');
	
	private $text_block = '';

	public function initUI()
	{
		$this->text_block = "<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</p>".
			"<blockquote>Etiam aliquet tincidunt augue. Cras dui. Quisque tincidunt pede vitae lorem.</blockquote>".
			"<p>Aenean semper wisi sed mi. Integer fermentum magna non magna laoreet faucibus. ".
			"Aenean molestie auctor ante. Donec vitae neque ut tellus tincidunt bibendum. ".
			"Donec sit amet sem. In elementum tellus consectetuer turpis. ".
			"Nam velit erat, pharetra vel, sollicitudin at, gravida eget, est. ".
			"Etiam risus tortor, scelerisque in, consectetuer et, mollis a, leo. Donec auctor.</p><p>".
			"Mauris tellus. Quisque sit amet nulla. Fusce vitae eros eu nunc volutpat aliquet. ".
			"Donec nibh. Donec ac libero. Etiam dictum. Cras fringilla nunc at justo. ".
			"Vestibulum quis magna eu nisl congue volutpat. Ut facilisis lobortis lacus. ".
			"Nullam non urna at elit malesuada dictum. Integer quis ligula. ".
			"Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</p><p>".
			"Vivamus metus ligula, varius sodales, dictum in, posuere sagittis, nisl. ".
			"Suspendisse potenti. Nulla non mauris id tortor eleifend auctor. Nullam mattis odio ac diam. ".
			"Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. ".
			"Aliquam ultrices mattis nisl. ".
			"Nullam diam metus, vestibulum sit amet, lacinia at, condimentum at, dolor. ".
			"Proin sagittis. Cras a sem et erat porta placerat. Sed auctor dui pellentesque ante. ".
			"Morbi pulvinar, tellus id sollicitudin dictum, augue massa molestie ligula, ".
			"sit amet pulvinar mauris neque nec quam.</p>";

		$content_block = $this->ui->getWidget('string_demos');

		ob_start();
		$this->testAll();
		$content_block->content = ob_get_clean();
	}
	
	protected function createLayout()
	{
		return new SwatLayout('../layouts/no_source.php');
	}

	private function testAll()
	{
		$this->testEllipsizeRight(20);
		$this->testEllipsizeMiddle(25);
		$this->testCondense();
	}

	private function testEllipsizeRight($length = 20)
	{
		echo '<h4>Right Ellipsize at '.$length.' Characters</h4>';

		echo '<ol class="string-demo">';
		foreach($this->strings as $string) {
			echo '<li>';
			echo '<div>'.$string.'</div>';
			echo '<div>'.SwatString::ellipsizeRight($string, $length).'</div>';
			echo '</li>';
		}
		echo '</ol>';
	}

	private function testEllipsizeMiddle($length = 20)
	{
		echo '<h4>Middle Ellipsize at '.$length.' Characters</h4>';

		echo '<ol class="string-demo">';
		foreach($this->strings as $string) {
			echo '<li>';
			echo '<div>'.$string.'</div>';
			echo '<div>'.SwatString::ellipsizeMiddle($string, $length).'</div>';
			echo '</li>';
		}
		echo '</ol>';
	}

	private function testCondense()
	{
		echo '<h4>Condense Text at 200 Characters</h4>';

		echo '<h5>Uncondensed Text:</h5>';
		echo '<div class="text-block">'.$this->text_block.'</div>';

		$text_block = SwatString::condense($this->text_block, 200);

		echo '<h5>Condensed Text:</h5>';
		echo '<div class="text-block">'.$text_block.'</div>';
	}
}

?>
