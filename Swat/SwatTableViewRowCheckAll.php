<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatTableViewRow.php');

/**
 * A an extra row containing a "check all" tool.
 */
class SwatTableViewRowCheckAll extends SwatTableViewRow {
	
	public function display(&$columns) {
		echo '<tr>';

		foreach ($columns as $column) {
			$count = 0;

			if ($column->name == 'checkbox') {
				$td_tag = new SwatHtmlTag('td');
				$td_tag->colspan = count($columns) - $count;

				$input_tag = new SwatHtmlTag('input');
				$input_tag->type = 'checkbox';
				$input_tag->name = 'check_all';
				$input_tag->onclick = 'checkAll(this.form, \'items\')';

				$label_tag = new SwatHtmlTag('label');
				$label_tag->for = 'check_all';

				$td_tag->open();
				$this->displayJavascript();
				$label_tag->open();
				$input_tag->display();
				echo _S('Check All');
				$label_tag->close();
				$td_tag->close();

				break;

			} else {
				$count++;
				echo '<td>&nbsp;</td>';
			}
		}

		echo '</tr>';
	}

	private function displayJavascript() {
		?>
		<script type="text/javascript" language="JavaScript">	
			function checkAll(my_form, id) {
				for (i = 0; i < my_form.elements['items[]'].length; i++) {
                    var chkbox = my_form.elements['items[]'][i];
                    if (my_form.check_all && chkbox.type=='checkbox') {
                        chkbox.checked = my_form.check_all.checked;
                        //TODO: make the highlighting work once we sort it out
						//if (theForm.chkall.checked) HLClass(chkbox,"highlight");
                        //else HLClass(chkbox,"");
 					}
				 }
			}
			//TODO: add javascript to set check_all = true/false if all
			// checkboxes are checked. We need to figure out how to best
			// add this to the onclick event of the checkboxes on the page
		</script>
		<?
	}

}
?>
