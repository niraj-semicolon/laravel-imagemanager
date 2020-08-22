<?php

namespace Semilara\Filemanager;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Storage;

class FileManagerController extends Controller
{

    public function __construct() 
    {
        $this->path = 'storage/app/public';
        $this->storage_path = base_path().'/'.$this->path;
        $this->url = env('APP_URL').'/'.$this->path;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request) {
        
        if (null !== $request->get('filter_name')) {
            $filter_name = rtrim(str_replace(array('*', '/', '\\'), '', $request->get('filter_name')), '/');
        } else {
            $filter_name = '';
        }

        // Make sure we have the correct directory
        if (null !== $request->get('directory')) {
            $directory = rtrim($this->storage_path .'/'. str_replace('*', '', $request->get('directory')), '/');
        } else {
            $directory = $this->storage_path;
        }

        if (null !== $request->get('directory')) {
            $data['directory'] = urlencode($request->get('directory'));
        } else {
            $data['directory'] = '';
        }

        if (null !== $request->get('filter_name')) {
            $data['filter_name'] = $request->get('filter_name');
        } else {
            $data['filter_name'] = '';
        }

        // Return the target ID for the file manager to set the value
        if (null !== $request->get('target')) {
            $data['target'] = $request->get('target');
        } else {
            $data['target'] = '';
        }


        // Parent
        $url = '';

        if (null !== $request->get('directory')) {
            $pos = strrpos($request->get('directory'), '/');

            if ($pos) {
                $url .= '&directory=' . urlencode(substr($request->get('directory'), 0, $pos));
            }
        }

        if (null !== $request->get('target')) {
            $url .= '&target=' . $request->get('target');
        }


        $data['parent'] = url('filemanager?1=1'. $url);

        // Refresh
        $url = '';

        if (null !== $request->get('directory')) {
            $url .= '&directory=' . urlencode($request->get('directory'));
        }

        if (null !== $request->get('target')) {
            $url .= '&target=' . $request->get('target');
        }

        $data['refresh'] = url('filemanager?1=1'. $url);

        $url = '';

        if (null !== $request->get('directory')) {
            $url .= '&directory=' . urlencode(html_entity_decode($request->get('directory'), ENT_QUOTES, 'UTF-8'));
        }

        if (null !== $request->get('filter_name')) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($request->get('filter_name'), ENT_QUOTES, 'UTF-8'));
        }

        if (null !== $request->get('target')) {
            $url .= '&target=' . $request->get('target');
        }

        $data['heading_title'] = 'Image Manager';
        return view('filemanager::filemanager',compact('data'));
    }

    /**
     * show pagination.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function pagination(Request $request) {
        
        if (null !== $request->get('filter_name')) {
            $filter_name = rtrim(str_replace(array('*', '/', '\\'), '', $request->get('filter_name')), '/');
        } else {
            $filter_name = '';
        }

        // Make sure we have the correct directory
        if (null !== $request->get('directory')) {
            $directory = rtrim($this->storage_path .'/'. str_replace('*', '', $request->get('directory')), '/');
        } else {
            $directory = $this->storage_path;
        }

        if (null !== $request->get('page')) {
            $page = $request->get('page');
        } else {
            $page = 1;
        }

        $directories = array();
        $files = array();
        
        $data['images'] = array();

        if (substr(str_replace('\\', '/', realpath($directory) . '/' . $filter_name), 0, strlen($this->storage_path)) == str_replace('\\', '/', $this->storage_path)) {
            // Get directories
            //ignore _thumb directories, as we are storing thumbs to _thumb directory
            $directories = preg_grep('#\_thumb$#', glob($directory . '/' . $filter_name . '*', GLOB_ONLYDIR), PREG_GREP_INVERT);
            if (!$directories) {
                $directories = array();
            }

            // Get files
            $files = glob($directory . '/' . $filter_name . '*.{jpg,jpeg,png,gif,JPG,JPEG,PNG,GIF}', GLOB_BRACE);

            if (!$files) {
                $files = array();
            }
        }

        // Merge directories and files
        $images = array_merge($directories, $files);

        // Get total number of files and directories
        $image_total = count($images);

        // Split the array based on current page number and max number of items per page of 10
        $images = array_splice($images, ($page - 1) * 24, 24);

        foreach ($images as $image) {
            $name = str_split(basename($image), 14);

            if (is_dir($image)) {
                $url = '';

                if (null !== $request->get('target')) {
                    $url .= '&target=' . $request->get('target');
                }

                if (null !== $request->get('thumb')) {
                    $url .= '&thumb=' . $request->get('thumb');
                }

                $data['images'][] = array(
                    'thumb' => '',
                    'name'  => implode(' ', $name),
                    'type'  => 'directory',
                    'path'  => mb_substr($image, mb_strlen($this->storage_path)),
                    'href'  => url('filemanager?directory=' . urlencode(mb_substr($image, mb_strlen($this->storage_path.'/'))) . $url)
                );
            } elseif (is_file($image)) {
                $data['images'][] = array(
                    'thumb' => $this->resizeImage(mb_substr($image, mb_strlen($this->storage_path)), 100, 100),
                    'name'  => implode(' ', $name),
                    'type'  => 'image',
                    'path'  => mb_substr($image, mb_strlen($this->storage_path)),
                    'href'  => $this->url.mb_substr($image, mb_strlen($this->storage_path)),
                );
            }
        }

        $data['heading_title'] = 'Image Manager';
        return view('filemanager::data',compact('data'));
    }

    //upload image
    public function upload(Request $request) {
        
        $json = array();

        $this->storage_path = storage_path('app/public');


        // Make sure we have the correct directory
        if (null !== $request->get('directory')) {
            $directory = rtrim($this->storage_path .'/'. $request->get('directory'), '/');
        } else {
            $directory = $this->storage_path;
        }
        
        // Check its a directory
        if (!is_dir($directory) || substr(str_replace('\\', '/', realpath($directory)), 0, strlen($this->storage_path)) != str_replace('\\', '/', $this->storage_path)) {
            $json['error'] = 'Warning: Directory does not exist!';
        }

        if (!$json) {
            
            // Check if multiple files are uploaded or just one

            if ($upload_files = $request->file('file')) {
                
                foreach ($upload_files as $upload_file) {

                    if (is_file($upload_file->getPathName())) {
                        // Sanitize the filename
                        $filename = basename(html_entity_decode($upload_file->getClientOriginalName(), ENT_QUOTES, 'UTF-8'));

                        // Validate the filename length
                        if ((mb_strlen($filename) < 3) || (mb_strlen($filename) > 255)) {
                            $json['error'] = 'Warning: Filename must be between 3 and 255!';
                        }

                        // Allowed file extension types
                        $allowed = array(
                            'jpg',
                            'jpeg',
                            'gif',
                            'png'
                        );

                        if (!in_array(mb_strtolower(mb_substr(strrchr($filename, '.'), 1)), $allowed)) {
                            $json['error'] = 'Warning: Incorrect file type!';
                        }

                        // Allowed file mime types
                        $allowed = array(
                            'image/jpeg',
                            'image/pjpeg',
                            'image/png',
                            'image/x-png',
                            'image/gif'
                        );

                        if (!in_array($upload_file->getMimeType(), $allowed)) {
                            $json['error'] = 'Warning: Incorrect file type!';
                        }

                        // Return any upload error
                        if (!$upload_file->isValid()) {
                            $json['error'] = trans('filemanager.error_upload_'.$upload_file->getError());
                        }
                    } else {
                        $json['error'] = 'Warning: File could not be uploaded for an unknown reason!';
                    }

                    if (!$json) {
                        $upload_file->move($directory , $filename);
                    }
                }
            }
        }

        if (!$json) {
            $json['success'] = 'Successfully Uploaded!';
        }

        return response()->json($json);
    }

    public function folder(Request $request) {
        
        $json = array();

        
        // Make sure we have the correct directory
        if (null !== $request->get('directory')) {
            $directory = rtrim($this->storage_path . $request->get('directory'), '/');
        } else {
            $directory = $this->storage_path;
        }

        // Check its a directory
        if (!is_dir($directory) || substr(str_replace('\\', '/', realpath($directory)), 0, strlen($this->storage_path)) != str_replace('\\', '/', $this->storage_path)) {
            $json['error'] = 'Warning: Directory does not exist!';
        }

        if ($request->isMethod('post')) {
            // Sanitize the folder name
            $folder = basename(html_entity_decode($request->get('folder'), ENT_QUOTES, 'UTF-8'));

            // Validate the filename length
            if ((mb_strlen($folder) < 2) || (mb_strlen($folder) > 128)) {
                $json['error'] = 'Warning: Folder name must be between 2 and 255!';
            }

            // Check if directory already exists or not
            if (is_dir($directory . '/' . $folder)) {
                $json['error'] = 'Warning: A file or directory with the same name already exists!';
            }
        }

        if (!isset($json['error'])) {
            mkdir($directory . '/' . $folder, 0777);
            chmod($directory . '/' . $folder, 0777);

            @touch($directory . '/' . $folder . '/' . 'index.html');

            $json['success'] = 'Success: Directory created!';
        }

        return response()->json($json);
    }

    public function delete(Request $request) {
       
        $json = array();

        if (null !== $request->get('path')) {
            $paths = $request->get('path');
        } else {
            $paths = array();
        }
        // Loop through each path to run validations
        foreach ($paths as $path) {
            // Check path exsists
            if ($path == $this->storage_path || substr(str_replace('\\', '/', realpath($this->storage_path . $path)), 0, strlen($this->storage_path)) != str_replace('\\', '/', $this->storage_path)) {
                $json['error'] = 'Warning: You can not delete this directory!';

                break;
            }
        }

        if (!$json) {
            // Loop through each path
            foreach ($paths as $path) {
                $path = rtrim($this->storage_path . $path, '/');

                // If path is just a file delete it
                if (is_file($path)) {
                    unlink($path);

                // If path is a directory beging deleting each file and sub folder
                } elseif (is_dir($path)) {
                    $files = array();

                    // Make path into an array
                    $path = array($path);

                    // While the path array is still populated keep looping through
                    while (count($path) != 0) {
                        $next = array_shift($path);

                        foreach (glob($next) as $file) {
                            // If directory add to path array
                            if (is_dir($file)) {
                                $path[] = $file . '/*';
                            }

                            // Add the file to the files to be deleted array
                            $files[] = $file;
                        }
                    }

                    // Reverse sort the file array
                    rsort($files);

                    foreach ($files as $file) {
                        // If file just delete
                        if (is_file($file)) {
                            unlink($file);

                        // If directory use the remove directory function
                        } elseif (is_dir($file)) {
                            rmdir($file);
                        }
                    }
                }
            }

            $json['success'] = 'Success: Your file or directory has been deleted!';
        }

        return response()->json($json);
    }

    public function resizeImage($filename, $width, $height) {
        
        $this->storage_path = storage_path('app/public');

        if (!is_file($this->storage_path . $filename) || substr(str_replace('\\', '/', realpath($this->storage_path . $filename)), 0, strlen($this->storage_path)) != str_replace('\\', '/', $this->storage_path)) {
            return;
        }

        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        $image_old = $filename;
        $image_new = '/_thumb' . mb_substr($filename, 0, mb_strrpos($filename, '.')) . '-' . $width . 'x' . $height . '.' . $extension;
        
        if (!is_file($this->storage_path . $image_new) || (filemtime($this->storage_path . $image_old) > filemtime($this->storage_path . $image_new))) {
            list($width_orig, $height_orig, $image_type) = getimagesize($this->storage_path . $image_old);
                 
            if (!in_array($image_type, array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF))) { 
                return $this->storage_path . $image_old;
            }
 
            $path = '';

            $directories = explode('/', dirname($image_new));

            foreach ($directories as $directory) {
                $path = $path . '/' . $directory;

                if (!is_dir($this->storage_path . $path)) {
                    @mkdir($this->storage_path . $path, 0777);
                }
            }

            if ($width_orig != $width || $height_orig != $height) {
                $this->resize($this->storage_path . $image_old, $width, $height, '', $this->storage_path . $image_new);
            } else {
                copy($this->storage_path . $image_old, $this->storage_path . $image_new);
            }
        }

        return $this->url.$image_new;

    }

    public function resize($file, $width = 0, $height = 0, $default = '', $file_new) {
    
        if (file_exists($file)) {
            
            $info = getimagesize($file);

            $file_width  = $info[0];
            $file_height = $info[1];
            $file_bits = isset($info['bits']) ? $info['bits'] : '';
            $file_mime = isset($info['mime']) ? $info['mime'] : '';

            if ($file_mime == 'image/gif') {
                $image = imagecreatefromgif($file);
            } elseif ($file_mime == 'image/png') {
                $image = imagecreatefrompng($file);
            } elseif ($file_mime == 'image/jpeg') {
                $image = imagecreatefromjpeg($file);
            }
        } else {
            exit('Error: Could not load image ' . $file . '!');
        }

        $xpos = 0;
        $ypos = 0;
        $scale = 1;

        $scale_w = $width / $file_width;
        $scale_h = $height / $file_height;

        if ($default == 'w') {
            $scale = $scale_w;
        } elseif ($default == 'h') {
            $scale = $scale_h;
        } else {
            $scale = min($scale_w, $scale_h);
        }

        if ($scale == 1 && $scale_h == $scale_w && $file_mime != 'image/png') {
            return;
        }

        $new_width = (int)($file_width * $scale);
        $new_height = (int)($file_height * $scale);
        $xpos = (int)(($width - $new_width) / 2);
        $ypos = (int)(($height - $new_height) / 2);

        $image_old = $image;
        $image = imagecreatetruecolor($width, $height);

        if ($file_mime == 'image/png') {
            imagealphablending($image, false);
            imagesavealpha($image, true);
            $background = imagecolorallocatealpha($image, 255, 255, 255, 127);
            imagecolortransparent($image, $background);
        } else {
            $background = imagecolorallocate($image, 255, 255, 255);
        }

        imagefilledrectangle($image, 0, 0, $width, $height, $background);

        imagecopyresampled($image, $image_old, $xpos, $ypos, 0, 0, $new_width, $new_height, $file_width, $file_height);
        imagedestroy($image_old);

        $file_width = $width;
        $file_height = $height;

        $info_new = pathinfo($file_new);
        $extension_new = strtolower($info_new['extension']);

        if ($extension_new == 'jpeg' || $extension_new == 'jpg') {
            imagejpeg($image, $file_new, 100);
        } elseif ($extension_new == 'png') {
            imagepng($image, $file_new);
        } elseif ($extension_new == 'gif') {
            imagegif($image, $file_new);
        }

        imagedestroy($image);

    }

}
