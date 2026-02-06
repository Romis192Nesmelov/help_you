/*!
 * jQuery UI Touch Punch 0.2.3
 *
 * Copyright 2011–2014, Dave Furfero
 * Dual licensed under the MIT or GPL Version 2 licenses.
 *
 * Depends:
 *  jquery.ui.widget.js
 *  jquery.ui.mouse.js
 */(function(t){function e(o,c){if(!(o.originalEvent.touches.length>1)){o.preventDefault();var h=o.originalEvent.changedTouches[0],i=document.createEvent("MouseEvents");i.initMouseEvent(c,!0,!0,window,1,h.screenX,h.screenY,h.clientX,h.clientY,!1,!1,!1,!1,0,null),o.target.dispatchEvent(i)}}if(t.support.touch="ontouchend"in document,t.support.touch){var n,u=t.ui.mouse.prototype,r=u._mouseInit,s=u._mouseDestroy;u._touchStart=function(o){var c=this;!n&&c._mouseCapture(o.originalEvent.changedTouches[0])&&(n=!0,c._touchMoved=!1,e(o,"mouseover"),e(o,"mousemove"),e(o,"mousedown"))},u._touchMove=function(o){n&&(this._touchMoved=!0,e(o,"mousemove"))},u._touchEnd=function(o){n&&(e(o,"mouseup"),e(o,"mouseout"),this._touchMoved||e(o,"click"),n=!1)},u._mouseInit=function(){var o=this;o.element.bind({touchstart:t.proxy(o,"_touchStart"),touchmove:t.proxy(o,"_touchMove"),touchend:t.proxy(o,"_touchEnd")}),r.call(o)},u._mouseDestroy=function(){var o=this;o.element.unbind({touchstart:t.proxy(o,"_touchStart"),touchmove:t.proxy(o,"_touchMove"),touchend:t.proxy(o,"_touchEnd")}),s.call(o)}}})(jQuery);
