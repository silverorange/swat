<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatHtmlHeadEntry.php';
require_once 'Swat/SwatHtmlHeadEntrySet.php';
require_once 'Swat/SwatInlineScriptList.php';
require_once 'Swat/SwatCommentHtmlHeadEntry.php';
require_once 'Swat/SwatLinkHtmlHeadEntry.php';
require_once 'Swat/SwatJavaScriptHtmlHeadEntry.php';
require_once 'Swat/SwatStyleSheetHtmlHeadEntry.php';
require_once 'Swat/SwatOutputter.php';
require_once 'Swat/SwatYUI.php';

class SwatDisplayContext
{
	protected $scripts;
	protected $style_sheets;
	protected $links;
	protected $comments;
	protected $outputter;
	protected $inline_scripts;

	public function __construct()
	{
		$this->setOutputter(new SwatOutputter());
		$this->setScriptSet(new SwatHtmlHeadEntrySet());
		$this->setStyleSheetSet(new SwatHtmlHeadEntrySet());
		$this->setCommentSet(new SwatHtmlHeadEntrySet());
		$this->setLinkSet(new SwatHtmlHeadEntrySet());
		$this->setInlineScriptList(new SwatInlineScriptList());
	}

	// actions

	public function out($string)
	{
		$this->outputter->out($string);

		return $this;
	}

	public function addYUI()
	{
		$yui = new SwatYUI(func_get_args());
		$this->addStyleSheet($yui->getStyleSheets());
		$this->addScript($yui->getScripts());
		$this->addComment($yui->getComments());
	}

	public function addScript($script)
	{
		if ($script instanceof SwatHtmlHeadEntrySet) {
			$this->scripts->add(
				$script->getByType(
					'SwatJavaScriptHtmlHeadEntry'
				)
			);
		} else {
			if (is_string($script)) {
				$script = new SwatJavaScriptHtmlHeadEntry(
					$script
				);
			}

			if (!($script instanceof SwatJavaScriptHtmlHeadEntry)) {
				throw new InvalidArgumentException(
					'$script must be either a URI string, a '.
					'SwatJavaScriptHtmlHeadEntry object or a '.
					'SwatHtmlHeadEntrySet object.'
				);
			}

			$this->scripts->add($script);
		}

		return $this;
	}

	public function addStyleSheet($style_sheet)
	{
		if ($style_sheet instanceof SwatHtmlHeadEntrySet) {
			$this->style_sheets->add(
				$style_sheet->getByType(
					'SwatStyleSheetHtmlHeadEntry'
				)
			);
		} else {
			if (is_string($style_sheet)) {
				$style_sheet = new SwatStyleSheetHtmlHeadEntry($style_sheet);
			}

			if (!($style_sheet instanceof SwatStyleSheetHtmlHeadEntry)) {
				throw new InvalidArgumentException(
					'Style-sheet must be either a string, a '.
					'SwatStyleSheetHtmlHeadEntry object or a '.
					'SwatHtmlHeadEntrySet object.'
				);
			}

			$this->style_sheets->add($style_sheet);
		}

		return $this;
	}

	public function addInlineScript($string)
	{
		$this->inline_scripts->add((string)$string);
		return $this;
	}

	public function addLink($link)
	{
		if ($link instanceof SwatHtmlHeadEntrySet) {
			$this->links->add(
				$link->getByType(
					'SwatLinkHtmlHeadEntry'
				)
			);
		} else {
			if (!($link instanceof SwatLinkHtmlHeadEntry)) {
				throw new InvalidArgumentException(
					'Link must be a SwatLinkHtmlHeadEntry object or a '.
					'SwatHtmlHeadEntrySet object.'
				);
			}

			$this->links->add($link);
		}

		return $this;
	}

	public function addComment($comment)
	{
		if ($comment instanceof SwatHtmlHeadEntrySet) {
			$this->comments->add(
				$comment->getByType(
					'SwatCommentHtmlHeadEntry'
				)
			);
		} else {
			if (is_string($comment)) {
				$comment = new SwatCommentHtmlHeadEntry($comment);
			}

			if (!($comment instanceof SwatCommentHtmlHeadEntry)) {
				throw new InvalidArgumentException(
					'Comment must be a string, a SwatCommentHtmlHeadEntry '.
					'object or a SwatHtmlHeadEntrySet object.'
				);
			}

			$this->comments->add($comment);
		}

		return $this;
	}

	public function getStyleSheets()
	{
		return $this->style_sheets;
	}

	public function getScripts()
	{
		return $this->scripts;
	}

	public function getInlineScripts()
	{
		return $this->inline_scripts;
	}

	public function getComments()
	{
		return $this->comments;
	}

	public function getLinks()
	{
		return $this->links;
	}

	// setters

	public function setStyleSheetSet(SwatHtmlHeadEntrySet $set)
	{
		$this->style_sheets = $set;
		return $this;
	}

	public function setScriptSet(SwatHtmlHeadEntrySet $set)
	{
		$this->scripts = $set;
		return $this;
	}

	public function setLinkSet(SwatHtmlHeadEntrySet $set)
	{
		$this->links = $set;
		return $this;
	}

	public function setCommentSet(SwatHtmlHeadEntrySet $set)
	{
		$this->comments = $set;
		return $this;
	}

	public function setInlineScriptList(SwatInlineScriptList $list)
	{
		$this->inline_scripts = $list;
		return $this;
	}

	public function setOutputter(SwatOutputter $outputter)
	{
		$this->outputter = $outputter;
		return $this;
	}
}

?>
