<?php
include __DIR__ . "/header.php";
require_once 'vendor/autoload.php';

$sck = "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";
\Stripe\Stripe::setApiKey($sck);

$groupedCart = [];
$lineItems = [];

$totalPrice = 0;

foreach ($_SESSION['cart'] as $item) {
    $productId = $item['productID'];
    if (!isset($groupedCart[$productId])) {
        $groupedCart[$productId] = $item;
        $groupedCart[$productId]['quantity'] = 1;
    } else {
        $groupedCart[$productId]['quantity']++;
    }
}

foreach ($groupedCart as $item) {
    $productId = $item['productID'];
    $StockItem = getStockItem($productId, $databaseConnection);
    $StockItemImage = getStockItemImage($item['productID'], $databaseConnection);

    $lineItem = [
        "quantity" => $item['quantity'],
        "price_data" => [
            "currency" => "eur",
            "unit_amount" => intval($StockItem['SellPrice'] * 100), // Convert to cents and ensure it's an integer
            "product_data" => [
                "name" => $StockItem['StockItemName'],
            ]
        ]
    ];

    $lineItems[] = $lineItem;
}

if(isset($_SESSION['klantInfoArray']) && $_SESSION['ingelogd']){
    $emailForm = $_SESSION['klantInfoArray'][0]['LogonName'];
}
else{
    $emailForm = "";
}

$checkout_session = \Stripe\Checkout\Session::create([
    "mode" => "payment",
    "success_url" => "http://localhost/NerdyGadgets/payment_success.php?session_id={CHECKOUT_SESSION_ID}",
    "cancel_url" => "http://localhost/NerdyGadgets/cart.php",
    "locale" => "nl",
    "line_items" => $lineItems,
    'billing_address_collection' => 'auto',
    'payment_method_configuration' => 'pmc_1OHtuNH8E5CxKfBaUrugwMvp',
    'customer_email' => $emailForm,
]);

http_response_code(303);
header("Location: " . $checkout_session->url);
