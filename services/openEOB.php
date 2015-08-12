<html>
<?php 
#	include_once"c:web/functions/working.php"; #working functions are here
#	include_once"c:web/classes/dental.php";
?>
<body>
<h2>Open EDI 835</h2>
program running!!!<br/>
<?php 
	$filepath="c:web/Dashboard/SYSTEM/EOB/a.x12";
	$eob1 = read835($filepath);
	if($eob1){
		$fh = fopen("c:web/Dashboard/SYSTEM/EOB/b.csv","w");
		foreach($eob1->toString() as $l){fwrite($fh,$l.chr(13).chr(10));}
		fclose($fh);
	}else{echo "file not found<br/>";}
?>
program complete!!<br/>
</body>
</html>
<?php
function getDentrix(){
#	PHPinfo();
	$connInfo=array("Database"=>"dentrix","UID"=>"jl149","PWD"=>"l3tm31n","ReturnDatesAsStrings"=>true);
	$dentrix=sqlsrv_connect("192.168.14.55,1435",$connInfo);
	return $dentrix;
}
function getElement($line,$i){
	$result=explode("*",$line);
	if(count($result)>$i){return $result[$i];}else{return false;}
}
function read835($filePath){
	$eob = new eob();
	$file = file($filePath);
	if($file==false){return false;}else{
		$line = explode("~",$file[0]);
		$i=0;
		if(getElement($line[$i],0)=="ISA"){$i++;}else{exit("bad Segment ".$i);}
		if(getElement($line[$i],0)=="GS"){$i++;}else{exit("bad Segment ".$i);}
		if(getElement($line[$i],0)=="ST"){$i++;}else{exit("bad Segment ".$i);}
		if(getElement($line[$i],0)=="BPR"){
			$eob->setCheckDate(getElement($line[$i],16));
			$eob->setCheckAmount(getElement($line[$i],2));
			$i++;
		}else{exit("bad Segment ".$i);}
		if(getElement($line[$i],0)=="TRN"){
			$eob->setCheckNumber(getElement($line[$i],2));
			$i++;
		}else{exit("bad Segment ".$i);}
		if(getElement($line[$i],0)=="REF"){$i++;}else{exit("bad Segment ".$i);}
		if(getElement($line[$i],0)=="REF"){$i++;}
		if(getElement($line[$i],0)=="DTM"){$i++;}else{exit("bad Segment ".$i);}
		if(getElement($line[$i],0)=="N1"){$i++;}else{exit("bad Segment ".$i);}
		if(getElement($line[$i],0)=="N3"){$i++;}else{exit("bad Segment ".$i);}
		if(getElement($line[$i],0)=="N4"){$i++;}else{exit("bad Segment ".$i);}
		if(getElement($line[$i],0)=="PER"){$i++;}else{exit("bad Segment ".$i);}
		if(getElement($line[$i],0)=="N1"){$i++;}else{exit("bad Segment ".$i);}
		if(getElement($line[$i],0)=="N4"){$i++;}else{exit("bad Segment ".$i);}
		if(getElement($line[$i],0)=="REF"){$i++;}
		if(getElement($line[$i],0)=="LX"){$i++;}else{exit("bad Segment ".$i);}
		if(getElement($line[$i],0)=="CLP"){
			while(getElement($line[$i],0)=="CLP"){
				$eobClaim = new eobClaim();
				$eobClaim->setEchoField(getElement($line[$i],1));
	$jclincval="UNDEF";
	$dentrix=getDentrix();
	if(!$dentrix){
		exit("Connection Failed!!");
		$jclincval="CON ERROR";
	}else{
		$jclincvala=explode("_",getElement($line[$i],1));
		$PARM1=$jclincvala[0];
		$sqlstring="select DDB_RSC.RSCID from DDB_RSC inner join DDB_CLAIM on DDB_RSC.URSCID=DDB_CLAIM.ClinicAppliedTo where DDB_CLAIM.CLAIMID=$PARM1";
		$sr=sqlsrv_query($dentrix,$sqlstring);
		if(!$sr){}else{
			while($row=sqlsrv_fetch_array($sr,SQLSRV_FETCH_NUMERIC)){
				$jclincval=$row[0];
			}
		}
	}
	sqlsrv_close($dentrix);
				$eobClaim->setClinicId($jclincval);
				$eobClaim->setTCN(getElement($line[$i],7));
				$eobClaim->setClaimCharge(getElement($line[$i],3));
				$eobClaim->setClaimPaid(getElement($line[$i],4));
				$eobClaim->setStatus(getElement($line[$i],2));
				$i++;
				while(getElement($line[$i],0)=="CAS"){
					if(getElement($line[$i],1)=="CO"){$eobClaim->setAdjustmentCode(getElement($line[$i],2));}
					$i++;
				}
				if(getElement($line[$i],0)=="NM1"){
						$eobClaim->setLastName(getElement($line[$i],3));
						$eobClaim->setFirstName(getElement($line[$i],4));
						$eobClaim->setMedicaid(getElement($line[$i],9));
						$i++;}else{exit("bad Segment ".$i);}
				while(getElement($line[$i],0)=="NM1"){$i++;}
				while(getElement($line[$i],0)=="MIA"){$i++;}
						if(getElement($line[$i],0)=="MOA"){
								$eobClaim->setRarCode(getElement($line[$i],3));
								$i++;}
				while(getElement($line[$i],0)=="REF"){
					if(getElement($line[$i],1)=="9A"){$eobClaim->setRateCode(getElement($line[$i],2));}
					$i++;
				}
				if(getElement($line[$i],0)=="DTM"){
					$eobClaim->setServiceDate(getElement($line[$i],2));
					$i++;}
				else{exit("bad Segment ".$i);}
				while(getElement($line[$i],0)=="DTM"){$i++;}
				while(getElement($line[$i],0)=="PER"){$i++;}
				while(getElement($line[$i],0)=="AMT"){$i++;}
				if(getElement($line[$i],0)=="SVC"){
					while(getElement($line[$i],0)=="SVC"){
						$eobClaimLine = new eobClaimLine();
						$adaCode = explode(":",getElement($line[$i],1));
						$eobClaimLine->setAdaCode($adaCode[1]);
						$eobClaimLine->setLineCharge(getElement($line[$i],2));
						$eobClaimLine->setLinePaid(getElement($line[$i],3));
						$i++;
						if(getElement($line[$i],0)=="DTM"){
							if(getElement($line[$i],1)=="472"){$eobClaimLine->setServiceDate(getElement($line[$i],2));}
							$i++;
						}else{exit("bad Segment ".$i);}
						while(getElement($line[$i],0)=="CAS"){
							if(getElement($line[$i],1)=="PR"){$eobClaimLine->setServiceAdj(getElement($line[$i],2));}
							if(getElement($line[$i],1)=="OA"){$eobClaimLine->setServiceAdj(getElement($line[$i],2));}
							if(getElement($line[$i],1)=="CO"){
								if(getElement($line[$i],2)=="94"){$eobClaimLine->setCapAddOn(getElement($line[$i],3));}
								if(getElement($line[$i],2)=="45"){$eobClaimLine->setOverPay(getElement($line[$i],3));}
								if(getElement($line[$i],5)=="45"){$eobClaimLine->setOverPay(getElement($line[$i],6));}
								if(getElement($line[$i],2)<>"45"&&getElement($line[$i],2)<>"94"){$eobClaimLine->setServiceAdj(getElement($line[$i],2));}
							}			
							$i++;
						}
						while(getElement($line[$i],0)=="REF"){
							if(getElement($line[$i],1)=="1S"){$eobClaimLine->setApgNumber(getElement($line[$i],2));}
							$i++;
						}
						while(getElement($line[$i],0)=="AMT"){
							if(getElement($line[$i],1)=="B6"){$eobClaimLine->setMaxAllowed(getElement($line[$i],2));}
							if(getElement($line[$i],1)=="ZK"){$eobClaimLine->setApgPaid(getElement($line[$i],2));}
							if(getElement($line[$i],1)=="ZL"){$eobClaimLine->setBlendPaid(getElement($line[$i],2));}
							$i++;
						}
						while(getElement($line[$i],0)=="QTY"){
							if(getElement($line[$i],1)=="ZK"){$eobClaimLine->setApgWeight(getElement($line[$i],2));}
							if(getElement($line[$i],1)=="ZL"){$eobClaimLine->setApgPercent(getElement($line[$i],2));}
							$i++;
						}
						while(getElement($line[$i],0)=="LQ"){
							if(getElement($line[$i],1)=="HE"){
								$eobClaimLine->setServiceRarc(getElement($line[$i],2));
							}
								$i++;}
						$eobClaim->addClaimLine($eobClaimLine);
					} // while SVC
				} // if SVC
				$eob->addEobClaim($eobClaim);
			} 
		}else{exit("bad Segment ".$i);} //if CLP
		if(getElement($line[$i],0)=="PLB"){$i++;}
		if(getElement($line[$i],0)=="SE"){$i++;}else{exit("bad Segment ".$i);}
		if(getElement($line[$i],0)=="GE"){$i++;}else{exit("bad Segment ".$i);}
		if(getElement($line[$i],0)=="IEA"){$i++;}else{exit("bad Segment ".$i);}
		return $eob;
	}
} //end function
class eob {
	private $checkNumber;
	private $checkDate;
	private $checkAmount;
	private $eobClaims;

	public function eob(){
			$this->eobClaims = array();}
	public function setCheckNumber($checkNumber){$this->checkNumber=$checkNumber;}
	public function setCheckDate($checkDate){$this->checkDate=$checkDate;}
	public function setCheckAmount($checkAmount){$this->checkAmount=$checkAmount;}
	public function addEobClaim($eobClaim){
			$this->eobClaims[]=$eobClaim;
	}
	public function toString(){
		$result = array();
		$result[] = $this->checkNumber.",".$this->checkDate.",".$this->checkAmount;
		$result[] = $this->getHeadingTitles();
		foreach($this->eobClaims as $claim){
			foreach($claim->toString() as $l){
				$result[]=$l;
			}
		}
		return $result;
	}
	private function getHeadingTitles(){
		return "LineNumber,Count,TCN,Echo,LastName,FirstName,Medicaid,Status,CARC,RARC,RateCode,ClaimBilled,ClaimPaid,Clinic,ServiceDate,AdaCode,LineBilled,LinePaid,MaxAllowed,ApgPaid,BlendPaid,ApgNumber,ApgWeight,ApgPercent,CARC,RARC,CappAddOn,OverPaid";
	}
}
class eobClaim {
	private $TCN;
	private $echoField;
	private $lastName;
	private $firstName;
	private $medicaid;
	private $status;
	private $adjustmentCode;
	private $rarCode;
	private $rateCode;
	private $claimCharge;
	private $claimPaid;
	private $claimLines;
	private $serviceDate;
	private $clinicId;

	public function eobClaim(){$claimLines = array();}
	public function setTCN($TCN){$this->TCN=$TCN;}
	public function setEchoField($echoField){$this->echoField=$echoField;}
	public function setLastName($lastName){
		$lastName=str_replace(",","_",$lastName);
		$this->lastName=$lastName;}
	public function setFirstName($firstName){
		$fistName=str_replace(",","_",$firstName);
		$this->firstName=$firstName;}
	public function setMedicaid($medicaid){$this->medicaid=$medicaid;}
	public function setStatus($status){$this->status=$status;}
	public function setAdjustmentCode($adjustmentCode){$this->adjustmentCode=$adjustmentCode;}
	public function setRarCode($rarCode){$this->rarCode=$rarCode;}
	public function setRateCode($rateCode){$this->rateCode=$rateCode;}
	public function setClaimCharge($claimCharge){$this->claimCharge=$claimCharge;}
	public function setClaimPaid($claimPaid){$this->claimPaid=$claimPaid;}
	public function addClaimLine($claimLine){$this->claimLines[]=$claimLine;}
	public function setServiceDate($serviceDate){$this->serviceDate=$serviceDate;}
	public function setClinicId($clinicId){$this->clinicId=$clinicId;}
	public function toString(){
		$result=array();
		$lineCount=0;
		if(count($this->claimLines)>0){
			foreach($this->claimLines as $line){
				$lineCount++;
				if($lineCount==1){$counter=1;}else{$counter=0;}
				$result[]= $lineCount.",".$counter.",T_".$this->TCN.",".$this->echoField.",".$this->lastName.",".$this->firstName.",".$this->medicaid.",".$this->status.",".$this->adjustmentCode.",".$this->rarCode.",".$this->rateCode.",".$this->claimCharge.",".$this->claimPaid.",".$this->clinicId.",".$line->toString();
			}
		}else{
			$result[]= "1,1,T_".$this->TCN.",".$this->echoField.",".$this->lastName.",".$this->firstName.",".$this->medicaid.",".$this->status.",".$this->adjustmentCode.",".$this->rarCode.",".$this->rateCode.",".$this->claimCharge.",".$this->claimPaid.",".$this->clinicId.",".$this->serviceDate;
		}
		return $result;
	}
}
class eobClaimLine {
	private $claimLineNumber;
	private $serviceDate;
	private $adaCode;
	private $lineCharge;
	private $linePaid;
	private $maxAllowed;
	private $apgPaid;
	private $blendPaid;
	private $apgNumber;
	private $apgWeight;
	private $apgPercent;
	private $serviceAdj;
	private $serviceRarc;
	private $capAddOn;
	private $overPay;

	public function setClaimLineNumber($claimLineNumber){$this->claimLineNumber=$claimLineNumber;}
	public function setServiceDate($serviceDate){$this->serviceDate=$serviceDate;}
	public function setAdaCode($adaCode){$this->adaCode=$adaCode;}
	public function setLineCharge($lineCharge){$this->lineCharge=$lineCharge;}
	public function setLinePaid($linePaid){$this->linePaid=$linePaid;}
	public function setMaxAllowed($maxAllowed){$this->maxAllowed=$maxAllowed;}
	public function setApgPaid($apgPaid){$this->apgPaid=$apgPaid;}
	public function setBlendPaid($blendPaid){$this->blendPaid=$blendPaid;}
	public function setApgNumber($apgNumber){$this->apgNumber=$apgNumber;}
	public function setApgWeight($apgWeight){$this->apgWeight=$apgWeight;}
	public function setApgPercent($apgPercent){$this->apgPercent=$apgPercent;}
	public function setServiceAdj($serviceAdj){$this->serviceAdj=$serviceAdj;}
	public function setServiceRarc($serviceAdj){$this->serviceRarc=$serviceAdj;}
	public function setCapAddOn($capAddOn){$this->capAddOn=$capAddOn;}
	public function setOverPay($overPay){$this->overPay=$overPay;}
	
	public function toString(){
		return $this->serviceDate.",".$this->adaCode.",".$this->lineCharge.",".$this->linePaid.",".$this->maxAllowed.",".$this->apgPaid.",".$this->blendPaid.",".$this->apgNumber.",".$this->apgWeight.",".$this->apgPercent.",".$this->serviceAdj.",".$this->serviceRarc.",".$this->capAddOn.",".$this->overPay;
	}
}
?>