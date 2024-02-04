<?php

include('simple_html_dom.php');

// Main page URL
$url = 'https://yourpetpa.com.au/collections/dog-health?page=1';

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
$productElements = $html->find('div.product-block');

foreach ($productElements as $productElement) {
    // Attempt to extract title, price, etc.
    $titleElement = $productElement->find('div.product-block__title > a', 0);
    $priceElement = $productElement->find('div.product-price > span.product-price__reduced', 0);

    // Check if elements are found before accessing properties
    $title = $titleElement ? $titleElement->plaintext : 'N/A';
    $price = $priceElement ? $priceElement->plaintext : 'N/A';

    // Make additional HTTP request to get description from the product detail page
    $productDetailUrl = $titleElement ? 'https://yourpetpa.com.au' . $titleElement->href : null;

    if ($productDetailUrl) {
        $productDetailContent = file_get_contents($productDetailUrl, false, $context);

        if ($productDetailContent !== false) {
            $productDetailHtml = new simple_html_dom();
            $productDetailHtml->load($productDetailContent);

            // Attempt to extract description
            $descriptionElement = $productDetailHtml->find('div.product__description', 0);
            $description = $descriptionElement ? $descriptionElement->plaintext : 'N/A';

            // Attempt to extract image URL using regular expression
            $imageUrl = 'N/A';

            preg_match('/"og:image" content="(.*?)"/', $productDetailContent, $matches);

            if (!empty($matches[1])) {
                $imageUrl = $matches[1];
            }

            $productDetailHtml->clear(); // Clear the Simple HTML DOM object for the product detail page

            // Add extracted data to $productsData array
            $productsData[] = [
                'Title' => $title,
                'Price' => $price,
                'Description' => $description,
                'Product URL' => $productDetailUrl,
                'Image URL' => $imageUrl,
                // Add other fields as needed
            ];
        }
    }
}

$html->clear();  // Clear the Simple HTML DOM object for the main page

// Check if $productsData is not empty
if (!empty($productsData)) {
    // Open CSV file for writing
    $csvFile = fopen('dogdatafeed.csv', 'w');

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
