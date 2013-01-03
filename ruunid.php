<?php 

include('includes/web_init.php');
include('includes/web_header.php');

?>
<div class="bodydiv">
<center>
<?php echo $html_navigation = html_navigation_bar(); ?>

<table class="r_t" width="65%">
<tr><td valign="top" class="r_c" colspan="2">
<h1><a name="sisukord">Sisukord</a></h1><ol>
<li><a href="#0">Nädalapäevad</a></li>
<li><a href="#10">Kuufaasid</a></li>
<li><a href="#20">Pööripäevad</a></li>
<li><a href="#114">Liikumatud tähtpäevad</a></li>
<li><a href="#1993">Liikuvad tähtpäevad</a></li>
</ol>
</td></tr>
<?php 

$dbh = new PDO($init_data['dbc']);

$runelabels = array();
foreach ($dbh->query("SELECT r.*, e.event FROM runes r, events e WHERE r.dbid > -1 AND r.dbid < 21 AND r.dbid = e.id ORDER BY r.dbid") as $row)
{ $runelabels['i'.$row['dbid']] = array('l'=>$row['event']); }

foreach ($dbh->query("SELECT r.*, e.event, e.maausk, e.more FROM runes r, events e WHERE r.dbid > -1 AND r.dbid = e.id ORDER BY r.dbid") as $row) {

    //double runes
    $doublerunes = '';
    $a = array();
    if($row['dbid'] != 20) {
        foreach ($dbh->query("SELECT * FROM events WHERE rune_id = ".$row['dbid']." AND id <> ".$row['dbid']." ORDER BY id") as $rowe) {
            $a[] = '<a href="'
                .'tahtpaevad'
                .$init_data['static_extension']
                .'#'.$rowe['id'].'">'
                .$rowe['maausk']
                .'</a>';
        }
        if(count($a)) {
            $doublerunes = "<br/>\nSama ruuni kasutab: ".implode(", ",$a);
        }
    }

    // weekdays
    $weekdayevents = '';
    if($row['dbid'] < 7) {
        $a = array();
        foreach ($dbh->query("SELECT * FROM events WHERE weekday = ".$row['dbid']." ORDER BY id") as $rowe) {
            $a[] = '<a href="tahtpaevad'
                .$init_data['static_extension']
                .'#'.$rowe['id'].'">'
                .compose_day_label($rowe)
                .'</a>';
        }
        if(count($a)) {
            $weekdayevents = "<br/>\nSel nädalapäeval toimub: ".implode(", ",$a);
        }
    }

    // solistices
    $solistices = '';
    if($row['dbid'] == 20) {
        $seasons = array('kevadine','suvine','sügisene','talvine');
        $a = array();
		/*
        foreach ($dbh->query("SELECT * FROM events WHERE flags & 8 > 0 ORDER BY id") as $rowe) {
            $a[] = '<a href="tahtpaevad'
                .$init_data['static_extension']
                .'#'.$rowe['id'].'">'
                .$seasons[(floor($rowe['id']/100)/3)-1]
                .'</a>';

        }
		*/
		foreach($seasons as $k => $v) {
			$a[] = '<a href="tahtpaevad'
                .$init_data['static_extension']
                .'#'.(21 + $k).'">'
                .$v
                .'</a>';
		}
        $solistices .= ": ".implode(", ",$a)."<br/>\n";
    }
    
	if($row['dbid'] >= 10 && $row['dbid'] <= 17) {
        $solistices .= trim($row['more']) == '' ? '' : "<br/>".$row['more']."\n";
    }
	
    // nonastronomical/non-week events will be fetched from events table
	$name = $row['dbid'] > 24 ? $row['maausk'] : $row['event'];
    $link = '<a href="tahtpaevad'
                .$init_data['static_extension']
                .'#'.$row['dbid'].'">';
    
    echo '<tr><td valign="top" class="r_c"><a name="'
    .$row['dbid']
    .'"><strong>'
    .$link.$name.($link == '' ? '' : '</a></a>')
    .'</strong>'
    
    .$solistices
    .$weekdayevents
    .$doublerunes
    
    ."<br/>\nSVG: "
    .'<a href="svg/'.$row['filename'].'">'
    .$row['filename']
    ."</a>"
    
    ."<br/>\nID andmebaasis: "
    .'<a href="#'.$row['dbid'].'">'
    .$row['dbid']
    ."</a>\n"
    
    ."</td>\n"
    .'<td valign="top" class="r_c">'
    .'<object data="svg/'.$row['filename'].'" type="image/svg+xml" height="200" width="'.$row['width'].'"></object>'
    ."</td>\n"
    ."</tr>\n\n";
}
  
 ?>
</table>

<br/>
<br/>
<?php 

echo $calendardata['html_dbcopy']; 

#echo "<pre>";print_r($kirikupyhad);echo "</pre>";

?>

<?php echo 
  html_db_copy($GLOBALS['init_data']['static_extension'])
  . '.<br/><br/>'
  . $html_navigation; ?>

</center>
</div>
</body>
</html>
