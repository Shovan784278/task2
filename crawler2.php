<?php

include('simple_html_dom.php');

$url = 'https://yourpetpa.com.au/';

$options = [
    'http' => [
        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3\r\n",
    ],
];

$context = stream_context_create($options);
$htmlContent = file_get_contents($url, false, $context);

if ($htmlContent === false) {
    echo 'Failed to retrieve HTML.';
    exit; // Exit the script if there's an error
}

$html = new simple_html_dom();
$html->load($htmlContent);

$productsData = [];

// Find the product elements
$productElements = $html->find('div.product-block__title-link');

foreach ($productElements as $productElement) {
    // Attempt to extract title, description, price, etc.
  
    $descriptionElement = $productElement->find('div.product-block__title>a', 0);
    $priceElement = $productElement->find('div.product-price>span.product-price__reduced', 0);

    // Check if elements are found before accessing properties
   
    $description = $descriptionElement ? $descriptionElement->plaintext : 'N/A';
    $price = $priceElement ? $priceElement->plaintext : 'N/A';

    //var_dump($title, $description, $price);

    // Add extracted data to $productsData array
    $productsData[] = [
        'Title' => $title,
        'Description' => $description,
        'Price' => $price,
        // Add other fields as needed
    ];
}


$html->clear();  // Clear the Simple HTML DOM object

// Check if $productsData is not empty
if (!empty($productsData)) {
    // Open CSV file for writing
    $csvFile = fopen('datafeed.csv', 'w');

    // Write header row
    fputcsv($csvFile, array_keys($productsData[0]));

    // Write product data
    foreach ($productsData as $product) {
        fputcsv($csvFile, $product);
    }

    fclose($csvFile);
} else {
    echo "No products found."; // or handle appropriately
}

?>
