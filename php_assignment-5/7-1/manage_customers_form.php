<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require("database.php");

// Initial query to fetch all customers with their country
$queryCustomers = 'SELECT c.*, co.countryName FROM customers c
                   LEFT JOIN countries co ON c.countryCode = co.countryCode';
$statement1 = $db->prepare($queryCustomers);
$statement1->execute();
$customers = $statement1->fetchAll();  
$statement1->closeCursor();

// Check if a last name was submitted
if (isset($_POST['last_name'])) {
    $lastName = $_POST['last_name'];

    // Query to search customers by last name, including country
    $queryCustomers = 'SELECT c.*, co.countryName FROM customers c
                       LEFT JOIN countries co ON c.countryCode = co.countryCode 
                       WHERE c.lastName LIKE :lastName';
    $statement1 = $db->prepare($queryCustomers);
    
    $statement1->bindValue(':lastName', '%' . $lastName . '%');
    $statement1->execute();
    
    // Get the customers that match the search criteria
    $customers = $statement1->fetchAll();  
    $statement1->closeCursor();
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Customers</title>
    <link rel="stylesheet" href="main.css"/>       
</head>
<body>
    <?php include 'view/header.php'; ?>
    <main>
        <h2>Customer Search</h2>
        <form action="manage_customers_form.php" method="post">
            <label for="last_name">Last Name:</label>
            <input type="text" name="last_name" id="last_name"/>
            <input type="submit" value="Search">
            <br>
        </form>
        
        <h2>Results</h2>
        <?php if (!empty($customers)): ?>
            <table>
                <tr>   
                    <th>Name</th>
                    <th>Email Address</th>
                    <th>City</th>
                    <th>Country</th>
                    <th></th>
                </tr>
                <?php foreach ($customers as $customer): ?>
                <tr>
                    <td><?php echo htmlspecialchars($customer['firstName'] . ' ' . $customer['lastName']); ?></td>
                    <td><?php echo htmlspecialchars($customer['email']); ?></td>
                    <td><?php echo htmlspecialchars($customer['city']); ?></td>
                    <td><?php echo htmlspecialchars($customer['countryName']); ?></td>
                    <td>
                        <!-- Edit button -->
                        <form action="select_customers_form.php" method="post">
                            <input type="hidden" name="customerID" value="<?php echo $customer['customerID']; ?>" />
                            <input type="submit" value="Select">
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No customers found with the last name '<?php echo htmlspecialchars($lastName); ?>'.</p>
        <?php endif; ?>
        <br/>
    </main>
    <footer><?php include 'view/footer.php'; ?></footer>
</body>
</html>
