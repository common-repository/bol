var productPage = 0;
var productFilterObj = [];

//setup arrow and paginations
function setupPager(qvan) {
	if (qvan <= QVAN) {
		jQuery(".pager ul").html("<li class='current'>1</li>");
		jQuery(".pager .amount").html(qvan);
		return;
	}

	var list = "";
	var pager = '<li><span title="vorige" class="previous">vorige</span></li>'+
				'<li><span title=" volgende" class="next"> volgende</span></li>';

	for (var i = 0; i<Math.ceil(qvan / QVAN); i++) {
		list += "<li><span>" + (i + 1) + "</span></li>";
	}
	list += pager;

	jQuery(".pager ul").html(list);
	jQuery(".pager .amount").html(qvan);

	jQuery(".pager li").each(function() {
		jQuery(this).click(function() {
			showProducts(jQuery("span", this).html());
		});
	});
}

function showProducts(str) {
	//switch in paginations
	switch (str) {
		case "next":
			productPage ++;
		break;

		case "prev":
			productPage --;
		break;

		default:
			productPage = Number(str) - 1;

	}

	//if first start add items to productFilterObj
	if (productFilterObj.length == 0) {
		jQuery("#dvResults .productlist li").each(function(index) {
			productFilterObj.push(jQuery(this));
		});
	}


	var pages = Math.ceil(productFilterObj.length / QVAN);
	var next = jQuery(".pager .next");
	var prev = jQuery(".pager .previous");

	//disable arrow button
	if (productPage < 0) {
		productPage = 0;
		return;
	}
	if (productPage > pages - 1) {
		productPage = pages;
		return;
	}

	//hide all items and remove bottom line
	jQuery("#dvResults .productlist li").hide();
	jQuery("#dvResults .productlist li").removeClass("line");

	//show items by current page
	for (var i = 0; i<productFilterObj.length; i++) {
		if (i >= productPage * QVAN && i < (productPage + 1) * QVAN) {
			jQuery(productFilterObj[i]).show();
		}
	}


	//add line on bottom for evrey 3 items in visible elements
	var visible = jQuery("#dvResults .productlist li:visible");
	var stratIndex = 0;
	var currentIndex = 2;

	while (currentIndex < visible.length) {
		if (currentIndex != visible.length - 1) {
			for (var i = stratIndex; i <= currentIndex; i++) {
				jQuery(visible[i]).addClass("line");
			}
		}
		stratIndex = currentIndex + 1;
		currentIndex = currentIndex + 3;
	}


	//show/hide arrow buttons
	next.each(function() {
		jQuery(this).addClass("disable");
	});
	prev.each(function() {
		jQuery(this).addClass("disable");
	});

	if (productPage < pages - 1) {
		next.removeClass("disable");
	}
	if (productPage > 0) {
		prev.removeClass("disable");
	}

	//select current page
	jQuery(".pages").each(function() {
		jQuery("li", this).each(function(index) {
			jQuery(this).removeClass("current");
			if (index == productPage) {
				jQuery(this).addClass("current");
			}
		});
	});
}