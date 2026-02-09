<?php
session_start();
include 'conexiune.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';


if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $surname = htmlspecialchars($_POST['surname']);
    $county = htmlspecialchars($_POST['county']);
    $city = htmlspecialchars($_POST['city']);
    $address = htmlspecialchars($_POST['address']);
    $postal_code = htmlspecialchars($_POST['postal_code']);
    $phone = htmlspecialchars($_POST['phone']);
    $email = htmlspecialchars($_POST['email']);
    $delivery_method = htmlspecialchars($_POST['delivery_method']);
    $payment_method = htmlspecialchars($_POST['payment_method']);
    $delivery_address = isset($_POST['delivery_address']) ? htmlspecialchars($_POST['delivery_address']) : '';
    $comments = htmlspecialchars($_POST['comments']);
    $order_date = date('Y-m-d');

    if ($delivery_address != 'different') {
        $delivery_city = htmlspecialchars($_POST['delivery_city']);
        $delivery_address_detail = htmlspecialchars($_POST['delivery_address_detail']);
        $delivery_postal_code = htmlspecialchars($_POST['delivery_postal_code']);
        
        if (empty($delivery_city) || empty($delivery_address_detail) || empty($delivery_postal_code)) {
            $error = "Toate câmpurile sunt obligatorii pentru adresa de livrare.";
        }
    }

    if (empty($name) || empty($surname) || empty($county) || empty($city) || empty($address) || empty($phone) || empty($email)) {
        $error = "Toate câmpurile sunt obligatorii.";
    }

    if (!isset($error)) {
        mysqli_begin_transaction($conn);
    
        try {
            $stmt = $conn->prepare("INSERT INTO orders (name, surname, county, city, address, postal_code, phone, email, delivery_method, payment_method, comments) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssssss", $name, $surname, $county, $city, $address, $postal_code, $phone, $email, $delivery_method, $payment_method, $comments);
            $stmt->execute();

            $order_id = $stmt->insert_id;
    
            foreach ($_SESSION['cart'] as $product_id => $product) {
                $quantity = $product['quantity'];
                $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
                $stmt->bind_param("iii", $order_id, $product_id, $quantity);
                $stmt->execute();
    
                $stmt = $conn->prepare("UPDATE products SET product_stock = product_stock - ? WHERE product_id = ?");
                $stmt->bind_param("ii", $quantity, $product_id);
                $stmt->execute();
            }
    
            mysqli_commit($conn);
    
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'YOUR_EMAIL_HERE'; 
                $mail->Password   = 'YOUR_APP_PASSWORD_HERE'; 
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('YOUR_EMAIL_HERE', 'Toy Store');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Order Confirmation';

                $mailContent = file_get_contents('Email_confirmation.html');
                
                $mailContent = str_replace('{{order_id}}', $order_id, $mailContent);
                $mailContent = str_replace('{{order_date}}', $order_date, $mailContent);
                $mailContent = str_replace('{{name}}', $name . ' ' . $surname, $mailContent);
                
                $mail->Body = $mailContent;
                $mail->send();
                
                echo 'Confirmation email has been sent.';
            } catch (Exception $e) {
                echo "Confirmation email could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }

            unset($_SESSION['cart']);
            header('Location: success.php');
            exit();
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error = "An error occurred while processing your order. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="Stil_checkout.css">
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
        <h1>Finalizare Comandă</h1>
        
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        
        <form action="checkout.php" method="post">
            <h2>Produse în coș</h2>
            <?php
            $total = 0;
            if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                echo "<table>";
                echo "<thead><tr><th>Produs</th><th>Preț</th><th>Cantitate</th><th>Total</th></tr></thead>";
                echo "<tbody>";
                foreach ($_SESSION['cart'] as $product_id => $product) {
                    $product_total = $product['price'] * $product['quantity'];
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($product['name']) . "</td>";
                    echo "<td>" . number_format($product['price'] / 100, 2) . " RON</td>";
                    echo "<td>" . htmlspecialchars($product['quantity']) . "</td>";
                    echo "<td>" . number_format($product_total / 100, 2) . " RON</td>";
                    echo "</tr>";
                    $total += $product_total;
                }
                echo "</tbody>";
                echo "<tfoot><tr><td colspan='3'>Total:</td><td>" . number_format($total / 100, 2) . " RON</td></tr></tfoot>";
                echo "</table>";
            } else {
                echo "<p class='cart-empty'>Coșul tău este gol.</p>";
            }
            ?>
            
            <h2>Informații de Livrare</h2>
            <label for="delivery_method">Selectaţi modalitatea de livrare:</label>
            <select name="delivery_method" id="delivery_method" required>
                <option value="courier">Curier rapid</option>
                <option value="pickup">Ridicare din magazinul fizic</option>
            </select>

            <label for="payment_method">Selectaţi modalitatea de plată:</label>
            <select name="payment_method" id="payment_method" required>
                <option value="cash_on_delivery">Ramburs</option>
                <option value="bank_transfer">Transfer bancar</option>
            </select>

            <h2>Datele dumneavoastră</h2>
            <label for="name">Nume*:</label>
            <input type="text" name="name" id="name" required>

            <label for="surname">Prenume*:</label>
            <input type="text" name="surname" id="surname" required>

            <label for="county">Judeţ/sector*:</label>
            <select name="county" id="county" required>
                <option value="alba">Alba</option>
            <option value="arad">Arad</option>
            <option value="arges">Argeș</option>
            <option value="bacau">Bacău</option>
            <option value="bihor">Bihor</option>
            <option value="bistrita-nasaud">Bistrița-Năsăud</option>
            <option value="botosani">Botoșani</option>
            <option value="braila">Brăila</option>
            <option value="brasov">Brașov</option>
            <option value="bucuresti-sector-1">București, Sector 1</option>
            <option value="bucuresti-sector-2">București, Sector 2</option>
            <option value="bucuresti-sector-3">București, Sector 3</option>
            <option value="bucuresti-sector-4">București, Sector 4</option>
            <option value="bucuresti-sector-5">București, Sector 5</option>
            <option value="bucuresti-sector-6">București, Sector 6</option>
            <option value="buzau">Buzău</option>
            <option value="calarasi">Călărași</option>
            <option value="caras-severin">Caraș-Severin</option>
            <option value="cluj">Cluj</option>
            <option value="constanta">Constanța</option>
            <option value="covasna">Covasna</option>
            <option value="dambovita">Dâmbovița</option>
            <option value="dolj">Dolj</option>
            <option value="galati">Galați</option>
            <option value="giurgiu">Giurgiu</option>
            <option value="gorj">Gorj</option>
            <option value="harghita">Harghita</option>
            <option value="hunedoara">Hunedoara</option>
            <option value="ialomita">Ialomița</option>
            <option value="iasi">Iași</option>
            <option value="ilfov">Ilfov</option>
            <option value="maramures">Maramureș</option>
            <option value="mehedinti">Mehedinți</option>
            <option value="mures">Mureș</option>
            <option value="neamt">Neamț</option>
            <option value="olt">Olt</option>
            <option value="prahova">Prahova</option>
            <option value="salaj">Sălaj</option>
            <option value="satu mare">Satu Mare</option>
            <option value="sibiu">Sibiu</option>
            <option value="suceava">Suceava</option>
            <option value="teleorman">Teleorman</option>
            <option value="timis">Timiș</option>
            <option value="tulcea">Tulcea</option>
            <option value="vaslui">Vaslui</option>
            <option value="valcea">Vâlcea</option>
            <option value="vrancea">Vrancea</option>
            </select>

            <label for="city">Localitate*:</label>
            <input type="text" name="city" id="city" required>

            <label for="address">Adresă*:</label>
            <input type="text" name="address" id="address" required>

            <label for="postal_code">Cod poştal*:</label>
            <input type="text" name="postal_code" id="postal_code" required>

            <label for="phone">Telefon*:</label>
            <input type="text" name="phone" id="phone" required>

            <label for="email">Adresă de e-mail*:</label>
            <input type="email" name="email" id="email" required>

            <label for="delivery_address">Adresa livrare:</label>
            <input type="checkbox" id="delivery_address" name="delivery_address" value="different" checked>
            <label for="delivery_address">Aceeaşi cu cea de mai sus</label>

            <div class="delivery-address-fields" id="delivery-address-fields">
                <label for="delivery_city">Localitate:</label>
                <input type="text" name="delivery_city" id="delivery_city">

                <label for="delivery_address_detail">Adresă:</label>
                <input type="text" name="delivery_address_detail" id="delivery_address_detail">

                <label for="delivery_postal_code">Cod poştal:</label>
                <input type="text" name="delivery_postal_code" id="delivery_postal_code">
            </div>

            <label for="comments">Observaţii:</label>
            <textarea name="comments" id="comments" rows="4" placeholder="Adăugați observații suplimentare"></textarea>

            <button type="submit" class="btn-checkout">Finalizează Comanda</button>
        </form>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deliveryCheckbox = document.getElementById('delivery_address');
            const deliveryFields = document.getElementById('delivery-address-fields');

            function updateDeliveryFields() {
                if (deliveryCheckbox.checked) {
                    deliveryFields.style.display = 'none';
                } else {
                    deliveryFields.style.display = 'block';
                }
            }
            updateDeliveryFields();

            deliveryCheckbox.addEventListener('change', updateDeliveryFields);
        });
    </script>
    <script src="script_pagP.js"></script>

    <a href="cart.php" class="shopping-cart-btn">
        <img src="IMGstatic/cart.png" alt="Coș de cumpărături">
    </a>    
</body>
</html>