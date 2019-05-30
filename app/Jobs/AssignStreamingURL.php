<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Video;
use App\Classes\UploadToAzure;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class AssignStreamingURL implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $videoId;
    private $fileName;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($videoId, $fileName)
    {
        $this->videoId = $videoId;
        $this->fileName = $fileName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $video = Video::find($this->videoId);

        if($video != null) {
            $azureUploader = new UploadToAzure();
            $jobIterations = 0;
            do {
                $job = $azureUploader->getJob($this->fileName);
                if($job->properties->state == "Finished") {
                    $paths = $azureUploader->getPaths($this->fileName);
                    $i = 0;
                    $video->hls_url = $paths->streamingPaths[0]->paths[0];
                    $video->dash_url = $paths->streamingPaths[1]->paths[0];
                    $video->smooth_streaming_url = $paths->streamingPaths[2]->paths[0];
                    $video->published = true;
                    $video->save();
                    return true;
                } else {
                    sleep(1*60);
                    $jobIterations++;
                }
            } while ($jobIterations < 10); 
            return false;           
        }
    }
}
