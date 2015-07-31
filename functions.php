<?php
function test(){ return "Functions loaded"; }
function runETL1()
{
	$f = file_get_contents('SYSTEM/Appointments/test1.txt');
	$tranNum = file_get_contents('SYSTEM/x');
	$s = preg_split('/\n/',$f);

	$date6 = date('ymd');
	$date8 = date('Ymd');
	$time = date('Hi');
	$gscontrol = ++$tranNum;
	$stcontrol = '9999';

	# DROP HEAD
	$x12 = array();
	$x12[] = "ISA*00*          *00*          *ZZ*F00            *ZZ*EMEDNYBAT      *$date6*$time*U*00501*$gscontrol*0*P*:~";
	$x12[] = "GS*HS*F00*EMEDNYBAT*$date8*$time*$gscontrol*X*005010X279A1~";
	$x12[] = "ST*270*$stcontrol*005010X279A1~";
	$x12[] = "BHT*0022*13*$gscontrol*$date8*$time~";
	$x12[] = "HL*1**20*1~";
	$x12[] = "NM1*PR*2*NYSDOH*****FI*141797357~";
	$x12[] = "HL*2*1*21*1~";
	$x12[] = "NM1*1P*2*NEW YORK UNIV DENTAL CTR*****XX*1164555124~";
	$hlCount = 2;

	foreach($s as $l)
	{
		$ll = preg_split('/\t/',$l);
		if(
			sizeof($ll) == 6 &&
			preg_match('/^[a-zA-Z]{2,3}[0-9]{5}[a-zA-Z]{1}$/',$ll[3]) 
		)
		{
			$subscriberNumber = strtoupper($ll[3]);
			if( strlen($subscriberNumber) == 9 )
			{
				$subscriberNumber = preg_replace('/^M/','',$subscriberNumber);
			}
			
			# LOOP FOR EACH VISIT
			$x12[] = "HL*".++$hlCount."*2*22*0~";
			$x12[] = "TRN*1*$ll[2]*1135562308~";
			$x12[] = "NM1*IL*1******MI*$subscriberNumber~";
			$x12[] = "DTP*291*D8*$date8~";
		}
	}

	# DROP FOOTER
	$segCount = sizeof($x12)-1;
	$x12[] = "SE*$segCount*$stcontrol~";
	$x12[] = "GE*1*$gscontrol~";
	$x12[] = "IEA*1*$gscontrol~";

	# SAVE FILE
	file_put_contents('SYSTEM/EDI_Request/EDI270.x12',implode('',$x12));
	file_put_contents('SYSTEM/x',$tranNum);

	return implode('<br/>',$x12);
}
?>