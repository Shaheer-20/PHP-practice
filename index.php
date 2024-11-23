<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Online Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --accent-color: #3498db;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background-color: var(--primary-color) !important;
            padding: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            color: white !important;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .navbar-text {
            color: #ecf0f1 !important;
        }

        .hero-section {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }

        .card {
            border: none;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        }

        .card-img-top {
            height: 200px;
            object-fit: cover;
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-title {
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .price {
            font-size: 1.25rem;
            color: var(--accent-color);
            font-weight: bold;
        }

        .btn-primary {
            background-color: var(--accent-color);
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #2980b9;
            transform: scale(1.05);
        }

        .btn-danger {
            border-radius: 25px;
            padding: 0.5rem 1.5rem;
        }

        .filters {
            margin-bottom: 2rem;
        }

        .search-box {
            position: relative;
            margin-bottom: 1rem;
        }

        .search-box input {
            padding-left: 2.5rem;
            border-radius: 25px;
            border: 1px solid #ddd;
        }

        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-store me-2"></i>Modern Shop
            </a>
            <div class="d-flex align-items-center">
                <div class="navbar-text me-3">
                    <i class="fas fa-user-circle me-2"></i>
                    <?php echo htmlspecialchars($_SESSION['username']); ?>
                </div>
                <a href="logout.php" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="hero-section">
        <div class="container text-center">
            <h1 class="display-4">Welcome to Modern Shop</h1>
            <p class="lead">Discover our amazing collection of products</p>
        </div>
    </div>

    <div class="container">
        <div class="filters">
            <div class="row">
                <div class="col-md-6">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" class="form-control" placeholder="Search products...">
                    </div>
                </div>
                <div class="col-md-6">
                    <select class="form-select">
                        <option selected>Sort by</option>
                        <option value="price-low">Price: Low to High</option>
                        <option value="price-high">Price: High to Low</option>
                        <option value="newest">Newest First</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <?php
            $result = $conn->query("SELECT * FROM Products");
            
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="col-md-4 mb-4">';
                    echo '    <div class="card h-100">';
                    echo '        <img src="' . htmlspecialchars($row['image']) . '" class="card-img-top" alt="' . htmlspecialchars($row['name']) . '">';
                    echo '        <div class="card-body d-flex flex-column">';
                    echo '            <h5 class="card-title">' . htmlspecialchars($row['name']) . '</h5>';
                    echo '            <p class="card-text flex-grow-1">' . htmlspecialchars($row['description']) . '</p>';
                    echo '            <div class="d-flex justify-content-between align-items-center mt-3">';
                    echo '                <span class="price">$' . htmlspecialchars($row['price']) . '</span>';
                    echo '                <a href="product_details.php?id=' . htmlspecialchars($row['id']) . '" class="btn btn-primary">';
                    echo '                    <i class="fas fa-eye me-2"></i>View Details';
                    echo '                </a>';
                    echo '            </div>';
                    echo '        </div>';
                    echo '    </div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="col-12 text-center">';
                echo '    <div class="alert alert-info">';
                echo '        <i class="fas fa-info-circle me-2"></i>No products available at the moment.';
                echo '    </div>';
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>