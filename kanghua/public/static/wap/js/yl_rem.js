(function (doc, win) {
	var docEl = doc.documentElement,
    resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
    recalc = function () {
		var clientWidth = docEl.clientWidth;
		if (!clientWidth) return;
		clientWidth / 10 >75 ? docEl.style.fontSize = '75px' : docEl.style.fontSize = clientWidth / 10 + 'px';
    };
	if (!doc.addEventListener) return;
	win.addEventListener(resizeEvt, recalc, false);
	doc.addEventListener('DOMContentLoaded', recalc, false);
})(document, window);
