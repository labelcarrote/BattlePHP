<?php
namespace BattlePHP\Storage;
/**
 * Uploader
 * - Helper for upload
 * - Note : works with upload progression in js (like in this presentation 
 * http://www.nczonline.net/blog/2012/11/13/javascript-apis-youve-never-heard-of/
 * (@ 27'34) 
 * For examples, see javascript in sawhat.js and flipapart.js
 * @author touchypunchy
 */
class Uploader{
    
    public static function get_img_extensions(){ 
        return array(".jpg",".png",".jpeg",".JPG",".gif");
    }
    
    /**
     * Get uploaded file (sended from a html form, in $_FILES), change its name and move it to the specified folder.
     * ex:
     * Uploader::process_form_file("amazingpicture","great/folder",10000,array(".jpg",".png",".bmp"))
     */
    public static function process_form_file($inputname, $targetpath, $sizemaxinbyte, $extensions, $output_filename_prefix = null){
        if(!isset($_FILES[$inputname]))
            throw new Exception("no file found");

        $filename = $_FILES[$inputname]['name'];
        $extension = strrchr($filename,'.');

        if(!in_array($extension, $extensions))
            throw new Exception("unallowed extension");

        $size = filesize($_FILES[$inputname]['tmp_name']);
        if($size > $sizemaxinbyte)
            throw new Exception("file is too big");

        // change name (note the @ before md5_file is here to hide the potential warning if file is too big)
        $output_filename = @md5_file($_FILES[$inputname]['tmp_name']).$extension;
        if(!is_null($output_filename_prefix))
            $output_filename = "{$output_filename_prefix}{$output_filename}";
        
        // Create directory if it does not exist
        $dir = "{$targetpath}/";
        if(!is_dir($dir))
            mkdir($dir, 0755, true);

        // Note: the temp file will be deleted after the script execution if not moved
        if(!move_uploaded_file($_FILES[$inputname]['tmp_name'],$dir.$output_filename))
            throw new Exception("can't move uploaded file");
            
        return $output_filename;
    }
}
