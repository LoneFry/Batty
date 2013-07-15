<?php
/**
 * Useful functions to use
 *
 * PHP version 5.3
 *
 * @category LoneFry
 * @package  Batty
 * @author   LoneFry <dev@lonefry.com>
 * @license  Creative Commons CC-NC-BY-SA
 * @link     http://github.com/LoneFry/Batty
 */

/**
 * Outputs a formatted batty comment
 *
 * @param string $comment Comment being formatted
 *
 * @return void
 */
function batty_comment($comment) {
	//Strip html out
	$comment = htmlspecialchars($comment);

	//Adds a link to a Batty Issue reference
	$comment = preg_replace(
		'/\[?Batty ?#0*([1-9]\d*)\]?/',
		'<a href="/Batty/issue/$1">${0}</a>',
		$comment
		);

	//Convert URLs into links
	$comment = preg_replace(
		'@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_#-.]*(\?\S+)?[^\.\s])?)?)@',
		'<a href="$1">$1</a>',
		$comment
		);
	echo $comment;
}
