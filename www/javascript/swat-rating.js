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

function SwatRating(id, max_value)
{
	this.id        = id;
	this.max_value = max_value;

	this.flydown    = document.getElementById(this.id + '_flydown');
	this.rating_div = document.getElementById(this.id);
	this.star_div   = document.createElement('div');

	YAHOO.util.Event.onDOMReady(this.init, this, true);
}

SwatRating.prototype.init = function()
{
	var Dom   = YAHOO.util.Dom;
	var Event = YAHOO.util.Event;

	Dom.setStyle(this.flydown, 'display', 'none');

	for (var i = 1; i <= this.max_value; i++) {
		var star = document.createElement('div');
		star.id  = this.id + '_star' + i;

		Dom.addClass(star, 'swat-rating-star');
		if (i <= parseInt(this.flydown.value)) {
			Dom.addClass(star, 'swat-rating-selected');
		}

		var anchor  = document.createElement('a');
		anchor.href = '#';

		star.appendChild(anchor);
		this.star_div.appendChild(star);

		Event.on(star, 'mouseover', this.handleFocus, i, this);
		Event.on(star, 'mouseout', this.handleBlur, this, true);
		Event.on(star, 'click', this.handleClick, i, this);
	}

	var clear = document.createElement('div');
	Dom.setStyle(clear, 'clear', 'both');

	this.rating_div.appendChild(this.star_div);
	this.rating_div.appendChild(clear);
}

SwatRating.prototype.handleFocus = function(event, focus_star)
{
	var Dom   = YAHOO.util.Dom;
	var Event = YAHOO.util.Event;

	Event.preventDefault(event);

	// code to handle the focus on the star
	for (var i = 1; i <= focus_star; i++) {
		Dom.addClass(Dom.get(this.id + '_star' + i), 'swat-rating-hover');
	}
}

SwatRating.prototype.handleBlur = function(event)
{
	var Dom   = YAHOO.util.Dom;
	var Event = YAHOO.util.Event;

	Event.preventDefault(event);

	// code to handle movement away from the star
	for (var i = 1; i <= this.max_value; i++) {
		Dom.removeClass(Dom.get(this.id + '_star' + i), 'swat-rating-hover');
	}
}

SwatRating.prototype.handleClick = function(event, clicked_star)
{
	var Dom   = YAHOO.util.Dom;
	var Event = YAHOO.util.Event;

	Event.preventDefault(event);

	// this resets the on style for each star
	for (var i = 1; i <= this.max_value; i++) {
		Dom.removeClass(Dom.get(this.id + '_star' + i), 'swat-rating-selected');
	}

	if (this.flydown.value === clicked_star.toString()) {
		this.flydown.value = '';
		for (var i = 1; i <= this.max_value; i++) {
			Dom.removeClass(Dom.get(this.id + '_star' + i),
				'swat-rating-hover');
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

	// cycle through stars
	for (var i = 1; i <= clicked_star; i++) {
		Dom.addClass(Dom.get(this.id + '_star' + i), 'swat-rating-selected');
	}
}
