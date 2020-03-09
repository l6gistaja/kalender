<?php 

$dirs_down = 2;
include(dirname(__DIR__,$dirs_down).'/includes/web_init.php');
if(!$init_data['dynamic']) exit;
$arhiiv_id = 7;
$fragm = [
    'header' => "<html>\n<head>\n<meta http-equiv=Content-Type content=\"text/html; charset=UTF-8\">\n<title>".'%1$s'."</title>\n<link rel=\"SHORTCUT ICON\" href=\"../favicon.ico\"/>\n<link rel=\"StyleSheet\" href=\"arhiiv.css\" type=\"text/css\"/>\n</head>\n<body>\n",
    'footer' => "</body>\n</html>",
    'main_title' => '<a href="%1$s" title="&lt;&lt; Tagasi">Eesti tähtpäevad</a>',
    'file' => 'e%1$05s.html',
    'dir' => dirname(__DIR__,$dirs_down).'/txt/'
];

$dbh = new PDO('sqlite:'.dirname(__DIR__,$dirs_down).'/'.str_replace('sqlite:','', $init_data['dbc']));
$events = array();
foreach ($dbh->query("select id, event, maausk from events") as $row) {
    $id = (int) $row['id'];
    $events['E'.$id] = ($id > 100 && $id < 1232 ? sprintf('%02d.%02d ', $id%100, floor($id/100)) : '')
        .($row['event'] ? $row['event'] : $row['maausk']);
}
//print_r($events);

$files = [];
$previous_id = 0;
$dbht = new PDO('sqlite:'.dirname(__DIR__,$dirs_down).'/archive.sqlite3');

foreach ($dbht->query("select * from archive where description <> '' order by date, url") as $row) {
    $date = (int) $row['date'];
    if($date != $previous_id) {
        echo sprintf("%05d %s\n", $date, $events['E'.$date]);
        if($previous_id) {
            file_put_contents($fragm['dir'].sprintf($fragm['file'],$previous_id), $file."\n".$fragm['footer']);
        }
        $file = sprintf($fragm['header'],$events['E'.$date]).'<h1>'.sprintf($fragm['main_title'],'index.html').': '.$events['E'.$date]."</h1>\nUuendatud ".date('Y-m-d H:i:s')."\n\n";
        $files[] = $date;
        $previous_id = $date;
    }
    $anchor = strtolower(preg_replace(['/[^\s\d\.A-Za-z]/','/\s+/'], ['','_'], str_replace(['Õ','õ','Ä','ä','Ö','ö','Ü','ü'], ['O','o','A','a','O','o','Y','y'], trim($row['name'])))).'_'.$row['id'];
    $file .= '<hr/><a name="'
        .$anchor
        .'"></a><h2><a href="#'
        .$anchor
        .'" title="Permalink">'
        .$row['name']
        ."</a></h2>\n"
        .((int) $row['newlines'] ? str_replace("\n", "<br/>\n", trim($row['description'])) : trim($row['description']))
        ."<br/>\n<br/>\n<strong>Allikas:</strong> <a href=\""
        .$row['url']
        ."\" target=\"_blank\">"
        .$row['url']
        ."</a><br/><br/>\n";
}
file_put_contents($fragm['dir'].sprintf($fragm['file'],$previous_id), $file."\n".$fragm['footer']);

$sthu = $dbht->prepare("INSERT INTO dead_urls (id, urlcategory_id, event_id, url, flags, http_status) VALUES (?, ?, ?, ?, ?, ?)");
$sthj = $dbht->prepare("INSERT INTO archive (date, description, newlines, url) VALUES (?, ?, ?, ?)");
foreach ($dbh->query("SELECT * FROM urls WHERE http_status <> 200") as $row) {
    //$sthu->execute([$row['id'], $row['urlcategory_id'], $row['event_id'], $row['url'], $row['flags'], $row['http_status']]);
    //$sthj->execute([$row['event_id'], '', 2, 'https://web.archive.org/web/*/'.$row['url']]);
}
$dbh->query("DELETE FROM urls WHERE http_status <> 200");
$sthu = null;
$sthj = null;

$dbht = null;

$indexfile = sprintf($fragm['header'],'Eesti tähtpäevad').'<h1>'.sprintf($fragm['main_title'],'../tahtpaevad.html')."</h1>\n<ol>\n";
$sthc = $dbh->prepare("SELECT COUNT(id) AS c FROM urls WHERE urlcategory_id = ? AND event_id = ?");
$sthi = $dbh->prepare("INSERT INTO urls (urlcategory_id, event_id, url, flags, http_status) VALUES (?, ?, ?, 0, 200)");
foreach($files as $id) {
    $indexfile .= "<li><a href=\"".sprintf($fragm['file'],$id)."\">".$events['E'.$id]."</a></li>\n";
    $sthc->execute([$arhiiv_id, $id]);
    $result = $sthc->fetch(PDO::FETCH_ASSOC);
    if((int) $result['c'] == 0) { $sthi->execute([$arhiiv_id, $id, sprintf($fragm['file'],$id)]); }
}
file_put_contents($fragm['dir'].'index.html', $indexfile."</ol>\nUuendatud ".date('Y-m-d H:i:s').$fragm['footer']);
$sthc = null;
$sthi = null;

$dbh = null;
