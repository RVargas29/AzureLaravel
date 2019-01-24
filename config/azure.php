<?php

return[
    'tenant_id' => env('AZURE_TENANT_ID'),
    'client_id' => env('AZURE_CLIENT_ID'),
    'client_secret' => env('AZURE_CLIENT_SECRET'),
    'subscription_id' => env('AZURE_SUBSCRIPTION_ID'),
    'account_name' => env('AZURE_ACCOUNT_NAME'),
    'resource_group_name' => env('AZURE_RESOURCE_GROUP_NAME'),
    'api_version' => env('AZURE_API_VERSION'),
    'streaming_policy_name' => env('AZURE_STREAMING_POLICY_NAME'),
    'transform_name' => env('AZURE_TRANSFORM_NAME'),
    'location' => env('AZURE_LOCATION'),
    'storage_account_name'  => env('AZURE_STORAGE_NAME'),
    'storage_account_key'       => env('AZURE_STORAGE_KEY'),
    'storage_account_container' => env('AZURE_STORAGE_CONTAINER'), 
    'streaming_endpoint_url' => env('AZURE_STREAMING_ENDOPOINT'),
];