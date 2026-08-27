<?php

namespace Stimulsoft\Laravel;

if (class_exists('\Illuminate\Support\ServiceProvider'))
{
    class StiServiceProvider extends \Illuminate\Support\ServiceProvider
    {
        public function boot()
        {
            \Illuminate\Support\Facades\Route::get('/vendor/stimulsoft/reports-php/scripts/{file}', function ($file) {
                $filePath = \Stimulsoft\StiResourcesHelper::getFilePath($file, __DIR__ . '/../..');
                if ($filePath === null)
                    abort(404);

                return response()->file($filePath, ["Content-Type"=>'text/javascript']);
            });

            \Illuminate\Support\Facades\Route::get('/vendor/stimulsoft/dashboard-php/scripts/{file}', function ($file) {
                $filePath = \Stimulsoft\StiResourcesHelper::getFilePath($file, __DIR__ . '/../../../dashboard-php');
                if ($filePath === null)
                    abort(404);

                return response()->file($filePath, ["Content-Type"=>'text/javascript']);
            });
        }
    }
}
