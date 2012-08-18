<?php 

include('includes/web_init.php'); 
include('includes/web_header.php');
$dbh = new PDO($init_data['dbc_descr']);

?>

<div class="bodydiv">

<center><?php echo $html_navigation = html_navigation_bar(); ?></center>

<h1><a name="sisukord">Sisukord</a></h1><ol>
<li><a href="#t2histused">Tähtpäevade tähistused</a></li>
<li><a href="#greg">Gregooriuse kalender</a></li>
<li><a href="#ab">Andmebaas</a>
   <ol type="A">
    <li><a href="#mallid">Tähtpäevade mallid</a></li>
    <li><a href="#idd">Tähtpäevade ID-d</a></li>
    <li><a href="#tabelid">Tabelid</a></li>
        <ol>
<?php 


$labels = array();
foreach ($dbh->query("SELECT * FROM tables ORDER BY table_name") as $row) {
  echo '<li>'
  .'<a href="#tabel.'.$row['table_name'].'">'.$row['table_name'].'<a>'
  .'</li>'."\n";
  $labels[$row['table_name']] = $row['description'];
}

echo '';

?>
        </ol>
   </ol> 
</li>
</ol>
<?php 


print_r($labels );

?>
<h1><a name="t2histused">Tähtpäevade tähistused</a></h1>

<table>
<tr><th>Sümbol</th><th>T&auml;hendus</th></tr>

<tr><td>+</td><td><a href="tahtpaevad<?php echo $init_data['static_extension']; ?>#munapyhad">Munapühade</a> või <a href="tahtpaevad<?php echo $init_data['static_extension']; ?>#advent">advendiga</a> seotud liikuvad kirikupühad.</td></tr>

<tr><td>X</td><td><a href="tahtpaevad<?php echo $init_data['static_extension']; ?>#liikuvad">Muud liikuvad tähtpäevad</a>, X on tähtpäeva esitäht.</td></tr>

<tr><td><strong style="color:red; font-family:courier;" title="<?php echo $i18net['puhkep2ev']; ?>">*</strong></td><td><?php echo $i18net['puhkep2ev']; ?></td></tr>

<tr><td><img style="lipp" src="lp.gif" title="<?php echo $i18net['lipup2ev']; ?>" alt="<?php echo $i18net['lipup2ev']; ?>"/></td><td><?php echo $i18net['lipup2ev']; ?></td></tr>

</table>

<h1><a name="greg">Gregooriuse kalender</a></h1>

kehtestati paavst Gregorius XIII poolt 1582 (4. oktoobrile 1582 järgnes 15. oktoober 1582) ebatäpsema <a href="http://en.wikipedia.org/wiki/Julian_calendar" target="_blank">juuliuse kalendri</a> asemele ja levis algul peamiselt katoliiklikes maades. Lääne-Euroopa protestantlikud riigid võtsid reformitud kalendri kasutusele alles 18. sajandil ja Ida-Euroopa riigid, kus ametlikuks usuks oli õigeusk, 20. sajandi esimesel veerandil. <strong>Eestis</strong> oli kalendrivahetus 1. veebruaril 1918.a., millele järgnes kohe 14. veebruar 1918.a.
<br /><br />

Niisiis tuleb arvestada, et <a href="http://en.wikipedia.org/wiki/Gregorian_calendar" target="_blank">gregooriuse kalender</a> pole minevikus alati kehtinud. Varemkehtinud juuliuse kalender jääb seejuures gregooriuse omast maha:<br /><br />

<table>
<tr><th colspan="2">Aastatevahemik</th><th>Juuliuse kalendri mahajäämus gregooriuse omast päevades</th></tr>

<tr><td>1582</td> <td>1699</td> <td>10</td></tr>
<tr><td>1700</td> <td>1799</td> <td>11</td></tr>
<tr><td>1800</td> <td>1899</td> <td>12</td></tr>
<tr><td>1900</td> <td>2099</td> <td>13</td></tr>
<tr><td>2100</td> <td>2199</td> <td>14</td></tr>

</table>

Allikas: Poulsen, E. The transition from Julian to Gregorian Calendar. <a href="http://www.rundetaarn.dk/engelsk/observatorium/gregorian.html" target="_blank">http://www.rundetaarn.dk/engelsk/observatorium/gregorian.html</a> 
<br /><br />

Samuti ei suuda siinkasutatud <a href="tahtpaevad<?php echo $init_data['static_extension']; ?>#2000">munapüha</a> leidmise nn. <a href="http://en.wikipedia.org/wiki/Computus#Gauss_algorithm" target="_blank">Gaussi valem</a> arvutada kevadisi liikuvaid kirikupühi väljaspool aastavahemikku 1500 ... 2299.


<h1><a name="ab">Andmebaas</a></h1>

on <?php echo (filesize('kalender.sdb')>>10) ?> kB <a href="http://www.sqlite.com/" target="_blank">SQLite</a> andmebaasifail <a href="kalender.sdb">kalender.sdb</a>, mille kasutamisele autor piiranguid ei sea. <br/>

Eeldusel et igal tähtpäeval on vähemalt 1 link tabelis urls, oleks lihtne päring kõigi andmete kättesaamiseks:
<blockquote><pre>
SELECT e.*, u.urlcategory_id, u.url, u.res_type, c.urlcategory, c.urlprefix 
FROM events e, urls u, urlcategories c 
WHERE e.id = u.event_id AND u.urlcategory_id = c.id 
ORDER BY e.id, c.urlcategory, u.url
</pre></blockquote>

Andmebaasi struktuuri kirjeldus on saadaval <?php echo (filesize('descriptions.sdb')>>10) ?> kB andmebaasifailina <a href="descriptions.sdb">descriptions.sdb</a>.
<h1><a name="mallid">Tähtpäevade mallid</a></h1>

<table>
<tr><th>Tähistus</th><th>Min</th><th>Max</th><th>Pikkus</th><th>Tähendus</th></tr>

<tr><td valign="top">KK</td><td valign="top">1</td><td valign="top">12</td><td valign="top">2</td><td>

Kuu numbrid, 01...12

</td></tr>

<tr><td valign="top">N</td><td valign="top">1</td><td valign="top">5</td><td valign="top">1</td><td>

Nädala number kuu sees, 5 tähendab kuu viimast nädalat.

</td></tr>

<tr><td valign="top"><a name="mallid.n">n</a></td><td valign="top">0</td><td valign="top">6</td><td valign="top">1</td><td>

Nädalapäeva number nädala sees, järjestuses P,E,T,K,N,R,L.

</td></tr>

<tr><td valign="top">PP</td><td valign="top">1</td><td valign="top">31</td><td valign="top">2</td><td>

Kuupäevad, 01...31.

</td></tr>

<tr><td valign="top">VV</td><td valign="top">1</td><td valign="top">53</td><td valign="top">2</td><td>

Nädala number aasta sees, 01...53.

</td></tr>

</table>





<h1><a name="idd">Tähtpäevade ID-d</a></h1>

<table>
<tr><th>Mall või =valem</th><th>Min</th><th>Max</th><th>Tüüp</th><th>Tähendus</th></tr>

<tr><td valign="top">KKPP</td><td valign="top">101</td><td valign="top">1231</td><td valign="top">Liikumatu</td><td valign="top">

Kindlal kalendripäeval olev tähtpäev.

</td></tr>

<tr><td valign="top">= 2000 + x</td><td valign="top">1635</td><td valign="top">2365</td><td valign="top">Liikuv, usuline</td><td valign="top">

x päeva 1. ülestõusmispühast.

</td></tr>

<tr><td valign="top">3VVn</td><td valign="top">3010</td><td valign="top">3536</td><td valign="top">Liikuv, usuline</td><td valign="top">

VV-inda nädala n-is nädalapäev enne 1. jõulupüha.

</td></tr>

<tr><td valign="top">1KKNn</td><td valign="top">10110</td><td valign="top">11256</td><td valign="top">Liikuv</td><td valign="top">

KK-nda kuu N-da nädala n-is nädalapäev. Vt. <a href="http://www.gnu.org/s/hello/manual/libc/TZ-Variable.html" target="_blank">POSIX TZ muutuja</a> DST osa formaat Mm.w.d.

</td></tr>

</table>


<h2><a name="tabelid">Tabelid</a></h2>

<?php 

$tablenames = array_keys($labels);
sort($tablenames);

foreach($tablenames as $tablename) {

    echo '<h3><a name="tabel.'.$tablename.'">Tabel '.$tablename.'</a></h3>
'.$labels[$tablename].'<br/><br/>
<table>
<tr><th>Veeru nimi</th><th>Veeru tüüp</th><th>Võti</th><th>Tähendus</th></tr>'."\n";

    foreach ($dbh->query("select * from tablecolumns where table_name='".$tablename."' order by column_name") as $row) {
    
        $row['column_key'] = trim($row['column_key']);
        if($row['column_key'] == 'PK') {
            $row['column_key'] = 'Esmasvõti';
        } else if($row['column_key'] != '') {
            $row['column_key'] = '<a href="#tabel.'.$row['column_key'].'">'.$row['column_key'].'</a>';
        }
        
        echo '<tr>
    <td valign="top"><a name="tabel.'.$tablename.'.'.$row['column_name'].'">'.$row['column_name'].'</a></td>
    <td valign="top">'.$row['column_type'].'</td>
    <td valign="top">'.$row['column_key'].'</td>
    <td>'.$row['description'].'</td>
</tr>'."\n";
    }
    
    echo '</table>'."\n";
    
}

?>

<br/><br/>
Viimati uuendatud <?php echo date('Y-m-d H:i:s'); ?>.
<br/><br/>
<center><?php echo $html_navigation; ?></center>

</div>

</body>
</html>