<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */

/**
 * A an extra row containing a "check all" tool.
 */
class SwatCheckAll extends SwatControl {
	public $series_name = null;	
	public $title;

	public function init() {
		$this->title = _S('Check All');
		$this->generateAutoName();
	}

	public function display() {
		if ($this->series_name == null)
			throw new SwatException('SwatCheckall: A series '.
				'name referencing the series of checkboxes '.
				'to apply to must be defined.');

		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'checkbox';
		$input_tag->id = $this->name;
		$input_tag->onclick = "swatCheckAll(this.form, '{$this->name}', '{$this->series_name}')";

		$label_tag = new SwatHtmlTag('label');
		$label_tag->for = $this->name;

		$label_tag->open();
		$input_tag->display();
		echo $this->title;
		$label_tag->close();
		$this->displayJavascript();

	}

	private function displayJavascript() {
		?>
		<script type="text/javascript" language="JavaScript">	
			function swatCheckAll(form, name, series) {
				check_all = document.getElementById(name);

				for (i = 0; i < form.elements[series + '[]'].length; i++) {
                    var chkbox = form.elements[series + '[]'][i];
                    if (check_all && chkbox.type=='checkbox') {
                        chkbox.checked = check_all.checked;
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
