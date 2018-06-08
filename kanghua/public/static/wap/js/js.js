'use strict';
var myApp = new Framework7({
    swipePanel: 'left'
});

var mainView = myApp.addView('.view-main', {
    dynamicNavbar: true
});


myApp.init();

var $$ = window.Dom7;

