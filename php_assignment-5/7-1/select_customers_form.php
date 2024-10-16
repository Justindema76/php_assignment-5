<?php

// Display errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection
require('database.php');

// Get the customer ID from the form submission
$customerID = filter_input(INPUT_POST, 'customerID', FILTER_VALIDATE_INT);

if ($customerID !== null && $customerID !== false) {
    
    // Fetch customer data
    $queryCustomer = 'SELECT * FROM customers WHERE customerID = :customerID';
    $statement = $db->prepare($queryCustomer);
    $statement->bindValue(':customerID', $customerID);
    $statement->execute();
    
    $customer = $statement->fetch();  
    $statement->closeCursor();
    
    // Check if customer was found
    if (!$customer) {
        echo 'No customer found with the provided ID.';
        exit();
    }
    
    // Fetch countries from the database
    $countriesQuery = 'SELECT countryCode, countryName FROM countries ORDER BY countryName';
    $countriesStatement = $db->prepare($countriesQuery);
    $countriesStatement->execute();
    $countries = $countriesStatement->fetchAll(PDO::FETCH_ASSOC);
    $countriesStatement->closeCursor();
} else {
    echo 'Invalid customer ID.';
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View/Update Customer</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <?php include 'view/header.php'; ?>
    
    <main>
        <h2>View/Update Customer</h2>
        
        <form action="update_customer.php" method="post" id="add_product_form">
            <!-- Hidden field for customerID -->
            <input type="hidden" name="customerID" value="<?php echo htmlspecialchars($customer['customerID']); ?>" />
            
            <label for="firstName">First Name:</label>
            <input type="text" name="firstName" value="<?php echo htmlspecialchars($customer['firstName']); ?>" /><br>

            <label for="lastName">Last Name:</label>
            <input type="text" name="lastName" value="<?php echo htmlspecialchars($customer['lastName']); ?>" /><br>

            <label for="email">Email:</label>
            <input type="text" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>" /><br>

            <label for="city">City:</label>
            <input type="text" name="city" value="<?php echo htmlspecialchars($customer['city']); ?>" /><br>

            <label for="address">Address:</label>
            <input type="text" name="address" value="<?php echo htmlspecialchars($customer['address']); ?>" /><br>

            <label for="state">State:</label>
            <input type="text" name="state" value="<?php echo htmlspecialchars($customer['state']); ?>" /><br>

            <label for="postalCode">Postal Code:</label>
            <input type="text" name="postalCode" value="<?php echo htmlspecialchars($customer['postalCode']); ?>" /><br>

            <label for="countryCode">Country:</label>
            <select name="countryCode" id="countryCode">
                <?php foreach ($countries as $country): ?>
                    
                    <option value="<?php echo htmlspecialchars($country['countryCode']); ?>"
                        <?php echo ($country['countryCode'] === $customer['countryCode']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($country['countryName']); ?>
                    </option>
                    
                <?php endforeach; ?>
            </select>
            <br>

            <label for="phone">Phone:</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($customer['phone']); ?>" /><br>

            <label for="password">Password:</label>
            <input type="text" name="password" value="<?php echo htmlspecialchars($customer['password']); ?>" /><br>

            <!-- Update customer button -->
            <div id="buttons">
                <input type="submit" value="Update Customers" class="submit-button"/><br />
            </div>
        </form>

        <p><a href="manage_customers_form.php">Search Customers</a></p>
    </main>
    
    <?php include 'view/footer.php'; ?>
</body>
</html>
