<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades;
use Illuminate\Support\Facades\Storage;

use App\Video;
use App\Tag;

class VideoController extends Controller
{   
    //Index 
    public function getIndex() {
        $videos = Videos::orderBy('created_at', 'desc')->get();
        return view('admin.video.index', ['videos' => $videos]);
    }

    //Add
    public function getAdd() {
        $tags = Tag::all();
        return view('admin.videos.add');
    }

    public function postAdd(Request $request) {
        $this->validate($request, [
            'title' => 'required|min:5',
            'url' => 'required'
        ]);

        $video = new Video([
            'title' => $request->input('title'),
            'url' => $request->input('url'),
            'thumbnail' => $request->input('thumbnail')
        ]);

        $video->save();
        $video->tags()->attach($request->input('tags') === null ? [] : $request->input('tags'));    

        return redirect()->route('admin.video.index')->with('info', 'Video added with title: ' . $request->input('title'));
    }
}




/*
class VideoControllerOld extends Controller {
    public function upload(Request $request) {
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
}*/