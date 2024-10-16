<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Get data

$product_code = filter_input(INPUT_POST, 'product_code');
$name = filter_input(INPUT_POST, 'name');
$version = filter_input(INPUT_POST, 'version', FILTER_VALIDATE_INT); // Use FILTER_VALIDATE_INT
$release_date = filter_input(INPUT_POST, 'release_date');

// Validate inputs
$error = "";
//case/switch statment for product code

switch (true){
    case empty ($product_code);
    $error = "Invalid product code.";
    break;
    
    case empty ($name);
    $error = "Invalid product name.";
    break;

    case $version === false;
    $error = "Version type must be an Integer.";
    break;
    
    case empty ($release_date);
    $error = "Invalid product Release date.";
    break;
}

// If there was an error, display it
if (!empty($error)) {
    include('errors/add_product_error.php');
    exit();
} else {
    require_once('database.php');
    
    // Check if the product_code already exists
    $checkQuery = 'SELECT COUNT(*) FROM products WHERE productCode = :product_code';
    $checkStatement = $db->prepare($checkQuery);
    $checkStatement->bindValue(':product_code', $product_code);
    $checkStatement->execute();
    $productExists = $checkStatement->fetchColumn();
    $checkStatement->closeCursor();
 // Product code already exists
    
    // Handle the case if the product code already exists
    if ($productExists > 0) {
        $error = "Product with code '$product_code' already exists. Please use a different code.";
        include('errors/add_product_error.php');
        exit();
    } else {
        // Add the product to the database if no duplicate is found
        $query = 'INSERT INTO products (productCode, name, version, releaseDate)
                  VALUES (:product_code, :name, :version, :release_date)';
        $statement = $db->prepare($query);
        $statement->bindValue(':product_code', $product_code);
        $statement->bindValue(':name', $name);
        $statement->bindValue(':version', $version);
        $statement->bindValue(':release_date', $release_date);

        // Execute the query to insert the product
        $statement->execute();
        $statement->closeCursor();

        // Redirect to the product management form
        header("Location: manage_products_form.php");
        exit(); 
    }
}
?>
