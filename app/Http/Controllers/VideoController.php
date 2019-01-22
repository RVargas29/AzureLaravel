<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    public function upload(Request $request) {
        /*$validation = $request->validate([
            'filepond' => 'required|file|mimetypes:video/x-ms-asf,video/x-flv,video/mp4,application/x-mpegURL,video/MP2T,video/3gpp,video/quicktime,video/x-msvideo,video/x-ms-wmv,video/avi'
        ]);*/
        return $this->saveFileLocally($request);  
    }

    public function delete(Request $request) {
        $input = $request->all();
        $i = 1;
    }

    private function saveFileLocally(Request $request) {
        $file = $request->file('filepond');    
        $fileName = $this->renameFile($file->getClientOriginalName());
        try {
            //Guardo localmente el archivo en una carpeta temporal.
            $filePath = Storage::putFileAs('tmp', $file, $fileName);
            //Ejecuto el trabajo para guardar el archivo en el servidor de Azure.
            $this->dispatch(new \App\Jobs\UploadToAzure($filePath));
            //Retorno el path temporal que se creó para el archivo.
            return $filePath;
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
