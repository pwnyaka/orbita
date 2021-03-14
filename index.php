<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLINFO_HEADER_OUT, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

curl_setopt($ch, CURLOPT_URL, 'https://brw-mebelizh.ru/prihozhie/prihozhie-1/');

$content = curl_exec($ch);

if (($error = curl_error($ch))) {
    echo "Curl error: " . $error;
}

curl_close($ch);

preg_match_all('#<div class="product-layout(.+?)<div class="product-layout#isu', $content, $matches);

$products = [];

foreach ($matches[0] as $key => $item) {
    preg_match('#<h4 class="modal-title">(.+?)</h4>#isu', $item, $name);
    $name[1] = htmlspecialchars_decode($name[1]);
    $products[$key]['name'] = $name[1];
    preg_match('#<img src="(.+?)"#isu', $item, $img);
    $products[$key]['img'] = $img[1];
    preg_match('#<div class="tab-pane active" id="tab-description">(.+?)</div>#isu', $item, $description);
    $products[$key]['description'] = $description[1];
    preg_match('#<p class="price">(.+?)</p>#isu', $item, $price);
    $price[1] = substr(trim($price[1]), 0, -8);
    $products[$key]['price'] = $price[1];

}
$user = 'root';
$pass = '';
$db = new PDO('mysql:host=localhost;dbname=my_shop', $user, $pass);
$statement = $db->prepare('INSERT INTO products (`name`, `img`, `description`, `price`) VALUES (:name, :img, :description, :price)');

foreach ($products as $product) {
    $params = [
        "name" => $product['name'],
        "img" => $product['img'],
        "description" => $product['description'],
        "price" => $product['price'],
    ];
    $statement->execute($params);
}

var_dump($products);