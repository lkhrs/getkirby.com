<?php

use Kirby\Cms\Pages;

return function($page, $filter) {

	$categories = option('plugins.categories');
	$category   = param('category');
	$heading    = 'Featured';


	if ($category && array_key_exists($category, $categories) === true) {
		$plugins = $page
			->children()
			->children()
			->filterBy('recommended', '');

		$plugins = $plugins->filterBy('category', $category);
		$heading = $categories[$category]['label'];

	} else if ($category === 'all') {
			$heading  = 'All plugins';
			$category = 'all';
			$plugins  = $page->grandChildren();
	} else if ($filter === 'k4') {
			$heading  = 'Kirby 4 plugins';
			$category = 'k4';
			$plugins  = $page->grandChildren()->filter('versions', '*=', '4');
	} else {
		$category = null;
		$plugins  = new Pages();

		if ($this->request()->url()->path()->first() !== 'plugins.json') {
			go('plugins/k4');
		}
	}

	$plugins = match (get('sort')) {
		'stars' => $plugins->sortBy('stars', 'desc'),
		default => $plugins->sortBy('title', 'asc'),
	};

	return [
		'categories'      => $categories,
		'currentCategory' => $category,
		'heading'         => $heading,
		'plugins'         => $plugins,
		'filter'          => $filter,
	];

};
