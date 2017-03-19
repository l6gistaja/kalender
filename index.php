<?php 

include('includes/web_init.php');
include('includes/web_header.php');
include($init_data['dynamic'] ? 'calendar_data.php' : 'includes/calendar_data_static.php');
include('includes/functions_calendar.php');

foreach(array('aasta','kuu','ekr') as $v22rtus){ ${$v22rtus}=floor(0+$_GET[$v22rtus]);}

# kui keegi yritab mingeid imelikke andmeid ette sqqta, siis saagu v22rtustks t2nased
if($ekr!=1) { $ekr=0; }
if($aasta<1) {$aasta=date('Y');}
if(($kuu<1)||($kuu>12)){$kuu=date('n');}
$op_aasta=($ekr)?1-$aasta:$aasta;

?>

<center>
<?php echo $html_navigation = html_navigation_bar(); ?>
<br/><br/>
<form method="get" action="index.php"><input type="submit" value="Kuva">&nbsp;&nbsp; 
<?php 

echo "<a href=\""
  .get_calendar_url($aasta, $kuu -1, $ekr)
  ."\" title=\"Eelnev kuu\" rel=\"nofollow\">&lt;</a> ";

echo "<select name=\"kuu\" onChange=\"this.form.submit();\">\n";
foreach($i18net['kuu'] as $v6ti => $v22rtus) {
	$valitud = ( $kuu == ($v6ti +1) )?' selected':'';
	echo " <option value=\"" . ($v6ti +1) . "\"$valitud>"
	  .strtolower($v22rtus)."</option>\n";
}
echo "</select>";

echo " <a href=\""
  .get_calendar_url($aasta, $kuu +1, $ekr)
  ."\" title=\"Järgnev kuu\" rel=\"nofollow\">&gt;</a>";
  
 ?>

&nbsp;&nbsp;
<a href="<?= get_calendar_url($aasta, $kuu -12, $ekr); ?>" title="Eelnev aasta" rel="nofollow">&lt;</a>
<input type="text" name="aasta" size="4" value="<?php echo $aasta; ?>" onChange="this.form.submit();">

<a href="<?= get_calendar_url($aasta, $kuu +12, $ekr); ?>" title="Järgnev aasta" rel="nofollow">&gt;</a>
 a.&nbsp;&nbsp;

<input type="checkbox" name="ekr" value="1"<?php if($ekr==1) { echo 'checked'; } ?> onChange="this.form.submit();"> e. Kr. või 
<a href="index.php">käesolev kuu</a>.

</form>
<?php

#$n2dalap2evad=array('E','T','K','N','R','L','P');
# $n2dalap2evad=array('Mo','Tu','We','Th','Fr','Sa','Su');

$liikumatud = & $calendardata['k'];

$p2evi_kuus=gregorius_p2evi_kuus($op_aasta, $kuu);
$liig_aasta=gregorius_liigaasta($op_aasta, $kuu);
$n2dalap2ev=gregorius_n2dalap2ev($op_aasta, $kuu, 1);
$neljas_jaanuar=gregorius_n2dalap2ev($op_aasta, 1, 4);
$kirikupyhad=gregorius_arvutakirik($op_aasta);
#foreach($kirikupyhad as $v6ti => $v22rtus) { echo " $v6ti => $v22rtus<BR>";}
$t2na=date('nd');
if(($kuu==1)&&($neljas_jaanuar<3))
{
	# ehk kui jaanuarisse mahub eelmise aasta n2dal
	# siis leida eelmise aasta viimase n2dala number
	# j2rgnev on "peaaegu koopia" alloleva else elsest
	  # j2rgneva m6te on selles, et leides aasta esimesest n2dalast mqqdund
	  # n2dalate arvu ja suurendades seda 2 v6rra, peaksime saama antud 
	  # kuu esimese n2dala numbri... a v6ibolla ei saa ka :)
	  $n2dala_nr=0;
	  for($i=1;$i<13;$i++)
		{
			$n2dala_nr+=gregorius_p2evi_kuus($op_aasta+(($ekr)?+1:-1), $i);
		}
	# (10-$neljas_jaanuar) peaks olema aasta 1. n2dala viimane p2ev.
	 $n2dala_nr=2+floor(($n2dala_nr-(10-$neljas_jaanuar))/7);
} # if(($kuu==1)&&($neljas_jaanuar<3))
else
{
  if($kuu==1) { $n2dala_nr=1; }
  else
	{
	  # j2rgneva m6te on selles, et leides aasta esimesest n2dalast mqqdund
	  # n2dalate arvu ja suurendades seda 2 v6rra, peaksime saama antud 
	  # kuu esimese n2dala numbri... a v6ibolla ei saa ka :)
	  $n2dala_nr=0;
	  for($i=1;$i<$kuu;$i++)
		{
			$n2dala_nr+=gregorius_p2evi_kuus($op_aasta, $i);
		}
	# (10-$neljas_jaanuar) peaks olema aasta 1. n2dala viimane p2ev.
	 $n2dala_nr=2+floor(($n2dala_nr-(10-$neljas_jaanuar))/7);
	}
} # if(($kuu==1)&&($neljas_jaanuar<3)) else

#print_r($calendardata['d']);

?>
<table class="k_t">
<tbody>
<tr>
<th class="k_th" colspan="8"><?php 

echo $i18net['kuu'][$kuu-1] ." " .$aasta .(($ekr)?' e. Kr.':''); 

?></th></tr>
<tr>
<td class="k_tn">nädal</td>
<td class="k_tnp">E</td>
<td class="k_tnp">T</td>
<td class="k_tnp">K</td>
<td class="k_tnp">N</td>
<td class="k_tnp">R</td>
<td class="k_tnp">L</td>
<td class="k_tnp">P</td>
</tr>
<tr><td class="k_tn"><?php echo $n2dala_nr; $n2dala_nr=((($kuu==1)&&($n2dala_nr>1))?1:$n2dala_nr+1); ?>.</td>
<?php 

$n2dp2evi = array(0,0,0,0,0,0,0);

for($i=0; $i<$n2dalap2ev; $i++)
	{
		echo "<td class=\"k_t0\">&nbsp;</td>\n";
	}
for($kuup2ev=1;$kuup2ev<=$p2evi_kuus; $kuup2ev++, $i++)
	{
		$kuup=($kuu*100)+$kuup2ev;
		if((!($i%7))&&($kuup2ev!=1)) { 
		  echo "</tr>\n<tr>\n<td class=\"k_tn\">"
		    .($n2dala_nr++)
		    .".</td>"; 
		}
		
		$html_muud = array();
		$html_kuup2ev = $kuup2ev;
		$lipup2ev = false;
		$vabap2ev = false;
        $lyhendet = false;
		
		// liikumatud
		if( in_array($kuup, $liikumatud) ) {
		  $viit = $kuup;
		  $html_kuup2ev = "<a href=\"tahtpaevad{$init_data['static_extension']}#$kuup\" title=\"{$calendardata['d'][$viit]['n']}\">$kuup2ev</a>";
		  if($calendardata['d'][$viit]['l'] == 1) { $lipup2ev = true; }
		  if($calendardata['d'][$viit]['f'] == 1) { $vabap2ev = true; }
          if($calendardata['d'][$viit]['w'] == 1) { $lyhendet = true; }
		}
		
		// liikuvad kirikupuhad
		if( in_array($kuup, array_keys($kirikupyhad)) ) {
		  $viit = $kirikupyhad[$kuup];
		  array_push($html_muud, 
		    "<a href=\"tahtpaevad{$init_data['static_extension']}#{$kirikupyhad[$kuup]}\" title=\"{$calendardata['d'][$viit]['n']}\">+</a>");
		  if($calendardata['d'][$viit]['l'] == 1) { $lipup2ev = true; }
		  if($calendardata['d'][$viit]['f'] == 1) { $vabap2ev = true; }
		}
		
		//muud liikuvad
		$jooksev_n2dp2ev = ($n2dalap2ev + $kuup2ev) % 7;
		$n2dp2evi[$jooksev_n2dp2ev]++;
		$liikuv_id = 10000 
		  + 100*$kuu 
		  + 10*$n2dp2evi[$jooksev_n2dp2ev] 
		  + $jooksev_n2dp2ev; //1KKNn
        // kuu viimane n2dal
        if($p2evi_kuus-$kuup2ev<7) {
            $kuu_viimane_n2dal = 10000
                + 100*$kuu
                + 50
                + $jooksev_n2dp2ev; //1KKNn
            #array_push($html_muud,$viit);
        } else { $kuu_viimane_n2dal = -1; }
        foreach(array($liikuv_id,$kuu_viimane_n2dal) as $li => $lv) {
            if( in_array($lv, $calendardata['l']) ) {
            $viit = $li ? $kuu_viimane_n2dal : $liikuv_id;
            array_push($html_muud,
                "<a href=\"tahtpaevad{$init_data['static_extension']}#{$viit}\" title=\"{$calendardata['d'][$viit]['n']}\">"
                .strtolower(substr($calendardata['d'][$viit]['n'], 0 ,1))
                ."</a>");
            if($calendardata['d'][$viit]['l'] == 1) { $lipup2ev = true; }
            if($calendardata['d'][$viit]['f'] == 1) { $vabap2ev = true; }
            }
        }      
		
		
		if($vabap2ev) { array_push($html_muud, 
		  "<a href=\"#\" title=\"{$i18net['puhkep2ev']}"
            .($lyhendet ? ' '.$i18net['lyhendet_tqqp2ev'] : '')
            ."\" class=\"k_lf\">*</a>"); }

		if($lipup2ev) { array_push($html_muud, 
		  (count($html_muud)?'':'&nbsp;')
		    ."<img style=\"lipp\" src=\"img/lp.gif\" title=\"{$i18net['lipup2ev']}\" alt=\"{$i18net['lipup2ev']}\"/>"); }
            
		$html_muud = array_unique($html_muud);
        
		echo "<td class=\""
		  . (($t2na==$kuup)?'k_t2na':'k_tp')
		  . "\">"
		  . implode("\n", $html_muud) . "<br/>\n" . $html_kuup2ev
		  . "</td>";
	}
	
	for(;($i%7)!=0; $i++)
	{
		echo "<td class=\"k_t0\">&nbsp;</td>\n";
	}
	
 ?>
 
 </tr>
</tbody></table>

<br/>
<?php 

echo $calendardata['html_dbcopy']; 

#echo "<pre>";print_r($kirikupyhad);echo "</pre>";

?>

</center>

</body>
</html>
