<?php 



ini_set('display_errors', 1);

error_reporting(E_ALL); // Enable all errors



$link = mysqli_connect('localhost', 'portalsemiconind_db', '9J#P+7Uv2OYE', 'portalsemiconind_db', 3306);



if (!$link) {

    die("Not connected: " . mysqli_connect_error()); // Corrected error handling

} else {

    echo "Connected successfully";

}

// create a table
/* CREATE TABLE payment_gateway_response (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    order_id VARCHAR(50) NOT NULL,
    payment_id VARCHAR(100) NULL,
    invoice_id VARCHAR(100)  NULL,
    currency VARCHAR(10) NOT NULL,
    gateway VARCHAR(50) NULL,
    amount DECIMAL(10,2) NOT NULL,
    amount_received DECIMAL(10,2) NULL,
    transaction_id VARCHAR(100) NULL,
    reference_id VARCHAR(100)  NULL,
    email VARCHAR(255) NOT NULL,
    status VARCHAR(120) NULL,
    response_json JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


using the above query:
*/


// create a table
$query = "CREATE TABLE payment_gateway_response (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(50) NOT NULL,
    payment_id VARCHAR(100) NULL,
    invoice_id VARCHAR(100)  NULL,
    currency VARCHAR(10) NOT NULL,
    gateway VARCHAR(50) NULL,
    amount DECIMAL(10,2) NOT NULL,
    amount_received DECIMAL(10,2) NULL,
    transaction_id VARCHAR(100) NULL,
    reference_id VARCHAR(100)  NULL,
    email VARCHAR(255) NOT NULL,
    status VARCHAR(120) NULL,
    response_json JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if (mysqli_query($link, $query)) {
    echo "Table payment_gateway_response created successfully";
} else {
    echo "Error creating table: " . mysqli_error($link);
}
