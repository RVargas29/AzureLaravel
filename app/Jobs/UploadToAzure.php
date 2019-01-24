<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class UploadToAzure implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $filePath;
    private $httpClient;
    private $defaultHeaders;

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
            $azureResult = Storage::disk('azure')->put($fileName . '.' . $extension, $file);
            if($azureResult) {
                $this->encodeVideo($fileName, $fileName . '.' . $extension);
            } else {
                return "No se pudo cargar el archivo.";
            }            
        } catch(Exception $e) {
            return "No se pudo cargar el archivo.";
        }        
    }

    private function encodeVideo($fileName, $azureFilePath) {
        //Creo el cliente HTTP
        $this->httpClient = new \GuzzleHttp\Client();       
        //Obtengo el token de autorización
        $authorizationToken = $this->getAuthToken();
        //Declaro los headers default para los request
        $this->defaultHeaders = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $authorizationToken,
        ];
        //Creo el output asset
        $this->createOutputAsset($fileName);
        //Si la transformación no existe la creo.
        $this->createTransformIfNotExist();
        //Creo el trabajo de transformación
        $this->createJob($azureFilePath, $fileName);
        //Creo el streaming locator
        $this->createStreamingLocator($fileName);
        //Genero los URL
        $this->generatePlaybackURLS($fileName);
        return false;
    }

    private function getAuthToken() {
        $response = $this->httpClient->request('POST',
            'https://login.microsoftonline.com/' . config('azure.tenant_id') .'/oauth2/token',
            [
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => config('azure.client_id'),
                    'client_secret' => config('azure.client_secret'),
                    'resource' => 'https://management.core.windows.net/',
                ],
            ]
        );
        $responseBody = json_decode($response->getBody()->getContents());
        return $responseBody->access_token;
    }

    private function createOutputAsset($fileName) {
        $response = $this->httpClient->request('PUT',
            'https://management.azure.com/subscriptions/'. config('azure.subscription_id') .'/resourceGroups/'. config('azure.resource_group_name') .'/providers/Microsoft.Media/mediaServices/' . config('azure.account_name') . '/assets/assetFor_'. $fileName . '?api-version=' . config('azure.api_version'),
            [
                'json' => [
                    'properties' => [
                        'description' => 'Output asset for: ' . $fileName . '.',
                        'storageAccountName' => config('azure.storage_account_name'),
                        'container' => config('azure.storage_account_container'),
                    ]
                ],
                'headers' => $this->defaultHeaders,
            ]
        );
        $responseBody = json_decode($response->getBody()->getContents());
    }

    private function createTransformIfNotExist() {
        //Intenta obtener la transformación
        try{
            $response = $this->httpClient->request('GET',
                'https://management.azure.com/subscriptions/'. config('azure.subscription_id') .'/resourceGroups/'. config('azure.resource_group_name') .'/providers/Microsoft.Media/mediaServices/' . config('azure.account_name') . '/transforms/' . config('azure.transform_name') . '?api-version=' . config('azure.api_version'),
                [
                    'headers' => $this->defaultHeaders,
                ]
            );
            $responseBody = json_decode($response->getBody()->getContents());
        } catch(ClientException $e) {
            //404, no existe la transformación entonces la creo.
            $response = $this->httpClient->request('PUT',
                'https://management.azure.com/subscriptions/'. config('azure.subscription_id') .'/resourceGroups/'. config('azure.resource_group_name') .'/providers/Microsoft.Media/mediaServices/' . config('azure.account_name') . '/transforms/' . config('azure.transform_name') . '?api-version=' . config('azure.api_version'),
                [
                    'json' => [
                        'properties' => [
                            'description' => 'Transformación básica para la cuenta Azure del IICA',
                            'outputs' => [
                                [
                                    'onError' => 'StopProcessingJob',
                                    'relativePriority' => 'Normal',
                                    'preset' => [
                                        '@odata.type' => '#Microsoft.Media.BuiltInStandardEncoderPreset',
                                        'presetName' => 'AdaptiveStreaming'
                                    ]
                                ],                               
                            ]
                        ]
                    ],
                    'headers' => $this->defaultHeaders,
                ]
            );
            $responseBody = json_decode($response->getBody()->getContents());
        }        
    }

    private function createJob($azureFilePath, $fileName) {
        $response = $this->httpClient->request('PUT',
            'https://management.azure.com/subscriptions/'. config('azure.subscription_id') .'/resourceGroups/'. config('azure.resource_group_name') .'/providers/Microsoft.Media/mediaServices/' . config('azure.account_name') . '/transforms/' . config('azure.transform_name') . '/jobs/process_' . $fileName . '?api-version=' . config('azure.api_version'),
            [
                'json' => [
                    'properties' => [
                        'input' => [
                            '@odata.type' => '#Microsoft.Media.JobInputHttp',
                            'baseUri' => 'https://samediaservicesiica.blob.core.windows.net/' . config('azure.storage_account_container') . '/',
                            'files' => [
                                $azureFilePath
                            ]
                        ],
                        'outputs' => [
                            [
                                '@odata.type' => '#Microsoft.Media.JobOutputAsset',
                                'assetName' => 'assetFor_'. $fileName
                            ]
                        ],
                        'priority' => 'Normal'
                    ]
                ],
                'headers' => $this->defaultHeaders,
            ]            
        );
        $responseBody = json_decode($response->getBody()->getContents());
    }

    private function createStreamingLocator($fileName) {
        $response = $this->httpClient->request('PUT',
            'https://management.azure.com/subscriptions/'. config('azure.subscription_id') .'/resourceGroups/'. config('azure.resource_group_name') .'/providers/Microsoft.Media/mediaServices/' . config('azure.account_name') . '/streamingLocators/locatorFor_' . $fileName . '?api-version=' . config('azure.api_version'),
            [
                'json' => [
                    'properties' => [
                        'StreamingPolicyName' => config('azure.streaming_policy_name'),
                        'AssetName' => 'assetFor_'. $fileName
                    ]
                ],
                'headers' => $this->defaultHeaders,
            ]
        );
        $responseBody = json_decode($response->getBody()->getContents());
    }

    private function listPaths($fileName) {
        $response = $this->httpClient->request('POST',
            'https://management.azure.com/subscriptions/'. config('azure.subscription_id') .'/resourceGroups/'. config('azure.resource_group_name') .'/providers/Microsoft.Media/mediaServices/' . config('azure.account_name') . '/streamingLocators/locatorFor_' . $fileName . '/listPaths?api-version=' . config('azure.api_version'),
            [
                'headers' => $this->defaultHeaders,
            ]
        );
        $responseBody = json_decode($response->getBody()->getContents());
        return $responseBody;
    }

    private function generatePlaybackURLS($steamingPaths) {
        //ToProgramYet
    }
}