<?php
//include the simple_html_dom library
require_once 'simple_html_dom.php';

// create DOM element from URL or file
$html = file_get_html('https://www.finelib.com/cities/lagos/business/beauty-services/barbing-salons/page-1');

// find all the rows of the table
$rows = $html->find('div[class="box-682 bg-none"]');

// create array to store extracted movie information
$listings = array();

if(count($rows)>1){
	// loop through each rows
	for($i=1; $i<count($rows); $i++){
		//extract poster image url
		$businessName = $rows[$i]->find('div[class="box-headings box-new-hed"] a',0)->plaintext;
		$address = $rows[$i]->find('div[class="cmpny-lstng-1"]',0)->plaintext;
		$phone_no = $rows[$i]->find('div[class="tel-no-div"]',0)->plaintext;
		$business_url = $rows[$i]->find('div[class="cmpny-lstng url"] a',0)->href;
        
        $html2 = file_get_html($business_url);
        $business_email = $html2->find('div[class="subb-bx MT-15"] a',0)->plaintext;
		
		// create movie array item
		$list = array("rank"=>$i, "BusinessName"=>trim($businessName), "Address"=>trim($address), "Phone"=>trim($phone_no), "Email"=>trim($business_email));
		
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