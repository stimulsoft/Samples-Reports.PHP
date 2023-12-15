<?php

namespace Stimulsoft\Laravel;

if (class_exists('\Illuminate\Support\ServiceProvider'))
{
	class StiServiceProvider extends \Illuminate\Support\ServiceProvider
	{
		public function boot()
		{
			\Illuminate\Support\Facades\Route::get('/vendor/stimulsoft/reports-php/scripts/{file}', function ($file) {
				return response()->file(__DIR__ . "/../../scripts/$file", ["Content-Type"=>'text/javascript']);
			});
			\Illuminate\Support\Facades\Route::get('/vendor/stimulsoft/dashboard-php/scripts/{file}', function ($file) {
				return response()->file(__DIR__ . "/../../../dashboard-php/scripts/$file", ["Content-Type"=>'text/javascript']);
			});
		}
	}
}
