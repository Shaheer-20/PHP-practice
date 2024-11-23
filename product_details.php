<?php
include 'db_connect.php';
$id = $_GET['id'];
$result = $conn->query("SELECT * FROM Products WHERE id = $id");
$product = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 class="my-4"><?php echo $product['name']; ?></h1>
        <img src="<?php echo $product['image']; ?>" class="img-fluid mb-4" alt="<?php echo $product['name']; ?>">
        <p><?php echo $product['description']; ?></p>
        <p><strong>Price: $<?php echo $product['price']; ?></strong></p>
        <a href="index.php" class="btn btn-secondary">Back to Home</a>
    </div>
</body>
</html>
