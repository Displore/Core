<?php

return [

	/**
	 * This setting turns off the backend.
	 * No routes, views, etc. will be loaded.
	 */
	'backend' => true,

	/**
	 * If you want to customise the Middleware used by Displore,
	 * you can do that by creating a new class and referring to it here.
	 * There are already a few written out for the various Displore packages.
	 */
	'middleware' => [

		'admin' => Displore\Biotope\Middleware\VerifyAdmin::class,

	],

];