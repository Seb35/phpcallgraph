<?php
/**
 * Class to convert a DOMNode into an Array
 */
class XML2Array {

    /**
     * @var DOMNode
     */  
    protected $node;

    public function __construct(DOMNode $node) {
        $this->node = $node;
    }

    /**
     * @return array
     */
    public function getArray() {
        $array = array();
        $array[$this->node->nodeName] = $this->toArray($this->node);
        return $array;
    }
    
    /**
     * @return array|string
     */
    protected function toArray(DOMNode $node) {
        $array = array();
        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $childNode) {
                if ($childNode->nodeType == XML_TEXT_NODE) {
                    $text = trim($childNode->nodeValue);
                    if (strlen($text) > 0) { // skip pure whitespace text nodes
                        $array[] = $text;
                    }
                } else {
                    $array[$childNode->localName] = $this->toArray($childNode);
                }
            }
        }
        return $array;
    }

    /**
     * @return string the array as PHP code
     */
    public function __toString() {
        $string = $this->arrayToString($this->getArray());
        return $string;
    }

    /**
     * @param integer $level
     * @return string
     */
    public static function arrayToString(Array $array, $level = 0) {
        $string = '';
        foreach ($array as $key => $value) {
            $string.= str_repeat('    ', $level);
            if (is_array($value)) {
                $string.= $key;
                $string.= "\n";
                $string.= self::arrayToString($value, $level + 1);
            } else {
                $string.= '"' . $value . "\"\n";
            }
        }
        return $string;
    }
}
?>
