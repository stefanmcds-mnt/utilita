<?php

namespace Utilita;

use Utilita\TextService;

class FolderFileService
{

    /**
     * Apri la direcotry
     *
     * @param string $dir
     * @param string $ext
     * @return mixed
     */
    public static function _OpenDir(?string $dir, ?string $ext = null)
    {
        //Imposto la directory da leggere
        $directory = $dir;
        // Apriamo una directory e leggiamone il contenuto.
        if (is_dir($directory)) {
            //Apro l'oggetto directory
            if ($directory_handle = opendir($directory)) {
                //Scorro l'oggetto fino a quando non è termnato cioè false
                while (($file = readdir($directory_handle)) !== false) {
                    //Se l'elemento trovato è diverso da una directory
                    //o dagli elementi . e .. lo visualizzo a schermo
                    if ((!is_dir($file)) & ($file != ".") & ($file != "..")) {
                        if (null !== $ext) {
                            $files[] = (preg_match('/^(' . $ext . ')$/', $file)) ? $file : NULL;
                        } else {
                            $files[] = $file;
                        }
                    }
                }
                //Chiudo la lettura della directory.
                closedir($directory_handle);
            }
        }
        return isset($files) ? $files : null;
    }

    /**
     * LEggi da un file
     *
     * @param mixed $dafile
     * @return mixed
     */
    public static function _DaFile($dafile)
    {
        // memorizzo in un array il file
        $prova = file($dafile);

        // la prima riga contiene il nome dei campi
        //$colonne = explode(';', $prova[0]);
        $colonne = str_getcsv($prova[0], ';');

        foreach ($colonne as $value) {
            $key[] = Textservice::_AccentedCharTransform(str_replace('"', '', $value));
        }
        $colonne = $key;

        // popoliamo array create per i dati tabella
        for ($i = 1; $i < count($prova); $i++) {
            //$value = explode(';', $prova[$i]);

            $value = str_getcsv($prova[$i], ';');
            $create = [];
            for ($a = 0; $a < count($colonne); $a++) {
                //$key = $this->accented_char_transform($colonne[$a]);
                $key = $colonne[$a];
                $key = str_replace(' ', '_', trim($key));
                //$key = str_replace('?', '', $key);
                //$encoding = mb_detect_encoding($value[$a], mb_detect_order(), false);
                //if ($encoding == "UTF-8") {
                //    $value[$a] = mb_convert_encoding($value[$a], 'UTF-8', 'UTF-8');
                //}
                //$create[trim($key)] = iconv(mb_detect_encoding($value[$a], mb_detect_order(), false), "ISO-8859-1", $value[$a]);
                //$create[trim($key)] = iconv('UTF-8//TRANSLIT','ISO-8859-1',$value[$a]);
                $create[trim($key)] = str_replace('"', '', trim($value[$a]));
            }
            $create['created_at'] = date('Y-d-m G:H:i', time());
            $create['updated_at'] = date('Y-d-m G:H:i', time());
            $dati[$i - 1] = $create;
        }

        $dati = ['colonne' => $colonne, 'dati' => $dati];
        return $dati;
    }

    /**
     * Apri la direcotry
     *
     * @param string $dir
     * @param string $ext
     * @return mixed
     */
    public static function OpenDir(?string $dir, ?string $ext = null)
    {
        //Imposto la directory da leggere
        $directory = $dir;
        // Apriamo una directory e leggiamone il contenuto.
        if (is_dir($directory)) {
            //Apro l'oggetto directory
            if ($directory_handle = opendir($directory)) {
                //Scorro l'oggetto fino a quando non è termnato cioè false
                while (($file = readdir($directory_handle)) !== false) {
                    //Se l'elemento trovato è diverso da una directory
                    //o dagli elementi . e .. lo visualizzo a schermo
                    if ((!is_dir($file)) & ($file != ".") & ($file != "..")) {
                        if (null !== $ext) {
                            $files[] = (preg_match('/^(' . $ext . ')$/', $file)) ? $file : NULL;
                        } else {
                            $files[] = $file;
                        }
                    }
                }
                //Chiudo la lettura della directory.
                closedir($directory_handle);
            }
        }
        return isset($files) ? $files : null;
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

    /**
     * Replace the given file.
     *
     * @param  string  $replace
     * @param  string  $file
     * @return void
     */
    protected function replaceFile(string $replace, string $file)
    {
        $stubs = dirname(__DIR__) . '/stubs';

        return file_put_contents(
            $file,
            file_get_contents("$stubs/$replace"),
        );
    }

    /**
     * Replace the given string in the given file.
     *
     * @param  string|array  $search
     * @param  string|array  $replace
     * @param  string  $file
     * @return void
     */
    public static function replaceInFile(string|array $search, string|array $replace, string $file)
    {
        return file_put_contents(
            $file,
            str_replace($search, $replace, file_get_contents($file))
        );
    }

    /**
     * Replace the given string in the given file using regular expressions.
     *
     * @param  string|array  $search
     * @param  string|array  $replace
     * @param  string  $file
     * @return void
     */
    public static function pregReplaceInFile(string $pattern, string $replace, string $file)
    {
        return file_put_contents(
            $file,
            preg_replace($pattern, $replace, file_get_contents($file))
        );
    }

    /**
     * Search a string and add a line
     *
     * @param string $search
     * @param string $add
     * @param string $file
     * @return void
     */
    public static function searchAndAddLineToFile(string $search, string $line, string $file)
    {
        $array = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        for ($i = 0; $i < count($array); $i++) {
            if (strstr($array[$i], $search)) {
                $array = array_slice($array, 0, $i) + [$line] + $array;
                break;
            }
        }
        return file_put_contents($file, implode("\n", $array));
    }
}
