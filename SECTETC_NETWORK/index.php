<?php
session_start();

require_once "config/database.php";

// Create shopping cart if it does not exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add product to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {

    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);

    if ($product_id) {

        $stmt = $conn->prepare("
            SELECT id, product_name, price, stock_quantity
            FROM products
            WHERE id = ? AND status = 'available'
        ");

        $stmt->execute([$product_id]);
        $product = $stmt->fetch();

        if ($product && $product['stock_quantity'] > 0) {

            $current_quantity = $_SESSION['cart'][$product_id]['quantity'] ?? 0;

            if ($current_quantity < $product['stock_quantity']) {

                $_SESSION['cart'][$product_id] = [
                    'product_id' => $product['id'],
                    'product_name' => $product['product_name'],
                    'price' => $product['price'],
                    'quantity' => $current_quantity + 1
                ];

                header("Location: index.php?added=1");
                exit;

            } else {
                header("Location: index.php?stock=1");
                exit;
            }
        }
    }

    header("Location: index.php");
    exit;
}

// Count cart items
$cartCount = 0;

foreach ($_SESSION['cart'] as $item) {
    $cartCount += $item['quantity'];
}

// Search and category filters
$search = trim($_GET['search'] ?? '');
$category_id = filter_input(INPUT_GET, 'category', FILTER_VALIDATE_INT);

$sql = "
    SELECT products.*, categories.category_name
    FROM products
    LEFT JOIN categories
    ON products.category_id = categories.id
    WHERE products.status = 'available'
";

$params = [];

if ($search !== '') {
    $sql .= " AND products.product_name LIKE ?";
    $params[] = "%$search%";
}

if ($category_id) {
    $sql .= " AND products.category_id = ?";
    $params[] = $category_id;
}

$sql .= " ORDER BY products.id DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Get categories
$categoryStmt = $conn->query("
    SELECT * FROM categories
    ORDER BY category_name ASC
");

$categories = $categoryStmt->fetchAll();

// Total products
$totalProducts = count($products);

// Safely display text
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// Display product image
function productImage($image) {

    if (!empty($image)) {

        $image = str_replace('\\', '/', $image);
        $image = ltrim($image, '/');

        if (strpos($image, 'assets/') === 0) {
            return $image;
        }

        return 'assets/uploads/' . rawurlencode(basename($image));
    }

    return '';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>SECTEC NETWORK LTD | Online Shop</title>

<style>

/* GENERAL */

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, Helvetica, sans-serif;
    background: #f5f7fa;
    color: #222;
}

a {
    text-decoration: none;
}

/* TOP BAR */

.top-bar {
    background: #071b36;
    color: white;
    padding: 10px 6%;
    display: flex;
    justify-content: space-between;
    font-size: 13px;
    flex-wrap: wrap;
    gap: 8px;
}

/* HEADER */

header {
    background: white;
    padding: 18px 6%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    flex-wrap: wrap;
    gap: 15px;
}

.logo {
    font-size: 25px;
    font-weight: bold;
    color: #0875c9;
}

.logo span {
    color: #071b36;
}

nav {
    display: flex;
    gap: 25px;
    align-items: center;
    flex-wrap: wrap;
}

nav a {
    color: #333;
    font-weight: 600;
    font-size: 14px;
}

nav a:hover {
    color: #0875c9;
}

.cart {
    background: #0875c9;
    color: white;
    padding: 12px 20px;
    border-radius: 5px;
}

/* HERO SECTION */

.hero {
    background: linear-gradient(120deg, #071b36, #0875c9);
    color: white;
    padding: 75px 8%;
    min-height: 350px;
    display: flex;
    align-items: center;
}

.hero-content {
    max-width: 650px;
}

.hero h1 {
    font-size: 42px;
    margin-bottom: 20px;
    line-height: 1.2;
}

.hero p {
    font-size: 17px;
    line-height: 1.7;
    margin-bottom: 25px;
    color: #e3efff;
}

.hero-button {
    display: inline-block;
    background: #ffb703;
    color: #071b36;
    padding: 14px 25px;
    border-radius: 5px;
    font-weight: bold;
}

.hero-button:hover {
    background: white;
}

/* FEATURES */

.features {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    padding: 35px 6%;
}

.feature {
    background: white;
    padding: 22px;
    text-align: center;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.feature h3 {
    color: #0875c9;
    margin-bottom: 8px;
}

.feature p {
    color: #666;
    font-size: 14px;
}

/* MAIN CONTENT */

.container {
    padding: 35px 6%;
}

.section-title {
    text-align: center;
    margin-bottom: 30px;
}

.section-title h2 {
    font-size: 30px;
    color: #071b36;
    margin-bottom: 10px;
}

.section-title p {
    color: #666;
}

/* CATEGORIES */

.categories {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 35px;
}

.category-btn {
    background: white;
    color: #071b36;
    border: 1px solid #ddd;
    padding: 11px 18px;
    border-radius: 25px;
    font-size: 13px;
    font-weight: bold;
}

.category-btn:hover,
.category-btn.active {
    background: #0875c9;
    color: white;
    border-color: #0875c9;
}

/* SEARCH */

.search-box {
    display: flex;
    justify-content: center;
    margin-bottom: 35px;
    gap: 8px;
}

.search-box input {
    width: 350px;
    max-width: 70%;
    padding: 14px;
    border: 1px solid #ccc;
    border-radius: 5px;
    outline: none;
}

.search-box button {
    padding: 14px 22px;
    border: none;
    background: #0875c9;
    color: white;
    border-radius: 5px;
    cursor: pointer;
}

/* PRODUCT GRID */

.product-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 22px;
}

.product-card {
    background: white;
    border-radius: 9px;
    overflow: hidden;
    box-shadow: 0 3px 12px rgba(0,0,0,0.07);
    transition: 0.3s;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}

.product-image {
    height: 200px;
    background: #edf2f7;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.no-image {
    font-size: 45px;
    color: #9aa9b8;
}

.product-info {
    padding: 18px;
}

.product-info h3 {
    font-size: 16px;
    color: #071b36;
    min-height: 40px;
    margin-bottom: 10px;
}

.product-category {
    font-size: 12px;
    color: #0875c9;
    margin-bottom: 10px;
}

.price {
    color: #0875c9;
    font-size: 21px;
    font-weight: bold;
    margin-bottom: 10px;
}

.stock {
    font-size: 12px;
    color: #238636;
    margin-bottom: 15px;
}

.add-cart {
    display: block;
    width: 100%;
    background: #0875c9;
    color: white;
    padding: 12px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
}

.add-cart:hover {
    background: #071b36;
}

.no-products {
    text-align: center;
    padding: 50px;
    background: white;
    border-radius: 8px;
    color: #666;
}

/* NOTIFICATIONS */

.message {
    max-width: 900px;
    margin: 20px auto;
    padding: 15px;
    text-align: center;
    border-radius: 5px;
    background: #d8f3dc;
    color: #176b32;
}

.warning {
    background: #fff3cd;
    color: #856404;
}

/* ABOUT */

.about {
    background: white;
    padding: 55px 8%;
    text-align: center;
    margin-top: 40px;
}

.about h2 {
    color: #071b36;
    font-size: 28px;
    margin-bottom: 18px;
}

.about p {
    max-width: 850px;
    margin: auto;
    color: #666;
    line-height: 1.8;
}

/* FOOTER */

footer {
    background: #071b36;
    color: white;
    padding: 40px 6% 20px;
}

.footer-content {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
}

.footer-content h3 {
    margin-bottom: 15px;
    color: #ffb703;
}

.footer-content p {
    color: #d0dbea;
    font-size: 14px;
    line-height: 1.8;
}

.copyright {
    text-align: center;
    border-top: 1px solid #34465c;
    margin-top: 30px;
    padding-top: 20px;
    font-size: 13px;
    color: #d0dbea;
}

/* RESPONSIVE DESIGN */

@media(max-width: 1000px) {

    .product-grid {
        grid-template-columns: repeat(3, 1fr);
    }

}

@media(max-width: 700px) {

    header {
        flex-direction: column;
    }

    nav {
        justify-content: center;
        gap: 15px;
    }

    .hero {
        padding: 55px 7%;
    }

    .hero h1 {
        font-size: 32px;
    }

    .product-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .features {
        grid-template-columns: 1fr;
    }

    .footer-content {
        grid-template-columns: 1fr;
    }

}

@media(max-width: 450px) {

    .product-grid {
        grid-template-columns: 1fr;
    }

}

</style>

</head>

<body>

<!-- TOP BAR -->

<div class="top-bar">

    <div>Welcome to SECTEC NETWORK LTD</div>

    <div>Call Us: +254 796 444 496</div>

</div>


<!-- HEADER -->

<header>

    <div class="logo">
        SECTEC <span>NETWORK</span>
    </div>

    <nav>

        <a href="index.php">Home</a>

        <a href="#products">Products</a>

        <a href="#about">About Us</a>

        <a href="#contact">Contact</a>

        <a href="cart.php" class="cart">
            🛒 Cart (<?= $cartCount ?>)
        </a>

        <a href="admin/login.php">Admin</a>

    </nav>

</header>


<!-- HERO SECTION -->

<section class="hero">

    <div class="hero-content">

        <h1>
            Smart Technology Solutions for Your Home and Business
        </h1>

        <p>
            Welcome to SECTEC NETWORK LTD. Your trusted supplier of
            fibre accessories, CCTV cameras, solar lighting,
            water heaters and power backup systems.
        </p>

        <a href="#products" class="hero-button">
            Explore Our Products
        </a>

    </div>

</section>


<!-- FEATURES -->

<section class="features">

    <div class="feature">

        <h3>Quality Products</h3>

        <p>Reliable technology products for your needs.</p>

    </div>

    <div class="feature">

        <h3>Affordable Prices</h3>

        <p>Competitive prices on our range of products.</p>

    </div>

    <div class="feature">

        <h3>Customer Support</h3>

        <p>We are ready to assist you with your purchases.</p>

    </div>

</section>


<!-- NOTIFICATIONS -->

<?php if (isset($_GET['added'])): ?>

    <div class="message">
        Product successfully added to your shopping cart!
    </div>

<?php endif; ?>


<?php if (isset($_GET['stock'])): ?>

    <div class="message warning">
        You cannot add more than the available stock.
    </div>

<?php endif; ?>


<!-- PRODUCTS SECTION -->

<section class="container" id="products">

    <div class="section-title">

        <h2>Our Products</h2>

        <p>
            Explore our range of quality technology products.
        </p>

    </div>


    <!-- CATEGORIES -->

    <div class="categories">

        <a href="index.php"
           class="category-btn <?= !$category_id ? 'active' : '' ?>">
            All Products
        </a>

        <?php foreach ($categories as $category): ?>

            <a href="index.php?category=<?= $category['id'] ?>#products"
               class="category-btn <?= $category_id == $category['id'] ? 'active' : '' ?>">

                <?= e($category['category_name']) ?>

            </a>

        <?php endforeach; ?>

    </div>


    <!-- SEARCH -->

    <form method="GET" action="index.php" class="search-box">

        <?php if ($category_id): ?>

            <input type="hidden" name="category"
                   value="<?= $category_id ?>">

        <?php endif; ?>

        <input
            type="text"
            name="search"
            placeholder="Search for products..."
            value="<?= e($search) ?>"
        >

        <button type="submit">
            Search
        </button>

    </form>


    <!-- PRODUCT COUNT -->

    <p style="margin-bottom:20px;color:#666;">

        Showing <?= $totalProducts ?> product(s)

    </p>


    <!-- PRODUCT DISPLAY -->

    <?php if (count($products) > 0): ?>

        <div class="product-grid">

            <?php foreach ($products as $product): ?>

                <div class="product-card">

                    <div class="product-image">

                        <?php $image = productImage($product['product_image']); ?>

                        <?php if ($image): ?>

                            <img
                                src="<?= e($image) ?>"
                                alt="<?= e($product['product_name']) ?>"
                                onerror="this.style.display='none';this.nextElementSibling.style.display='block';"
                            >

                            <div class="no-image" style="display:none;">
                                📦
                            </div>

                        <?php else: ?>

                            <div class="no-image">
                                📦
                            </div>

                        <?php endif; ?>

                    </div>


                    <div class="product-info">

                        <div class="product-category">

                            <?= e($product['category_name'] ?? 'General') ?>

                        </div>


                        <h3>

                            <?= e($product['product_name']) ?>

                        </h3>


                        <div class="price">

                            KSh <?= number_format($product['price'], 2) ?>

                        </div>


                        <div class="stock">

                            <?php if ($product['stock_quantity'] > 0): ?>

                                In Stock: <?= (int)$product['stock_quantity'] ?>

                            <?php else: ?>

                                Out of Stock

                            <?php endif; ?>

                        </div>


                        <?php if ($product['stock_quantity'] > 0): ?>

                            <form method="POST" action="index.php">

                                <input
                                    type="hidden"
                                    name="product_id"
                                    value="<?= $product['id'] ?>"
                                >

                                <button
                                    type="submit"
                                    name="add_to_cart"
                                    class="add-cart"
                                >

                                    🛒 Add to Cart

                                </button>

                            </form>

                        <?php else: ?>

                            <button class="add-cart" disabled
                                    style="background:#999;">

                                Out of Stock

                            </button>

                        <?php endif; ?>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php else: ?>

        <div class="no-products">

            <h3>No Products Found</h3>

            <p>
                Try another search term or select a different category.
            </p>

            <br>

            <a href="index.php" class="hero-button">
                View All Products
            </a>

        </div>

    <?php endif; ?>

</section>


<!-- ABOUT SECTION -->

<section class="about" id="about">

    <h2>About SECTEC NETWORK LTD</h2>

    <p>

        SECTEC NETWORK LTD is committed to providing reliable
        technology solutions and quality products. We specialize
        in fibre accessories, CCTV security systems, solar lighting,
        water heating solutions and power backup systems.

        Our goal is to provide customers with dependable products
        at competitive prices.

    </p>

</section>


<!-- FOOTER -->

<footer id="contact">

    <div class="footer-content">

        <div>

            <h3>HINET SOLUTIONS</h3>

            <p>
                Your trusted technology solutions provider.
            </p>

        </div>


        <div>

            <h3>Contact Information</h3>

            <p>

                Phone: +254 796 444 496

                <br>

                Email: sectecnetwork@gmail.com

                <br>

                Location: Montana Mall, 1st Floor M02,
                Along Luthuli Avenue, Nairobi.

            </p>

        </div>


        <div>

            <h3>Business Hours</h3>

            <p>

                Monday - Saturday: 8:00 AM - 6:00 PM

                <br>

                Sunday: Closed

            </p>

        </div>

    </div>


    <div class="copyright">

        &copy; <?= date('Y') ?> SECTEC NETWORK LTD.
        All Rights Reserved.

    </div>

</footer>

</body>

</html>