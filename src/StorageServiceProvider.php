<?php

namespace Gurinder\Storage;

use League\Flysystem\Filesystem;
use Gurinder\Storage\Storage\Storage;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Support\ServiceProvider;
use Gurinder\Storage\Contracts\StorageContract;
use Superbalist\Flysystem\GoogleStorage\GoogleStorageAdapter;

class StorageServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/gstorage.php' => config_path('gstorage.php')
        ], 'gstorage::config');

        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');

        $factory = $this->app->make('filesystem');
        /* @var FilesystemManager $factory */

        $factory->extend('gcs', function ($app, $config) {
            $storageClient = new StorageClient([
                'projectId'   => $config['project_id'],
                'keyFilePath' => array_get($config, 'key_file'),
            ]);
            $bucket = $storageClient->bucket($config['bucket']);
            $pathPrefix = array_get($config, 'path_prefix');
            $storageApiUri = array_get($config, 'storage_api_uri');
            $adapter = new GoogleStorageAdapter($storageClient, $bucket, $pathPrefix, $storageApiUri);
            return new Filesystem($adapter);
        });

    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/gstorage.php', 'gstorage');

        // $this->app->register(\Superbalist\LaravelGoogleCloudStorage\GoogleCloudStorageServiceProvider::class);

        $this->app->bind(StorageContract::class, function ($app) {
            return new GoogleStorage();
        });

        $this->app->alias(Storage::Class, 'Gurinder\Storage\Facades\Storage');


    }

}