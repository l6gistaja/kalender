<html>
  <head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<title>Backup <xsl:value-of select="/kalender/timeiso8601"/></title>
<link rel="StyleSheet" type="text/css" media="screen" href="../kalender.css"/>
<link rel="SHORTCUT ICON" href="../favicon.ico"/>
</head>
<body>
<a href="data/">Valmis backupid</a><br/><br/>
  
<form action="index.php" method="get">
<input type="submit" name="generate" value="Loo">
 uus backup (<input type="checkbox" name="wget" value="1"> koos wget-failiga).<br/>
Salasõna: <input type="text" name="pwd">
</form>

<?php 
if($_REQUEST['generate'] && $_REQUEST['pwd'] = 'pumuki') {

  $db = array(

    'urlcategories' => array(
      'txt' => array('site', 'urlprefix', 'urlcategory',),
    ),

    'events' => array(
      'txt' => array('maausk', 'more', 'event',),
    ),
    
    'urls' => array(
      'txt' => array('url',),
      'select' => 'select * from urls order by event_id, urlcategory_id, id'
    ),
    
  ); 

  $time_iso8601 = date('Y-m-d H:i:s');
  $time = preg_replace('/[^\d]+/', '', $time_iso8601);
  $path = 'data/' . $time ;
  mkdir ($path, 0755);

  //PHP5 & SQLite3 style data access
  $dbh = new PDO('sqlite:../kalender.sdb'); 
  
  if($_REQUEST['wget'] == '1') {
    mkdir ($path . '/web', 0755);
    $fp = fopen($path . '/wget.bash', 'w');
    foreach ($dbh->query('select u.id, uc.urlprefix, u.url from urls u, urlcategories uc where u.urlcategory_id not in (1,7) and u.urlcategory_id = uc.id') as $row) {
      fwrite($fp, "wget -O 'web/{$row['id']}.html' '{$row['urlprefix']}{$row['url']}';\n");
    }
    fclose($fp);
  }

  $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>' . "\n"
    .'<?xml-stylesheet type="text/xsl" href="../../html.xsl"?>' . "\n"
    .'<kalender>' . "\n"
    .'<time>' . $time . '</time>' . "\n"
    .'<timeiso8601>' . $time_iso8601 . '</timeiso8601>' . "\n"
    .'<db>' . "\n";
    
   $wgetf = '';
    
  foreach($db as $table => $tabledata) {

    $xml .= '<' . $table . '>' . "\n";

    $int = array();
    $i = 0;
    foreach ($dbh->query(
      $tabledata['select'] 
      ? $tabledata['select'] 
      : 'select * from ' . $table) as $row) {
      
      if($i == 0) {
	foreach($row as $k => $v) {
	  if(!is_numeric($k) && !in_array($k, $tabledata['txt'])) {
	    array_push($int, $k);
	  }
	}
      }
      
      $xml .= '<r';
      $txta = array();
      foreach($row as $k => $v) {
      
	if(is_numeric($k)) { continue; }
	
	if(in_array($k, $int)) {
	  $xml .= ' ' . $k . '="' . $v . '"';
	} else {
	  if(trim($v) != '') {
	    $txta[$k] = $v;
	  }
	}
	
      }
      
      $xml .= '>' . "\n";
	  
      foreach($txta as $k => $v) {
	$xml .= '<' . $k . '>' . htmlspecialchars($v) . '</' . $k . '>'. "\n";
      }
      
      $xml .= '</r>' . "\n";
      
      $i++;
      
    }
    
    $xml .= '<txt>' . implode(',', $tabledata['txt']) . "</txt>\n";
    $xml .= '<int>' . implode(',', $int) . "</int>\n";
    
    $xml .= '</' . $table . '>' . "\n";

  }

  $xml .= '</db>' . "\n";

  $xml .=  '</kalender>' . "\n";


  $fp = fopen($path . '/index.xml', 'w');
  fwrite($fp, $xml);
  fclose($fp);

  #echo '<pre>'.htmlspecialchars($xml).'</pre>';
  echo '<br/>Loodud uus backup: <a href="'.$path.'">'.$path.'</a>';

}

?>
</body>
</html>