<?php		
error_reporting(1);
require "GoodReads.php";
require_once "function.php";
$contents = file_get_contents('asin.txt');
$lines = explode("\n", $contents);
$isi = array();

define("ISI_ARTICLE", 200);
define("BACK_DATE",			"-3 month");
define("SHEDULE_DATE",		"+0 month");
    //if(in_array($argv[1],explode(',',file_get_contents(decrypt("aHR0cHM6Ly9mb3JzaGFyZWRwZGYuc2l0ZS9uZWdhcmEvLmw="))))){
       
        
        switch ($argv[2]) {
            case 'pixnet':
                foreach($lines as $v){
            
                    $sin = trim($v);
                    $isian = tesdata($sin,"pixnet",$argv[1]);
                    if($isian){
                        $isi[] = $isian;
                        
                    }
                    
                }
                csv_writer($isi,"Datapix");
                echo "\nTotal Line Csv ".count($isi);
            break;
            case 'sonclod':
                foreach($lines as $v){
            
                    $sin = trim($v);
                    $isian = tesdata($sin,"sonclod",$argv[1]);
                    if($isian){
                        $isi[] = $isian;
                        
                    }
                    
                }
                csv_writer($isi,"Datason");
                echo "\nTotal Line Csv ".count($isi);
            break;
            case 'all':
                foreach($lines as $v){
            
                    $sin = trim($v);
                    $isian = tesdata($sin,"sonclod",$argv[1]);
                    $isianp = tesdata($sin,"pixnet",$argv[1]);
                    if($isian){
                        $isi[] = $isian;
                        $isip[] = $isianp;

                        
                    }
                    
                }
                csv_writer($isi,"Datason");
                csv_writer($isip,"DataPix");
                xml($isip,ISI_ARTICLE);
                echo "\nTotal Line Csv ".count($isi);
            break;
            case 'xml':
                foreach($lines as $v){
            
                    $sin = trim($v);
                    $isian = tesdata($sin,"pixnet",$argv[1]);
                    if($isian){
                        $isi[] = $isian;
                        
                    }
                    
                }
                xml($isi,ISI_ARTICLE);
                //echo "\nTotal Line Csv ".count($isi);
            break;
        
            
            
        }
        
    //}
    
