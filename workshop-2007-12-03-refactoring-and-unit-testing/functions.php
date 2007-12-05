<?php
    /**
     * Gibt eine Variable als PHP-Code aus
     *
     * @param mixed $var Variable die ausgegeben werden soll
     * @param string $varname Variablenname der angezeigt werden soll
     * @param bool $print gibt an ob der Inhalt der Variablen direkt
     *                    mit Syntax-Highlighting ausgegeben werden soll
     * @return string
     */
    function mini_debugger($var, $varname = '', $linenumber = 0, $print = true)
    {
        #$output = '<textarea rows="10" cols="80">';
        #$output.= $varname . ' = ' . htmlentities(var_export($var, true)) . ';';
        #$output.= '</textarea>' . "\n";
        #$output = '<textarea><pre align="left">';
        #$output.= $varname . ' = ' . htmlentities(var_export($var, true)).';';
        #$output.= '</textarea></pre>'."\n";
        $output = '<?php ' . "\n";
        if ($linenumber > 0)
        $output.= '/*' . $linenumber . '*/ ';
        $output.= $varname . ' = ' . var_export($var, true) . ';' . "\n";
        $output.=' ?>' . "\n";
        if ($print == true)
        {
            highlight_string($output);
        }
        return $output;
    }


    /**
     * Gibt alle vom Benutzer definierten Funktionen und Methoden aus
     */
    function list_user_defined_functions()
    {
        $arr = get_defined_functions();
        mini_debugger($arr['user'], '$arr[\'user\']');
        # auch interessant:
        # mini_debugger(get_defined_constants(), 'get_defined_constants()');
        # mini_debugger(get_defined_vars(), 'get_defined_vars()');
    }


    /**
     *
     */
    function format_seconds($seconds)
    {
        $sec=floor($seconds);
        $min=floor($sec/60);
        if($min>=1){
            $output=$min.'min '.($sec-$min*60).'s';
        }else{
            $output=$sec.'s';
        }
        return $output;
    }


    /**
     *
     */
    function getmicrotime()
    {
        list($usec, $sec) = explode(' ',microtime());
        return ((float)$usec + (float)$sec);
    }


    /**
     * Funktion zur Zeitmessung in Mikrosekunden
     *
     * @param mixed $id identifiziert eine Messreihe (Zeichenkette oder Ganzzahl)<br>
     * @param int $mode Steuerungsparameter<br>
     * $mode=0 >> Anfangszeitpunkt speichern<br>
     * $mode=1 >> zweite Messung<br>
     * $mode=2 >> Berechnung & Rückgabe<br>
     * $mode=3 >> zweite Messung + Berechnung & Rückgabe<br>
     * $mode=4 >> Zwischenzeit berechnen und zurückgeben<br>
     * @return float
     */
    function timer($mode=0,$id=0)
    {
        global $my_current_temporary_timer_timestamps;
        switch ($mode)
        {
            case 0: $my_current_temporary_timer_timestamps[$id][0]=microtime();
                    break;
            case 1: $my_current_temporary_timer_timestamps[$id][1]=microtime();
                    break;
            case 2: list($usec0,$sec0)=explode(' ',$my_current_temporary_timer_timestamps[$id][0]);
                    list($usec1,$sec1)=explode(' ',$my_current_temporary_timer_timestamps[$id][1]);
                    return ((float)((int)$sec1 - (int)$sec0) + (float)$usec1 - (float)$usec0);
            case 3: $my_current_temporary_timer_timestamps[$id][1]=microtime();
                    list($usec0,$sec0)=explode(' ',$my_current_temporary_timer_timestamps[$id][0]);
                    list($usec1,$sec1)=explode(' ',$my_current_temporary_timer_timestamps[$id][1]);
                    return ((float)((int)$sec1 - (int)$sec0) + (float)$usec1 - (float)$usec0);
            case 4: $zwischenzeit=microtime();
                    list($usec0,$sec0)=explode(' ',$my_current_temporary_timer_timestamps[$id][0]);
                    list($usec1,$sec1)=explode(' ',$zwischenzeit);
                    return ((float)((int)$sec1 - (int)$sec0) + (float)$usec1 - (float)$usec0);
        }
    }


    /**
     * Fortschrittsanzeige im Browser
     *
     * @param float $progress Fortschritt (z.B. 0.35672 für 35,672 Prozent)
     * @param mixed $id ermöglicht es verschiedene Fortschrittsanzeigen zu unterscheiden (Zeichenkette oder Ganzzahl)
     * @param string $mode wählt den Typ der Grafischen Anzeige aus. Mögliche Werte: 'piechart' oder 'bar'
     */
    function show_progress($progress=0.0,$id=0,$mode='bar')
    {
        static $status;
        if($mode=='piechart'){
            $pieces=8;
        }elseif($mode=='bar'){
            $pieces=20;
        }else{
            return false;
        }
        if(!isset($status[$id]['image_drawed']) or $status[$id]['image_drawed']!=true){
            if($mode=='piechart'){
                echo '<img src="'.$GLOBALS['central_system_directories']["online_codecenter"].'progress/pie0.gif" name="function_show_progress_image_with_id_'.$id.'"><br>';
            }elseif($mode=='bar'){
                $javascript_code='
                    function showprogress(id,whichbar) {
                        var imgid;
                        for(currentbar=0; currentbar<='.$pieces.'; currentbar++) {
                            imgid = "function_show_progress_image_with_id_" + id + "_prog" + currentbar;
                            if(currentbar<=whichbar && window.document.images[imgid] && window.document.images[imgid].style.visibility != "visible") {
                                window.document.images[imgid].style.visibility = "visible";
                            }
                            if(currentbar>whichbar && window.document.images[imgid] && window.document.images[imgid].style.visibility == "visible") {
                                window.document.images[imgid].style.visibility = "hidden";
                            }
                        }
                    }'."\n";
                echo JavaScript::getScriptElement($javascript_code);
                echo '<table align="center" border="1" cellspacing="0" cellpadding="0"><tr><td>'."\n";
                echo '<table border="0" cellspacing="1" cellpadding="0"><tr>'."\n";
                for($i=1;$i<=$pieces;$i++){
                    echo '  <td class=pb><img id="function_show_progress_image_with_id_'.$id.'_prog'.$i.'" src="'.$GLOBALS['central_system_directories']["online_codecenter"].'progress/progressbar.gif" style="visibility: hidden; margin: 0px"></td>'."\n";
                }
                echo "</table>\n</td></tr>\n</table>";
            }
            flush();
            $status[$id]['image_drawed']=true;
            $status[$id]['status']=0;
        }
        $newstatus=floor($progress*$pieces);
        if($status[$id]['status']!=$newstatus and 0<=$newstatus and $newstatus<=$pieces){
            if($mode=='piechart'){
                echo JavaScript::changeImageSource('function_show_progress_image_with_id_'.$id,$GLOBALS['central_system_directories']["online_codecenter"].'progress/pie'.$newstatus.'.gif');
            }elseif($mode=='bar'){
                echo JavaScript::getScriptElement('  showprogress(\''.$id.'\','.$newstatus.')'."\n");
            }
            $status[$id]['status']=$newstatus;
            flush();
        }

    }


    /**
     * Führt Funktionen deren Namen in einer Jobliste übergeben werden wiederholt aus,
     * um dannach Statistische Informationen auszugeben
     *
     * @param array $jobs Jobliste mit den Funktions- bzw. Methodennamen und Objektreferenzen und entsprechenden Argumenten
     * @param int $anzahl Anzahl der Wiederholungen
     * @param bool $print_values mit true werden die Rückgabewerte ausgegeben
     * @param bool $show_progress mit true wird der Fortschritt in der Statusleiste und mit einer Grafik angezeigt
     *
     * Das Array $jobs sollte beispielsweise folgende Struktur haben:<br>
     * <code>
     *  $jobs = array (
     *      0 => array (
     *          0 => 'Funktionsname',
     *          1 => array (
     *              0 => 'erstes Argument',
     *              1 => 'zweites Argument',
     *              )
     *          ),
     *      1 => array (
     *          0 => array (
     *              0 => &$Referenz_auf_ein_Objekt
     *              1 => 'Methodenname',
     *              ),
     *          1 => array (
     *              0 => 'erstes Argument',
     *              )
     *          )
     *      );
     * </code>
     *
     * Anwendungsbeispiel:
     * <code>
     *  $jobs[]=array('get_html_title_of',array('http://127.0.0.1/sites/24id.de/24id.net/sw-art.24id.net/Galerie/galerie_aktuell.htm'));
     *  $jobs[]=array('get_html_title_v2_of',array('http://127.0.0.1/sites/24id.de/24id.net/sw-art.24id.net/Galerie/galerie_aktuell.htm'));
     *  $jobs[]=array('file',array('http://127.0.0.1/sites/24id.de/24id.net/sw-art.24id.net/Galerie/galerie_aktuell.htm'));
     *  $jobs[]=array('file',array('test.htm'));
     *  $jobs[]=array('file',array('http://127.0.0.1/sites/fakko.de-keywordcenter/test.htm'));
     *  $jobs[]=array('get_html_title_v3_of',array('test.htm'));
     *  $jobs[]=array('get_html_title_v3_of',array('test.htm'));
     *  $jobs[]=array('get_html_title_v3_of',array('test.htm'));
     *  $jobs[]=array('get_html_title_v3_of',array('test.htm'));
     *  $jobs[]=array('get_html_title_v4_of',array('test.htm'));
     *  $jobs[]=array('get_html_title_v4_of',array('test.htm'));
     *  $jobs[]=array('get_html_title_v4_of',array('test.htm'));
     *  $jobs[]=array('get_html_title_v4_of',array('test.htm'));
     *  $jobs[]=array(array(&$c_object_handler,'get_object_ids'),array($GLOBALS['central_system_directories']["dms_objects"]));
     *  performance_test($jobs,100);
     * </code>
     *
     * Es empfielt sich, wie im Beispiel gezeigt, die gleiche Funktion oder Methode
     * mehrfach in die Jobliste aufzunehmen. Wenn sich dann große Unterschiede in
     * den Durchschnittszeiten derselben Funktion bzw. Methode ergeben, war die
     * Anzahl der Wiederholungen zu gering und die Messergebnisse sind nicht
     * repräsentativ.
     */
    function performance_test($jobs, $anzahl = 1, $print_values = false, $show_progress = true)
    {
        #ini_set('max_execution_time',30);
        $progressinterval = $anzahl<=100 ? 1 : round($anzahl/100);
        if($show_progress){
            $show_progress_id=uniqid("");
            show_progress(0,$show_progress_id);
        }
        $timer = new Timer();
        foreach($jobs as $key=>$c_job){
            $keys[]=$key;
        }
        $numkeys=count($keys);
        for ($i = 1; $i <= $anzahl and $timer->getTime() <= ini_get('max_execution_time') - 1; ++$i) {
            foreach($keys as $key){
                $jobTimer = new Timer();
                $value[$key]=call_user_func_array($jobs[$key][0],$jobs[$key][1]);
                $time[$key] += $jobTimer->getTime();
                clearstatcache();
                if($i==1){
                    # Funktionsnamen in der Jobliste speichern
                    $c_job=$jobs[$key];
                    $jobs[$key][2]=(is_array($c_job[0]) ? get_class($c_job[0][0]).'->'.$c_job[0][1] : $c_job[0]).'()';
                    if($print_values){
                        mini_debugger($value[$key],($key+1).'. '.$jobs[$key][2]);
                    }
                }
            }
            if ($show_progress and $progressinterval > 0 and $i / $progressinterval - floor($i / $progressinterval) == 0) {
                show_progress($i / $anzahl,$show_progress_id);
                echo JavaScript::changeStatus(
                    round($i/$anzahl*100) . ' % - '
                    . number_format($i, 0, ',', '.')
                    . '/' . number_format($anzahl, 0, ',', '.')
                    . ' - ' . format_seconds($timer->getTime(), 1, '.', '')
                    . '/' . format_seconds(round($timer->getTime() / $i * $anzahl))
                );
                flush();
            }
            srand((float)microtime()*1000000);
            shuffle($keys); #Schlüssel durcheinnanderbringen
        }
        if ($show_progress) {
            echo JavaScript::changeStatus(
                round(($i - 1) / $anzahl * 100) . ' % - '
                . number_format(($i - 1), 0, ',', '.')
                . '/' . number_format($anzahl, 0, ',', '.')
                . ' - ' . format_seconds($timer->getTime(), 1, '.', '')
                . '/' . format_seconds(ceil($timer->getTime() / $i * $anzahl))
            );
        }
        echo "<br>\n";
        echo '<table border="0" cellpadding="3">'."\n";
        # Bestzeit ermitteln
        foreach($jobs as $key=>$c_job){
            if(!isset($mintime) or $time[$key]<$mintime){
                $mintime=$time[$key];
            }
            if(!isset($maxtime) or $time[$key]>$maxtime){
                $maxtime=$time[$key];
            }
        }
        foreach ($jobs as $key => $c_job) {
            echo '<tr><td>'.($key+1).'. '.$c_job[2].'</td><td>'.round($time[$key],2).'</td><td>'.($time[$key]/$anzahl).'</td><td>'.round($time[$key]*100/$mintime).' %'.'</td><td>'.round($time[$key]*100/$maxtime).' %'."</td></tr>\n";
        } 
        echo "</table>\n";
        echo 'Gesammtlaufzeit: ' . round($timer->stopAndGetTime(), 2) . ' Sekunden bei '
            . ($i - 1) . ' Wiederholungen (' . ($timer->getTime() / $anzahl) . ' Sekunden pro Durchgang)';
    }
?>
