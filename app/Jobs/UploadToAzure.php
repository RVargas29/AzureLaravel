<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades;
use Illuminate\Support\Facades\Storage;

class UploadToAzure implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $filePath;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        return $this->uploadFileToAzure($this->filePath);
    }

    private function uploadFileToAzure($filePath) {
        $file = Storage::get($filePath);

        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $fileName = basename($filePath, '.' . $extension);

        try {
            //Inicio la carga del video a Azure.
            return Storage::disk('azure')->put($fileName . '/' . $fileName . '.' . $extension, $file);
        } catch(Exception $e) {
            return "No se pudo cargar el archivo.";
        }        
    }

    private function notify() {

    }
}
