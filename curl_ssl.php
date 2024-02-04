<?php
    // phpinfo();
    $ch = curl_init('https://yourpetpa.com.au/');
    $option = curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3');
    curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
    curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 'false');

    $result = curl_exec($ch);
    $error = curl_error($ch);

    if($error){
        echo $error;
    }
?>