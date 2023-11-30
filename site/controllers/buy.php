<?php

return function ($page) {
	// uncomment to test a specific country
	// Buy\Paddle::visitor('US');

	return [
		'discounts' => option('buy.volume'),
		'sale'      => new Buy\Sale(),
		'questions' => $page->find('answers')->children(),
		'visitor'   => Buy\Paddle::visitor()
	];
};
