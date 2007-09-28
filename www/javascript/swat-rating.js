/**
 * Rating control for Swat
 *
 * Based on the AJAXy Star-rating Script For Yahoo! UI Library (YUI)
 *
 * By Ville Säävuori <Ville@Unessa.net>
 * http://www.unessa.net/en/hoyci/projects/yui-star-rating/
 *
 * In turn, based loosely on Wil Stuckeys jQuery Star Rating Plugin:
 * http://sandbox.wilstuckey.com/jquery-ratings/
 *
 * Respecting the original licence, this script is also
 * dual licensed under the MIT and GPL licenses:
 *
 *  - http://www.opensource.org/licenses/mit-license.php
 *  - http://www.gnu.org/licenses/gpl.html
 *
 * Adapted for Swat by silverorange
 *
 * Adaptations copyright 2007 silverorange. As permitted by the MIT license,
 * adaptations are licensed under the LGPL License 2.1:
 *
 *  - http://www.gnu.org/copyleft/lesser.html
 */

function SwatRating(id)
{
	this.id = id;
	this.flydown = document.getElementById(this.id);
	this.stardiv = document.createElement('div');
	this.ratingdiv = document.getElementById(this.id + '_rating_div');

	this.setupStyles();
}

SwatRating.prototype.setupStyles = function()
{
	YAHOO.util.Dom.setStyle(this.flydown, 'display', 'none');
	YAHOO.util.Dom.addClass(this.stardiv, 'rating');

	// make the stars
	for (var i = 1; i <= 4; i++) {
		// first, make a div and then an a-element in it
		var star = document.createElement('div');
		star.id = this.id + '_star' + i;
		var a = document.createElement('a');
		a.href = '#' + i;
		a.innerHTML = i;
		YAHOO.util.Dom.addClass(star, 'star');
		star.appendChild(a);
		this.stardiv.appendChild(star);

		// add needed listeners to every star
		YAHOO.util.Event.addListener(star, 'mouseover', this.handleFocus, i, this);
		YAHOO.util.Event.addListener(star, 'mouseout', this.handleBlur, this, true);
		YAHOO.util.Event.addListener(star, 'click', this.handleClick, i, this);
	}

	this.ratingdiv.appendChild(this.stardiv);

	for (var i = 1; i <= parseInt(this.flydown.value); i++) {
		var star = YAHOO.util.Dom.get(this.id + '_star' + i);
		var a = star.firstChild;
		YAHOO.util.Dom.addClass(star, 'on');
	}
}

SwatRating.prototype.handleFocus = function(event, focus_star)
{
	// code to handle the focus on the star
	for (var i = 1; i <= focus_star; i++) {
		var star = YAHOO.util.Dom.get(this.id + '_star' + i);
		var a = star.firstChild;
		YAHOO.util.Dom.addClass(star, 'hover');
	}
}

SwatRating.prototype.handleBlur = function(event)
{
	// code to handle movement away from the star
	for (var i = 1; i <= 4; i++) {
		var star = YAHOO.util.Dom.get(this.id + '_star' + i);
		YAHOO.util.Dom.removeClass(star, 'hover');
	}
}

SwatRating.prototype.handleClick = function(event, clicked_star)
{
	// this resets the on style for each star
	for (var i = 1; i <= 4; i++) {
		var star = YAHOO.util.Dom.get(this.id + '_star' + i);
		var a = star.firstChild;
		YAHOO.util.Dom.removeClass(star, 'on');
	}

	if (this.flydown.value === clicked_star.toString()){
		this.flydown.value = null;
		for (var i = 1; i <= 4; i++) {
			var star = YAHOO.util.Dom.get(this.id + '_star' + i);
			var a = star.firstChild;
			YAHOO.util.Dom.removeClass(star, 'hover');
		}
		return;
	}

	// this will set the current value of the flydown
	for (var i = 0; i < this.flydown.childNodes.length; i++) {
		var option = this.flydown.childNodes[i];
		if (option.value == clicked_star.toString()) {
			this.flydown.value = clicked_star;
			break;
		}
	}

	// cycle trought 1..5 stars
	for (var i = 1; i <= clicked_star; i++) {
		var star = YAHOO.util.Dom.get(this.id + '_star' + i);
		var a = star.firstChild;
		YAHOO.util.Dom.addClass(star, 'on');
	}
}
