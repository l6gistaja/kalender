<?php 

function get_calendar_url($year, $month, $bc = 0) {
  $months = ($bc == 1)
    ? -12*($year-1) + $month
    : 12*$year + $month;
  return '?aasta='.(
    ($months > 12)
      ? floor(($months-1) /12) .'&kuu=' .( (($months-1)%12) +1) //AD
      : -floor(($months-13) /12).'&kuu=' .( (($months-12)%12) +12).'&ekr=1' //BC
  );
}

function gregorius_liigaasta($aasta)
 {
	$y=0;
	if($aasta%400==0) { $y=1; } else
	 { if($aasta%100==0) { $y=0; } else
		 { if($aasta%4==0) { $y=1; }}}
	return $y;
 }


function gregorius_p2evi_kuus($aasta, $kuu)
 {
	switch ($kuu)
	{
    case 1: return 31; break;
    case 2: return 28+gregorius_liigaasta($aasta); break;
    case 3: return 31; break;
    case 4: return 30; break;
    case 5: return 31; break;
    case 6: return 30; break;
    case 7: return 31; break;
    case 8: return 31; break;
    case 9: return 30; break;
    case 10: return 31; break;
    case 11: return 30; break;
    case 12: return 31; break;
	default: return 0;
	}
 }

function gregorius_n2dalap2ev($aasta, $kuu, $kuup2ev)
 {
	$p2evi_ringis=0;
	$ring=$aasta%400; # gregooriuse kalendris kordub samal kuup2eval sama n2dalap2ev iga 400 aasta j2rel
	if($ring<0) { $ring+=400; }
	$t2issajandeid=(floor($ring/100))%4;
	$p2evi_ringis+=($t2issajandeid)?((36524*$t2issajandeid)+1):0; # palju on p2evi $t2issajandeid sajandeis?
		# +1 seep2rast, et ringi 1. sajandi 00 on liigaasta
	for($i=$t2issajandeid*100;$i<$ring; $i++) # lisame t2issajandite j22kaastate p2evad
	 {
		$p2evi_ringis+=365+gregorius_liigaasta($i);
	 }
    for($i=1;$i<$kuu; $i++) # lisame antud kuu esimesele kuupäevale eelnevad aasta p2evad
	 {
		$p2evi_ringis+=gregorius_p2evi_kuus($aasta, $i);
	 }
	return ($p2evi_ringis+$kuup2ev-1+5)%7;
	# -1 seep2rast, et antud kuup2ev on PRAEGU, kuid pole veel mqqdunud.
	# +5 seep2rast, et 400 aastaste ringide 1. (00) aasta 1. jaanuar on laup2ev.
	# v2ljundiks seega 0=E,1=T,,6=P
 }

function gregorius_ylesT6usmisPyha($aasta)
 {
  # t6lge JavaScriptist
  $x02=0;
  $x1=floor(($aasta-1500)/400)*3;
  $x2=floor(($aasta/100)+1)%4;
  if($x2>1) { $x02=44+$x2+$x1; } else { $x02=45+$x1; }
  $x=floor($x02/2);
  $y=($x02-1)%7;  //NB! Nii arvutatud x & y kehtivad vaid aastail 1500..2299
  $a=$aasta%19;   //F. Gaussi 1. ylest6usmispyha leidmise valemid
  $b=$aasta%4;
  $c=$aasta%7;
  $d=(19*$a+$x)%30;
  $e=(2*$b+4*$c+6*$d+$y)%7;
  $ylest6us1=$d+$e+22;  //juhul kui 1. YTP on m@rtsis...
  if($ylest6us1>31) { $ylest6us1=$d+$e+391; } else { $ylest6us1+=300; }
  //kui aga m@rtsi 1. YTP ei mahu (>31) siis on ta aprillis.
  //(minu kymnendkujul kuup@evaformaat on kkpp (N: 25. dets. = 1225))
  return $ylest6us1; 
 }

function gregorius_aastaalgusest($kuup,$aasta)
 {
  # t6lge JavaScriptist
  //mitu päeva on möödunud aasta algusest?
  $kuu=floor($kuup/100)-1;
  $kuupaev=$kuup%100;
  $paevi=$i=0;
  for($i=0;$i<$kuu;$i++) $paevi+=gregorius_p2evi_kuus($aasta,$i+1);
  return ($paevi+$kuupaev);
 }

function gregorius_leiakirik($aasta, $ppy, $alus)
 {
   # t6lge JavaScriptist
   //sisesta päeva järjenr. aastas (alus)
   // ja mingi nihe selle suhtes päevades (ppy) ning aasta
   // väljundiks on selle päeva kuup=kuu*100+päev
  $kirikupyha = $alus+$ppy;
  $paev=$paevi=$i=0;
  for($i=0;$kirikupyha>$paevi+gregorius_p2evi_kuus($aasta,$i+1);$i++)
  { $paevi+=gregorius_p2evi_kuus($aasta,$i+1); }
  $paev=$kirikupyha-$paevi;
  return (($i+1)*100+$paev);
 }

function gregorius_arvutakirik($aasta)
 {
   # t6lge JavaScriptist
   //arvutab ja laeb liikuvate kirikupyhade massiivi parent.kirik
  $kirik=array();
  $ppy = & $GLOBALS['calendardata']['y'];

    if(($aasta>1499)&&($aasta<2300)) #JS on1523
      {
		//järgnevad 13 kirikupyha arvestatakse 1. ylest6usmispyha suhtes nihetega
		$lihav6te=gregorius_aastaalgusest(gregorius_ylesT6usmisPyha($aasta),$aasta);
		$i=0;
		$l = count($ppy);
		for($i=0; $i<$l; $i++)
			{
				$kirik[''.gregorius_leiakirik($aasta, $ppy[$i] -2000, $lihav6te)] = $ppy[$i];
			}
      }
	//Arvutame 1. advendi
	$nadal=gregorius_n2dalap2ev($aasta,12,25);
	$n2dalap2ev=gregorius_n2dalap2ev($op_aasta, $kuu, 1);

	$abi=359+gregorius_liigaasta($aasta)-($nadal+1)-21;
	$kirik[''.gregorius_leiakirik($aasta, 0,$abi)]= 3040; //vana: 13;
	$abi=359+gregorius_liigaasta($aasta)-($nadal+1)-28;
	$kirik[''.gregorius_leiakirik($aasta, 0,$abi)]= 3050; //vana: 14;
	
	return $kirik;
 }