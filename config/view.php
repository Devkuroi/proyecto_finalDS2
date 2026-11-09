<?php

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most templating systems load templates from disk. Here you may specify
    | an array of paths that should be checked for your views. Of course
    | the usual Laravel view path has already been registered for you.
    |
    */

    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | This option determines where all the compiled Blade templates will be
    | stored for your application. In Windows/OneDrive environments, using
    | the system temp directory prevents OneDrive file-lock contention on rename().
    |
    */

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        windows_os()
            ? (function () {
                $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'laravel_cali_views';
                if (!is_dir($path)) {
                    @mkdir($path, 0777, true);
                }
                return $path;
            })()
            : realpath(storage_path('framework/views'))
    ),

];
