/**
 * Image cropper widget.
 * Author: Julien Lecomte <jlecomte@yahoo-inc.com>
 * Copyright (c) 2007, Yahoo! Inc. All rights reserved.
 * Code licensed under the BSD License:
 * http://developer.yahoo.net/yui/license.txt
 * Requires YUI >= 2.3.
 *
 * @module image-cropper
 * @title Image cropper
 * @namespace YAHOO.widget
 * @requires yahoo,dom,event,dragdrop
 */

/**
 * Image cropper widget.
 * @namespace YAHOO.widget
 * @class ImageCropper
 * @constructor
 * @param {String | HTMLImageElement} img Accepts a string to use as an ID or an actual DOM reference.
 * @param {Object} config Optional configuration object. The caller is responsible for making sure the
 *     config object contains values that make sense! For example, don't set x and w to exceed the total
 *     width of the image. Otherwise, unexpected things will happen (most likely, the specified xyratio
 *     will not be enforced properly) This object can contain the following keys:
 *         "x", "y", "w" and "h": default position and size
 *         "xyratio": ratio between width and height
 *
 */
YAHOO.widget.ImageCropper = function ( img, config ) {

    // A few shortcuts
    var YD = YAHOO.util.Dom;
    var YE = YAHOO.util.Event;
    var YL = YAHOO.lang;

    /************************************************************************
     * PRIVATE MEMBERS
     ************************************************************************/

    /**
     * Keep a reference to ourselves, for use in private methods.
     * @property _self
     * @type ImageCropper
     * @private
     */
    var _self = this;

    /**
     * Reference to the outer HTML element.
     * @property _outerElem
     * @type HTMLDivElement
     * @private
     */
    var _outerElem;

    /**
     * Reference to the HTML element representing the cropping region.
     * @property _cropElem
     * @type HTMLDivElement
     * @private
     */
    var _cropElem;

    /**
     * Flag indicating whether the image cropper has been initialized.
     * @property _initialized
     * @type Boolean
     * @default false
     * @private
     */
    var _initialized = false;

    /**
     * Timer used to check whether the image has been loaded in WebKit.
     * @property _loadTimer
     * @private
     */
    var _loadTimer;

    /**
     * Returns the pixel border width as an integer for
     * the specified border and the specified element.
     * @method _getPixelBorderWidth
     * @param {HTMLElement} el an actual DOM reference
     * @param {String} border "top", "right", "bottom" or "left"
     * @return {Number} border width
     * @private
     */
    function _getPixelBorderWidth ( el, border ) {

        var p, s, value;

        if ( YAHOO.env.ua.ie ) {

            switch ( border ) {
                case "top":
                    value = el.clientTop;
                    break;
                case "right":
                    value = el.offsetWidth - el.clientWidth - el.clientLeft;
                    break;
                case "bottom":
                    value = el.offsetHeight - el.clientHeight - el.clientTop;
                    break;
                case "left":
                    value = el.clientLeft;
                    break;
                default:
                    throw new Error( "Invalid border: " + border );
            }

        } else {

            switch ( border ) {
                case "top":
                    p = "border-top-width";
                    break;
                case "right":
                    p = "border-right-width";
                    break;
                case "bottom":
                    p = "border-bottom-width";
                    break;
                case "left":
                    p = "border-left-width";
                    break;
                default:
                    throw new Error( "Invalid border: " + border );
            }

            s = YD.getStyle( el, p );
            value = parseInt( s, 10 );
        }

        return value;
    }

    /**
     * Returns the position (relative to its offset parent) and size of the
     * specified element, independently of the box model used (i.e. this method
     * yields the same results in standards and quirks mode on all browsers).
     * The returned coordinates correspond to the position and size of the box
     * inside the element's borders (including the padding, but not including
     * the borders, which is what we care about in this widget)
     * @method _getGeometry
     * @param {HTMLElement} el an actual DOM reference
     * @return {Object} an object containing the members "x", "y", "w" and "h"
     * @private
     */
    function _getGeometry ( el ) {

        var ol, ot, blw, btw, pblw, pbtw;

        blw = _getPixelBorderWidth( el, "left" );
        btw = _getPixelBorderWidth( el, "top" );

        ol = el.offsetLeft + blw;
        ot = el.offsetTop + btw;

        if ( YAHOO.env.ua.gecko || YAHOO.env.ua.opera ) {

            pblw = _getPixelBorderWidth( el.offsetParent, "left" );
            pbtw = _getPixelBorderWidth( el.offsetParent, "top" );

            if ( YAHOO.env.ua.gecko ) {
                ol += pblw;
                ot += pbtw;
            } else if ( YAHOO.env.ua.opera ) {
                ol -= pblw;
                ot -= pbtw;
            }
        }

        return {
            x: ol,
            y: ot,
            w: el.clientWidth,
            h: el.clientHeight
        };
    }

    /**
     * Sets the specified element's position and size independently of the box
     * model used (i.e. this method yields the same results in standards and
     * quirks mode on all browsers). The specified coordinates correspond to
     * the position and size of the box inside the element's borders (including
     * the padding, but not including the borders, which is what we care about
     * in this widget)
     * @method _setGeometry
     * @param {HTMLElement} el an actual DOM reference
     * @param {Number} x left
     * @param {Number} y top
     * @param {Number} w width
     * @param {Number} h height
     * @private
     */
    function _setGeometry ( el, x, y, w, h ) {

        var p, d;

        el.style.position = "absolute";

        el.style.left = x + "px";
        el.style.top = y + "px";
        el.style.width = w + "px";
        el.style.height = h + "px";

        p = _getGeometry( el );

        d = p.x - x;
        if ( d !== 0 ) {
            el.style.left = ( x - d ) + "px";
        }

        d = p.y - y;
        if ( d !== 0 ) {
            el.style.top = ( y - d ) + "px";
        }

        d = p.w - w;
        if ( d !== 0 ) {
            el.style.width = ( w - d ) + "px";
        }

        d = p.h - h;
        if ( d !== 0 ) {
            el.style.height = ( h - d ) + "px";
        }
    }

    /**
     * Initialize the image cropper. Called when the specified image is loaded.
     * @method _init
     * @private
     */
    function _init () {

        var mask, x, y, w, h, r, p, dd;

        if ( _initialized ) {
            // This routine may be called several times in the case of Opera.
            return;
        }

        // There does not seem to be a cross-browser way of checking whether
        // an image has its natural size. Since this whole thing assumes the
        // image has its natural size, set its width and height to "auto".
        img.style.width = "auto";
        img.style.height = "auto";

        // Create the outer HTML element.
        _outerElem = document.createElement( "DIV" );
        _outerElem.className = "image-cropper";
        img.parentNode.replaceChild( _outerElem, img );
        _outerElem.appendChild( img );

        // Create a semi-opaque layer on top of the image.
        mask = document.createElement( "DIV" );
        mask.className = "mask";
        // Without specifying its width and height explicitly (instead of using
        // 100%), IE6 would not expand the mask to cover the entire image.
        // Setting its width and height does not hurt the other browsers.
        mask.style.width = img.clientWidth + "px";
        mask.style.height = img.clientHeight + "px";
        _outerElem.appendChild( mask );

        // Create the crop region.
        _cropElem = document.createElement( "DIV" );
        _cropElem.className = "cropper";
        _cropElem.style.background = "url(" + img.src + ")";
        _outerElem.appendChild( _cropElem );

        // Set the original size and position of the crop region.

        if ( config ) {
            w = config.w;
            h = config.h;
            x = config.x;
            y = config.y;
            r = config.xyratio;
        }

        if ( !YL.isNumber( w ) || w < 0 || w > img.clientWidth ) {
            // Invalid width. Defaults to 1/3 the image width.
            w = Math.floor( img.clientWidth / 3 );
        }

        if ( YL.isNumber( r ) && r > 0 ) {
            h = r * w;
        }

        if ( !YL.isNumber( h ) || h < 0 || h > img.clientHeight ) {
            // Invalid height. Defaults to 1/3 the image height.
            h = Math.floor( img.clientHeight / 3 );
        }

        if ( !YL.isNumber( x ) || x < 0 || x + w > img.clientWidth ) {
            // Invalid left. Defaults to centering the crop region.
            x = ( img.clientWidth - w ) / 2;
        }

        if ( !YL.isNumber( y ) || y < 0 || y + h > img.clientHeight ) {
            // Invalid top. Defaults to centering the crop region.
            y = ( img.clientHeight - h ) / 2;
        }

        _setGeometry( _cropElem, x, y, w, h );

        // Show the appropriate portion of the image in the crop region.
        p = _getGeometry( _cropElem );
        _cropElem.style.backgroundPosition = ( -p.x ) + "px " + ( -p.y ) + "px";

        // Create the resize hooks.
        if ( !config || config.noresize !== true ) {
            _createHooks();
        }

        // Set up the drag'n'drop...
        dd = new YAHOO.util.DD( _cropElem );

        dd.startDrag = function () {
            p = _getGeometry( _cropElem );
            this.resetConstraints();
            this.setXConstraint( p.x, img.clientWidth - p.x - p.w );
            this.setYConstraint( p.y, img.clientHeight - p.y - p.h );
        };

        dd.onDrag = function ( evt ) {
            p = _getGeometry( _cropElem );
            _cropElem.style.backgroundPosition = ( -p.x ) + "px " + ( -p.y ) + "px";
        };

        dd.endDrag = function ( evt ) {
            _self.onChangeEvent.fire();
        };

        _initialized = true;
    }

    /**
     * Creates the resize hooks.
     * @method _createHooks
     * @private
     */
    function _createHooks () {

        var i, hook, dd, mouseX, mouseY, obj;

        for ( i = 0; i < 4 || ( !config || !YL.isNumber( config.xyratio ) || config.xyratio <= 0 ) && i < 8; i++ ) {

            // Create a resize hook...
            hook = document.createElement( "DIV" );
            _cropElem.appendChild( hook );

            // Set its class names...
            switch (i) {

                case 0:
                    YD.addClass( hook, "t" );
                    YD.addClass( hook, "l" );
                    YD.addClass( hook, "tl" );
                    break;
                case 1:
                    YD.addClass( hook, "t" );
                    YD.addClass( hook, "r" );
                    YD.addClass( hook, "tr" );
                    break;
                case 2:
                    YD.addClass( hook, "b" );
                    YD.addClass( hook, "l" );
                    YD.addClass( hook, "bl" );
                    break;
                case 3:
                    YD.addClass( hook, "b" );
                    YD.addClass( hook, "r" );
                    YD.addClass( hook, "br" );
                    break;
                case 4:
                    YD.addClass( hook, "t" );
                    YD.addClass( hook, "c" );
                    YD.addClass( hook, "tc" );
                    break;
                case 5:
                    YD.addClass( hook, "m" );
                    YD.addClass( hook, "r" );
                    YD.addClass( hook, "mr" );
                    break;
                case 6:
                    YD.addClass( hook, "b" );
                    YD.addClass( hook, "c" );
                    YD.addClass( hook, "bc" );
                    break;
                case 7:
                    YD.addClass( hook, "m" );
                    YD.addClass( hook, "l" );
                    YD.addClass( hook, "ml" );
                    break;
            }

            // Set up the hook drag'n'drop...
            dd = new YAHOO.util.DD( hook );

            // Do this so the YUI DnD library does not move things on its own.
            // The downside is that we have to handle the constraints by hand.
            dd.alignElWithMouse = function ( el, iPageX, iPageY ) {};

            dd.startDrag = function ( x, y ) {
                obj = _getGeometry( _cropElem );
                mouseX = x;
                mouseY = y;
            };

            dd.onDrag = function ( evt ) {

                var dx, dy, x, y, w, r, h, p;

                dx = YE.getPageX( evt ) - mouseX;
                dy = YE.getPageY( evt ) - mouseY;

                hook = this.getEl();

                if ( YD.hasClass( hook, "l" ) ) {
                    if ( dx < 0 ) {
                        dx = Math.max( -obj.x, dx );
                    } else {
                        dx = Math.min( obj.w, dx );
                    }
                    x = obj.x + dx;
                    w = obj.w - dx;
                } else if ( YD.hasClass( hook, "r" ) ) {
                    if ( dx < 0 ) {
                        dx = Math.max( -obj.w, dx );
                    } else {
                        dx = Math.min( img.clientWidth - obj.x - obj.w, dx );
                    }
                    x = obj.x;
                    w = obj.w + dx;
                } else {
                    dx = 0;
                    x = obj.x;
                    w = obj.w;
                }

                if ( YD.hasClass( hook, "t" ) ) {
                    if ( dy < 0 ) {
                        dy = Math.max( -obj.y, dy );
                    } else {
                        dy = Math.min( obj.h, dy );
                    }
                    y = obj.y + dy;
                    h = obj.h - dy;
                } else if ( YD.hasClass( hook, "b" ) ) {
                    if ( dy < 0 ) {
                        dy = Math.max( -obj.h, dy );
                    } else {
                        dy = Math.min( img.clientHeight - obj.y - obj.h, dy );
                    }
                    y = obj.y;
                    h = obj.h + dy;
                } else {
                    dy = 0;
                    y = obj.y;
                    h = obj.h;
                }

                if ( config && YL.isNumber( config.xyratio ) && config.xyratio > 0 ) {
                    // Handle the constraints.
                    r = config.xyratio;
                    h = Math.floor( w * r );
                    if ( YD.hasClass( hook, "t" ) ) {
                        y = obj.y + obj.h - h;
                        if ( y < 0 ) {
                            y = 0;
                            dy = -obj.y;
                            h = obj.h - dy;
                            w = Math.floor( h / r );
                            if ( YD.hasClass( hook, "l" ) ) {
                                dx = Math.floor( -dy / r );
                                x = obj.x - dx;
                            }
                        }
                    } else {
                        if ( y + h > img.clientHeight ) {
                            h = img.clientHeight - y;
                            w = Math.floor( h / r );
                            if ( YD.hasClass( hook, "l" ) ) {
                                dy = h - obj.h;
                                dx = Math.floor( dy / r );
                                x = obj.x - dx;
                            }
                        }
                    }
                }

                _setGeometry( _cropElem, x, y, w, h );

                p = _getGeometry( _cropElem );
                _cropElem.style.backgroundPosition = ( -p.x ) + "px " + ( -p.y ) + "px";
            };

            dd.endDrag = function ( evt ) {
                _self.onChangeEvent.fire();
            };
        }
    }

    /************************************************************************
     * PUBLIC MEMBERS
     ************************************************************************/

    /**
     * Fired when the crop region has been moved or resized.
     * @event onChangeEvent
     */
    this.onChangeEvent = new YAHOO.util.CustomEvent( "onChange" );

    /**
     * Returns the current crop region.
     * @method getCropRegion
     * @return {Object} an object containing the members "x", "y", "w", and "h"
     */
    this.getCropRegion = function () {
        return _getGeometry( _cropElem );
    };

    /************************************************************************
     * CONSTRUCTOR
     ************************************************************************/

    // In case the caller passed in the id string of the image.
    img = YD.get( img );

    // Check the validity of the parameters passed to the constructor.
    if ( !img || img.tagName !== "IMG" || config && !YL.isObject( config ) ) {
        throw new Error( "Invalid argument" );
    }

    // Figure out whether the rendered size of the image is known.
    // In WebKit, don't use the onload event. It seems unreliable.

    if ( YAHOO.env.ua.webkit ) {

        _loadTimer = setInterval( function () {
            if ( img.width !== 0 || img.height !== 0 ) {
                clearInterval( _loadTimer );
                _init();
            }
        }, 100 );

    } else if ( !img.complete || img.naturalWidth === 0 ) {

        // The browser is either still loading the image, or it may be done,
        // but the image was not loaded (network problem, missing resource)
        // We don't need to differentiate between these two cases.

        YE.addListener( img, "load", _init );

    } else {

        // The image seems to be loaded.
        _init();

    }

};
