<?php
function test(){ return "Functions loaded"; }
function setupETL2()
{
	if(file_exists('SYSTEM/EDI_Response/elly.json'))
	{
		return 'file on line...Please clear the line';
	}
	elseif(file_exists('SYSTEM/EDI_Response/a.x12'))
	{
		$f = file_get_contents('SYSTEM/EDI_Response/a.x12');

		$_SESSION['segs'] = preg_split('/~/',$f);
		$seg  = '';

		$m = array();
		$_SESSION['elly'] = array();

		$m[] = 'Start reading file';
		
		try
		{
			$seg = array_shift($_SESSION['segs']);
			if(preg_match('/^ISA\*/',$seg))    {}else{throw new exception('1');}
			
			$seg = array_shift($_SESSION['segs']);
			if(preg_match('/^GS\*/',$seg))     {}else{throw new exception('2');}
			
			$seg = array_shift($_SESSION['segs']);
			if(preg_match('/^ST\*271\*/',$seg)){}else{throw new exception('3');}
			
			$seg = array_shift($_SESSION['segs']);
			if(preg_match('/^BHT\*0022\*/',$seg))
			{
				$t = preg_split('/\*/',$seg);
				$_SESSION['elly']['batchId'] = $t[3];
			}
			else{throw new exception('4');}
			
			$seg = array_shift($_SESSION['segs']);
			if(preg_match('/^HL\*/',$seg)) {}else{throw new exception('5');}

			$seg = array_shift($_SESSION['segs']);
			if(preg_match('/^NM1\*/',$seg)){}else{throw new exception('6');}

			$seg = array_shift($_SESSION['segs']);
			if(preg_match('/^PER\*/',$seg)){}else{throw new exception('7');}

			$seg = array_shift($_SESSION['segs']);
			if(preg_match('/^HL\*/',$seg)) {}else{throw new exception('8');}

			$seg = array_shift($_SESSION['segs']);
			if(preg_match('/^NM1\*/',$seg)) {}else{throw new exception('9');}

			# START CLAIM Loop
			$_SESSION['elly']['claims'] = array();
		}
		catch(exception $e)
		{
			$error = $e->getMessage();
			$m[] = "ERROR!!! $error";
			$m[] = $seg;
			return implode('<br/>',$m);
		}
	}
	else
	{
		return "No response file... load file...";
	}

}
function runETL2()
{
		$m = array();
		try
		{
			if(preg_match('/^HL\*[0-9]+\*2\*22/',$_SESSION['segs'][0]))
			{
				$seg = array_shift($_SESSION['segs']);

				$claim = array();
	
				$seg = array_shift($_SESSION['segs']);
				if(preg_match('/^TRN\*/',$seg))
				{
					$t = preg_split('/\*/',$seg);
					$claim['id'] = $t[2];
				}
				else{throw new exception('11');}

				$seg = array_shift($_SESSION['segs']);
				if(preg_match('/^NM1\*/',$seg))
				{
					$t = preg_split('/\*/',$seg);
					$claim['medicaid'] = $t[9];
				}else{throw new exception('12');}
	
				if(preg_match('/^AAA\*/',$_SESSION['segs'][0])) { $seg = array_shift($_SESSION['segs']); }
				if(preg_match('/^N3\*/',$_SESSION['segs'][0])) { $seg = array_shift($_SESSION['segs']); }
				if(preg_match('/^N4\*/',$_SESSION['segs'][0])) { $seg = array_shift($_SESSION['segs']); }
				if(preg_match('/^DMG\*/',$_SESSION['segs'][0])) { $seg = array_shift($_SESSION['segs']); }
				if(preg_match('/^DTP\*/',$_SESSION['segs'][0])) { $seg = array_shift($_SESSION['segs']); }
				if(preg_match('/^DTP\*/',$_SESSION['segs'][0])) { $seg = array_shift($_SESSION['segs']); }
				if(preg_match('/^DTP\*/',$_SESSION['segs'][0])) { $seg = array_shift($_SESSION['segs']); }
	
				$claim['insurance'] = array();
	
				if(preg_match('/^EB\*1\*IND\*30\*\*MA Eligible/',$_SESSION['segs'][0]))
				{
					$seg = array_shift($_SESSION['segs']);
					$claim['insurance'][] = 'Med: Medicaid';
				}

				if(preg_match('/^EB\*1\*IND\*30\*\*Eligible Only/',$_SESSION['segs'][0]))
				{
					$seg = array_shift($_SESSION['segs']);
					$claim['insurance'][] = 'Med: Medicaid';
				}

				if(preg_match('/^EB\*1\*IND\*30\*\*Presumptive Eli/',$_SESSION['segs'][0]))
				{
					$seg = array_shift($_SESSION['segs']);
					$claim['insurance'][] = 'Med: Medicaid';
				}

				if(preg_match('/^EB\*1\*IND\*30\*\*Community Coverage No LTC/',$_SESSION['segs'][0]))
				{
					$seg = array_shift($_SESSION['segs']);
					$claim['insurance'][] = 'Med: Medicaid No LTC';
				}
	
				if(preg_match('/^EB\*1\*IND\*30\*\*Community Coverage w\/CBLTC/',$_SESSION['segs'][0]))
				{
					$seg = array_shift($_SESSION['segs']);
					$claim['insurance'][] = 'Med: Medicaid CBLTC';
				}

				if(preg_match('/^EB\*1\*IND\*30\*\*Outpatient Coverage w\/ CBLTC/',$_SESSION['segs'][0]))
				{
					$seg = array_shift($_SESSION['segs']);
					$claim['insurance'][] = 'Med: Medicaid Outpatient CBLTC';
				}

				if(preg_match('/^EB\*1\*IND\*30\*\*Outpatient Coverage No LTC/',$_SESSION['segs'][0]))
				{
					$seg = array_shift($_SESSION['segs']);
					$claim['insurance'][] = 'Med: Medicaid Outpatient No LTC';
				}

				if(preg_match('/^EB\*1\*IND\*30\*\*Eligible Only Outpatient Care/',$_SESSION['segs'][0]))
				{
					$seg = array_shift($_SESSION['segs']);
					$claim['insurance'][] = 'Med: Medicaid Outpatient';
				}

				if(preg_match('/^EB\*1\*IND\*30\*\*Emergency Services Only/',$_SESSION['segs'][0]))
				{
					$seg = array_shift($_SESSION['segs']);
					$claim['insurance'][] = 'Med: Medicaid Emergency Only';
				}

				if(preg_match('/^EB\*1\*IND\*30\*\*Medicare Coinsurance Deductible Only/',$_SESSION['segs'][0]))
				{
					$seg = array_shift($_SESSION['segs']);
					$claim['insurance'][] = 'Med: Medicaid Medicare Deductible';
				}

				if(preg_match('/^EB\*1\*IND\*30\*/',$_SESSION['segs'][0]))
				{
					$seg = array_shift($_SESSION['segs']);
					$claim['insurance'][] = 'Med: Medicaid';
				}

				if(preg_match('/^EB\*6\*IND\*30\*\*No Coverage/',$_SESSION['segs'][0]))
				{
					$seg = array_shift($_SESSION['segs']);
					$claim['insurance'][] = 'NO: No';
				}
	
				if(preg_match('/^EB\*6\*IND\*30/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}

				while(preg_match('/^MSG\*/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}

				# Start MMC plan loop
				while(preg_match('/^EB\*U\*IND\*30\*\*ELIGIBLE PCP/',$_SESSION['segs'][0]))
				{
					$seg = array_shift($_SESSION['segs']);
					while(preg_match('/^MSG\*/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
					if(preg_match('/^LS\*2120/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
					if(preg_match('/^NM1\*Y2/',$_SESSION['segs'][0]))
					{
						$seg = array_shift($_SESSION['segs']);
						$t = preg_split('/\*/',$seg);
						$claim['insurance'][] = "MMC: ".$t[3];
					}
					if(preg_match('/^N3\*/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
					if(preg_match('/^N4\*/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
					if(preg_match('/^PER\*IC\*/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
					if(preg_match('/^LE\*2120/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
				}
				# End MMC plan loop
	
				if(preg_match('/^EB\*B\*IND\*30/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
				if(preg_match('/^EB\*B\*IND\*4/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
				if(preg_match('/^EB\*B\*IND\*5/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
				if(preg_match('/^EB\*B\*IND\*48/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
				if(preg_match('/^EB\*B\*IND\*50/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
				if(preg_match('/^EB\*B\*IND\*86/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
				if(preg_match('/^EB\*B\*IND\*88/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
				if(preg_match('/^EB\*B\*IND\*91/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
				if(preg_match('/^EB\*B\*IND\*92/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}

				$safty = 0;
				while(preg_match('/^EB\*/',$_SESSION['segs'][0]))
				{
					if($safty++ > 1000){break;}
				if(preg_match('/^EB\*1\*IND\*1/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
				if(preg_match('/^EB\*1\*IND\*4/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
				if(preg_match('/^EB\*1\*IND\*5/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
				if(preg_match('/^EB\*1\*IND\*33/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
				if(preg_match('/^EB\*1\*IND\*35/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
				if(preg_match('/^EB\*1\*IND\*47/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
				if(preg_match('/^EB\*1\*IND\*48/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
				if(preg_match('/^EB\*1\*IND\*50/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
				if(preg_match('/^EB\*1\*IND\*86/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
				if(preg_match('/^EB\*1\*IND\*88/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
				if(preg_match('/^EB\*1\*IND\*98/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
				if(preg_match('/^EB\*1\*IND\*AG/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
				if(preg_match('/^EB\*1\*IND\*AL/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
				if(preg_match('/^EB\*1\*IND\*MH/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
				if(preg_match('/^EB\*1\*IND\*UC/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
				if(preg_match('/^EB\*1\*IND\*82/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
				if(preg_match('/^EB\*I\*IND\*48/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
				if(preg_match('/^EB\*I\*IND\*54/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
				if(preg_match('/^EB\*N\*IND\*35/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
				if(preg_match('/^EB\*N\*IND\*48/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
				if(preg_match('/^EB\*N\*IND\*50/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
				if(preg_match('/^EB\*N\*IND\*88/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
				if(preg_match('/^EB\*N\*IND\*98/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
				if(preg_match('/^EB\*F\*IND\*88/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
				if(preg_match('/^EB\*F\*IND\*98/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
				if(preg_match('/^EB\*F\*IND\*35/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
				if(preg_match('/^EB\*F\*IND\*5/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
				if(preg_match('/^EB\*Y\*IND\*AG/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
				if(preg_match('/^EB\*Y\*IND\*AG/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
				if(preg_match('/^DTP\*291\*D8\*/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}

				if(preg_match('/^LS\*2120/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
				if(preg_match('/^NM1\*P3/',$_SESSION['segs'][0]))
				{
					$seg = array_shift($_SESSION['segs']);
					$t = preg_split('/\*/',$seg);
					$claim['insurance'][] = "OTHER1: ".$t[3];
				}
				if(preg_match('/^LE\*2120/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}

				# Start other plan loop
				while(preg_match('/^EB\*R\*IND\*30/',$_SESSION['segs'][0]))
				{
					$seg = array_shift($_SESSION['segs']);
					if(preg_match('/^REF\*18\*/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
					if(preg_match('/^REF\*6P\*/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
					if(preg_match('/^LS\*2120/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
					if(preg_match('/^NM1\*P4/',$_SESSION['segs'][0]))
					{
						$seg = array_shift($_SESSION['segs']);
						$t = preg_split('/\*/',$seg);
						$claim['insurance'][] = "OTHER: ".$t[3];
					}
					if(preg_match('/^N3\*/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
					if(preg_match('/^N4\*/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
					if(preg_match('/^PER\*IC\*/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
					if(preg_match('/^LE\*2120/',$_SESSION['segs'][0])){$seg = array_shift($_SESSION['segs']);}
				}
				# End other plan loop
				}

				$_SESSION['elly']['claims'][$claim['medicaid']] = $claim;

			}
			else
			{
				$seg = array_shift($_SESSION['segs']);
				if(preg_match('/^SE\*/',$seg)){}else{throw new exception('100');}
				$seg = array_shift($_SESSION['segs']);
				if(preg_match('/^GE\*/',$seg)){}else{throw new exception('101');}
				$seg = array_shift($_SESSION['segs']);
				if(preg_match('/^IEA\*/',$seg)){}else{throw new exception('102');}

				$m[] = 'All good :)';

				file_put_contents('SYSTEM/EDI_Response/elly.json',json_encode($_SESSION['elly']));
				return json_encode($_SESSION['elly']);
			}
			# End Claim loop
		}
		catch(exception $e)
		{
			$error = $e->getMessage();
			$m[] = "ERROR!!! $error";
			$m[] = $seg;
			return implode('<br/>',$m);
		}
}
function mergeETL2()
{
	$outputRows = array();
	$f = file_get_contents('SYSTEM/Appointments/test1.txt');
	$o = file_get_contents('SYSTEM/EDI_Response/elly.json');
	$obj = json_decode($o,true);
	$claims = $obj['claims'];
	$s = preg_split('/\r\n/',$f);
	$table = '<table>';
	$head = array_shift($s);
	foreach($s as $l)
	{
		$outputItems = array();
		$ll = preg_split('/\t/',$l);
		if( sizeof($ll) == 6 )
		{
			$table .= '<tr>';
			foreach($ll as $i)
			{
				$table .= '<td>'.$i.'</td>';
				$outputItems[] = $i;
			}
			$ins1 = '';
			$ins2 = '';
			$medNum = '';

			if( preg_match('/^[a-zA-Z]{2,3}[0-9]{5}[a-zA-Z]{1}$/',$ll[3]) )
			{
				$ins1 = 'na';
				$ins2 = 'na';
				if( strlen($ll[3]) == 9 )
				{
					$medNum = preg_replace('/^M/','',$ll[3]);
				}
				else
				{
					$medNum = $ll[3];
				}
			}
			if(isset($claims[$medNum]))
			{
				if( isset($claims[$medNum]['insurance'][0]) ){ $ins1 = $claims[$medNum]['insurance'][0]; }
				if( isset($claims[$medNum]['insurance'][1]) ){ $ins2 = $claims[$medNum]['insurance'][1]; }
			}

			$table .= '<td>'.$ins1.'</td>';
			$outputItems[] = $ins1;

			$table .= '<td>'.$ins2.'</td>';
			$outputItems[] = $ins2;

			$table .= '<td>'.'stat'.'</td>';
			$outputItems[] = 'stat';

			$table .= '</tr>';
			$outputRows[] = implode("\t",$outputItems);
		}
	}
	$table .= '</table>';
	file_put_contents('SYSTEM/Final_Report/a.txt',implode("\r\n",$outputRows));
	#return $table;
	return "File created";
}
function runETL1()
{
	if(file_exists('SYSTEM/EDI_Request/EDI270.x12'))
	{
		return 'file on line...Please clear the line';
	}
	else
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
}
?>