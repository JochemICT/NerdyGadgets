<?php
include __DIR__ . "/header.php";

$customerID = 0;


if(isset($_SESSION['personID']) && $_SESSION['personID'] != "" ){
    $customerID = $_SESSION['personID'];
}
$productID = $_POST['productID'];
$title = $_POST['review_title'];
$description = $_POST['review_description'];
$pro1 = $_POST['review_pros_1'];
$pro2 = $_POST['review_pros_2'];
$pro3 = $_POST['review_pros_3'];
$con1 = $_POST['review_cons_1'];
$con2 = $_POST['review_cons_2'];
$con3 = $_POST['review_cons_3'];
$amount = $_POST['review_ranking'];





addReview($productID, $customerID, $title, $description, $amount, $pro1, $pro2, $pro3, $con1, $con2, $con3);


