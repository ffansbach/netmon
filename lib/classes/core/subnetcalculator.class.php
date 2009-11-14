<?php

//Source-Class (Under GPL-License): http://sourceforge.net/projects/subntcalc/

class SubnetCalculator {
function binnmtowm($binin){
	$binin=rtrim($binin, "0");
	if (!ereg("0",$binin) ){
		return str_pad(str_replace("1","0",$binin), 32, "1");
	} else return "1010101010101010101010101010101010101010";
}

function bintocdr ($binin){
	return strlen(rtrim($binin,"0"));
}

function bintodq ($binin) {
	if ($binin=="N/A") return $binin;
	$binin=explode(".", chunk_split($binin,8,"."));
	for ($i=0; $i<4 ; $i++) {
		$dq[$i]=bindec($binin[$i]);
	}
        return implode(".",$dq) ;
}

function bintoint ($binin){
        return bindec($binin);
}

function binwmtonm($binin){
	$binin=rtrim($binin, "1");
	if (!ereg("1",$binin)){
		return str_pad(str_replace("0","1",$binin), 32, "0");
	} else return "1010101010101010101010101010101010101010";
}

function cdrtobin ($cdrin){
	return str_pad(str_pad("", $cdrin, "1"), 32, "0");
}

function dotbin($binin,$cdr_nmask){
	// splits 32 bit bin into dotted bin octets
	if ($binin=="N/A") return $binin;
	$oct=rtrim(chunk_split($binin,8,"."),".");
	if ($cdr_nmask > 0){
		$offset=sprintf("%u",$cdr_nmask/8) + $cdr_nmask ;
		return substr($oct,0,$offset ) . "&nbsp;&nbsp;&nbsp;" . substr($oct,$offset) ;
	} else {
	return $oct;
	}
}

function dqtobin($dqin) {
        $dq = explode(".",$dqin);
        for ($i=0; $i<4 ; $i++) {
           $bin[$i]=str_pad(decbin($dq[$i]), 8, "0", STR_PAD_LEFT);
        }
        return implode("",$bin);
}

function inttobin ($intin) {
        return str_pad(decbin($intin), 32, "0", STR_PAD_LEFT);
}

function tr(){
	echo "\t<tr>";
	for($i=0; $i<func_num_args(); $i++) echo "<td>".func_get_arg($i)."</td>";
	echo "</tr>\n";
}

public function getSubnetClass($bin_net, $cdr_nmask) {
	//Get Dotbin
	$dotbin_net = SubnetCalculator::dotbin($bin_net,$cdr_nmask);
	//Determine Class
	if (ereg('^0',$bin_net)){
		$class="A";
	}elseif (ereg('^10',$bin_net)){
		$class="B";
	}elseif (ereg('^110',$bin_net)){
		$class="C";
	}elseif (ereg('^1110',$bin_net)){
		$class="D";
		$special="Multicast Address Space";
	}else{
		$class="E";
		$special="Experimental Address Space";
	}
	
	if (ereg('^(00001010)|(101011000001)|(1100000010101000)',$bin_net)){
		$special='RFC-1918 Private Internet Address';
	}
	return array('dotbin_net'=>$dotbin_net, 'special'=>$special, 'class'=>$class);
}

public function getBinHost($dq_host) {
	return 	SubnetCalculator::dqtobin($dq_host);
}

public function getBinBcast($dq_host, $cdr_nmask) {
	//Takes care of 31 and 32 bit masks.
	$bin_bcast = str_pad(substr(SubnetCalculator::getBinHost($dq_host),0,$cdr_nmask),32,1);

	if (SubnetCalculator::getHostsTotal($cdr_nmask)<=0 AND SubnetCalculator::getBinNet($dq_host, $cdr_nmask)===$bin_bcast) {
		return 0;
	} else {
		return $bin_bcast;
	}
}

public function getBinNet($dq_host, $cdr_nmask) {
	return str_pad(substr(SubnetCalculator::getBinHost($dq_host),0,$cdr_nmask),32,0);
}

public function getBinFirstIP($dq_host, $cdr_nmask) {
	//Takes care of 31 and 32 bit masks.
	if (SubnetCalculator::getHostsTotal($cdr_nmask)>0) {
		return str_pad(substr(SubnetCalculator::getBinNet($dq_host, $cdr_nmask),0,31),32,1);
	} else {
		return 0;
	}
}

public function getBinLastIP($dq_host, $cdr_nmask) {
	//Takes care of 31 and 32 bit masks.
	if (SubnetCalculator::getHostsTotal($cdr_nmask)>0) {
		return $bin_last=(str_pad(substr(SubnetCalculator::getBinBcast($dq_host, $cdr_nmask),0,31),32,0));
	} else {
		return 0;
	}
}


public function getDqLastIp($dq_host, $cdr_nmask) {
	$binin = SubnetCalculator::getBinLastIP($dq_host, $cdr_nmask);
	if ($binin==0) return $binin;
		$binin=explode(".", chunk_split($binin,8,"."));
		for ($i=0; $i<4 ; $i++) {
			$dq[$i]=bindec($binin[$i]);
	}
	return implode(".",$dq) ;
}

public function getDqFirstIp($dq_host, $cdr_nmask) {
	$binin = SubnetCalculator::getBinFirstIP($dq_host, $cdr_nmask);
	if ($binin==0) return $binin;
		$binin=explode(".", chunk_split($binin,8,"."));
		for ($i=0; $i<4 ; $i++) {
			$dq[$i]=bindec($binin[$i]);
	}
	return implode(".",$dq) ;
}






public function getHostsTotal($cdr_nmask) {
	//Takes care of 31 and 32 bit masks.
	$host_total = (bindec(str_pad("",(32-$cdr_nmask),1)) - 1);
	if($host_total>0) {
		return $host_total;
	} else {
		return 0;
	}
}

public function checkIfDqHostIsValid($dq_host) {
	//Check for valid $dq_host
	if(! ereg('^0.',$dq_host)){
		foreach( explode(".",$dq_host) as $octet ){
			if($octet > 255){ 
				return false;
			}
		}
	}
	return true;
}

public function checkIfInputIsValidAndCIDR($my_net_info) {
	$my_net_info=rtrim($my_net_info);
	if (!ereg("/",$my_net_info)){
		return false;
	} elseif (! ereg('^([0-9]{1,3}\.){3}[0-9]{1,3}(( ([0-9]{1,3}\.){3}[0-9]{1,3})|(/[0-9]{1,2}))$',$my_net_info)){
		return false; 
	} else {
		return $my_net_info;
	}
}

public function getDqHost($my_net_info){
	$my_net_info = explode("/", $my_net_info);
	return $my_net_info[0];
}

public function getCdrNmask($my_net_info) {
	if (ereg("/",$my_net_info)){
		$my_net_info = explode("/", $my_net_info);
		if (!($my_net_info[1] >= 0 && $my_net_info[1] <= 32)){
			return false;
		} else {
			return $my_net_info[1];
		}
	} else {
		return false;
	}
}

}

?>