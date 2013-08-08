/**
 * Forked from cbpHorizontalMenu.js v1.0.0
 * http://www.codrops.com
 *
 * Licensed under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 * 
 * Copyright 2013, Codrops
 * http://www.codrops.com
 */

var epicMenu = (function() {

	var $listItems = $('#menu-top > ul > li' ),
		$menuItems = $listItems.children( 'a' ),
		$menuHeight = parseInt($('#menu-top').outerHeight(true)),
		$body = $( 'body' ),
		current = -1;

	function init() {
		$menuItems.click(function(){
			open(event);
			if($(this).attr('href')=="#") event.preventDefault();
		});
		$listItems.click( function(event){
			event.stopPropagation();
		});
	}

	function open( event ) {

		if( current !== -1 ) {
			$listItems.eq( current ).removeClass( 'menu-top-open' );	
		}
		var $item = $( event.currentTarget ).parent( 'li' ),
			idx = $item.index();

		if( current === idx ) {
			$item.removeClass( 'menu-top-open' );
			$('#menu-top').css({ height: "auto" });
			current = -1;
		}
		else {
			$item.addClass( 'menu-top-open' );
			$('#menu-top').css({
				height: (parseInt($item.find('.menu-top-sub').outerHeight(true)) + $menuHeight) + "px"
			});
			current = idx;
			$body.off( 'click' ).on( 'click', close );
		}

		return false;

	}

	function close( event ) {
		$listItems.eq( current ).removeClass( 'menu-top-open' );
		$('#menu-top').css({ height: $menuHeight + "px" });
		current = -1;
	}

	return { init : init };

})();

$(function() {
	epicMenu.init();
});