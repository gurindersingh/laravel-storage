<?php

namespace Gurinder\Storage\Storage;


use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Storage;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class MakeInstanceOfUploadedFileFromBase64
{

    use UploadUtils;

    /**
     * @var
     */
    protected $string;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var
     */
    protected $path;

    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected $disk;

    /**
     * @var
     */
    protected $extension;

    /**
     * @var array
     */
    protected $result = [];

    /**
     * @var null|string
     */
    protected $tempFilePath;

    /**
     * @var
     */
    protected $imageBase64Data;

    /**
     * @var null
     */
    protected $uploadedFileInstance = null;

    /**
     * MakeInstanceOfUploadedFileFromBase64 constructor.
     * @param $string
     */
    public function __construct($string)
    {
        $this->string = $string;

        $this->disk = Storage::disk('local');

        $this->tempDir = (new TemporaryDirectory);

        $imageParts = explode(";base64,", $string);

        $this->imageBase64Data = $imageParts[1];

        $this->name = md5(str_random(10) . time());

        $this->extension = explode("image/", $imageParts[0])[1];

        if ($this->tempFilePath = $this->uploadImageToTemporaryFolder()) {

            $absolutePathToFile = storage_path("app" . DIRECTORY_SEPARATOR . $this->tempFilePath);

            $this->setUploadedFileInstance(getUploadedFileInstance($absolutePathToFile));

        }

    }

    /**
     * @return null|string
     */
    protected function uploadImageToTemporaryFolder()
    {
        $uuid = md5(Uuid::uuid4()->toString() . time() . str_random(10));

        $imageDir = "temp" . DIRECTORY_SEPARATOR . "$uuid";

        $tempFilePath = $imageDir . DIRECTORY_SEPARATOR . "$this->name.$this->extension";

        if ($this->disk->put($tempFilePath, base64_decode($this->imageBase64Data))) {

            $this->optimizeImage(storage_path("app/{$tempFilePath}"));

            return $tempFilePath;
        }

        return null;
    }

    /**
     * Delete this folder
     */
    public function deleteDir()
    {
        $path = explode(DIRECTORY_SEPARATOR, $this->tempFilePath);

        array_pop($path);

        $this->disk->deleteDirectory(implode(DIRECTORY_SEPARATOR, $path));
    }

    /**
     * @return mixed
     */
    public function getInstance()
    {
        return $this->uploadedFileInstance;
    }

    /**
     * @param mixed $uploadedFileInstance
     */
    public function setUploadedFileInstance($uploadedFileInstance): void
    {
        $this->uploadedFileInstance = $uploadedFileInstance;
    }

}