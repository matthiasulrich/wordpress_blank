jQuery(document).ready(function ($) {
/*document.onreadystatechange = function () {
if (document.readyState === 'complete') {
*/

/* ============================================ *\

	Allgemein
	
\* ============================================ */
/* global cssVars, css_beautify */


/* =============================================================== *\ 
 	 Hamburger 
\* =============================================================== */ 
var $hauptmenue_listen_breite = $("#menu .main_menu_container").outerWidth();
var $rausschieben = 0 - $hauptmenue_listen_breite;
$("#menu.out").css('transform', "matrix(1,0,0,1," + $rausschieben +",0)");

$(".hamburger").on('click', function(){

	//Klassen verwalten
	$(this).toggleClass('is-active');
	$(".main_menu_container").toggleClass('bounceOutLeft');
	$(".main_menu_container").toggleClass('bounceInLeft');

	$("#menu").toggleClass("out");
	$hauptmenue_listen_breite = $("#menu .main_menu_container").outerWidth();
	$rausschieben = 0 - $hauptmenue_listen_breite;
	$("#menu").css('transform', "matrix(1,0,0,1,0,0)");
	$("#menu.out").css('transform', "matrix(1,0,0,1," + $rausschieben +",0)");
/*
if($("#menu").css('transform')!="matrix(1, 0, 0, 1, 0, 0)"){
	$("#menu").css('transform', 'matrix(1, 0, 0, 1, 0, 0)');
}else{
$("#menu").css('transform', 'matrix(1, 0, 0, 1,' + $hauptmenue_listen_breite + ', 0)');	
}
	// Menu rein- und rausschieben
console.log($("#menu").css('transform'));	
*/

});


/*schöner Seite laden*/
$("#content_container").animate({
	opacity:1
   	},{
   	duration:500,
   	complete : function(){},
});
/* =============================================================== *\ 
 	 Isotope 
\* =============================================================== */ 
// init Isotope
/*var $grid = $('.grid').isotope({
	itemSelector: '.grid_item'
});

// store filter for each group
var filters = [];

// change is-checked class on buttons
$('.bereich_menu').on( 'click', 'button', function( event ) {
	var $target = $( event.currentTarget );
	$target.toggleClass('is-checked');
	var isChecked = $target.hasClass('is-checked');
	var filter = $target.attr('data-filter');
	
	if ( isChecked ) {
		addFilter( filter );
	} else {
		removeFilter( filter );
	}	
	
	$grid.isotope({ filter: filters.join(',') });
});

function addFilter( filter ) {
	if ( filters.indexOf( filter ) == -1 ) {
		filters.push( filter );
	}
}

function removeFilter( filter ) {
	var index = filters.indexOf( filter);
	if ( index != -1 ) {
		filters.splice( index, 1 );
	}
}
*/
var my_isotope_buttons_filter_values = [];
var my_isotope_buttons_titles = [];

$(".grid .grid_item").each(function(){
	$data_filter = $(this).attr('data-temp-filter'); //den Wert des Attributes auslesen
	//$(this).closest("div").addClass($data_filter);	// übergeben
	my_isotope_buttons_filter_values.push($data_filter);	// Array befüllen > für Buttons	
	//folgende Zwei Zeilen wahrsch weglassen
	$data_title = $(this).find('*[data-temp-cat_name]').attr('data-temp-cat_name'); // Wert für Button-Beschriftung auslesen
	my_isotope_buttons_titles.push($data_title); // Array befüllen
});
/* =============================================================== *\ 
 	 Isotope with Hash  
\* =============================================================== */ 
function getHashFilter() {
	var hash = location.hash;
	var matches = location.hash.match( /filter=([^&]+)/i );
	var hashFilter = matches && matches[1];
	return hashFilter && decodeURIComponent( hashFilter );
}
var hashFilter = getHashFilter();
/*$( function() {*/
	var $grid = $('.grid');

  	var $filters = $('.my_isotope_filters.button-group').on( 'click', 'button', function() {
		var filterAttr = $( this ).attr('data-filter');
		
		$(this).siblings().removeClass('is_checked'); // bei geschwistern is_checked löschen
		$(this).toggleClass('is_checked'); // bei sich selber is_checked toggeln
		if($(this).hasClass('is_checked')){ //wenn is_checked
			filterAttr = $(this).attr('data-filter'); // filter_wert übergeben
			//filterAttr = filterAttr.replace(".", "");
		}else{
			filterAttr = "*"; // ansonsten alles/nichts filtern
		}
    	location.hash = 'filter=' + encodeURIComponent( filterAttr ); // set filter in hash
  	});

  	var isIsotopeInit = false;

	function onHashchange() {
		hashFilter = getHashFilter();
		if ( !hashFilter && isIsotopeInit ) {
			return;
		}
		isIsotopeInit = true;

		$grid.isotope({
			itemSelector: '.grid_item',
			filter: hashFilter,
			masonry:{ columnWidth: '.grid_sizer',}
		});
		//console.log(hashFilter);

		if ( hashFilter ) {
			$filters.find('.is_checked').removeClass('is_checked');
			$filters.find('[data-filter="' + hashFilter + '"]').addClass('is_checked');
		}
		show_all_buttons_toggle();
	}

	$(window).on( 'hashchange', onHashchange );
	onHashchange(); //trigger event handler to init Isotope
/*});  
*/
function show_all_buttons_toggle(){
	if($('.is_checked').length === 0){
		$('.my_isotope_filters.button-group button').addClass('show_all');
	}else{
		$('.my_isotope_filters.button-group button').removeClass('show_all');
	}
}

$grid.isotope({
	itemSelector: '.grid_item',
	filter: hashFilter,
	masonry:{ columnWidth: '.grid_sizer',}
});

/* =============================================================== *\ 
 	 //end Isotope 
\* =============================================================== */ 
  



/*
}//readyState
}//onreadystatechange
*/
});//ready beenden
