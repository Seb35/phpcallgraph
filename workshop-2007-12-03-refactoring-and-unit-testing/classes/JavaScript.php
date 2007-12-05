<?php
class JavaScript {

    /**
     *
     */
    public static function getScriptElement($javascript_code)
    {
        $output = '<script language="JavaScript" type="text/javascript"><!--' . "\n";
        $output.= $javascript_code;
        $output.= '//--></script>' . "\n";
        return $output;
    }

    /**
     *
     */
    public static function getAlert($msg)
    {
        $output = self::getScriptElement("  window.alert('" . $msg . "')\n");
        $output.= "<noscript><p><b>" . $msg . "</b></p></noscript>";
        echo $output;
    }

    /**
     *
     */
    public static function getBackLink($backlink_url = '', $mode = 'link', $text = 'zur&uuml;ck')
    {
        if (empty($backlink_url)) {
            $backlink_url = (getenv("HTTP_REFERER") != '') ? getenv("HTTP_REFERER") : $GLOBALS['onlineserverurl'];
        }
        $javascript_backlink = ($mode == 'link') ? '<a href="javascript:history.back()">' . $text . '</a>' : '<input type="button" value="' . $text . '" onClick="javascript:history.back()" class="flatbutton">';
        $output = self::getScriptElement('  document.write(\'' . $javascript_backlink . '\')' . "\n");
        $output.= '<noscript><a href="' . $backlink_url . '">' . htmlentities($text) . '</a></noscript>' . "\n";
        return $output;
    }

    /**
     *
     */
    public static function getButtonLink($url, $text = '')
    {
        if (strlen($text) == 0) {
            $text = $url;
        }
        $javascript_buttonlink = '<input type="button" value="' . $text . '" onClick="self.location.href=\\\'' . $url . '\\\'" class="flatbutton">';
        $output = self::getScriptElement('  document.write(\'' . $javascript_buttonlink . '\')' . "\n");
        $output.= '<noscript><a href="' . $url . '">' . htmlentities($text) . '</a></noscript>' . "\n";
        return $output;
    }

    /**
     *
     */
    public static function changeStatus($value)
    {
        $output = self::getScriptElement('  window.status=\'' . $value . '\';' . "\n");
        return $output;
    }

    /**
     *
     */
    public static function changeImageSource($imgname, $src)
    {
        $output = self::getScriptElement('  window.document.images[\'' . $imgname . '\'].src=\'' . $src . '\';' . "\n");
        return $output;
    }

}
?>
