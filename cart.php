<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coș de Cumpărături</title>
    <link rel="stylesheet" href="Stil_cart.css">
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
            <input type="text" name="search" placeholder="Cauta...">
            <button type="submit">Cauta</button>
        </div>
    </form>
    <div class="container">
    <h1>Coșul de Cumpărături</h1>
    <?php
include 'conexiune.php';

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $cart = $_SESSION['cart'];
    $product_ids = array_keys($cart);

    if (!empty($product_ids)) {
        $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
        $sql = "SELECT product_id, product_stock FROM products WHERE product_id IN ($placeholders)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(str_repeat('i', count($product_ids)), ...$product_ids);
        $stmt->execute();
        $result = $stmt->get_result();
        $stock_data = [];

        while ($row = $result->fetch_assoc()) {
            $stock_data[$row['product_id']] = $row['product_stock'];
        }

        $updated_cart = [];
        foreach ($cart as $product_id => $product) {
            if (isset($stock_data[$product_id]) && $stock_data[$product_id] >= $product['quantity']) {
                $updated_cart[$product_id] = $product;
            }
        }

        $_SESSION['cart'] = $updated_cart;

        if (count($cart) != count($updated_cart)) {
            echo "<p>Unele produse nu erau disponibile în stoc și au fost eliminate automat din coșul dvs.</p>";
        }
    }

    if (!empty($_SESSION['cart'])) {
        echo "<form action='cart.php' method='post'>";
        echo "<table>";
        echo "<thead><tr><th>Produs</th><th>Preț</th><th>Cantitate</th><th>Total</th><th>Acțiuni</th></tr></thead>";
        echo "<tbody>";

        foreach ($_SESSION['cart'] as $product_id => $product) {
            $product_total = $product['price'] * $product['quantity'];
            echo "<tr>";
            echo "<td><a href='Pag_produs.php?id=" . htmlspecialchars($product_id) . "'>" . htmlspecialchars($product['name']) . "</a></td>";
            echo "<td>" . number_format($product['price'] / 100, 2) . " RON</td>";
            echo "<td><input type='number' name='quantity[" . htmlspecialchars($product_id) . "]' value='" . htmlspecialchars($product['quantity']) . "' min='1'></td>";
            echo "<td>" . number_format($product_total / 100, 2) . " RON</td>";
            echo "<td><a href='cart.php?action=remove&product_id=" . htmlspecialchars($product_id) . "'>Șterge</a></td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "<tfoot><tr><td colspan='3'>Total:</td><td>" . number_format(array_sum(array_map(function($p) { return $p['price'] * $p['quantity']; }, $_SESSION['cart'])) / 100, 2) . " RON</td></tr></tfoot>";
        echo "</table>";
        echo "<input type='hidden' name='action' value='update'>";
        echo "<button type='submit'>Actualizează</button>";
        echo "</form>";
        echo "<a href='checkout.php' class='btn-checkout'>Finalizează comanda</a>";
    } else {
        echo "<p>Coșul tău este gol.</p>";
    }
} else {
    echo "<p>Coșul tău este gol.</p>";
}

$conn->close();
?>

</div>
    <?php

if (isset($_POST['action']) && $_POST['action'] == 'update') {
    if (isset($_SESSION['cart'])) {
        foreach ($_POST['quantity'] as $product_id => $quantity) {
            $product_id = intval($product_id);
            $quantity = intval($quantity);

            if ($quantity > 0) {
                $_SESSION['cart'][$product_id]['quantity'] = $quantity;
            } else {
                unset($_SESSION['cart'][$product_id]);
            }
        }
    }
    header('Location: cart.php');
    exit();
}

if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']);
    
    if (isset($_SESSION['cart'])) {
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
    }
    
    header('Location: cart.php');
    exit();
}
?>

</script>
    <script src="script_pagP.js"></script>
    </div>
</body>
</html>
