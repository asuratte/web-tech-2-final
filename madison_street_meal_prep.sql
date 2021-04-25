-- drop database if exists, create if not exists, and select the madison_street_meal_prep database --
DROP DATABASE IF EXISTS `madison_street_meal_prep`;
CREATE DATABASE IF NOT EXISTS `madison_street_meal_prep`;
USE madison_street_meal_prep;

-- create table `madison_street_meal_prep`.`mealPlans` --
CREATE TABLE IF NOT EXISTS `madison_street_meal_prep`.`mealPlans` (
  `planID` INT NOT NULL AUTO_INCREMENT,
  `planName` VARCHAR(50) NOT NULL,
  `breakfastPrice` DECIMAL(5,2) NOT NULL COMMENT '\n',
  `lunchPrice` DECIMAL(5,2) NOT NULL,
  `dinnerPrice` DECIMAL(5,2) NOT NULL,
  `planDescription` VARCHAR(200) NOT NULL,
  `planImage` VARCHAR(200) NOT NULL,
  PRIMARY KEY (`planID`, `planName`),
  UNIQUE INDEX `planId_UNIQUE` (`planID` ASC));

-- create table `madison_street_meal_prep`.`customers` --
CREATE TABLE IF NOT EXISTS `madison_street_meal_prep`.`customers` (
  `customerID` INT NOT NULL AUTO_INCREMENT,
  `firstName` VARCHAR(255) NOT NULL,
  `lastName` VARCHAR(255) NOT NULL,
  `streetAddress` VARCHAR(255) NOT NULL,
  `city` VARCHAR(255) NOT NULL,
  `state` VARCHAR(255) NOT NULL,
  `zipCode` INT NOT NULL,
  `phoneNumber` VARCHAR(20) NOT NULL,
  `email` VARCHAR(320) NOT NULL,
  `username` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `dietaryPreference` VARCHAR(255) NULL,
  PRIMARY KEY (`customerID`));

-- create table `madison_street_meal_prep`.`orders` --
CREATE TABLE IF NOT EXISTS `madison_street_meal_prep`.`orders` (
  `orderID` INT NOT NULL AUTO_INCREMENT,
  `orderDate` DATETIME NOT NULL,
  `orderTotal` DECIMAL(7,2) NOT NULL,
  `customerID` INT NOT NULL,
  PRIMARY KEY (`orderID`),
  UNIQUE INDEX `orderID_UNIQUE` (`orderID` ASC),
  CONSTRAINT `customerID`
    FOREIGN KEY (`customerID`)
    REFERENCES `madison_street_meal_prep`.`customers` (`customerID`));

-- create table `madison_street_meal_prep`.`addOns` --
CREATE TABLE IF NOT EXISTS `madison_street_meal_prep`.`addOns` (
  `addOnID` INT NOT NULL AUTO_INCREMENT,
  `addOnName` VARCHAR(50) NOT NULL,
  `addOnPrice` DECIMAL(5,2) NOT NULL,
  `addOnImage` VARCHAR(200) NOT NULL,
  `addOnDescription` VARCHAR(200) NOT NULL,
  PRIMARY KEY (`addOnID`),
  UNIQUE INDEX `addOnID_UNIQUE` (`addOnID` ASC));

-- create table `madison_street_meal_prep`.`lineItems` --
CREATE TABLE IF NOT EXISTS `madison_street_meal_prep`.`lineItems` (
  `itemID` INT NOT NULL AUTO_INCREMENT,
  `orderID` INT NOT NULL,
  `itemQuantity` INT NOT NULL,
  `itemName` VARCHAR(50) NOT NULL,
  `itemTotal` DECIMAL(7,2) NOT NULL,
  PRIMARY KEY (`itemID`, `orderID`),
  CONSTRAINT `orderID`
    FOREIGN KEY (`orderID`)
    REFERENCES `madison_street_meal_prep`.`orders` (`orderID`));

-- create table `madison_street_meal_prep`.`deliveryZipCodes` --
CREATE TABLE IF NOT EXISTS `madison_street_meal_prep`.`deliveryZipCodes` (
  `zipCode` INT NOT NULL,
  PRIMARY KEY (`zipCode`));

-- create table `madison_street_meal_prep`.`currentMeals` --
CREATE TABLE IF NOT EXISTS `madison_street_meal_prep`.`currentMeals` (
  `planID` INT NOT NULL AUTO_INCREMENT,
  `breakfastName` VARCHAR(50) NOT NULL,
  `breakfastDescription` VARCHAR(200) NOT NULL,
  `breakfastImage` VARCHAR(200) NOT NULL,
  `lunchName` VARCHAR(50) NOT NULL,
  `lunchDescription` VARCHAR(200) NOT NULL,
  `lunchImage` VARCHAR(200) NOT NULL,
  `dinnerName` VARCHAR(50) NOT NULL,
  `dinnerDescription` VARCHAR(200) NOT NULL,
  `dinnerImage` VARCHAR(200) NOT NULL,
  PRIMARY KEY (`planID`),
  CONSTRAINT `planID`
    FOREIGN KEY (`planID`)
    REFERENCES `madison_street_meal_prep`.`mealPlans` (`planID`));


  CREATE TABLE IF NOT EXISTS `madison_street_meal_prep`.`states` (
    `abbreviation` char(2) NOT NULL DEFAULT '',
    `name` varchar(128) NOT NULL DEFAULT '',
    PRIMARY KEY (`abbreviation`)
  );

-- insert data into table `madison_street_meal_prep`.`mealPlans` --
INSERT INTO `mealPlans` (planID, planName, breakfastPrice, lunchPrice, dinnerPrice, planDescription, planImage) VALUES
(1, 'Standard', 6.00, 9.50, 12.00, 'Our standard plan includes a variety of fresh meat, fish, and vegetables. Best for those with no dietary restrictions.', 'meals-standard.jpg'),
(2, 'Gluten Free', 6.00, 10.50, 13.00, 'Our gluten free plan is similar to our standard plan, but is free from any gluten-containing ingredients.', 'meals-glutenfree.jpg'),
(3, 'Vegetarian', 6.00, 8.00, 11.00, 'Our vegetarian plan is based around fresh vegetables, high-quality dairy and eggs, and whole grains.', 'meals-vegetarian.jpg'),
(4, 'Vegan', 6.00, 9.00, 10.00, 'Our vegan plan is completely plant-based and protein-rich.', 'meals-vegan.jpg'),
(5, 'Keto', 8.00, 10.50, 14.00, 'Our keto plan is higher in protein and fat, and lower in carbohydrates than our standard plan.', 'meals-keto.jpg'),
(6, 'Diabetic', 7.00, 10.00, 12.00, 'Our diabetic plan is specially balanced to support healthy blood sugar levels.', 'meals-diabetic.jpg');

-- insert data into table `madison_street_meal_prep`.`customers` --
INSERT INTO `customers` (customerID, firstName, lastName, streetAddress, city, state, zipCode, phoneNumber, email, username, password, dietaryPreference) VALUES
(1, 'Amber', 'Suratte', '3104 W. Pheasant St.', 'Woodstock', 'IL', 60098, '815-453-9922', 'asuratte@gmail.com', 'mjade1138', '$2y$12$apS00y2OvvRWdYU2KNLrDe/i.z50jLNcUB4u3LL5Bg0x1QIFB7uNK', 'Vegetarian'),
(2, 'Lexi', 'Bebis', '2254 Oeffling Dr.', 'Johnsburg', 'IL', 60051, '815-364-2290', 'lexibebis@hotmail.com', 'convexcacti', '$2y$12$J1npX4kTSmJbwdJYGY3yMukpMlQ/UvEf.fbqelnRF4MLJnD8EROxG', 'Standard'),
(3, 'Kyle', 'Suratte', '3104 W. Pheasant St.', 'Woodstock', 'IL', 60098, '815-222-9283', 'ksuratte@hushmail.com', '$2y$12$81DiYfzieTtMRNc.a0g3retmyqluBpGZ4j.AVuzoCj.xn4.NgmVGy', 'Peanut13', 'Keto');

-- insert data into table `madison_street_meal_prep`.`orders` --
INSERT INTO `orders` (orderID, orderDate, orderTotal, customerID) VALUES
(1, '2021-04-15 11:59:59', 185.93, 1),
(2, '2021-03-28 09:25:44', 63.75, 2),
(3, '2021-02-28 05:12:50', 104.13, 3);

-- insert data into table `madison_street_meal_prep`.`addOns` --
INSERT INTO `addOns` (addOnID, addOnName, addOnPrice, addOnImage, addOnDescription) VALUES
(1, 'Fresh Pressed Juice, 16oz', 5.00, 'add_ons_juice.jpg', 'We start with whole fruits and vegetables and use a cold-press process to extract every last drop.'),
(2, 'Seasonal Fruit Salad, 1 lb', 8.00, 'add_ons_fruit_salad.jpg', 'A mix of 2-4 seasonal fruits, cut up and ready to eat.'),
(3, 'Hummus & Veggie Platter', 10.00, 'add_ons_hummus.jpg', 'Hummus paired with 2-4 seasonal vegetables, ready to serve or snack on.');

-- insert data into table `madison_street_meal_prep`.`lineItems` --
INSERT INTO `lineItems` (itemID, orderID, itemQuantity, itemName, itemTotal) VALUES
(1, 1, 7, 'Vegetarian Breakfast', 42.00),
(2, 1, 7, 'Vegetarian Lunch', 56.00),
(3, 1, 7, 'Vegetarian Dinner', 77.00),
(4, 2, 5, 'Standard Dinner', 60.00),
(5, 3, 7, 'Keto Dinner', 98.00);

-- insert data into table `madison_street_meal_prep`.`deliveryZipCodes` --
INSERT INTO `deliveryZipCodes` (zipCode) VALUES
(60098),
(60097),
(60050),
(60051),
(60014);

-- insert data into table `madison_street_meal_prep`.`currentMeals` --
INSERT INTO `currentMeals` (planID, breakfastName, breakfastDescription, breakfastImage, lunchName, lunchDescription, lunchImage, dinnerName, dinnerDescription, dinnerImage) VALUES
(1, 'Truffle & Fontina Quiche', 'With Arugula & Pistachio Salad', 'truffle_fontina_quiche.jpg', 'Cashew Chicken Stir Fry', 'With Vegetables & Quinoa', 'cashew_chicken_stir_fry.jpg', 'Pan-Seared Cod & Lemon Butter', 'With Kale & Saffron Rice', 'cod_lemon_butter.jpg'),
(2, 'Gluten-Free Blintzes', 'Topped With Mixed Berries', 'truffle_fontina_quiche.jpg', 'Mexican-Spiced Steaks & Creamy Chipotle Sauce', 'With Jalapeno Cheddar Potato Cakes', 'mexican_steaks.jpg', 'Baked Chicken & Zucchini Rice Casserole', 'With Fontina & Parmesan', 'chicken_zucchini_rice.jpg'),
(3, 'Truffle & Fontina Quiche', 'With Arugula & Pistachio Salad', 'truffle_fontina_quiche.jpg', 'Ponzu-Sesame Tofu & Vegetables', 'Over Brown Rice', 'ponzu_tofu.jpg', 'Roasted Red Pepper Pasta', 'With Vegan Parmesan', 'roasted_red_pepper_pasta.jpg'),
(4, 'Tofu Scramble with Parsley', 'On Wheat Toast', 'tofu_scramble.jpg', 'Ponzu-Sesame Tofu & Vegetables', 'Over Brown Rice', 'ponzu_tofu.jpg', 'Roasted Red Pepper Pasta', 'With Vegan Parmesan', 'roasted_red_pepper_pasta.jpg'),
(5, 'Keto Silver Dollar Pancakes', 'With Grass-Fed Butter', 'keto_pancakes.jpg', 'Italian Salami Lunch Wraps', 'With Arugula and Red Onion', 'keto_wrap.jpg', 'Garlic Chicken Stir Fry', 'With Broccoli & Spinach', 'keto_stir_fry.jpg'),
(6, 'Truffle & Fontina Quiche', 'With Arugula & Pistachio Salad', 'truffle_fontina_quiche.jpg', 'Power Lunch Salad', 'With Spinach & Apples', 'diabetic_salad.jpg', 'Pan-Seared Cod & Lemon Butter', 'With Kale & Saffron Rice', 'cod_lemon_butter.jpg');

-- insert data into table `madison_street_meal_prep`.`states` --
insert into states (abbreviation, name) VALUES
('AL','Alabama'),
('AK','Alaska'),
('AS','American Samoa'),
('AZ','Arizona'),
('AR','Arkansas'),
('CA','California'),
('CO','Colorado'),
('CT','Connecticut'),
('DE','Delaware'),
('DC','District of Columbia'),
('FM','Federated States of Micronesia'),
('FL','Florida'),
('GA','Georgia'),
('GU','Guam'),
('HI','Hawaii'),
('ID','Idaho'),
('IL','Illinois'),
('IN','Indiana'),
('IA','Iowa'),
('KS','Kansas'),
('KY','Kentucky'),
('LA','Louisiana'),
('ME','Maine'),
('MH','Marshall Islands'),
('MD','Maryland'),
('MA','Massachusetts'),
('MI','Michigan'),
('MN','Minnesota'),
('MS','Mississippi'),
('MO','Missouri'),
('MT','Montana'),
('NE','Nebraska'),
('NV','Nevada'),
('NH','New Hampshire'),
('NJ','New Jersey'),
('NM','New Mexico'),
('NY','New York'),
('NC','North Carolina'),
('ND','North Dakota'),
('MP','Northern Mariana Islands'),
('OH','Ohio'),
('OK','Oklahoma'),
('OR','Oregon'),
('PW','Palau'),
('PA','Pennsylvania'),
('PR','Puerto Rico'),
('RI','Rhode Island'),
('SC','South Carolina'),
('SD','South Dakota'),
('TN','Tennessee'),
('TX','Texas'),
('UT','Utah'),
('VT','Vermont'),
('VI','Virgin Islands'),
('VA','Virginia'),
('WA','Washington'),
('WV','West Virginia'),
('WI','Wisconsin'),
('WY','Wyoming');

-- create database user and grant access --
GRANT SELECT, INSERT, UPDATE, DELETE
ON *
TO madison_street_meal_prep@localhost
IDENTIFIED BY 'Woodstock01';
