<?php
require_once('vendor/autoload.php');
use Scriptotek\GoogleBooks\GoogleBooks;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;


function encrypt($text)
{
   return base64_encode($text);
}

function decrypt($text)
{
   return base64_decode($text);
}
function bacaHTML($url){
    $data = curl_init();
    curl_setopt($data, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36");
    curl_setopt($data, CURLOPT_SSL_VERIFYPEER, 0 );
    curl_setopt($data, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($data, CURLOPT_URL, $url);
    curl_setopt($data, CURLOPT_FOLLOWLOCATION, true);
    $hasil = curl_exec($data);
    curl_close($data);
    return $hasil;
}

function csv_writer($content,$FN){
    
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    setlocale(LC_TIME, 'id_ID');
    date_default_timezone_set('Asia/Jakarta');
    $day = strftime("%B");
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->fromArray($content, NULL, 'A1');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
    $writer->setDelimiter(',');
    $writer->setEnclosure('"');
    $writer->setLineEnding("\r\n");
    $writer->setSheetIndex(0);
    $writer->setUseBOM(true);
    
    $writer->save("$FN.csv");
}
function spintax($s){

    preg_match('#\{(.+?)\}#is',$s,$m);
    if(empty($m)) return $s;

    $t = $m[1];

    if(strpos($t,'{')!==false){
        $t = substr($t, strrpos($t,'{') + 1);
    }
    $parts = explode("|", $t);
    $s = preg_replace("+\{".preg_quote($t)."\}+is", $parts[array_rand($parts)], $s, 1);

    return spintax($s);
}
function getdata($asin){
    $api = new GoodReads('VAmrmu8kBvhLOiqzQxrpNg');
    $data = $api->getBookByISBN($asin);
    print_r ($data);
    if(!empty($user)){
		if ($data['book']['image_url']=='https://s.gr-assets.com/assets/nophoto/book/111x148-bcc042a9c91a29c1d680899eff700a03.png') {
            $imgurl = getimage($data['book']['isbn']);
        }else {
            $imgurl =  preg_replace('/._([A-Z])\w+/','',$data['book']['image_url']);
    
        }
        $title = $data['book']['title'];
        $author = $data['book']['authors']['author']['name'];
        $description = $data['book']['description'];
        $publisher = $data['book']['publisher'];
        $pubdate = "{$data['book']['publication_day']}-{$data['book']['publication_month']}-{$data['book']['publication_year']}";
        //$imgurl =  preg_replace('/._([A-Z])\w+/','',$data['book']['image_url']);
        //echo $imgurl;
        $description = html_entity_decode($description, ENT_COMPAT, 'UTF-8');
        $fichier = 'file.csv';
        $fp = fopen('results.csv', 'a');
        // header("Content-type: application/vnd.ms-excel");
        // header("Content-Encoding: UTF-8");
        // header('Content-Disposition: attachment; filename="results.csv"');
        // header('Pragma: no-cache');
        // header('Expires: 0');
        fwrite($fp, "\xEF\xBB\xBF"); // <--- add this
        $desc = "{$title} eBook PDF\n\nDownload Link ==>> https://forsharedpdf.site/album/{$asin}\n\n{$description}";
        $tit = "{$title} - {$author}";
        $data = array($imgurl,$tit,strip_tags($desc));
        //echo $description;
        fputcsv($fp, $data, ",");
        fclose($fp);
	}
    
}

function getimage ($asin){
	if (!file_exists("./data/{$asin}")) {
            $st = bacaHTML("https://www.googleapis.com/books/v1/volumes?q=isbn:{$asin}");
            file_put_contents("./data/{$asin}", $st);
        }
        else {
            $st = file_get_contents("./data/{$asin}");
            
        }
    //$st = bacaHTML("https://www.googleapis.com/books/v1/volumes?q=isbn:{$asin}");
    $manage = json_decode($st);
    $item = $manage->items[0]->volumeInfo;
	if ($item){
    $desc = $item->description;
	$parts = parse_url($item->imageLinks->thumbnail);
    //echo $item->authors[0];
	parse_str($parts['query'], $query);
    $url = '';
    if($query['id']){
		$gambar = "https://books.google.com/books/publisher/content/images/frontcover/{$query['id']}?fife=w400-h600&source=gbs_api";
	}else{
		$gambar = '';
	}
	if($dtl["authors"][0]){
		$author = $dtl["authors"][0];
	}else{
		$author = '';
	}
	if($dtl["description"]){
		$description = $dtl["description"];
	}else{
		$description = '';
	}
	if($dtl["publishedDate"]){
		$date = $dtl["publishedDate"];
	}else{
		$date = '';
	}
	$book = array(
        'author' => $author,
        'image' => $gambar,
        'description' => $description,
        'date' => $date
    );
	}
    return $book;
	
}
function grapi($asin){
    $URLa = "https://www.goodreads.com/book/isbn?isbn=".$asin."&key=VAmrmu8kBvhLOiqzQxrpNg";
    
    try {
        if (!file_exists("./data/gr_{$asin}")) {
            $xml_response = bacaHTML($URLa);
            $xml = new SimpleXMLElement($xml_response);
            $item = $xml->book;
            $xml1 = simplexml_load_string($xml_response);
            $cdata = $xml1->asXML();
            file_put_contents("./data/gr_{$asin}", $cdata);
        }
        else {
            $xml_response = file_get_contents("./data/gr_{$asin}");
            $xml = new SimpleXMLElement($xml_response);
            $item = $xml->book;
        }
        if(empty($item)){
            die();
        }
        $gbapi = getimagepi($item->isbn);
        $title = $item->title;
        //echo $title;
        $author=$authors->name;
        $page= $item->num_pages;
        $publisher = $item->publisher;
        $publication=$item->work->original_publication_day."-".$item->work->original_publication_month."-".$item->work->original_publication_year;
        $author = $item->authors->author->name;
        if ($item->image_url=='https://s.gr-assets.com/assets/nophoto/book/111x148-bcc042a9c91a29c1d680899eff700a03.png') {
            $imgurl = $gbapi['image'];
            if (empty($imgurl)) {
                //exit(0);
                return false;
            }
        }else {
            $imgurl =  preg_replace('/._([A-Z])\w+/','',$item->image_url);
            //return true;
        }
        if (empty($item->description)) {
            $description = $gbapi['description'];
            //echo $description;
            //return true;
        }else {
            $description = $item->description;
            //echo $description;
        }
        $description = html_entity_decode($description, ENT_COMPAT, 'UTF-8');
            $fichier = 'file.csv';
            $fp = fopen('results.csv', 'a');
            header("Content-type: application/vnd.ms-excel");
            header("Content-Encoding: UTF-8");
            header('Content-Disposition: attachment; filename="results.csv"');
            header('Pragma: no-cache');
            header('Expires: 0');
            fwrite($fp, "\xEF\xBB\xBF"); // <--- add this
            $desc = "{$title} eBook PDF\n\nDownload Link ==>> https://read.forsharedpdf.site/album/{$asin}\n\n{$description}";
            $tit = "{$title} - {$author}";
            $data = array($imgurl,$tit,strip_tags($desc));
            //echo $title."\n";
            fputcsv($fp, $data, ",");
            fclose($fp);
    } catch (Exception $e) {
        echo $e;
    }
}
function getimagepi($isbn){
    if (!file_exists("./data/{$isbn}")) {   
        $books = new GoogleBooks(['key' => 'AIzaSyBBMWpp_t8Yi4vy9mrdDIqrsssWhDtYhi8']);
	    $volume = $books->volumes->byIsbn($isbn);
        $result = json_decode($volume,true);
    }else {
        $volume = file_get_contents("./data/{$isbn}");
        $result = json_decode($volume,true);
    }
	if ($result){
	$dtl = $result["volumeInfo"];
    //print_r($dtl["imageLinks"]);

	$parts = parse_url($dtl["imageLinks"]["thumbnail"]);
	parse_str($parts['query'], $query);
    $gambar = "https://books.google.com/books/publisher/content/images/frontcover/{$query['id']}?fife=w400-h600&source=gbs_api";
    //echo $gambar;
    $book = array(
        'author' => $dtl["authors"][0],
        'image' => $gambar,
        'description' => $dtl["description"],
        'date' => $dtl["publishedDate"]
    );
    if (!file_exists("./data/{$isbn}")) {
        file_put_contents("./data/{$isbn}", json_encode($result));
    }
	return $book;
	}
	
}
function htmldata($asin){
    $api = new GoodReads('VAmrmu8kBvhLOiqzQxrpNg', 'C:/laragon/www/booktes/data/');
    $data = $api->getBookByISBN($asin);
    if(empty($data['book'])){
		exit();
	}
    if ($data['book']['image_url']=='https://s.gr-assets.com/assets/nophoto/book/111x148-bcc042a9c91a29c1d680899eff700a03.png') {
        $imgurl = getimage($data['book']['isbn']);
    }else {
        $imgurl =  preg_replace('/._([A-Z])\w+/','',$data['book']['image_url']);

    }
    $title = $data['book']['title'];
    $author = $data['book']['authors']['author']['name'];
    $description = $data['book']['description'];
    $publisher = $data['book']['publisher'];
    $pubdate = "{$data['book']['publication_day']}-{$data['book']['publication_month']}-{$data['book']['publication_year']}";
    //echo $imgurl;
    $description = html_entity_decode($description, ENT_COMPAT, 'UTF-8');
    $fichier = 'file.csv';
    $fp = fopen('results.csv', 'a');
    header("Content-type: application/vnd.ms-excel");
    header("Content-Encoding: UTF-8");
    header('Content-Disposition: attachment; filename="results.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');
    fwrite($fp, "\xEF\xBB\xBF"); // <--- add this
    $desc = "{$title} eBook PDF\n\nDownload Link ==>> https://forsharedpdf.site/album/{$asin}\n\n{$description}";
    $tit = "{$title} - {$author}";
    $data = array($tit,$desc);
    //echo $description;
    fputcsv($fp, $data, ",");
    fclose($fp);
}




function tesdata($asin,$type,$lpe){
    $asinn = strval( $asin );
    //echo $asinn;
    try {
        $der = preg_replace( "/\r|\n/", "", $asinn );
        $URLa = "https://www.goodreads.com/book/isbn?isbn=".$der."&key=VAmrmu8kBvhLOiqzQxrpNg";    
        if (!file_exists("./data/gr_{$asin}")) {
            $xml_response = bacaHTML($URLa);
            $xml = new SimpleXMLElement($xml_response);
            $item = $xml->book;
            $xml1 = simplexml_load_string($xml_response);
            $cdata = $xml1->asXML();
            file_put_contents("./data/gr_{$asin}", $xml_response);
        }
        else {
            $xml_response = file_get_contents("./data/gr_{$asin}");
            $xml = new SimpleXMLElement($xml_response);
            $item = $xml->book;
        }
        
        $loger = $der." ==> Data Kosong\n";
        if(!empty($item)){
        $gbapi = getimage($item->isbn);
        $title = $item->title;
        //echo $title;
        $author=$authors->name;
        $page= $item->num_pages;
        $publisher = $item->publisher;
        $publication=$item->work->original_publication_day."-".$item->work->original_publication_month."-".$item->work->original_publication_year;
        $author = $item->authors->author->name;
        if ($item->image_url=='https://s.gr-assets.com/assets/nophoto/book/111x148-bcc042a9c91a29c1d680899eff700a03.png') {
            $imgurl = $gbapi['image'];
            if (empty($imgurl)) {
                //exit(0);
                echo $loger;
                return false;
            }
        }else {
            $imgurl =  preg_replace('/._([A-Z])\w+/','',$item->image_url);
            //return true;
        }
        if (empty($item->description)) {
            $description = $gbapi['description'];
            //echo $description;
            //return true;
        }else {
            $description = $item->description;
            //echo $description;
        }
            $description = html_entity_decode($description, ENT_COMPAT, 'UTF-8');
		
            $lp = "https://booklibs.live/";
           //$lp = trim(bacaHTML("https://collectionlibs.tech/?url=https://booklibs.live/{$asin}"));
            $desc = "{$title} Full\n Here : {$lpe}{$asin}\n{$description}";
            $next =  "<=.Supporting format: PDF, EPUB, Kindle, Audio, MOBI, HTML, RTF, TXT, etc.\nRead or Download EPUB/pdf {$title} Kindle Unlimited by {$author} (Author) PDF is a great book to read and that's why I recommend reading {$title} on Textbook.";
            $desc2 = "*read Pdf {$title} {$author}.\nGet here=> http://edu.booklibs.live/{$asin}\n{$next}";
            //echo $next;
            $tum = "<p><b>{$title}</b></p><p>&nbsp; by {$author}</p><figure data-orig-height='264' data-orig-width='264'><img src='{$imgurl}' data-orig-height='264' data-orig-width='264'></figure><p><a href='$lpe{$asin}'><b><span style='color: #000000'>DOWNLOAD NOW</span>&nbsp;</b></a>&nbsp;&rArr;&nbsp;<a href='$lpe{$asin}'><b>{$title}</b></a></p><p><b>Description :</b></p><p>{$description}.</p>";
			$html = "<p><strong>EPUB & PDF Ebook {$title} | EBOOK ONLINE DOWNLOAD</strong></p><p><em>by {$author}.</em></p><p style='text-align: center;'><a href='$lpe{$asin}' target='_blank' rel='nofollow'>  <img src='{$imgurl}'  width='400' height='375' alt='EBOOK {$title}' /></a></p><ul><li><strong>Download Link : </strong><a href='$lpe{$asin}'>DOWNLOAD {$title}</a></li><li><strong>Read More : </strong><a href='$lpe{$asin}'>READ {$title}</a></li></ul><p><strong>Ebook PDF {$title}</strong> | EBOOK ONLINE DOWNLOAD<br />Hello Book lovers, If you want to download free Ebook, you are in the right place to download Ebook.<strong> Ebook {$title} EBOOK ONLINE DOWNLOAD</strong> in English is available for free here, Click on the download LINK below to download Ebook {$title} 2020 PDF Download in English by {$author} (Author).</p><p>&nbsp;</p><p><strong>Description Book:</strong>&nbsp;</p><p>$description</p>";
            $tit = "{$title} - {$author}";
			$tit = str_replace( ',', '', $tit );
            $spin = "{*%|>|<-}{{Download|Read|PDF|eBook|eBook (Download)|Download (PDF)|(PDF) Download} $title {eBook |BOOK |}BY $author|{Download|Read|PDF|eBook|eBook [Download]|Download [PDF]|[PDF] Download} $title {eBook |BOOK |}BY $author}";
            $titspin = spintax($spin);
            $data = array($imgurl, (string) $tit,(string)strip_tags ($desc));
            $datahtml = array((string) $tit,(string) ($html));
            $htmltum = array((string) $tit,(string) ($tum));

            echo $der."\n";
            $return = "";
            if($type == "sonclod"){
                $return = $data;
            }elseif ($type == "pixnet") {
                $return = $datahtml;
            }elseif ($type == "tumblr") {
                $return = $htmltum;
            }
            //csv_writer($data);

			//csv_compiler($data);
			$keys = array(
        'title' => $tit,
        'html' => $html,
    );

			
            
			//return true;
            
        }else {
            echo $loger;
        }
    } catch (Exception $e) { 
        echo $e;
    }
    return $return;
}