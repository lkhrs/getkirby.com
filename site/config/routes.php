<?php

use Buy\Product;
use Kirby\Cms\Page;

return [
	[
		'pattern' => '.well-known/security.txt',
		'action'  => function () {
			go('security.txt');
		}
	],
	[
		'pattern' => 'hooks/clean',
		'method'  => 'GET|POST',
		'action'  => function () {
			$key = option('keys.hooks');

			if (empty($key) === false && get('key') === $key) {
				kirby()->cache('pages')->flush();
				kirby()->cache('diffs')->flush();
				kirby()->cache('plugins')->flush();
				kirby()->cache('reference')->flush();
			}

			go();
		}
	],
	[
		'pattern' => 'releases/(:num)\-(:any)',
		'action'  => function ($generation, $major) {
			return go('releases/' . $generation . '.' . $major);
		}
	],
	[
		'pattern' => 'releases/(:num)\.(:any)',
		'action'  => function ($generation, $major) {
			return page('releases/' . $generation . '-' . $major);
		}
	],
	[
		'pattern' => 'releases/(:num)\.(:any)/(:all?)',
		'action'  => function ($generation, $major, $path) {
			return page('releases/' . $generation . '-' . $major . '/' . $path);
		}
	],
	[
		'pattern' => 'buy/(:any)',
		'action' => function (string $product) {
			try {
				$product = Product::from($product);
				$price   = $product->price();
				$prices  = [
					'EUR:'                 . $product->price('EUR', 1)->sale(),
					$price->currency . ':' . $price->sale(),
				];

				go($product->checkout('buy', ['prices' => $prices]));
			} catch (Throwable $e) {
				die($e->getMessage() . '<br>Please contact us: support@getkirby.com');
			}
		},
	],
	[
		'pattern' => 'buy/volume',
		'method'  => 'POST',
		'action'  => function() {
			$product  = get('product', 'basic');
			$volume   = get('volume', 5);

			try {
				$product = Product::from($product);
				$price   = $product->price();
				$prices  = [
					'EUR:'                 . $product->price('EUR', 1)->volume($volume),
					$price->currency . ':' . $price->volume($volume),
				];

				$url = $product->checkout('buy', [
					'prices'            => $prices,
					'quantity'          => $volume,
					'quantity_variable' => false,
				]);

				go($url);
			} catch (Throwable $e) {
				die($e->getMessage() . '<br>Please contact us: support@getkirby.com');
			}
		}
	],
	[
		'pattern' => 'buy/volume/(:any)/(:num)',
		'action'  => function(string $product, int $volume) {
			try {
				$product = Product::from($product);
				$price   = $product->price();
				$prices  = [
					'EUR:'                 . $product->price('EUR', 1)->volume($volume),
					$price->currency . ':' . $price->volume($volume),
				];

				$url = $product->checkout('buy', [
					'prices'            => $prices,
					'quantity'          => $volume,
					'quantity_variable' => false,
				]);

				go($url);
			} catch (Throwable $e) {
				die($e->getMessage() . '<br>Please contact us: support@getkirby.com');
			}
		}
	],
	[
		'pattern' => 'pixels',
		'action'  => function () {
			return new Page([
				'slug'     => 'pixels',
				'template' => 'pixels',
				'content'  => [
					'title' => 'Pixels'
				]
			]);
		}
	],
	[
		'pattern' => 'plugins/k4',
		'action'  => function () {
			return page('plugins')->render(['filter' => 'k4']);
		}
	],
	[
		'pattern' => 'plugins/new',
		'action'  => function () {
			return page('plugins')->render(['filter' => 'published']);
		}
	],
];
