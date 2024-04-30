<?php

namespace Utilita;

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
}
