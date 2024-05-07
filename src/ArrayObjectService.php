<?php

namespace Utilita;

use Utilita\FolderfileService;

class ArrayObjectService
{

    /**
     * checkItem
     *
     * @param array $request ojbect from Request class
     * @return array
     */
    public static function _CheckItem($request)
    {
        foreach ($request as $key => $value) {
            if (is_array($value)) {
                $array[$key] = call_user_func(__FUNCTION__, $value);
            } else {
                $array[$key] = $value;
            }
        }
        return (isset($array)) ? $array : false;
    }

    /**
     * Array Difference
     *
     * Es.:
     * - recursiveDiff($array1,$array2,$array3)
     * - recursiveDiff("NO KEY", $arr1,$arr2) do no preserve array key
     *
     * @param arrays
     * @return array of difference
     */
    public static function _recursiveDiff()
    {
        $args = func_get_args();
        $function = (is_string($args[0])) ? $args[0] : false;
        if ($function) array_shift($args);
        $diff = [];
        foreach (array_shift($args) as $key => $val) {
            for ($i = 0, $j = 0, $tmp = array($val), $count = count($args); $i < $count; $i++)
                if (is_array($val))
                    if (!isset($args[$i][$key]) || !is_array($args[$i][$key]) || empty($args[$i][$key]))
                        $j++;
                    else
                        $tmp[] = $args[$i][$key];
                elseif (!array_key_exists($key, $args[$i]) || $args[$i][$key] !== $val)
                    $j++;
            if (is_array($val)) {
                $tmp = call_user_func_array(__FUNCTION__, $tmp);
                if (!empty($tmp)) ($function) ? $diff = $tmp : $diff[$key] = $tmp;
                elseif ($j == $count) $diff[$key] = $val;
            } elseif ($j == $count && $count) $diff[$key] = $val;
        }
        return (empty($diff)) ? false : $diff;
    }


    /**
     * Validate JSON
     *
     * @param mixed $string
     * @return mixed
     */
    public static function _JsonValidate($string)
    {
        // decode the JSON data
        $result = json_decode($string);
        // switch and check possible JSON errors
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                $error = ''; // JSON is valid // No error has occurred
                break;
            case JSON_ERROR_DEPTH:
                $error = 'The maximum stack depth has been exceeded.';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $error = 'Invalid or malformed JSON.';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $error = 'Control character error, possibly incorrectly encoded.';
                break;
            case JSON_ERROR_SYNTAX:
                $error = 'Syntax error, malformed JSON.';
                break;
                // PHP >= 5.3.3
            case JSON_ERROR_UTF8:
                $error = 'Malformed UTF-8 characters, possibly incorrectly encoded.';
                break;
                // PHP >= 5.5.0
            case JSON_ERROR_RECURSION:
                $error = 'One or more recursive references in the value to be encoded.';
                break;
                // PHP >= 5.5.0
            case JSON_ERROR_INF_OR_NAN:
                $error = 'One or more NAN or INF values in the value to be encoded.';
                break;
            case JSON_ERROR_UNSUPPORTED_TYPE:
                $error = 'A value of a type that cannot be encoded was given.';
                break;
            default:
                $error = 'Unknown JSON error occured.';
                break;
        }
        if ($error !== '') {
            // throw the Exception or exit // or whatever :)
            //exit($error);
            return false;
        }
        // everything is OK
        return $result;
    }

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
                static::_setNode($result, $grouping, $depth);
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
        $node = static::_setTree($grouping, $depth);
    }

    /**
     * check if array is multdimesional
     */
    public static function _ArrayMultiDim($a)
    {
        rsort($a);
        return isset($a[0]) && is_array($a[0]);
    }
}
