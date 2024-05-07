<?php

namespace Utilita;

class HtmlService
{
    /**
     * Toglie da input html array i caratteri
     *
     * @param [type] $item
     * @return void
     */
    public static function _htmlArray($item)
    {
        $pattern = [
            '/["]/',
        ];
        $replace = [
            '',
        ];
        foreach ($item as $k => $v) {
            if (is_array($v)) {
                $array[preg_replace($pattern, $replace, $k)] = call_user_func(__FUNCTION__, $v);
            } else {
                $array[preg_replace($pattern, $replace, $k)] = $v;
            }
        }
        return (isset($array)) ? $array : false;
    }

    /**
     * Resize Image
     *
     * @param string $imagePath
     * @param string $imagePath
     * @param string $newPath
     * @param integer $newWidth
     * @param integer $newHeight
     * @param string $outExt
     * @return string|null
     */
    public static function _ResizeImage(
        string $imagePath = null,
        string $imageExt = null,
        string $newPath = null,
        int $newWidth = 0,
        int $newHeight = 0,
        string $outExt = 'DEFAULT'
    ): ?string {

        if (null === $imagePath || !file_exists($imagePath)) {
            return false;
        }
        if ($outExt === 'DEFAULT') {
            $outExt = $imageExt;
        }
        $types = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_BMP, IMAGETYPE_WEBP];
        $type = exif_imagetype($imagePath);
        if (!in_array($type, $types)) {
            return false;
        }
        list($width, $height) = getimagesize($imagePath);
        if ($newWidth === 0) {
            $newWidth = ceil($width / 3);
        }
        if ($newWidth > 390) {
            $newWidth = 390;
        }
        if ($newHeight === 0) {
            $newHeight = ceil($height / 3);
        }
        if ($newHeight > 255) {
            $newHeight = 255;
        }
        $outBool = in_array($outExt, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']);
        switch ($type) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($imagePath);
                if (!$outBool) $outExt = 'jpg';
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($imagePath);
                if (!$outBool) $outExt = 'png';
                break;
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($imagePath);
                if (!$outBool) $outExt = 'gif';
                break;
            case IMAGETYPE_BMP:
                $image = imagecreatefrombmp($imagePath);
                if (!$outBool) $outExt = 'bmp';
                break;
            case IMAGETYPE_WEBP:
                $image = imagecreatefromwebp($imagePath);
                if (!$outBool) $outExt = 'webp';
        }
        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        //TRANSPARENT BACKGROUND
        $color = imagecolorallocatealpha($newImage, 0, 0, 0, 127); //fill transparent back
        imagefill($newImage, 0, 0, $color);
        imagesavealpha($newImage, true);

        //ROUTINE
        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Rotate image on iOS
        /*
        if (function_exists('exif_read_data') && $exif = exif_read_data($imagePath, 'IFD0')) {
            if (isset($exif['Orientation']) && isset($exif['Make']) && !empty($exif['Orientation']) && preg_match('/(apple|ios|iphone)/i', $exif['Make'])) {
                switch ($exif['Orientation']) {
                    case 8:
                        if ($width > $height) $newImage = imagerotate($newImage, 90, 0);
                        break;
                    case 3:
                        $newImage = imagerotate($newImage, 180, 0);
                        break;
                    case 6:
                        $newImage = imagerotate($newImage, -90, 0);
                        break;
                }
            }
        }
        */
        switch (true) {
            case in_array($outExt, ['jpg', 'jpeg']):
                if (null === $newPath) {
                    ob_start();
                    imagejpeg($newImage);
                    $success = ob_get_contents();
                    ob_end_clean();
                } else {
                    $success = imagejpeg($newImage, $newPath);
                }
                break;
            case $outExt === 'png':
                if (null === $newPath) {
                    ob_start();
                    imagepng($newImage);
                    $success = ob_get_contents();
                    ob_end_clean();
                } else {
                    $success = imagepng($newImage, $newPath);;
                }
                break;
            case $outExt === 'gif':
                if (null === $newPath) {
                    ob_start();
                    imagegif($newImage);
                    $success = ob_get_contents();
                    ob_end_clean();
                } else {
                    $success = imagegif($newImage, $newPath);
                }
                break;
            case  $outExt === 'bmp':
                if (null === $newPath) {
                    ob_start();
                    imagebmp($newImage);
                    $success = ob_get_contents();
                    ob_end_clean();
                } else {
                    $success = imagebmp($newImage, $newPath);
                }
                break;
            case  $outExt === 'webp':
                if (null === $newPath) {
                    ob_start();
                    imagewebp($newImage);
                    $success = ob_get_contents();
                    ob_end_clean();
                } else {
                    $success = imagewebp($newImage, $newPath);
                }
        }
        if (!$success) {
            return false;
        }
        //return $newPath;
        return $success;
    }
}
