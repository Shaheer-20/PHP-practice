<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add'])) {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $image = $_POST['image'];

        $stmt = $conn->prepare("INSERT INTO Products (name, description, price, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $name, $description, $price, $image);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $image = $_POST['image'];

        $stmt = $conn->prepare("UPDATE Products SET name = ?, description = ?, price = ?, image = ? WHERE id = ?");
        $stmt->bind_param("ssisi", $name, $description, $price, $image, $id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];

        $stmt = $conn->prepare("DELETE FROM Products WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}

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
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --success-color: #2ecc71;
            --warning-color: #f1c40f;
            --danger-color: #e74c3c;
            --light-gray: #f8f9fa;
            --dark-gray: #343a40;
        }

        body {
            background-color: var(--light-gray);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .navbar {
            background-color: var(--primary-color);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .sidebar {
            background-color: white;
            min-height: calc(100vh - 56px);
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            position: fixed;
            top: 56px;
            left: 0;
            width: 250px;
            z-index: 1000;
            padding-top: 20px;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }

        .card-header {
            background-color: white;
            border-bottom: 2px solid var(--secondary-color);
            padding: 1rem 1.5rem;
            font-weight: 600;
        }

        .stats-card {
            background: linear-gradient(45deg, var(--secondary-color), var(--primary-color));
            color: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 20px;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background-color: var(--light-gray);
            color: var(--dark-gray);
            font-weight: 600;
            border: none;
        }

        .table tbody td {
            vertical-align: middle;
            padding: 1rem;
        }

        .btn {
            border-radius: 5px;
            padding: 0.5rem 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-action {
            padding: 0.4rem 0.8rem;
            margin: 0 0.2rem;
        }

        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .modal-content {
            border-radius: 10px;
            border: none;
        }

        .modal-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 10px 10px 0 0;
        }

        .form-control {
            border-radius: 5px;
            border: 1px solid #dee2e6;
            padding: 0.6rem 1rem;
        }

        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .nav-link {
            color: var(--dark-gray);
            padding: 0.8rem 1.5rem;
            margin: 0.2rem 0;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background-color: var(--light-gray);
            color: var(--secondary-color);
        }

        .nav-link.active {
            background-color: var(--secondary-color);
            color: white;
        }

        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-store me-2"></i>Admin Dashboard
            </a>
            <div class="d-flex align-items-center">
                <span class="text-white me-3">
                    <i class="fas fa-user me-2"></i><?php echo htmlspecialchars($_SESSION['username']); ?>
                </span>
                <a href="logout.php" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="nav flex-column">
            <a href="#" class="nav-link active">
                <i class="fas fa-box me-2"></i>Products
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-chart-bar me-2"></i>Analytics
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-users me-2"></i>Users
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-cog me-2"></i>Settings
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Stats Cards -->
        <div class="row">
            <div class="col-md-4">
                <div class="stats-card">
                    <h3><?php echo $products->num_rows; ?></h3>
                    <p class="mb-0">Total Products</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <h3>$<?php 
                        $total = 0;
                        $products->data_seek(0);
                        while($row = $products->fetch_assoc()) {
                            $total += $row['price'];
                        }
                        echo number_format($total, 2);
                    ?></h3>
                    <p class="mb-0">Total Value</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <h3><?php echo date('F Y'); ?></h3>
                    <p class="mb-0">Current Period</p>
                </div>
            </div>
        </div>

        <!-- Add Product Card -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Add New Product</h5>
                <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    <i class="fas fa-plus me-2"></i>Add Product
                </button>
            </div>
        </div>

        <!-- Products Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Product List</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $products->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><img src="<?php echo $row['image']; ?>" alt="Product" class="product-image"><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                                <td>$<?php echo number_format($row['price'], 2); ?></td>
                                <td>
                                    <button class="btn btn-warning btn-action" data-bs-toggle="modal" data-bs-target="#editProductModal" data-id="<?php echo $row['id']; ?>" data-name="<?php echo htmlspecialchars($row['name']); ?>" data-description="<?php echo htmlspecialchars($row['description']); ?>" data-price="<?php echo $row['price']; ?>" data-image="<?php echo htmlspecialchars($row['image']); ?>">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form action="" method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <button class="btn btn-danger btn-action" name="delete">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Product Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" class="form-control" name="price" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Product Image URL</label>
                            <input type="text" class="form-control" name="image" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="add">Add Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="editProductId">
                        <div class="mb-3">
                            <label for="editName" class="form-label">Product Name</label>
                            <input type="text" class="form-control" name="name" id="editName" required>
                        </div>
                        <div class="mb-3">
                            <label for="editDescription" class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="editDescription" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="editPrice" class="form-label">Price</label>
                            <input type="number" class="form-control" name="price" id="editPrice" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="editImage" class="form-label">Product Image URL</label>
                            <input type="text" class="form-control" name="image" id="editImage" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-warning" name="edit">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript and Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Pre-fill the edit modal with product data
        var editProductModal = document.getElementById('editProductModal');
        editProductModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            var name = button.getAttribute('data-name');
            var description = button.getAttribute('data-description');
            var price = button.getAttribute('data-price');
            var image = button.getAttribute('data-image');

            var modalId = editProductModal.querySelector('#editProductId');
            var modalName = editProductModal.querySelector('#editName');
            var modalDescription = editProductModal.querySelector('#editDescription');
            var modalPrice = editProductModal.querySelector('#editPrice');
            var modalImage = editProductModal.querySelector('#editImage');

            modalId.value = id;
            modalName.value = name;
            modalDescription.value = description;
            modalPrice.value = price;
            modalImage.value = image;
        });
    </script>
</body>
</html>
