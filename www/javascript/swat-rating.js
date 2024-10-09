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
class SwatRating {
  constructor(id, max_value) {
    this.id = id;
    this.max_value = max_value;
    this.stars = [];
    this.sensitive = true;

    window.addEventListener('DOMContentLoaded', () => {
      this.init();
    });
  }

  init() {
    this.flydown = document.getElementById(this.id + '_flydown');
    this.rating_div = document.getElementById(this.id);
    this.sensitive = !this.rating_div.classList.contains('swat-insensitive');
    this.flydown.style.display = 'none';

    var star_div = document.createElement('div');
    star_div.className = 'swat-rating-star-container';

    for (var i = 1; i <= this.max_value; i++) {
      var star = document.createElement('span');
      const star_number = i;
      star.id = this.id + '_star' + i;
      star.tabIndex = '0';

      star.classList.add('swat-rating-star');
      if (i <= parseInt(this.flydown.value, 10)) {
        star.classList.add('swat-rating-selected');
      }

      star_div.appendChild(star);

      star.addEventListener('focus', (e) => {
        this.handleFocus(e, star_number);
      });
      star.addEventListener('blur', (e) => {
        this.handleBlur(e, star_number);
      });
      star.addEventListener('mouseover', (e) => {
        this.handleFocus(e, star_number);
      });
      star.addEventListener('mouseout', (e) => {
        this.handleBlur(e, star_number);
      });
      star.addEventListener('click', (e) => {
        this.handleClick(e, star_number);
      });
      star.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' || e.key === 'Space') {
          e.preventDefault();
          this.handleClick(e, star_number);
        }
      });

      this.stars.push(star);
    }

    var clear = document.createElement('div');
    clear.className = 'swat-rating-clear';

    this.rating_div.appendChild(star_div);
    this.rating_div.appendChild(clear);
  }

  setSensitivity(sensitivity) {
    if (sensitivity) {
      this.rating_div.classList.remove('swat-insensitive');
      this.sensitive = true;
    } else {
      this.rating_div.classList.add('swat-insensitive');
      this.sensitive = false;
    }
  }

  handleFocus(event, focus_star) {
    if (!this.sensitive) {
      return;
    }

    for (var i = 0; i < focus_star; i++) {
      this.stars[i].classList.add('swat-rating-hover');
    }
  }

  handleBlur(event) {
    // code to handle movement away from the star
    for (var i = 0; i < this.max_value; i++) {
      this.stars[i].classList.remove('swat-rating-hover');
    }
  }

  handleClick(event, clicked_star) {
    if (!this.sensitive) {
      return;
    }

    // reset 'on' style for each star
    for (var i = 0; i < this.max_value; i++) {
      this.stars[i].classList.remove('swat-rating-selected');
    }

    // if you click on the current rating, it sets the rating to empty
    if (this.flydown.value === clicked_star.toString()) {
      this.flydown.value = '';
      for (var i = 0; i < this.max_value; i++) {
        this.stars[i].classList.remove('swat-rating-hover');
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
    for (var i = 0; i < clicked_star; i++) {
      this.stars[i].classList.add('swat-rating-selected');
    }
  }

  getValue() {
    var value = null;

    var index = this.flydown.value;
    if (index !== null && index !== '') {
      value = this.flydown.options[index].value;
    }

    return value;
  }

  setValue(rating) {
    // clear 'on' style for each star
    for (var i = 0; i < this.max_value; i++) {
      this.stars[i].classList.remove('swat-rating-selected');
    }

    if (rating === '' || rating === null) {
      this.flydown.value = '';
      for (var i = 0; i < this.max_value; i++) {
        this.stars[i].classList.remove('swat-rating-hover');
      }
    } else {
      // set the current value of the flydown
      for (var i = 0; i < this.flydown.options.length; i++) {
        var option = this.flydown.options[i];
        if (option.value == rating) {
          this.flydown.value = i;
          break;
        }
      }

      // set 'on' style for each star
      for (var i = 0; i < rating; i++) {
        this.stars[i].classList.add('swat-rating-selected');
      }
    }
  }
}
