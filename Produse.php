<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toy Store</title>
    <link rel="stylesheet" href="Stil_PCatalog.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="info-bar">
        <p>Livrare gratuita pentru comenzi de peste ... lei&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Expediere in 24-48h</p>
    </div>
    <nav>
        <ul>
            <li><a href="#Deschide Cautare" id="search-link"><div class="text-nav">Cautare</div></a></li>
            <li>
                <a href="Site.html">
                    <div class="text-nav">
                    <img src="IMGstatic/file-VC2LZR86tYByQtYYTmsNQUrA.png" alt="Logo" class="logo-image">
                    </div>
                </a>
            </li>
            <li><a href="Contact.html"><div class="text-nav">Contact</div></a></li>
        </ul>
    </nav>
    <form action="Produse.php" method="get" id="search-form">
        <div class="search-box" id="search-box">
            <input type="text" name="search" placeholder="Cauta..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit">Cauta</button>
        </div>
    </form>

     <div class="container">
        <div class="filter-box" id="filterBox">
                    <h2 align="center">
                        <a href="javascript:void(0);" id="disableFilter">
                            <div class="text-stylish">Filtre</div>
                        </a>
                    </h2>
                    <form method="get" action="Produse.php">
                        <?php
                        include 'conexiune.php';

                        $search = isset($_GET['search']) ? $_GET['search'] : '';
                        $categories = isset($_GET['categories']) ? $_GET['categories'] : [];
                        $gender = isset($_GET['gender']) ? $_GET['gender'] : '';

                        echo '<input type="hidden" name="search" value="' . htmlspecialchars($search) . '">';
                        echo '<input type="hidden" name="gender" value="' . htmlspecialchars($gender) . '">';

                        $sql = "SELECT DISTINCT pc.product_category_id, pc.category_name
                                FROM product_category pc
                                LEFT JOIN category_gender_link cgl ON pc.product_category_id = cgl.product_category_id
                                LEFT JOIN product_gender pg ON cgl.product_gender_id = pg.product_gender_id
                                WHERE 1=1";

                        if ($gender) {
                            $sql .= " AND pg.product_gender_id = ?";
                        }

                        $stmt = $conn->prepare($sql);

                        if ($gender) {
                            $stmt->bind_param('i', $gender);
                        }

                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            echo "<div class='filter-category'>";
                            echo "<h3>Categorii</h3>";
                            while ($row = $result->fetch_assoc()) {
                                echo "<div>";
                                echo "<input type='checkbox' name='categories[]' value='" . $row['product_category_id'] . "'";
                                if (in_array($row['product_category_id'], $categories)) {
                                    echo " checked";
                                }
                                echo ">";
                                echo "<label>" . $row['category_name'] . "</label>";
                                echo "</div>";
                            }
                            echo "</div>";
                        } else {
                            echo "Nu există categorii pentru genul selectat.";
                        }

                        $conn->close();
                        ?>
                        <button type="submit">Aplica Filtre</button>
                    </form>
                </div>
                <div class="catalog_prod" id="catalogProd">
            <div class="filter-text">
                <div class="text-stylish">
                    <h2>
                        <a href="javascript:void(0);" id="toggleFilter">Filtre</a>
                    </h2>
                </div>
            </div>
            <?php
                include 'conexiune.php';
                $search = isset($_GET['search']) ? $_GET['search'] : '';
                $categories = isset($_GET['categories']) ? $_GET['categories'] : [];
                $gender = isset($_GET['gender']) ? $_GET['gender'] : '';
                $bind_types = '';
                $bind_params = [];

                $sql = "SELECT p.product_id, p.product_name, p.product_price, pi.image_filename
                        FROM products p
                        LEFT JOIN (
                            SELECT product_id, MIN(image_id) as min_image_id
                            FROM product_images
                            GROUP BY product_id
                        ) temp ON p.product_id = temp.product_id
                        LEFT JOIN product_images pi ON temp.min_image_id = pi.image_id
                        LEFT JOIN category_gender_link cgl ON p.product_category_id = cgl.product_category_id
                        LEFT JOIN product_gender pg ON cgl.product_gender_id = pg.product_gender_id
                        WHERE 1=1";

                if ($search) {
                    $sql .= " AND p.product_name LIKE ?";
                    $bind_types .= 's';
                    $bind_params[] = "%$search%";
                }

                if ($gender) {
                    $sql .= " AND pg.product_gender_id = ?";
                    $bind_types .= 'i';
                    $bind_params[] = $gender;
                }

                if (!empty($categories)) {
                    $category_ids_placeholder = implode(',', array_fill(0, count($categories), '?'));
                    $sql .= " AND p.product_category_id IN ($category_ids_placeholder)";
                    $bind_types .= str_repeat('i', count($categories));
                    $bind_params = array_merge($bind_params, $categories);
                }

                $stmt = $conn->prepare($sql);

                if ($bind_types) {
                    $stmt->bind_param($bind_types, ...$bind_params);
                }

                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Obține stocul produsului
                        $product_id = $row['product_id'];
                        $query_stock = "SELECT product_stock FROM products WHERE product_id = ?";
                        $stmt_stock = $conn->prepare($query_stock);
                        $stmt_stock->bind_param('i', $product_id);
                        $stmt_stock->execute();
                        $result_stock = $stmt_stock->get_result();
                        $stock_data = $result_stock->fetch_assoc();
                        $product_stock = $stock_data['product_stock'];
                
                        echo "<div class='product";
                        if ($product_stock == 0) {
                            echo " out-of-stock";
                        }
                        echo "'>";
                
                        if ($product_stock > 0) {
                            echo "<a href='Pag_produs.php?id=" . $row['product_id'] . "'>";
                        }
                        
                        echo "<img src='images/" . htmlspecialchars($row["image_filename"]) . "' alt='" . htmlspecialchars($row["product_name"]) . "'>";
                        echo "<h2>" . htmlspecialchars($row["product_name"]) . "</h2>";
                        echo "<p>Preț: " . number_format($row["product_price"] / 100, 2) . " RON</p>";
                        
                        if ($product_stock > 0) {
                            echo "</a>";
                            echo "<form action='add_to_cart.php' method='post' class='add-to-cart-form'>";
                            echo "<input type='hidden' name='product_id' value='" . htmlspecialchars($row['product_id']) . "'>";
                            echo "<input type='hidden' name='product_name' value='" . htmlspecialchars($row['product_name']) . "'>";
                            echo "<input type='hidden' name='product_price' value='" . htmlspecialchars($row['product_price']) . "'>";
                            echo "<button type='submit' class='btn-add-to-cart'>Adaugă în coș</button>";
                            echo "</form>";
                        } else {
                            echo "<p>Stoc Epuizat</p>";
                        }
                        
                        
                        echo "</div>";
                    }
                }

                $conn->close();
            ?>
        </div>
    </div>

    <div>
        <?php if (isset($_GET['added']) && $_GET['added'] == 1): ?>
            <div id="notification" class="notification">
                Produsul a fost adăugat în coș!
            </div>
        <?php endif; ?>

    </div>

    <a href="cart.php" class="shopping-cart-btn">
        <img src="IMGstatic/cart.jpg" alt="Coș de cumpărături">
    </a>    
    <script src ="notificare.js"></script>
    <script src="script_pagP.js"></script>
    <script src="script_filter.js"></script>
</body>
</html>
