<?php
//include the simple_html_dom library
require_once 'simple_html_dom.php';

// create DOM element from URL or file
$html = file_get_html('https://www.businesslist.com.ng/category/barbers/city:lagos');

// find all the rows of the table
$rows = $html->find('div[class="company g_0"]');

// create array to store extracted movie information
$listings = array();

if(count($rows)>1){
	// loop through each rows
	for($i=1; $i<count($rows); $i++){
		//extract poster image url
		$businessName = $rows[$i]->find('h4 a',0)->plaintext;
		$address = $rows[$i]->find('div[class="address"]',0)->plaintext;
		$business_url = $rows[$i]->find('h4 a',0)->href;
        
        $html2 = file_get_html('https://www.businesslist.com.ng'.$business_url);
        $phone_no = $html2->find('div[class="text phone"]',0)->plaintext;
		
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

$csvName = "data/buslist.csv";
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