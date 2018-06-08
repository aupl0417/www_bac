var toPage = new Framework7(); 
var $$ = Dom7;

var mainView = toPage.addView('.view-main', {
	dynamicNavbar: true
});
toPage.onPageInit('about', function (page) {});


$$.getJSON('http://www.diaoyude.cn/api/v1/share?access_token=bZ2p7xTXPslrQjIKNbsmSi1h9rJ8y_dx&callback=f7jsonp_14970803523741', function (data) {
	var html = '';
	for( i=0; i<=4; i++){		
		html += '<div class="swiper-slide"><a href=""><img src="'+data.items[i].thumbs+'"></a></div>'
	}
	$$('.banner_one').html( html )

	var mySwiper1 = toPage.swiper('.swiper-1', {
		pagination:'.swiper-1 .swiper-pagination',
		spaceBetween: 50
	});

	var text = '';
	for( i=4; i<=10; i++){		
		text += '<div class="swiper-slide"><a href=""><img src="'+data.items[i].thumbs+'"></a><p class="banner_text">'+data.items[i].title+'</p></div>'
	}
	$$('.banner_tow').html( text )

	var mySwiper2 = toPage.swiper('.swiper-2', {
		pagination:'.swiper-2 .swiper-pagination',
		spaceBetween: 10,
		slidesPerView: 2.5
	});

	var txt = '';
	for( i=10; i<=16; i++){		
		txt += '<div class="swiper-slide"><a href=""><img class="rmb5" src="'+data.items[i].thumbs+'"></a><a href=""><img class="rmb5" src="'+data.items[i+6].thumbs+'"></a></div>'
	}
	$$('.banner_three').html( txt )

	var mySwiper3 = toPage.swiper('.swiper-3', {
		pagination:'.swiper-3 .swiper-pagination',
		spaceBetween: 10,
		slidesPerView: 2.5
	});
});





