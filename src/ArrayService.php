<?php

namespace Utilita;

use Utilita\FolderfileService;

class ArrayService
{


    /*
    * Return array converted to object
    * Using __FUNCTION__ (Magic constant)
    * for recursive call
    */
    public static function _ArrayToObject($d)
    {
        $function = 'static::_ArrayToObject';
        if (is_array($d)) {
            //return (object) array_map(__FUNCTION__, $d);
            return (object) array_map($function, $d);
            //return (object) array_map(forward_static_call(__FUNCTION__), $d);
        } else {
            // Return object
            return $d;
        }
    }

    /*
    * Return array converted to object
    * Using __FUNCTION__ (Magic constant)
    * for recursive call
    */
    public static function ArrayToObject($d)
    {
        $function = 'static::ArrayToObject';
        if (is_array($d)) {
            //return (object) array_map(__FUNCTION__, $d);
            return (object) array_map($function, $d);
            //return (object) array_map(forward_static_call(__FUNCTION__), $d);
        } else {
            // Return object
            return $d;
        }
    }


    /*
    * Return object converted to array
    * Using __FUNCTION__ (Magic constant)
    * for recursive call
    */
    public static function _ObjectToArray($d = null)
    {
        $function = 'static::_ObjectToArray';
        if (is_object($d)) {
            // Gets the properties of the given object
            // with get_object_vars function
            $d = get_object_vars($d);
        }
        if (is_array($d)) {
            //return (object) array_map(__FUNCTION__, $d);
            return (object) array_map($function, $d);
            //return (object) array_map(forward_static_call(__FUNCTION__), $d);
        } else {
            // Return array
            return $d;
        }
    }

    /*
    * Return object converted to array
    * Using __FUNCTION__ (Magic constant)
    * for recursive call
    */
    public static function ObjectToArray($d = null)
    {
        $function = 'static::_ObjectToArray';
        if (is_object($d)) {
            // Gets the properties of the given object
            // with get_object_vars function
            $d = get_object_vars($d);
        }
        if (is_array($d)) {
            //return (object) array_map(__FUNCTION__, $d);
            return (object) array_map($function, $d);
            //return (object) array_map(forward_static_call(__FUNCTION__), $d);
        } else {
            // Return array
            return $d;
        }
    }

    /**
     * Ceate a Tree of a structure folder by an array
     *
     * @param array|null $rawPaths
     * @return array
     */
    public static function _CreateArrayTree(?array $rawPaths)
    {
        // 1. Convert the paths into arrays.
        $paths = array_map(function ($path) {
            return explode("/", $path);
        }, $rawPaths);
        // 2. Find the maximum path depth.
        $maxDepth = 0;
        for ($i = 0; $i < count($rawPaths); $i++) {
            if (($count = substr_count($rawPaths[$i], "/")) > $maxDepth) {
                $maxDepth = $count;
            }
        }
        // 3. Group paths based on their level of depth.
        $groupings = [];
        for ($j = 0; $j <= $maxDepth; $j++) {
            $groupings[] = array_filter($paths, function ($p) use ($j) {
                return count($p) === ($j + 1);
            });
        }
        // 4. Merge groupings in a hierarchical format.
        $result = [];
        for ($depth = 0; $depth <= $maxDepth; $depth++) {
            array_map(function ($grouping) use (&$result, $depth) {
                ArrayService::_setNode($result, $grouping, $depth);
            }, $groupings[$depth]);
        }
        // 5. Reset the result's array indices/keys.
        $arrayIterator = new \RecursiveArrayIterator(array_values($result));
        $recursiveIterator = new \RecursiveIteratorIterator($arrayIterator, \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($recursiveIterator as $key => $value) {
            if (is_array($value) && ($key === "children")) {
                $value = array_values($value);
                // Get the current depth and traverse back up the tree, saving the modifications.
                $currentDepth = $recursiveIterator->getDepth();
                for ($subDepth = $currentDepth; $subDepth >= 0; $subDepth--) {
                    // Get the current level iterator.
                    $subIterator = $recursiveIterator->getSubIterator($subDepth);
                    // If we are on the level we want to change, use the replacements ($value), otherwise set the key to the parent iterators value.
                    $subIterator->offsetSet($subIterator->key(), ($subDepth === $currentDepth ? $value : $recursiveIterator->getSubIterator(($subDepth + 1))->getArrayCopy()));
                }
            }
        }
        // 6. Print output.
        //var_export($recursiveIterator->getArrayCopy());
        $bol = $recursiveIterator->getArrayCopy();
        return (isset($bol) && count($bol) > 0) ? $bol : false;
    }

    /**
     * Set Array Tree
     *
     * @param mixed $grouping
     * @param mixed $depth
     * @return array
     */
    public static function _setTree(&$grouping, &$depth): array
    {
        $pathBuilder = $grouping[$depth];
        for ($i = 0; $i < $depth; $i++) {
            $pathBuilder = $grouping[$depth - ($i + 1)] . "/" . $pathBuilder;
        }
        return [
            "label" => $grouping[$depth],
            "path" => $pathBuilder,
            "children" => null,
            "files" => FolderFileService::_OpenDir(public_path("storage/$pathBuilder")),
        ];
    }

    /**
     * Set Node Tree
     *
     * @param mixed $result
     * @param mixed $grouping
     * @param mixed $depth
     * @return void
     */
    public static function _setNode(&$result, $grouping, $depth)
    {
        $node = &$result[$grouping[0]];
        if ($depth) {
            for ($i = ($depth - 1); $i >= 0; $i--) {
                $node = &$node["children"][$grouping[$depth - $i]];
            }
        }
        $node = self::_setTree($grouping, $depth);
    }
}
