<?php
/**
 * viewer for the call graph examples
 */
class ExampleViewer {

    public function __construct($dir = '.') {
        $examples = $this->getExamples($dir);
        $this->showExamples($examples, $dir);
    }

    public function getExamples($dir) {
        $thumbnails = glob("$dir/resized_*.png");
        foreach ($thumbnails as $thumbnail) {
            $name = substr(basename($thumbnail, '.png'), 8);
            $image = "$dir/$name.png";
            $size = getimagesize($image);
            if ($size[0] <= 980 and $size[1] <= 300) {
                $thumbnail = $image;
            }
            $examples[$name] = $thumbnail;
        }
        return $examples;
    }

    public function showExamples($examples, $dir) {
        echo "<nter>\n";
        foreach ($examples as $name => $thumbnail) {
            echo <<< EOF
<h2>$name</h2>
<a href="$dir/$name.png"><img src="$thumbnail" border="0"></a>

EOF;
        }
        echo "</center>\n";
    }

}
