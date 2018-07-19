<?php

namespace Gurinder\Storage\Storage;


use Illuminate\Http\UploadedFile;
use Gurinder\Storage\Storage\Image\RemoveImage;
use Gurinder\Storage\Contracts\StorageContract;
use Gurinder\Storage\Storage\Image\ImageUploader;
use Illuminate\Support\Facades\Storage as LaravelStorage;

class Storage implements StorageContract
{

    protected $variations = null;

    protected $public = false;

    protected $name = null;

    /**
     * @param        $disk
     * @param        $image
     * @param string $pathPrefix
     * @param array  $variations
     * @param bool   $public
     * @return array
     * @throws \Exception
     */
    public function uploadImage($image)
    {
        if (is_string($image)) {

            $uploadedFileInstaceGenerator = (new MakeInstanceOfUploadedFileFromBase64($image));

            $imageUploader = (new ImageUploader($uploadedFileInstaceGenerator->getInstance()));

        } else if ($image instanceof UploadedFile) {

            $imageUploader = (new ImageUploader($image));

        } else {

            throw new \Exception("Invalid base64 encoded source or invalid UploadedFile instance", 500);

        }

        $imageData = $imageUploader
            ->setVariations(is_null($this->variations) ? config('media.image_variations') : $this->variations)
            ->setName($this->name)
            ->setPublic($this->public)
            ->upload();

        isset($uploadedFileInstaceGenerator) ? $uploadedFileInstaceGenerator->deleteDir() : null;

        return $imageData;


    }

    /**
     * @param       $disk
     * @param array $paths
     * @return mixed|void
     */
    public function removeImages($disk, $paths = [], $removeFromLocalPublic = false)
    {
        (new RemoveImage($disk, $paths, $removeFromLocalPublic))->remove();
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
        $disk = $disk ?: config('filesystems.default');

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

    public function setVariations($variations)
    {
        $this->variations = $variations;

        return $this;
    }

    public function setPublic($public)
    {
        $this->public = $public;

        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

}