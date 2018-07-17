<?php

namespace Gurinder\Storage\Storage;


use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;

trait UploadUtils
{

    /**
     * @param string $path
     * @return string
     */
    protected function sanitizePath(string $path): string
    {
        $path = rtrim($path);
        return rtrim($path, DIRECTORY_SEPARATOR);
    }

    /**
     * @param $pathToImage
     */
    protected function optimizeImage($pathToImage)
    {
        ImageOptimizer::optimize($pathToImage);
    }

}