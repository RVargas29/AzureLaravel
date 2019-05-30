<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades;
use Illuminate\Support\Facades\Storage;

use App\Video;
use App\Tag;
use App\Classes\UploadToAzure;
use App\Jobs\AssignStreamingURL;

class VideoController extends Controller
{   
    //Index 
    public function getIndex() {
        $videos = Video::orderBy('created_at', 'desc')->get();
        return view('admin.video.index', ['videos' => $videos]);
    }

    //Add
    public function getAdd() {
        $tags = Tag::all();
        return view('admin.video.add', ['tags' => $tags]);
    }

    public function postAdd(Request $request) {
        $this->validate($request, [
            'title' => 'required|min:5',
        ]);

        $video = new Video([
            'title' => $request->input('title'),
            'description' => $request->input('description')
        ]);

        $file = $request->file('video');

        $fileName = $this->saveFile($file);

        $video->save();
        $video->tags()->attach($request->input('tags') === null ? [] : $request->input('tags'));    

        AssignStreamingURL::dispatch($video->id, $fileName)->delay(now()->addMinutes(5));

        return redirect()->route('admin.video.index')->with('info', 'Video added with title: ' . $request->input('title'));
    }

    private function saveFile($file) {
        $fileName = $this->renameFile($file->getClientOriginalName());
        try {
            $azureUploader = new UploadToAzure();
            return $azureUploader->uploadFileToAzure($file, $fileName);            
        } catch (Exception $e) {
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