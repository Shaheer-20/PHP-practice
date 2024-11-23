<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

// Handle form submissions for adding, editing, or deleting products
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add'])) {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $image = $_POST['image'];

        if ($stmt = $conn->prepare("INSERT INTO Products (name, description, price, image) VALUES (?, ?, ?, ?)")) {
            $stmt->bind_param("ssis", $name, $description, $price, $image);
            $stmt->execute();
            $stmt->close();
        }
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $image = $_POST['image'];

        if ($stmt = $conn->prepare("UPDATE Products SET name = ?, description = ?, price = ?, image = ? WHERE id = ?")) {
            $stmt->bind_param("ssisi", $name, $description, $price, $image, $id);
            $stmt->execute();
            $stmt->close();
        }
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];

        if ($stmt = $conn->prepare("DELETE FROM Products WHERE id = ?")) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Fetch all products from the database
$products = $conn->query("SELECT * FROM Products");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="fas fa-store me-2"></i>Admin Dashboard</a>
            <div class="d-flex align-items-center">
                <span class="text-white me-3"><i class="fas fa-user me-2"></i><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Manage Products</h1>

        <!-- Add Product Form -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5>Add New Product</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <input type="text" name="name" class="form-control" placeholder="Product Name" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <input type="text" name="description" class="form-control" placeholder="Description" required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <input type="number" step="0.01" name="price" class="form-control" placeholder="Price" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <input type="text" name="image" class="form-control" placeholder="Image URL">
                        </div>
                        <div class="col-md-1 mb-3">
                            <button type="submit" name="add" class="btn btn-success w-100">Add</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Products List -->
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h5>Product List</h5>
            </div>
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $products->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                                <td>$<?php echo number_format($row['price'], 2); ?></td>
                                <td><img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Product Image" style="width: 50px; height: 50px;"></td>
                                <td>
                                    <!-- Edit Button -->
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['id']; ?>">Edit</button>

                                    <!-- Delete Button -->
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product?')">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <button name="delete" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Product</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                <div class="mb-3">
                                                    <label>Product Name</label>
                                                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($row['name']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Description</label>
                                                    <input type="text" name="description" class="form-control" value="<?php echo htmlspecialchars($row['description']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Price</label>
                                                    <input type="number" step="0.01" name="price" class="form-control" value="<?php echo $row['price']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Image URL</label>
                                                    <input type="text" name="image" class="form-control" value="<?php echo htmlspecialchars($row['image']); ?>">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" name="edit" class="btn btn-primary">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
