
function catapultAcceptCookies() {
	days = 30;
	var date = new Date();
	date.setTime(date.getTime()+(days*24*60*60*1000));
	var expires = "; expires="+date.toGMTString();
	document.cookie = "catAccCookies=true"+expires+"; path=/";
	jQuery("#catapult-cookie-bar").hide();
	jQuery("html").css("margin-top","0");
}
