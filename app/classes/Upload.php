<?php
/**
 * Created by PhpStorm.
 * User: Ahmed
 * Date: 9/19/2018
 * Time: 8:05 PM
 */

namespace App\classes;
use App\UserData;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeExtensionGuesser;

class Upload
{
    public static function productsSheet($name,$newName){
        $target_dir = "../uploadedExcelFiles/";
        $target_file = $target_dir . $newName .'_' .basename($_FILES[$name]["name"]);
        $uploadOk = 1;

        // allowed formats :
        $formats = [
             'xls',
             'xlam',
             'xlsb',
             'xlsm',
             'xltm',
             'xlsx',
             'bin',
        ];


        // check file extensions
        $guesser = new MimeTypeExtensionGuesser ;
        $format  = $guesser->guess($_FILES[$name]['type']);

        // check file type :
        if(!in_array($format,$formats)){
            $uploadOk = 0 ;
        }

        // Check file size
        if ($_FILES[$name]["size"] > 45000000) {
            $uploadOk = 0;
        }
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            return false;
            // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES[$name]["tmp_name"], $target_file)) {
                return [
                    'path' => $target_file,
                    'format' => $_FILES[$name]['type']
                ];
            } else {
                return false;
            }
        }
    }
}
