<?php 

include_once('includes/web_init.php');
$pgname = get_request_name();
$standalone = false;
if($pgname == 'calendar_data.php') { $standalone = true; }

$calendardata = array('k' => array(), 'y' => array(), 'a' => array(), 'l' => array());
$dbh = new PDO($init_data['dbc']); 
foreach ($dbh->query("SELECT maausk, event, id, flags FROM events WHERE id > 100") as $row) {
  
  $row = array_merge($row, eventflags($row['id'],$row['flags']));
  
  if($db_row['sol'] < 3) { //ärme pööripäevi (esialgu) liikumatute hulka lisa
  
    $el = array('n' => compose_day_label($row));
    if($row['dayfree'] == 1) { $el['f'] = 1; }
    if($row['dayflag'] == 1) { $el['l'] = 1; }
    if($row['shorterworkdayb4'] == 1) { $el['w'] = 1; }
    $calendardata['d'][$row['id']] = $el;

    if($row['id'] < 1232) { $key = 'k'; } // kuupäevad
    else if($row['id'] <  2366) { $key = 'y'; } // ylest6usmispyhad
    else if($row['id'] <  3537) { $key = 'a'; } // advendid
    else if($row['id'] <  11257) { $key = 'l'; } // liikuvad
      
    array_push($calendardata[$key], intval($row['id']));
    
  }
  $calendardata['d'][320]['n'] = 'Kevadine pööripäev';
  $calendardata['d'][621]['n'] = 'Suvine pööripäev';
  $calendardata['html_dbcopy'] = html_db_copy('.html');
  
}

if($standalone) {
  if($_REQUEST['wgetter']) {
        echo "<?php\n\n// overwrite includes/calendar_data_static.php with this\n\n$"."calendardata = "
        . var_export($calendardata, true)
        . ";";
  } else {
    echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/></head><body>'
        . "<pre>&lt;?php\n\n// overwrite includes/calendar_data_static.php with this\n\n$"."calendardata = "
        . htmlspecialchars(var_export($calendardata, true))
        . ";</pre></body></html>";
  }
}