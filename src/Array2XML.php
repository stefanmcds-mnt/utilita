<?php

namespace Utilita;

use DOMDocument;
use DOMImplementation;
use DOMNode;
use Exception;

/**
 * Array2XML: A class to convert PHP array in to XML
 * It also takes into account attributes names unlike SimpleXML in PHP
 * It returns the XML in form of DOMDocument class for further manipulation.
 * It throws exception if the tag name or attribute name has illegal chars.
 *
 * Author : Lalit Patel
 * Website: http://www.lalit.org/lab/convert-php-array-to-xml-with-attributes
 * License: Apache License 2.0
 *          http://www.apache.org/licenses/LICENSE-2.0
 *
 * Usage:
 *       $xml = Array2XML::createXML('root_node_name', $php_array);
 *       echo $xml->saveXML();
 */
class Array2XML
{
    /**
     * @var string
     */
    private static $encoding = 'UTF-8';

    /**
     * @var DOMDocument|null
     */
    private static $xml = null;

    /**
     * Get the root XML node, if there isn't one, create it.
     *
     * @return DOMDocument|null
     */
    public static function getXMLRoot()
    {
        if (empty(self::$xml)) {
            self::init();
        }
        return self::$xml;
    }

    /**
     * Initialize the root XML node [optional].
     *
     * @param string $version
     * @param string $encoding
     * @param bool   $standalone
     * @param bool   $format_output
     */
    public static function init(
        ?string $version = '1.0',
        ?string $encoding = 'UTF-8',
        ?bool $standalone = false,
        ?bool $format_output = true
    ) {
        self::$xml = new DOMDocument($version, $encoding);
        self::$xml->xmlStandalone = $standalone;
        self::$xml->formatOutput = $format_output;
        self::$encoding = $encoding;
    }

    /**
     * Convert an Array to XML.
     *
     * @param string $node_name - name of the root node to be converted
     * @param array  $arr       - array to be converted
     * @param array  $docType   - optional docType
     *
     * @return DOMDocument
     * @throws Exception
     */
    public static function createXML(
        ?string $node_name = null,
        ?array $arr = [],
        ?array $docType = []
    ) {
        $xml = self::getXMLRoot();
        // BUG 008 - Support <!DOCTYPE>
        if (!empty($docType)) {
            $xml->appendChild(
                (new DOMImplementation())
                    ->createDocumentType(
                        $docType['name'] ?? '',
                        $docType['publicId'] ?? '',
                        $docType['systemId'] ?? ''
                    )
            );
        }
        $xml->appendChild(self::convert($node_name, $arr));
        self::$xml = null;    // clear the xml node in the class for 2nd time use.
        return $xml;
    }

    /**
     * Convert an Array to XML.
     *
     * @param string $name      - name of the root node to be converted
     * @param mixed  $arr       - array or other to be converted
     *
     * @return DOMNode
     *
     * @throws Exception
     */
    public static function convert(
        ?string $name,
        mixed $arr
    ) {
        //print_arr($name);
        $xml = self::getXMLRoot();
        $node = $xml->createElement($name);
        if (is_array($arr)) {
            // get the attributes first.;
            if (array_key_exists('@attributes', $arr) && is_array($arr['@attributes'])) {
                foreach ($arr['@attributes'] as $key => $value) {
                    if (!self::isValidTagName($key)) {
                        throw new Exception('[Array2XML] Illegal character in attribute name. attribute: ' . $key . ' in node: ' . $name);
                    }
                    $node->setAttribute($key, self::bool2str($value));
                }
                unset($arr['@attributes']); //remove the key from the array once done.
            }
            // check if it has a value stored in @value, if yes store the value and return
            // else check if its directly stored as string
            if (array_key_exists('@value', $arr)) {
                $node->appendChild($xml->createTextNode(self::bool2str($arr['@value'])));
                unset($arr['@value']);    //remove the key from the array once done.
                //return from recursion, as a note with value cannot have child nodes.
                return $node;
            }
            if (array_key_exists('@cdata', $arr)) {
                $node->appendChild($xml->createCDATASection(self::bool2str($arr['@cdata'])));
                unset($arr['@cdata']);    //remove the key from the array once done.
                //return from recursion, as a note with cdata cannot have child nodes.
                return $node;
            }
        }
        //create subnodes using recursion
        // recurse to get the node for that key
        if (is_array($arr)) {
            foreach ($arr as $key => $value) {
                // maybe $key can be a numeric index
                if (!is_numeric($key)) {
                    if (!self::isValidTagName($key)) {
                        throw new Exception('[Array2XML] Illegal character in tag name. tag: ' . $key . ' in node: ' . $name);
                    }
                    if (is_array($value) && is_numeric(key($value))) {
                        // MORE THAN ONE NODE OF ITS KIND;
                        // if the new array is numeric index, means it is array of nodes of the same kind
                        // it should follow the parent key name
                        foreach ($value as $k => $v) {
                            $node->appendChild(self::convert($key, $v));
                        }
                    } else {
                        // ONLY ONE NODE OF ITS KIND
                        $node->appendChild(self::convert($key, $value));
                    }
                } else {
                    $node->appendChild($xml->createElement($value));
                }
                unset($arr[$key]); //remove the key from the array once done.
            }
        }
        // after we are done with all the keys in the array (if it is one)
        // we check if it has any text value, if yes, append it.
        if (!is_array($arr)) {
            $node->appendChild($xml->createTextNode(self::bool2str($arr)));
        }
        return $node;
    }

    /**
     * Get string representation of boolean value.
     *
     * @param mixed $v
     *
     * @return string
     */
    private static function bool2str(?string $v)
    {
        //convert boolean to text value.
        $v = $v === true ? 'true' : $v;
        $v = $v === false ? 'false' : $v;
        return $v;
    }

    /**
     * Check if the tag name or attribute name contains illegal characters
     * Ref: http://www.w3.org/TR/xml/#sec-common-syn.
     *
     * @param string $tag
     *
     * @return bool
     */
    private static function isValidTagName(?string $tag)
    {
        $pattern = '/^[a-z_]+[a-z0-9\:\-\.\_]*[^:]*$/i';
        return preg_match($pattern, $tag, $matches) && $matches[0] == $tag;
    }
}
