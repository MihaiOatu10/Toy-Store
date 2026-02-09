# 🧸 Toy Store - E-commerce Platform

A legacy personal project demonstrating a functional e-commerce flow, including product cataloging, cart management, and automated email order confirmation.

---

## 🚀 Features

* **Product Catalog:** Filterable list of products with dynamic stock status.
* **Shopping Cart:** Full session-based cart functionality (add, remove, update quantities).
* **Checkout Process:** Secure order processing with transaction support (MySQL).
* **Email Notifications:** Automatic order confirmation emails sent via PHPMailer (SMTP).
* **Responsive UI:** Clean, pastel-themed design optimized for various screen sizes.

---

## 🛠️ Tech Stack

* **Backend:** PHP 8.x
* **Database:** MySQL
* **Frontend:** HTML5, CSS3, JavaScript
* **Libraries:** PHPMailer (Pre-bundled for easy setup)

---

## 📦 Installation & Setup

This project is designed to be **Plug & Play**. All core dependencies and the database schema are included in the repository.

### 1. Clone the repository
```bash
git clone [https://github.com/MihaiOatu10/Toy-Store.git](https://github.com/MihaiOatu10/Toy-Store.git)
```
### 2. Database Setup
1. Open your MySQL manager (e.g., **phpMyAdmin** or **MySQL Workbench**).
2. Create a new database named `ecommerceshop`.
3. Import the `database.sql` file located in the root of this project into your newly created database.

### 3. Environment Configuration

#### 🔑 Database (`conexiune.php`)
Update the following variables to match your local environment:
* `$servername` — typically `localhost`
* `$username` — your DB username (e.g., `root`)
* `$password` — your DB password
* `$database` — set to `ecommerceshop`

#### 📧 Email (`checkout.php`)
Update the PHPMailer settings with your SMTP credentials:
* `$mail->Username` — Your Gmail address.
* `$mail->Password` — Your Google App Password.

---

## 🏃 Running the Project

* **VS Code:** Use the "PHP Server" extension to launch the project.
* **XAMPP/WAMP:** Move the folder to `htdocs` or `www` and access it via `localhost/Toy-Store`.

---

## ⚠️ Security Note
For portfolio purposes, sensitive credentials have been replaced with placeholders. Ensure you use environment variables or secure config files for any production-ready applications.

## 📸 Media Disclaimer
All product images (e.g., Barbie, Hot Wheels) used in this project are for educational and portfolio demonstration purposes only. All rights belong to their respective owners.

## 📜 License
This project is for personal portfolio use. No commercial license is provided.
