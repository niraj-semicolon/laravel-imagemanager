<?php

Route::get('/filemanager', 'semilara\filemanager\FileManagerController@index');
Route::post('/filemanager/upload', 'semilara\filemanager\FileManagerController@upload');
Route::post('/filemanager/folder', 'semilara\filemanager\FileManagerController@folder');
Route::post('/filemanager/delete', 'semilara\filemanager\FileManagerController@delete');
Route::get('/filemanager/pagination', 'semilara\filemanager\FileManagerController@pagination');
