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

    protected $deletedPaths = [];

    protected $removeFromLocalPublic;

    /**
     * RemoveImage constructor.
     * @param       $disk
     * @param array $paths
     */
    public function __construct($disk, $paths = [], $removeFromLocalPublic = false)
    {
        $this->disk = Storage::disk($disk);

        $this->paths = $paths;

        $this->removeFromLocalPublic = $removeFromLocalPublic;
    }

    /**
     * Remove Images from disk
     */
    public function remove()
    {
        foreach ($this->paths as $path) {

            $path = $this->removeFromLocalPublic ? "public/{$path}" : $path;

            if ($this->disk->delete($path)) {
                $this->deletedPaths[] = $path;
            }
        }

        return empty($this->deletedPaths) ? false : $this->deletedPaths;
    }
}