<?php		
error_reporting(1);
require "GoodReads.php";
require_once "func.php";
$contents = file_get_contents('asin.txt');
$lines = explode("\n", $contents);
$tw = file_get_contents('blade/tw.txt');



//Multi template pisah nggae koma
//conto define("TEMPLATE_PDF",array("tema_1","tema_0","tema_2"));
//conto ke 2 define("TEMPLATE",array("tema_1","tema_0","tema_2"));

// "TEMPLATE_PDF" NGGE PDF
// "TEMPLATE" NGGE HTML

define("TEMPLATE_PDF",array("tema_1"));
define("TEMPLATE",array("tema_0"));
define("ISI_ARTICLE", 2000);  
define("BACK_DATE",			            "-3 month");
define("SHEDULE_DATE",		            "+0 month");
define("LP",               "https://neobook.tech/");

define("MAX_HEIGHT",			            420);
define("MAX_WIDTH",		            380);






setlocale(LC_TIME, 'id_ID');
date_default_timezone_set('Asia/Jakarta');
$foldername = readline("Enter Folder Export Name: ");
$dir = "export/$foldername/";
if ($foldername == null){
    $day = strftime("%B");
    $date = date("H-d-m-Y")."/";
    $dir = "export/$date";
}
        
if (!file_exists($dir)) {
    mkdir($dir, 0755, true);
    
    //mkdir($pdf_dir, 0755, true);
}

$filter = '';
$blog = '';

// Loop through all the arguments
foreach ($argv as $arg) {
  // Check if the argument is in the format --key=value
  if (strpos($arg, '--') === 0 && strpos($arg, '=') !== false) {
    // Split the argument into key and value
    list($key, $value) = explode('=', substr($arg, 2), 2);
    // Save the value to the corresponding variable
    ${$key} = $value;
  }
}


$badWords = file('badwords.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);echo $tw."\n";
$RanClass = RandomString(5);
switch ($argv[1]) {
    case 'pixnet':
        $data = goodapii("pixnet",LP);
        csv_writer($data,"Datapix");
        echo "\nTotal Line Csv ".count($data)."\n";
    break;
    case 'sonclod':
        $data = goodapii("sonclod",LP);
        csv_writer($data,"Datason");
        echo "\nTotal Line Csv ".count($data)."\n";
    break;
    case 'pdf':
        $data = goodapii("pdf",LP);
        csv_writer($data,"Data");
        //print_r($data);
        echo "\nTotal Line Csv ".count($data)."\n";
    break;
    case 'pin':
        $data = goodapii("pin",LP);
        csv_writer($data,"Data");
        
        
        
        
        
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
    case 'csvh':
        $data = goodapii("pixnet",LP);
        csvheader($data,ISI_ARTICLE);
    break;
}
    
