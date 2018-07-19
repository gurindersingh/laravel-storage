<?php

if (!function_exists('validateBase64ImageString')) {

    /**
     * Check if Image string is valid or not
     *
     * @param       $string
     * @param int   $maxSizeInMB
     * @param array $validMimeTypes
     * @return boolean
     */
    function validateBase64ImageString($string, $maxSizeInMB = 3, $validMimeTypes = ['image/jpg', 'image/png', 'image/jpeg', 'image/gif'])
    {
        try {

            $base64string = base64_decode(explode(";base64,", $string)[1]);

            $info = getimagesizefromstring($base64string);

            unset($string);

            if (!$info || ($info[0] <= 0) || ($info[1] <= 0)) return false;

            if (is_array($validMimeTypes) && !empty($validMimeTypes)) {
                if (!in_array($info['mime'], $validMimeTypes)) return false;
            }

            $sizeInKB = (strlen($base64string) - substr_count(substr($base64string, -2), '=')) / 1024;

            $sizeInMB = $sizeInKB / 1024;

            if ($maxSizeInMB && ($sizeInMB > $maxSizeInMB)) return false;

            unset($base64string);

            return true;

        } catch (\Exception $exception) {

            return false;

        }
    }
}

if (!function_exists('getUploadedFileInstance')) {

    /**
     * Get Uploaded File instance
     *
     * @param string $path
     * @param bool   $public
     * @return \Illuminate\Http\UploadedFile
     */
    function getUploadedFileInstance($path, $public = false)
    {
        $name = \Illuminate\Support\Facades\File::name($path);

        $extension = \Illuminate\Support\Facades\File::extension($path);

        $originalName = $name . '.' . $extension;

        $mimeType = \Illuminate\Support\Facades\File::mimeType($path);

        $size = \Illuminate\Support\Facades\File::size($path);

        $error = null;

        return new \Illuminate\Http\UploadedFile($path, $originalName, $mimeType, $size, $error, $public);
    }
}

if (!function_exists('storageUrl')) {

    /**
     * @param      $path
     * @param null $prefix
     * @return string
     */
    function storageUrl($path, $prefix = null)
    {
        $pathPrefix = '/';

        if ($cloudUrlPrefix = config('media.cloud_url_prefix')) {
            $pathPrefix = $prefix ?: trim($cloudUrlPrefix, '/');
        }

        $path = trim($path, '/');

        return "{$pathPrefix}/{$path}";
    }

}