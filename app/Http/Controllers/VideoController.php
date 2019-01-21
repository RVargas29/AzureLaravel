<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades;
use Illuminate\Support\Facades\Storage;

//use App\Jobs\UploadToAzure;

class VideoController extends Controller
{
    public function upload(Request $request) {
        /*$validation = $request->validate([
            'filepond' => 'required|file|mimetypes:video/x-ms-asf,video/x-flv,video/mp4,application/x-mpegURL,video/MP2T,video/3gpp,video/quicktime,video/x-msvideo,video/x-ms-wmv,video/avi'
        ]);*/
        return $this->saveFileLocally($request);  
    }

    private function saveFileLocally(Request $request) {
        $file = $request->file('filepond');    
        $fileName = $this->renameFile($file->getClientOriginalName());

        try {
            return Storage::putFile('tmp', $file, $fileName);
            //return Storage::disk('azure')->put($fileName, $file);
        }catch(Exception $e) {
            return null;
        }
    }

    private function renameFile($originalFileName) {
        $sb = time() . "_";
        $separatedCharacters = str_split(strtolower($originalFileName));

        foreach($separatedCharacters as $char) {
            if (($char >= '0' && $char <= '9') || ($char >= 'A' && $char <= 'Z') || ($char >= 'a' && $char <= 'z') || $char == '.' || $char == '_')
            {
                $sb = $sb . $char;
            }
        }
        
        return $sb;
    }
}
