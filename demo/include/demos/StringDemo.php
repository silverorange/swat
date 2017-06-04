<?php

require_once 'Demo.php';

/**
 * Several SwatString tests
 *
 * This tests the various static methods of SwatString and displays the test
 * results in a SwatContentBlock.
 *
 * @package   SwatDemo
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class StringDemo extends Demo
{
	// {{{ private properties

	private $strings = array(
		'Suspendisse potenti. Cras varius diam. Fusce mollis pharetra sapien. Curabitur vel tellus vel nisi luctus tempus.',
		'Nullam consequat metus porttitor libero. Integer rhoncus. Phasellus tortor.',
		'Quisque quis nulla.',
		'Vu bei Eisen p&auml;ift Keppchen, R&auml;is Blieder da dem, en n&euml;t ma\'n d\'Gaassen.',
		'Br&eacute;t Dall Schiet hu n&euml;t, um w&auml;it onser hirem get, si Dall gemaacht Fletschen bei.',
		'Pellentesque iaculis egestas nibh.',
		'Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae;',
		'Hello, world.',
		'Foobar',
		'麻生十番',
	);

	private $text_blocks = array();

	private $unformatted_text_blocks = array();

	// }}}
	// {{{ public function buildDemoUI()

	public function buildDemoUI(SwatUI $ui)
	{
		$this->text_blocks[] = "<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</p>".
			"<blockquote><p>Etiam aliquet tincidunt augue. Cras dui. Quisque tincidunt pede vitae lorem.</p></blockquote>".
			"<p>Aenean semper wisi sed mi. Integer fermentum magna non magna laoreet faucibus. Aenean molestie auctor ante. Donec vitae neque ut tellus tincidunt bibendum. Donec sit amet sem. In elementum tellus consectetuer turpis. Nam velit erat, pharetra vel, sollicitudin at, gravida eget, est. Etiam risus tortor, scelerisque in, consectetuer et, mollis a, leo. Donec auctor.</p>".
			"<p>Mauris tellus. Quisque sit amet nulla. Fusce vitae eros eu nunc volutpat aliquet. Donec nibh. Donec ac libero. Etiam dictum. Cras fringilla nunc at justo. Vestibulum quis magna eu nisl congue volutpat. Ut facilisis lobortis lacus. Nullam non urna at elit malesuada dictum. Integer quis ligula. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</p>".
			"<p>Vivamus metus ligula, varius sodales, dictum in, posuere sagittis, nisl. Suspendisse potenti. Nulla non mauris id tortor eleifend auctor. Nullam mattis odio ac diam. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Aliquam ultrices mattis nisl. Nullam diam metus, vestibulum sit amet, lacinia at, condimentum at, dolor.</p>";

		$this->text_blocks[] = "<p>Br&eacute;t Dall Schiet hu n&euml;t, um w&auml;it onser hirem get, si Dall gemaacht Fletschen bei.</p>".
			"<p>Dat d&eacute; Mier grouss, all Mamm Dauschen da. Am nun Dall Minutt löschteg, k&eacute; gebotzt Fr&eacute;ijor d&eacute;n, blo fort Plett'len et. Ze bl&euml;tzen Fletschen och, oft en s&euml;tzen Schuebersonndeg erem, Mamm Schied aus do. Ech am gudden gebotzt, wa Eisen pr&auml;chteg hir, dir m&eacute;ngem H&eacute;mecht Keppchen un.</p>".
			"<p>Vu bei Eisen p&auml;ift Keppchen, R&auml;is Blieder da dem, en n&euml;t ma'n d'Gaassen. D'Wise d'Hierz an ech, ech k&eacute; iech st&eacute;t riede. Blo do Dohannen d'K&agrave;chen schn&eacute;iw&auml;iss. Hu d'W&eacute;&euml;n bleiwe get. Sou vill Blummen vu, m&auml; eng H&auml;ren d'Wise S&auml;iten, si w&eacute;i sch&eacute;inste Margr&eacute;itchen. Halm spilt m&eacute;ngem wat ke.</p>".
			"<p>D'Pied grousse ons wa, oft g&euml;tt schl&eacute;it bl&eacute;nken si, d'Blumme gewalteg mat ze. An drun Milliounen sin, d&eacute; hier w&auml;it esou r&euml;m. Op vun koum derfir klinzecht, en dir wuel spilt schaddreg. Dem fu genuch Schuebersonndeg bleiwe, d'Mier Fletschen wa all. Mamm meescht Dohannen d&eacute;n de, de denkt Kl&eacute;der d&eacute;n, wat aremt ugedon löschteg da. Vu get m&eacute;ngem d'Loft.</p>".
			"<p>Hie zielen d'Pied d'Kam&auml;iner de. D&eacute; Duerf duerch w&auml;r, main d'Kanner dan fu. Um z&euml;nne ruffen m&eacute;i. Sinn Noper mat ke, g&eacute;t en Ierd ruffen. W&auml;r brommt d'w&auml;iss Faarwen op.</p>".
			"<p>D'Vioule Minutt Schuebersonndeg r&euml;m um, op Gaas zw&euml;schen mat. Eise Engel j&eacute;ngt sin am, k&eacute; fond gesiess heemlech m&eacute;i. D'Gaassen Nuechtegall schn&eacute;iw&auml;iss fu ass, Noper r&euml;schten wee k&eacute;, ze d'B&euml;scher Schuebersonndeg gemaacht n&euml;t. Fest fr&euml;sch och hu. Iech Wand wielen et all, d&eacute; vun geet hannendrun.</p>";

		$this->unformatted_text_blocks[] = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit.\n\n".
			"<blockquote><p>Etiam aliquet tincidunt augue. Cras dui. Quisque tincidunt pede vitae lorem.</p></blockquote>\n\n".
			"Aenean semper wisi sed mi. Integer fermentum magna non magna laoreet faucibus. Aenean molestie auctor ante. Donec vitae neque ut tellus tincidunt bibendum. Donec sit amet sem. In elementum tellus consectetuer turpis. Nam velit erat, pharetra vel, sollicitudin at, gravida eget, est. Etiam risus tortor, scelerisque in, consectetuer et, mollis a, leo. Donec auctor.\n\n\n".
			"<strong>Mauris tellus.</strong>Quisque sit amet nulla. Fusce vitae eros eu nunc volutpat aliquet. Donec nibh. Donec ac libero. Etiam dictum. Cras fringilla nunc at justo. Vestibulum quis magna eu nisl congue volutpat. Ut <em>facilisis lobortis</em> lacus. Nullam non urna at elit malesuada dictum. Integer quis ligula. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.\n\n".
			"Vivamus metus ligula, varius sodales, dictum in, posuere sagittis, nisl.\nSuspendisse potenti. Nulla non mauris id tortor eleifend auctor.<br />Nullam mattis odio ac diam. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Aliquam ultrices mattis nisl. Nullam diam metus, vestibulum sit amet, lacinia at, condimentum at, dolor.";

		$right_ellipsize = $ui->getWidget('right_ellipsize');
		ob_start();
		$this->testEllipsizeRight(20);
		$right_ellipsize->content = ob_get_clean();

		$middle_ellipsize = $ui->getWidget('middle_ellipsize');
		ob_start();
		$this->testEllipsizeMiddle(25);
		$middle_ellipsize->content = ob_get_clean();

		$condense = $ui->getWidget('condense');
		ob_start();
		$this->testCondense();
		$condense->content = ob_get_clean();

		$condense_to_name = $ui->getWidget('condense_to_name');
		ob_start();
		$this->testCondenseToName();
		$condense_to_name->content = ob_get_clean();

		$to_xhtml = $ui->getWidget('to_xhtml');
		ob_start();
		$this->testToXHTML();
		$to_xhtml->content = ob_get_clean();
	}

	// }}}
	// {{{ protected function createLayout()

	protected function createLayout()
	{
		return new SiteLayout($this->app,
			'../include/layouts/xhtml/no-source.php');
	}

	// }}}
	// {{{ private function testEllipsizeRight()

	private function testEllipsizeRight($length = 20)
	{
		echo '<ol class="string-demo">';
		foreach($this->strings as $string) {
			echo '<li>';
			echo '<div>'.$string.'</div>';
			echo '<div>'.SwatString::ellipsizeRight($string, $length).'</div>';
			echo '</li>';
		}
		echo '</ol>';
	}

	// }}}
	// {{{ private function testEllipsizeMIddle()

	private function testEllipsizeMiddle($length = 20)
	{
		echo '<ol class="string-demo">';
		foreach($this->strings as $string) {
			echo '<li>';
			echo '<div>'.$string.'</div>';
			echo '<div>'.SwatString::ellipsizeMiddle($string, $length).'</div>';
			echo '</li>';
		}
		echo '</ol>';
	}

	// }}}
	// {{{ private function testCondense()

	private function testCondense()
	{
		foreach ($this->text_blocks as $text_block) {
			echo '<h5>Uncondensed Text:</h5>';
			echo '<div class="text-block">'.$text_block.'</div>';

			$condensed_text_block = SwatString::condense($text_block, 200);

			echo '<h5>Condensed Text:</h5>';
			echo '<div class="text-block">'.$condensed_text_block.'</div>';
		}
	}

	// }}}
	// {{{ private function testCondenseToName()

	private function testCondenseToName()
	{
		echo '<p>Condense to name can be used to condense a title into a name identifier.</p>';

		echo '<ol class="string-demo">';
		foreach($this->strings as $string) {
			echo '<li>';
			echo '<div>'.$string.'</div>';
			echo '<div>'.SwatString::condenseToName($string).'</div>';
			echo '</li>';
		}
		echo '</ol>';
	}

	// }}}
	// {{{ private function testToXHTML()

	private function testToXHTML()
	{
		foreach ($this->unformatted_text_blocks as $text_block) {
			echo '<h5>Plain Text:</h5>';
			echo '<div class="string-unformatted-text">'.
				nl2br(htmlspecialchars($text_block, ENT_COMPAT, 'UTF-8')).'</div>';

			$xhtml_text_block = SwatString::toXHTML($text_block);

			echo '<h5>XHTML Text:</h5>';
			echo '<div class="string-unformatted-text">'.
				nl2br(htmlspecialchars($xhtml_text_block, ENT_COMPAT, 'UTF-8')).'</div>';
		}
	}

	// }}}
}

?>
