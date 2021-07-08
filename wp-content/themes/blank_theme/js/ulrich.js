jQuery(document).ready(function ($) {
document.onreadystatechange = function () {
if (document.readyState === 'complete') {


/* ============================================ *\

	Allgemein
	
\* ============================================ */

/*schöner Seite laden*/
$("#content_container").animate({
	opacity:1
   	},{
   	duration:500,
   	complete : function(){},
});







}//readyState
}//onreadystatechange
});//ready beenden
