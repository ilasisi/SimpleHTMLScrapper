<?php
//include the simple_html_dom library
require_once 'simple_html_dom.php';

// create DOM element from URL or file
$html = file_get_html('https://businesses.connectnigeria.com/search/?keyword=barbing&loc=lagos');

// find all the rows of the table
$rows = $html->find('div[class="row mb24"]');

// create array to store extracted movie information
$listings = array();

if(count($rows)>1){
	// loop through each rows
	for($i=1; $i<count($rows); $i++){
		//extract poster image url
		$businessName = $rows[$i]->find('div[class="col-sm-12"] a h4',0)->plaintext;
		$address1 = $rows[$i]->find('div[class="col-sm-7 col-md-8"] p[class="mb0 hidden-xs"]',0)->plaintext;
		$address2 = $rows[$i]->find('div[class="col-sm-7 col-md-8"] p[class="mb2 hidden-xs"]',0)->plaintext;
        $address = $address1. ", ".$address2;
		$phone_no = $rows[$i]->find('div[class="display-block pull-left mr10 mb1 c-blue hidden-xs hidden-sm"] a',0)->plaintext
		
		// create movie array item
		$list = array("rank"=>$i, "BusinessName"=>trim($businessName),"Address"=>trim($address),"Phone"=>trim($phone_no));
		
		// store the movie item into movies array
		array_push($listings, $list);
	}
}

// clear the dom object to avoid memory leak
$html->clear(); 
unset($html);

$headers = ["S/N", "Business Name", "Address", "Phone Numbers", "Email"];

$csvName = "data/barbing.csv";
$fileHandle = fopen($csvName, 'w') or die('Can\'t create .csv file, try again later.');

//Add the headers
fputcsv($fileHandle, $headers);

//Add the data
foreach ($listings as $list) {
    if(fputcsv($fileHandle, $list)){
        echo 'XML file have been generated successfully.';  
    }else{
        echo 'XML file generation error.'; 
    }
}

fclose($fileHandle);
?>