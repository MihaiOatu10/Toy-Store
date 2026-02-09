DROP TABLE IF EXISTS product_category;
CREATE TABLE product_category (
product_category_id int NOT NULL,
category_name varchar(100) DEFAULT NULL,
PRIMARY KEY (product_category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

LOCK TABLES product_category WRITE;
INSERT INTO product_category VALUES (1,'Papusi'),(2,'Masinute'),(3,'Premergatoare');
UNLOCK TABLES;

DROP TABLE IF EXISTS product_gender;
CREATE TABLE product_gender (
product_gender_id int NOT NULL,
gender_name varchar(10) DEFAULT NULL,
PRIMARY KEY (product_gender_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

LOCK TABLES product_gender WRITE;
INSERT INTO product_gender VALUES (1,'fete'),(2,'baieti'),(3,'bebelusi');
UNLOCK TABLES;

DROP TABLE IF EXISTS orders;
CREATE TABLE orders (
id int NOT NULL AUTO_INCREMENT,
name varchar(255) DEFAULT NULL,
surname varchar(255) DEFAULT NULL,
county varchar(255) DEFAULT NULL,
city varchar(255) DEFAULT NULL,
address varchar(255) DEFAULT NULL,
postal_code varchar(20) DEFAULT NULL,
phone varchar(20) DEFAULT NULL,
email varchar(255) DEFAULT NULL,
delivery_method varchar(50) DEFAULT NULL,
payment_method varchar(50) DEFAULT NULL,
delivery_address text,
comments text,
order_date timestamp NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS products;
CREATE TABLE products (
product_id int NOT NULL,
product_name varchar(100) DEFAULT NULL,
product_price int DEFAULT NULL,
product_stock int DEFAULT NULL,
product_category_id int DEFAULT NULL,
PRIMARY KEY (product_id),
KEY product_category_id (product_category_id),
CONSTRAINT products_ibfk_1 FOREIGN KEY (product_category_id) REFERENCES product_category (product_category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

LOCK TABLES products WRITE;
INSERT INTO products VALUES (1,'Premergator Sea Adventure',26999,5,3),(2,'Masinuta Hot Wheels',3999,50,2),(3,'Papusa barbie',5499,25,1),(4,'Balon Rosu',1000,0,2);
UNLOCK TABLES;

DROP TABLE IF EXISTS category_gender_link;
CREATE TABLE category_gender_link (
product_category_id int NOT NULL,
product_gender_id int NOT NULL,
PRIMARY KEY (product_category_id,product_gender_id),
KEY product_gender_id (product_gender_id),
CONSTRAINT category_gender_link_ibfk_1 FOREIGN KEY (product_category_id) REFERENCES product_category (product_category_id),
CONSTRAINT category_gender_link_ibfk_2 FOREIGN KEY (product_gender_id) REFERENCES product_gender (product_gender_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

LOCK TABLES category_gender_link WRITE;
INSERT INTO category_gender_link VALUES (1,1),(2,2),(3,3);
UNLOCK TABLES;

DROP TABLE IF EXISTS order_items;
CREATE TABLE order_items (
id int NOT NULL AUTO_INCREMENT,
order_id int DEFAULT NULL,
product_id int DEFAULT NULL,
quantity int NOT NULL,
PRIMARY KEY (id),
KEY order_id (order_id),
KEY product_id (product_id),
CONSTRAINT order_items_ibfk_1 FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE,
CONSTRAINT order_items_ibfk_2 FOREIGN KEY (product_id) REFERENCES products (product_id)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS product_details;
CREATE TABLE product_details (
detail_id int NOT NULL,
product_id int DEFAULT NULL,
product_description text,
color varchar(50) DEFAULT NULL,
recommended_age varchar(50) DEFAULT NULL,
material varchar(100) DEFAULT NULL,
PRIMARY KEY (detail_id),
KEY product_id (product_id),
CONSTRAINT product_details_ibfk_1 FOREIGN KEY (product_id) REFERENCES products (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

LOCK TABLES product_details WRITE;
INSERT INTO product_details VALUES (1,3,'O păpușă Barbie cu rochie roz.','Roz','3+ ani','Plastic'),(2,2,'Mașinuță de jucărie Hot Wheels.','Roșu','3+ ani','Metal'),(3,1,'premergator ergonomic pentru bebeluși.','Alb','0-1 ani','Silicon'),(4,4,'Un balon rosu','Rosu','0+','Cauciuc');
UNLOCK TABLES;

DROP TABLE IF EXISTS product_images;
CREATE TABLE product_images (
image_id int NOT NULL,
product_id int DEFAULT NULL,
image_filename varchar(255) DEFAULT NULL,
PRIMARY KEY (image_id),
KEY product_id (product_id),
CONSTRAINT product_images_ibfk_1 FOREIGN KEY (product_id) REFERENCES products (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

LOCK TABLES product_images WRITE;
INSERT INTO product_images VALUES (1,3,'barbie1.jpg'),(2,3,'barbie2.jpg'),(3,2,'hotwheels1.jpg'),(4,2,'hotwheels2.jpg'),(5,1,'premergator1.jpg'),(6,1,'premergator2.jpg'),(7,4,'balon1.jpg'),(8,4,'balon2.jpg');
UNLOCK TABLES;