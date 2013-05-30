/**
 * Javascript file for 'Batty' issue tracking system
 * File: /^Batty/js/batty.js
 *
 * @category LoneFry
 * @package  Batty
 * @author   LoneFry <dev@lonefry.com>
 * @license  Creative Commons CC-NC-BY-SA
 * @link     http://github.com/LoneFry/Batty
 */

var Batty_XHR;

/**
 * Sends Ajax request to save subscription
 *
 * @return void
 */
function Batty_Subscribe(data) {
	Batty_XHR = window.ActiveXObject?new ActiveXObject("Microsoft.XMLHTTP")
		:new XMLHttpRequest();

	//Set post string
	var post = 'json=' + JSON.stringify(data);

	Batty_XHR.open('POST', '/Batty/subscribe/', true);
	Batty_XHR.onreadystatechange = Batty_Subscribe_;
	Batty_XHR.setRequestHeader("Content-Type", 'application/x-www-form-urlencoded');
	Batty_XHR.setRequestHeader("Content-length", post.length);
	Batty_XHR.send(post);
}

/**
 * Sets response text
 *
 * @return void
 */
function Batty_Subscribe_() {
	if (Batty_XHR.readyState != 4) {
		return;
	}
	if (Batty_XHR.responseText == "1") {
		Batty_Ajax_Message.innerHTML = "Subscription saved.";
	} else {
		Batty_Ajax_Message.innerHTML = "Subscription failed.";
	}
}
