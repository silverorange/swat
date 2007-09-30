/**
 * Rating control for Swat
 *
 * Copyright (c) 2007 silverorange
 *
 *  Swat is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU Lesser General Public
 *  License as published by the Free Software Foundation; either
 *  version 2.1 of the License, or (at your option) any later version.
 *
 *  This library is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 *  Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU Lesser General Public
 *  License along with this library; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin Street, Fifth Floor,
 *  Boston, MA  02110-1301  USA
 *
 * This file incorporates work covered by the following copyright and
 * permission notices:
 *
 *     Copyright (c) 2007 Ville Säävuori <Ville@Unessa.net>
 *        http://www.unessa.net/en/hoyci/projects/yui-star-rating/
 *
 *     Copyright (c) 2006 Wil Stuckeys
 *        http://sandbox.wilstuckey.com/jquery-ratings/
 *
 *    Permission is hereby granted, free of charge, to any person
 *    obtaining a copy of this software and associated documentation
 *    files (the "Software"), to deal in the Software without
 *    restriction, including without limitation the rights to use,
 *    copy, modify, merge, publish, distribute, sublicense, and/or sell
 *    copies of the Software, and to permit persons to whom the
 *    Software is furnished to do so, subject to the following
 *    conditions:
 *
 *    The above copyright notice and this permission notice shall be
 *    included in all copies or substantial portions of the Software.
 *
 *    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 *    EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 *    OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 *    NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 *    HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 *    WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 *    FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 *    OTHER DEALINGS IN THE SOFTWARE.
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

	if (this.flydown.value === clicked_star.toString()) {
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
