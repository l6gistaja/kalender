<?php 

// MAIN CONF // MAIN CONF // MAIN CONF // MAIN CONF // MAIN CONF // MAIN CONF // MAIN CONF

$init_data = array(
  'dynamic' => false, // true, if under devel
  'dbc' => 'sqlite:kalender.sdb'
);

// BUILDING OTHER CONF // BUILDING OTHER CONF // BUILDING OTHER CONF // BUILDING OTHER CONF 

$init_data['static_extension'] = $init_data['dynamic'] ? '.php' : '.html';

$init_data['pages'] = array(

  array(
    'page' => 'index',
    'ext' => '.php',
    'title' => 'Kalender'
  ),
  
  array(
    'page' => 'tahtpaevad',
    'ext' => $init_data['static_extension'],
    'title' => 'Tähtpäevad'
  ),
  
  array(
    'page' => 'ruunid',
    'ext' => $init_data['static_extension'],
    'title' => 'Ruunid'
  ),
  
  array(
    'page' => 'abi',
    'ext' => $init_data['static_extension'],
    'title' => 'Abilehekülg'
  ),
  
);

// find current page
$namepieces = explode('.', get_request_name());
$pgname = $namepieces[0];
foreach($init_data['pages'] as $pgno => $pg) {
  if($pgname == $pg['page']/*.$pg['ext']*/) {
    $init_data['currentpg'] = $pgno;
    break;
  }
}


$i18net = array(

  'kuu_om' => array(
     'Jaanuari' ,
     'Veebruari' ,
     'Märtsi' ,
     'Aprilli' ,
     'Mai' ,
     'Juuni' ,
     'Juuli' ,
     'Augusti' ,
     'Septembri' ,
     'Oktoobri' ,
     'Novembri' ,
     'Detsembri' 
  ),
  
  'n2dalap' => array(
     'pühapäev' ,
     'esmaspäev' ,
     'teisipäev' ,
     'kolmapäev' ,
     'neljapäev' ,
     'reede' ,
     'laupäev' 
  ),
  
  'kuu' => array(
     'Jaanuar' ,
     'Veebruar' ,
     'Märts' ,
     'Aprill' ,
     'Mai' ,
     'Juuni' ,
     'Juuli' ,
     'August' ,
     'September' ,
     'Oktoober' ,
     'November' ,
     'Detsember' 
  ),
  
  
  'lipup2ev' => 'On lipu(heiskamis)päev.',
  'puhkep2ev' => 'On riigipüha ja puhkepäev.',
  't2htp2ev' => 'On riiklik tähtpäev.'
  
);

function get_request_name() {

  $namepieces = explode('/', $_SERVER['SCRIPT_NAME']);
  return $namepieces[count($namepieces)-1];
}

function html_db_copy($ext) {

  return '<a href="abi'
    . $ext
    .'#ab">Andmebaasi</a>tõmmis loodud '.date('Y-m-d H:i:s');
}

function html_navigation_bar() {
  $y = '';
  foreach($GLOBALS['init_data']['pages'] as $pgno => $pg) { 
    
    if($pgno > 0) { $y .= '&nbsp;|&nbsp;'; }
    
    if($GLOBALS['init_data']['currentpg'] == $pgno) { 
    
      $y .= '<strong>'
	. $pg['title']
	.'</strong>';
      
    } else {
    
      $y .= '<a href="'
	. $pg['page'].$pg['ext']
	.'">'
	. $pg['title']
	.'</a>';
	
    }
 
  }
  return $y;
}

function compose_day_label($db_row, $delimiter = '; ') {
  $label_a = array();
  $str = trim($db_row['event']);
  if($str != '') { array_push($label_a, $str); }
  $strm = $db_row['sol']> 0 ? '' : trim($db_row['maausk']);
  if($strm != '' && $strm != $str) { array_push($label_a, $strm); }
  return implode($delimiter, $label_a);
}

#echo "<pre>";print_r($init_data);echo "</pre>";