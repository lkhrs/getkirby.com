<?php

use Kirby\Http\Remote;

return [
	'command' => function ($cli) {

		$kirby = $cli->kirby();
		$kirby->impersonate('kirby');

		$plugins = page('plugins')->grandChildren()->filter(function ($plugin) {
			return str_contains($plugin->repository()->value() ?? '', 'github') === true;
		});

		foreach ($plugins as $plugin) {
			$repo = $plugin->repository()->value();
			$api  = str_replace('https://github.com', 'https://api.github.com/repos', $repo);

			$response = Remote::get($api, [
				'headers' => [
					'User-Agent' => 'Kirby Bot',
					'Authorization' => 'Bearer ' . option('keys.github'),
				]
			]);

			$cli->success($api);

			if ($response->code() !== 200) {
				$cli->error($response->json());
				continue;
			}

			$json  = $response->json();
			$stars = $json['stargazers_count'] ?? 0;

			$cli->dump($stars);

			$plugin->update([
				'stars' => $stars
			]);
		}

	}
];
