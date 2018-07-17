<?php

namespace Gurinder\Storage\Storage;


use Illuminate\Http\UploadedFile;
use Gurinder\Storage\Storage\Image\RemoveImage;
use Gurinder\Storage\Contracts\StorageContract;
use Gurinder\Storage\Storage\Image\ImageUploader;
use Illuminate\Support\Facades\Storage as LaravelStorage;

class Storage implements StorageContract
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
    public function uploadImage($disk, $image, $variations = [], $public = false, $pathPrefix = 'images')
    {
        if (is_string($image)) {

            $uploadedFileInstaceGenerator = (new MakeInstanceOfUploadedFileFromBase64($image));

            $imageUploaded = (new ImageUploader($disk, $uploadedFileInstaceGenerator->getUploadedFileInstance()));

        } else if ($image instanceof UploadedFile) {

            $imageUploaded = (new ImageUploader($disk, $image));

        } else {

            throw new \Exception("Invalid base64 encoded source or invalid UploadedFile instance", 500);

        }

        $imageData = $imageUploaded->setPath($pathPrefix)->setVariations($variations)->setPublic($public)->upload();

        isset($uploadedFileInstaceGenerator) ? $uploadedFileInstaceGenerator->deleteDir() : null;

        return $imageData;


    }

    /**
     * @param       $disk
     * @param array $paths
     * @return mixed|void
     */
    public function removeImages($disk, $paths = [])
    {
        (new RemoveImage($disk, $paths))->remove();
    }

    /**
     * @param UploadedFile $document
     * @param bool         $public
     * @param null         $disk
     * @param string       $pathPrefix
     * @return mixed|string
     */
    public function uploadDocument(UploadedFile $document, $public = false, $disk = null, $pathPrefix = 'documents')
    {
        $disk = $disk ?: config('media.disk');

        return LaravelStorage::disk($disk)->putFile('documents', $document);
    }

    /**
     * @param $disk
     * @param $path
     * @return mixed
     */
    public function deleteDocumentByPath($disk, $path)
    {
        return LaravelStorage::disk($disk)->delete($path);
    }

    /**
     * @param UploadedFile $video
     * @param string       $pathPrefix
     * @param array        $variations
     * @param bool         $public
     * @return mixed|void
     */
    public function uploadVideo(UploadedFile $video, $variations = [], $public = false, $pathPrefix = 'videos')
    {

    }

}