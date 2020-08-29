<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Image Manager Path
    |--------------------------------------------------------------------------
    |
    | Here you may specify what folder to open with Image Manager. 
    | Preferrable path would be from root folder.
    */
    'imagemanager_path' => 'storage/app/public',

    /*
    |--------------------------------------------------------------------------
    | Default Image Manager file extensions and mime types
    |--------------------------------------------------------------------------
    |
    | Here you may specify what file extensions & mime type to open with Image Manager. 
    | 
    */
    'allowed_file_extension' => ['jpg','jpeg','gif','png','webp'],
    'allowed_file_mime_types' => ['image/jpeg','image/pjpeg','image/png','image/x-png','image/gif','image/webp'],
    /*
    |--------------------------------------------------------------------------
    | Default Image Manager thumbnail height and width
    |--------------------------------------------------------------------------
    |
    | Here you may specify what should be height x width of thumbnails in Image Manager. 
    | 
    */
    'thumbnail_height' => '100',
    'thumbnail_width' => '100',


];
