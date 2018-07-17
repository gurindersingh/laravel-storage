<?php

namespace Gurinder\Storage\Contracts;

use Illuminate\Http\UploadedFile;

interface StorageContract
{


    /**
     * @param        $disk
     * @param        $image
     * @param string $pathPrefix
     * @param array  $variations
     * @param bool   $public
     * @return array
     * @throws \Exception
     */
    public function uploadImage($disk, $image, $variations = [], $public = false, $pathPrefix = 'images');

    /**
     * @param       $disk
     * @param array $paths
     * @return mixed
     */
    public function removeImages($disk, $paths = []);

    /**
     * @param UploadedFile $document
     * @param string       $pathPrefix
     * @param array        $variations
     * @param bool         $public
     * @return mixed
     */
    public function uploadDocument(UploadedFile $document, $variations = [], $public = false, $pathPrefix = 'documents');

    /**
     * @param UploadedFile $video
     * @param string       $pathPrefix
     * @param array        $variations
     * @param bool         $public
     * @return mixed
     */
    public function uploadVideo(UploadedFile $video, $variations = [], $public = false, $pathPrefix = 'videos');
}