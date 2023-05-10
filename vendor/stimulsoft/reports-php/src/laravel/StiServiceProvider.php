<?php

namespace Stimulsoft\Laravel;

if (class_exists('\Illuminate\Support\ServiceProvider'))
{
	class StiServiceProvider extends \Illuminate\Support\ServiceProvider
	{
		public function boot()
		{
			\Illuminate\Support\Facades\Route::get('/vendor/stimulsoft/reports-php/scripts/{file}', function ($file) {
				return file_get_contents(__DIR__ . "/../../scripts/$file");
			});
		}
	}
}
