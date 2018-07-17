<?php

namespace Gurinder\Storage\Storage\Image;


use Illuminate\Support\Facades\Storage;

class RemoveImage
{
    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected $disk;

    /**
     * @var array
     */
    protected $paths;

    /**
     * RemoveImage constructor.
     * @param       $disk
     * @param array $paths
     */
    public function __construct($disk, $paths = [])
    {
        $this->disk = Storage::disk($disk);

        $this->paths = $paths;
    }

    /**
     * Remove Images from disk
     */
    public function remove()
    {
        foreach ($this->paths as $path) {
            $this->disk->delete($path);
        }
    }
}