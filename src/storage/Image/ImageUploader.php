<?php

namespace Gurinder\Storage\Storage\Image;


use Ramsey\Uuid\Uuid;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Gurinder\Storage\Storage\UploadUtils;

class ImageUploader
{
    use UploadUtils;

    protected $disk;

    protected $image;

    protected $name = null;

    protected $extension;

    protected $media = [];

    protected $public = false;

    protected $imageInContext;

    protected $variations = [];

    protected $path = 'images';

    protected $interventionImage;

    protected $isLocal = false;

    public function __construct(UploadedFile $uploadedImage = null)
    {
        $disk = config('filesystems.default');

        $this->disk = Storage::disk($disk);

        $this->isLocal = $disk == 'local';

        $this->image = $uploadedImage;

        $this->name = $this->getName();

        $this->extension = $extension = $uploadedImage->getClientOriginalExtension();

        $this->setMedia($disk);

    }

    public function upload()
    {
        $this->setInterventionImage();

        if (empty($this->variations)) {
            $this->saveOriginalOnly();
        } else {
            $this->saveVariations();
        }

        return $this->media;
    }

    protected function saveOriginalOnly()
    {
        $this->interventionImage->backup();

        $this->media['name'] = $this->name;

        $storagePath = ltrim($this->path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . "{$this->name}-original.{$this->extension}";

        $path = $this->isLocal && $this->public ? "public" . DIRECTORY_SEPARATOR . $storagePath : $storagePath;

        $this->disk->put(
            $path,
            $this->interventionImage->stream($this->extension),
            $this->public ? 'public' : ''
        );

        $this->media['variations']['original'] = [
            'path'      => $storagePath,
            'mime_type' => $this->image->getClientMimeType(),
            'width'     => $this->interventionImage->width(),
            'height'    => $this->interventionImage->height()
        ];

        $this->imageInContext = null;

        $this->interventionImage->reset();

        return $this;
    }

    protected function saveVariations()
    {
        foreach ($this->variations as $variationType => $variation) {

            $this->interventionImage->backup();

            $image = $this->interventionImage->fit($variation['width'], $variation['height']);

            $this->media['name'] = $this->name;

            $storagePath = ltrim($this->path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $this->name . '-' . $variationType . '.' . $this->extension;

            $path = $this->isLocal && $this->public ? "public" . DIRECTORY_SEPARATOR . $storagePath : $storagePath;

            $this->disk->put(
                $path,
                $image->stream($this->extension),
                $this->public ? 'public' : ''
            );

            $this->media['variations'][$variationType] = [
                'path'      => $storagePath,
                'mime_type' => $this->image->getClientMimeType(),
                'width'     => $variation['width'],
                'height'    => $variation['height']
            ];

            $this->interventionImage->reset();
        }

        return $this;
    }


    /**
     * Set intervention instance to variable for use
     */
    public function setInterventionImage()
    {
        $this->interventionImage = Image::make($this->image);;

        $orientation = $this->interventionImage->exif('Orientation');

        if (!empty($orientation)) {

            switch ($orientation) {
                case 8:
                    $this->interventionImage = $this->interventionImage->rotate(90);
                    break;

                case 3:
                    $this->interventionImage = $this->interventionImage->rotate(180);
                    break;

                case 6:
                    $this->interventionImage = $this->interventionImage->rotate(-90);
                    break;
            }

        }

        return $this;
    }

    /**
     * @param mixed $variations
     * @return ImageUploader
     */
    public function setVariations($variations = [])
    {
        $this->variations = $variations;
        return $this;
    }

    protected function getName()
    {
        return md5(Uuid::uuid4()->toString() . time() . str_random(10));
    }

    /**
     * @param bool $public
     * @return ImageUploader
     */
    public function setPublic(bool $public)
    {
        $this->public = $public ?: $this->public;
        $this->media['public'] = $this->public;
        return $this;
    }

    public function setMedia($disk)
    {
        $this->media = [
            'name'         => $this->name,
            'extension'    => $this->extension,
            'mime_type'    => $this->image->getClientMimeType(),
            'file_type'    => 'image',
            'public'       => $this->public ? true : false,
            'variations'   => [],
            'storage_disk' => $disk
        ];
    }

    /**
     * @param mixed $path
     * @return ImageUploader
     */
    public function setPath($path)
    {
        $this->path = $this->sanitizePath($path);
        return $this;
    }

    /**
     * @param mixed $disk
     * @return ImageUploader
     */
    public function setDisk($disk)
    {
        $this->disk = $disk;
        return $this;
    }

    /**
     * @param mixed $disk
     * @return ImageUploader
     */
    public function setName($name)
    {
        $this->name = $name ?: $this->name;
        return $this;
    }

}