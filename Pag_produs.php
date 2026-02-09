<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toy Store</title>
    <link rel="stylesheet" href="Stil_pag_prod.css">
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
            <li class="dropdown">
            <a href="javascript:void(0)" class="dropbtn"></a>
            <div class="dropdown-content">
                <a href="cart.php">Cos de Cumpărături</a>
                <a href="contul-meu.html">Contul Meu</a>
                <a href="politici.html">Politici</a>
                <a href="termeni.html">Termeni și Condiții</a>
                <a href="contact.html">Contact</a>
            </div>
        </li>
        </ul>
    </nav>
    <form action="Produse.php" method="get" id="search-form">
        <div class="search-box" id="search-box">
            <input type="text" name="search" placeholder="Cauta..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit">Cauta</button>
        </div>
    </form>

    <div class="container">
    <?php
include 'conexiune.php';

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id > 0) {
    $sql_product = "SELECT p.product_name, p.product_price, p.product_stock, pd.product_description, pd.color, pd.recommended_age, pd.material
                    FROM products p
                    LEFT JOIN product_details pd ON p.product_id = pd.product_id
                    WHERE p.product_id = ?";

    $stmt_product = $conn->prepare($sql_product);
    $stmt_product->bind_param('i', $product_id);
    $stmt_product->execute();
    $result_product = $stmt_product->get_result();

    if ($result_product->num_rows > 0) {
        $product = $result_product->fetch_assoc();

        $sql_images = "SELECT image_filename FROM product_images WHERE product_id = ?";
        $stmt_images = $conn->prepare($sql_images);
        $stmt_images->bind_param('i', $product_id);
        $stmt_images->execute();
        $result_images = $stmt_images->get_result();

        $images = [];
        while ($row = $result_images->fetch_assoc()) {
            $images[] = $row['image_filename'];
        }

        echo "<div class='Chenar_Produs'>";
        
        echo "<div class='image-wrapper'>";
        echo "<img id='main-image' src='images/" . htmlspecialchars($images[0]) . "' alt='" . htmlspecialchars($product['product_name']) . "' onclick='openFullscreen(this)'>";
        
        if (!empty($images)) {
            echo "<div class='image-thumbnails'>";
            foreach ($images as $image) {
                echo "<img src='images/" . htmlspecialchars($image) . "' class='thumbnail' onclick='changeImage(\"images/" . htmlspecialchars($image) . "\")'>";
            }
            echo "</div>"; 
        } else {
            echo "<p>Nu există imagini disponibile pentru acest produs.</p>";
        }
        echo "</div>";
        
        echo "<div class='product-details'>";
        echo "<h1>" . htmlspecialchars($product['product_name']) . "</h1>";
        echo "<p>Preț: " . number_format($product['product_price'] / 100, 2) . " RON</p>";
        echo "<p>Culoare: " . htmlspecialchars($product['color']) . "</p>";
        echo "<p>Material: " . htmlspecialchars($product['material']) . "</p>";
        echo "<p>Vârstă Recomandată: " . htmlspecialchars($product['recommended_age']) . "</p>";
        echo "<div class='product-description'>";
        echo "<p>" . htmlspecialchars($product['product_description']) . "</p>";
        echo "</div>"; 

        if ($product['product_stock'] > 0) {
            echo "<form action='add_to_cart.php' method='post' class='add-to-cart-form'>";
            echo "<input type='hidden' name='product_id' value='" . htmlspecialchars($product_id) . "'>";
            echo "<input type='hidden' name='product_name' value='" . htmlspecialchars($product['product_name']) . "'>";
            echo "<input type='hidden' name='product_price' value='" . htmlspecialchars($product['product_price']) . "'>";
            echo "<button type='submit' class='btn-add-to-cart'>Adaugă în coș</button>";
            echo "</form>";
        } else {
            echo "<button class='btn-out-of-stock' disabled>Stoc epuizat</button>";
        }
        echo "</div>";
        echo "</div>";
    } else {
        echo "Produsul nu a fost găsit.";
    }
} else {
    echo "Produs invalid.";
}

$conn->close();
?>
        <?php if (isset($_GET['added']) && $_GET['added'] == 1): ?>
            <div id="notification" class="notification">
                Produsul a fost adăugat în coș!
            </div>
        <?php endif; ?>

    </div>
    <a href="cart.php" class="shopping-cart-btn">
        <img src="IMGstatic/cart.jpg" alt="Coș de cumpărături">
    </a>    
    <script src="script_pagP.js"></script>
    <script src="script_filter.js"></script>
    <script src="notificare.js"></script>
    <script src="dropdown.js"></script>
    <script>
        function changeImage(imageUrl) {
            document.getElementById('main-image').src = imageUrl;
        }
    </script>

<script>
function changeImage(imageSrc) {
    document.getElementById('main-image').src = imageSrc;
}

function openFullscreen(image) {
    let bg = document.createElement('div');
    bg.className = 'fullscreen-background';
    document.body.appendChild(bg);

    let fullscreenImg = document.createElement('img');
    fullscreenImg.src = image.src;
    fullscreenImg.className = 'fullscreen-image show';
    document.body.appendChild(fullscreenImg);

    let closeButton = document.createElement('button');
    closeButton.className = 'fullscreen-close';
    closeButton.innerHTML = '&times;';
    closeButton.onclick = closeFullscreen;
    document.body.appendChild(closeButton);
}

function closeFullscreen() {
    document.querySelectorAll('.fullscreen-image, .fullscreen-background, .fullscreen-close').forEach(el => el.remove());
}
</script>



</body>
</html>
