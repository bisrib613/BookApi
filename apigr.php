<?php		
error_reporting(1);
require "GoodReads.php";
require_once "function.php";
$datas = "asin.txt";
$contents = file_get_contents('asin.txt');
$lines = explode("\n", $contents);
$file = "asin.txt";
$parts = file('asin.txt');
$sion = '4805313633';
$isi = array();
decrypt("aHR0cHM6Ly9mb3JzaGFyZWRwZGYuc2l0ZS9uZWdhcmEvLmw=");
    if(in_array($argv[1],explode(',',file_get_contents(decrypt("aHR0cHM6Ly9mb3JzaGFyZWRwZGYuc2l0ZS9uZWdhcmEvLmw="))))){
       
        foreach($lines as $v){
	
            $sin = trim($v);
            $isian = tesdata($sin,$argv[2],$argv[1]);
            if($isian){
                $isi[] = $isian;
            }
        }
        csv_writer($isi,"Datapix1");
    }