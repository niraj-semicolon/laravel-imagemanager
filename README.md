# Laravel Imagemanager

Laravel Image Manager for v5.8.


## Installation

1. Add  ```"semilara/filemanager": "dev-master"``` in your ```composer.json```

2. Run ```composer update``` from your terminal.

3. Add ServiceProvider to Providers array in ```config/app.php```
	```Semilara\Filemanager\FilemanagerServiceProvider::class,```

4. Publish 
	 ```php artisan vendor:publish --provider="Semilara\Filemanager\FilemanagerServiceProvider" --tag="filemanager" --force```

## Usage
1. Add site's non public url to .env's ```APP_URL```

2. Add these scripts to footer of the page you want to set filemanager to, or set it on global footer
```
<script src="{{ asset('js/jquery-2.1.1.min.js') }}"></script>
<script src="{{ asset('js/filemanager.js') }}"></script>
```
3. Add these to ```<head>``` tag
```
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="{{ asset('js/app.js') }}" defer></script>
<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<link href="{{ asset('css/filemanager.css') }}" rel="stylesheet">
<link href="{{ asset('css/font-awesome/css/font-awesome.css') }}" rel="stylesheet">
```
Note: No need to add csrf-token,app.js and app.css if you are already added.

4. Add following code to blade file enable file manager 
```
<div class="image-manager-main-div">
                            
    <div style="display: none;" class="image-manager-image-div"><img src="" alt="" title=""/></div>
    <button type="button" class="image-manager-button">Set image</button>
    <input type="hidden" class="thumb-placement" name="image" id="thumb-placement" />

</div>
```
Note : if you want to use multiple thumbnails on single page then make sure to make id="thumb-placement" unique and take name="image" as an array name="image[]"

## Change config

All the default config are set in ```config/imagemanager.php```

1. Change default folder to load in imagemanager, update imagemanager_path variable. Path should be from root folder  
```
	'imagemanager_path' => 'storage/app/public',
```
2. Change supported image types, update allowed_file_extension and allowed_file_mime_types variables.  
```
	'allowed_file_extension' => ['jpg','jpeg','gif','png','webp'],
	'allowed_file_mime_types' => ['image/jpeg','image/pjpeg','image/png','image/x-png','image/gif','image/webp'],
```
3. Change Imagemanager list thumbnail width and height, update thumbnail_height and thumbnail_weight variables.  
```
	'thumbnail_height' => '100',
	'thumbnail_width' => '100',
```
## Credit
* [@opencart](https://github.com/opencart) We are inspired by Opencart's Filemanager and have used the code to develop this package.
* [@savanihd](https://github.com/savanihd) Got inspired by Hardik's code (https://www.itsolutionstuff.com/post/how-to-implement-infinite-ajax-scroll-pagination-in-laravel-5example.html) for lazyloading in image manager
