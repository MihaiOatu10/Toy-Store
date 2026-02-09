<?php
session_start();


if (isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);
    $product_name = $_POST['product_name'];
    $product_price = floatval($_POST['product_price']);

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] += 1;
    } else {
        $_SESSION['cart'][$product_id] = array(
            'name' => $product_name,
            'price' => $product_price,
            'quantity' => 1
        );
    }

    header('Location: ' . $_SERVER['HTTP_REFERER'] . '&added=1');
    exit();
} else {
    echo "Produsul nu a fost selectat.";
}
