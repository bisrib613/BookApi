<?php		
error_reporting(1);
require "GoodReads.php";
require_once "func.php";
$contents = file_get_contents('asin.txt');
$lines = explode("\n", $contents);
$tw = file_get_contents('blade/tw.txt');
define("RANDOM_TEMPLATE","YES");
define("ISI_ARTICLE", 1000);  //Isi seng di maksud sak file xml maksimal pirang konten (maksimal 1000 per xml)
define("BACK_DATE",			            "-3 month");
define("SHEDULE_DATE",		            "+0 month");
define("LP",               "https://neobook.tech/");
       


echo $tw."\n";

switch ($argv[1]) {
    case 'pixnet':
        $data = goodapii("pixnet",LP);
        csv_writer($data,"Datapix");
        echo "\nTotal Line Csv ".count($data);
    break;
    case 'sonclod':
        $data = goodapii("sonclod",LP);
        csv_writer($data,"Datason");
        echo "\nTotal Line Csv ".count($data);
    break;
    case 'all':
        
        $data1 = goodapii("pixnet",LP);
        $data = goodapii("sonclod",LP);
        csv_writer($data1,"Datapix");
        csv_writer($data,"Datason");
        xml($data1,ISI_ARTICLE);
        echo "\nTotal Line Csv ".count($data1);
    break;
    case 'xml':
        $data = goodapii("pixnet",LP);
        xml($data,ISI_ARTICLE);
       
    break;
}
    
