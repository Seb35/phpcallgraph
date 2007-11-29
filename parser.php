<?php
if (!extension_loaded('parse_tree')) {
   $prefix = (PHP_SHLIB_SUFFIX === 'dll') ? 'php_' : '';
   dl($prefix . 'parse_tree.' . PHP_SHLIB_SUFFIX);
}

require_once 'XML2Array.php';

define('LIBXML_OPTIONS', LIBXML_DTDLOAD | LIBXML_NOENT | LIBXML_DTDATTR | LIBXML_NOCDATA);

$dir = '../../trunk/test/testfiles';
$outputDir = 'output';

$directoryIterator = new DirectoryIterator($dir);
while ($directoryIterator->valid()) {
    if ($directoryIterator->isFile()) {
        echo $directoryIterator->current(), "\n";
        $name = basename($directoryIterator->current(), '.php');

        // generate parse tree
        $xml1 = new DOMDocument();
        $xml1->loadXML(parse_tree_from_file($directoryIterator->getPathname()), LIBXML_OPTIONS);
        $xml1->save("$outputDir/$name.xml");

        // convert XML to array
        $xml2array = new XML2Array($xml1);
        file_put_contents("$outputDir/$name.txt", $xml2array);

        // transform XML tree to PHP code
        /*
        $xml2 = new DOMDocument();
        $xml2->load('toWrite.xsl', LIBXML_OPTIONS);

        $xsl = new XSLTProcessor();
        $xsl->importStyleSheet($xml2);
        echo $xsl->transformToXML($xml1);
        //*/
    }
    $directoryIterator->next();
}
?>
