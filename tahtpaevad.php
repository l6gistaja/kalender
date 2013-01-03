<?php 

include('includes/web_init.php'); 
include('includes/web_header.php');

?>

<div class="bodydiv">

<?php 

echo '<center>';
echo $html_navigation = html_navigation_bar();
echo '</center>';

$mall_abid = '<br />ID andmebaasis: <a href="#{abid}">{abid}</a>'."\n";
$eid = -1;
$linkno = 0;

$index = array(
    array(
      'min' => 1634,
      'i8n' => 'Liikuvad usupühad: munapühad',
      'a' => 'munapyhad'
    ),
    array(
      'min' => 3009,
      'i8n' => 'Liikuvad usupühad: advent',
      'a' => 'advent'
    ),
    array(
      'min' => 10109,
      'i8n' => 'Muud liikuvad tähtpäevad',
      'a' => 'liikuvad'
    ),
    array(
      'i8n' => 'Peamised allikad ja viited',
      'a' => 'allikad'
    )
);

for($i=11; $i>-1; $i--) {
  array_unshift($index, array(
      'min' => ($i+1) *100,
      'i8n' => $i18net['kuu'][$i],
      'a' => 'q'.($i+1),
    )
    );
}

array_unshift($index, array(
      'min' => 20,
      'i8n' => 'Pööripäevad',
      'a' => 'pqqrip2evad'
));

//PHP5 & SQLite3 style data access
$dbh = new PDO($init_data['dbc']); 

echo '<h1><a name="sisukord">Sisukord</a></h1><ol>'. "\n";
for($i=0; $i<count($index); $i++) {
  echo '<li><a href="#'.$index[$i]['a'].'">'.$index[$i]['i8n'].'</a></li>'. "\n";
  if($index[$i]['min']) {
    $res = $dbh->query( "SELECT min(id) m FROM events WHERE id > " . $index[$i]['min'] )->fetchAll();
    $index[$i]['a_el'] = $res[0]['m'];
  }
}
echo '</ol>'. "\n";

$urlcategory0 = '';
$paragraph = 0;

$sql = "SELECT e.*, u.urlcategory_id, u.url, u.res_type, c.urlcategory, c.urlprefix"
  ." FROM events e, urls u, urlcategories c"
  ." WHERE e.id = u.event_id AND u.urlcategory_id = c.id"
  //." AND e.maausk IS NOT NULL AND e.maausk <> ''" //maausu filter
  ." AND c.http_status = 200 AND u.http_status = 200 " //surnd linkide filter
  ." ORDER BY e.id, c.urlcategory, u.url";

foreach ($dbh->query($sql) as $row) {

    $row = array_merge($row, eventflags($row['id'],$row['flags']));
      
    if($eid != $row['id']) {

        if($eid != -1) { // uus tähtpäev algab
            echo str_replace('{abid}',$eid,$mall_abid);
            echo '</div>' . "\n";
            $urlcategory0 =  '';
        }
		
		$runelink = '';
		if($row['rune_id']) {
			$sql = "SELECT r.* FROM runes r WHERE r.dbid=".$row['rune_id'];
			foreach($dbh->query($sql) as $rune) {
				/*
				$runeurl = 'r.php?f='.$rune['filename']
					.'&w='.$rune['width']
					.'&n='.$row['maausk'];
				*/
				$runeurl = 'svg/'.$rune['filename'];
				$runelink = '<a target="_blank" href="'.$runeurl.'" onClick="return r(\''.$row['maausk'].'\','.$rune['width'].',\''.$rune['filename'].'\');" title="Vaata sirvikalendri ruuni">';
				break;
			}
		} 
		
	if($index[$paragraph]['a_el'] == $row['id']) {
	  echo '<br/><br/><h1><a name="'.$index[$paragraph]['a'].'">'.$index[$paragraph]['i8n'].'</a></h1>'. "\n";
	  $paragraph ++;
	}
	
    
    $weekday = '';
    
	// leia päeva toimumise kirjeldus ID j2rgi
	
	if($row['id'] < 25) {	$event_time = ''; } 
	else if($row['id'] < 1232) {	$event_time = 
	
	    ($row['id'] % 100) 
	    . '.' . floor($row['id'] / 100); } 
	    	
	else if($row['id'] <  2366) { 
       $event_time = 
	    ($row['id'] - 2000)
	    . ' <a href="#2000" title="päeva 1. Ülestõusmispühast">p1Ü</a>'; 
           
         $weekday = '<br/><a href="ruunid'
                .$init_data['static_extension']
                .'#'.$row['weekday'].'">'
                .ucfirst($i18net['n2dalap'][$row['weekday']])
                .'</a>.';
	}
	else if($row['id'] <  3537) { $event_time = 
	
	    floor(($row['id'] - 3000) / 10)  . '. '
        .'<a href="ruunid'
                .$init_data['static_extension']
                .'#'.($row['id'] % 10).'">'
	    .$i18net['n2dalap'][$row['id'] % 10]
	    . '</a> <a href="#1225" title="enne 1. Jõulupüha">e1J</a>'; }
	    
	else if($row['id'] <  11257) { 
       $n2dal = floor($row['id'] / 10) % 10;
       $event_time = 
	    $i18net['kuu_om'][floor($row['id'] / 100) -101] . ' '
	    . ($n2dal == 5 ? 'viimane ' : $n2dal . '. ')
        .'<a href="ruunid'
                .$init_data['static_extension']
                .'#'.($row['id'] % 10).'">'
	    . $i18net['n2dalap'][$row['id'] % 10]
        . '</a>'; } 
	    
	else { $event_time = "Teadmata toimumisajaga : " . $row['id']; }
	
	
        echo  "\n" . "\n" . '<br /><br /><strong><a name="' .
            $row['id'] . '">' .
            $event_time .
            '</a>'.
			($event_time == '' ? '' : ': ' ).
             ( trim($row['event']) != '' ? $row['event'] : $row['maausk'] ) .
            '</strong><div class="day">' . "\n";

	if(trim($row['maausk']) != '') {
            echo '<br />Maausu püha: <strong>' . $runelink .$row['maausk']
                .($runelink == '' ? '' : '</a>')
                ."</strong>\n";
        }
        
        if($row['dayflag'] == '1') {
            echo '<br /><img src="est.gif" width=71 height=46 alt="" align=left><strong>*</strong> '
	      . $i18net['lipup2ev']
	      . "\n";
        }
        
        if($row['dayfree'] == '1') {
            echo '<br /><strong><font color="red">*</font></strong> '
	      . $i18net['puhkep2ev']
	      . "\n";
        }

        if($row['daystate'] == '1') {
            echo '<br /><strong>*</strong> '
	      . $i18net['t2htp2ev']
	      . "\n";
        }
        
        if($row['shorterworkdayb4'] == '1') {
            echo '<br /><strong>*</strong> '
          . $i18net['lyhendet_tqqp2ev']
          . "\n";
        }
        
        echo $weekday;
        
        if(trim($row['more']) != '') {
            echo '<br />' . $row['more'] . "\n";
        }
        
        $eid = $row['id'];
        $linkno = 0;

    }

    $urlcategory1 = ( ( $row['urlcategory_id'] == 0 && preg_match('/^http:\/\/(www.)?([^\/]+)\//', $row['urlprefix'].$row['url'], $matches) ) 
	      ? $matches[2] : $row['urlcategory']);
    $urlno = '';
    if( $urlcategory0 != $urlcategory1 ) {
      $urlnr = 1;
    } else {
      $urlnr ++;
      $urlno = ' '.$urlnr;
    }
    $urlcategory0 = $urlcategory1;
	
    $restype = '';
    if(strtoupper($row['res_type'])=='A') { $restype = '<sup> audio</sup>'; }
    if(strtoupper($row['res_type'])=='V') { $restype = '<sup> video</sup>'; }
       
    echo ($linkno ? ', ' : '<br />Viited: ')
        . '<a href="' . $row['urlprefix'] . $row['url'] . '">' 
        . $urlcategory1 . $urlno
        . '</a>' . $restype ."\n";
        
        
    $linkno ++;
	
	//print_r($row);    
}

# viimane ID
echo str_replace('{abid}',$eid,$mall_abid);
                
//select * from urls where urlcategory_id = 4 order by event_id

?>
</div>

<?php 

echo '<br/><br/><br/><h1><a name="allikad">Peamised allikad ja viited</a></h1><ol>'."\n";

foreach ($dbh->query(
  "SELECT c.urlcategory, c.site, count(u.id) as ucc, c.id  FROM urlcategories c, urls u WHERE  c.id = u.urlcategory_id"
  ." AND c.http_status = 200" //surnud linkide filter
  ." GROUP BY c.id ORDER BY c.urlcategory"
) as $src) {
  echo '<li>'.$src['urlcategory'].' ('.$src['ucc'].')'
  .( $src['id'] != '0' ? ':   <a href="'.$src['site'].'">'.$src['site'].'</a>' : '')
  .'</li>'."\n";
  //echo '<li>'."\n";print_r($src);echo '</li>'."\n";
}

echo '</ol>';

?>
<br/>
<?php echo 
  html_db_copy($GLOBALS['init_data']['static_extension']) 
  . '.<br/><br/><center>' 
  . $html_navigation
  . '.</center>'; ?>

</div>

</body>
</html>