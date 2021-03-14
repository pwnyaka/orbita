<?php
$user = 'root';
$pass = '';
$db = new PDO('mysql:host=localhost;dbname=my_shop', $user, $pass);

$statement = $db->prepare('SELECT * FROM products');
$statement->execute();
$products = $statement->fetchAll();

$db2 = new PDO('mysql:host=localhost;dbname=opencart_test', $user, $pass);

$date = date('Y-m-d');

//CATEGORY

$category = $db2->prepare("INSERT INTO category SET image = ' ', parent_id = 0, top = 1, `column` = 1, sort_order = 1, status = 1,
date_added = NOW(), date_modified = NOW()");

$category->execute();
$categoryId = $db2->lastInsertId();
var_dump($categoryId);

$categoryDesc = $db2->prepare("INSERT INTO category_description SET category_id = '$categoryId',
language_id = 1, name = 'Прихожие', description = 'Прихожие', meta_title = 'Прихожие', meta_description = ' ', meta_keyword = ' '");
$categoryDesc->execute();

$categoryLayout = $db2->prepare("INSERT INTO category_to_layout SET category_id = '$categoryId', store_id = 0, layout_id = 0");
$categoryLayout->execute();

$categoryStore = $db2->prepare("INSERT INTO category_to_store SET category_id = '$categoryId', store_id = 0");
$categoryStore->execute();

// PRODUCT

$stmtProduct = $db2->prepare("INSERT INTO product SET model = :model,
 sku = ' ', upc = ' ',
  ean = ' ', jan = ' ',
   isbn = ' ', mpn = ' ',
    location = ' ', quantity = 777,
     minimum = 1, subtract = 1,
      stock_status_id = 1, date_available = '$date',
       manufacturer_id = 1, shipping = 1,
        price = :price, points = 1,
         weight = 0, weight_class_id = 1,
          length = 0, width = 0,
           height = 0, length_class_id = 1,
            status = 1, tax_class_id = 1,
             sort_order = 1, date_added = NOW(), date_modified = NOW(), image = :image");

$statementDesc = $db2->prepare("INSERT INTO product_description SET product_id = :id, language_id = 1,
 name = :name, description = :description, tag = ' ', meta_title = :name, meta_description = ' ', meta_keyword = ' '");

$productToCategory = $db2->prepare("INSERT INTO product_to_category SET product_id = :id, category_id = '$categoryId'");
$productToLayout = $db2->prepare("INSERT INTO product_to_layout SET product_id = :id, store_id = 0, layout_id = 0");
$productToStore = $db2->prepare("INSERT INTO product_to_store SET product_id = :id, store_id = 0");



foreach ($products as $key => $product) {
    $productParams = [
        "model" => 'model' . ' ' . $key,
        "image" => $product['img'],
        "price" => $product['price'],
    ];
    try {
        $stmtProduct->execute($productParams);
        $id = $db2->lastInsertId();
        $descParams = [
            "id" => $id,
            "name" => $product["name"],
            "description" => $product["description"]
        ];
        $statementDesc->execute($descParams);

        $categoryParams = [
          "id" => $id,
        ];
        $productToCategory->execute($categoryParams);
        $productToLayout->execute($categoryParams);
        $productToStore->execute($categoryParams);
    }
    catch (PDOException $exception) {
        echo $exception->getMessage();
    }

}