<?php
include __DIR__ . "/header.php";
?>
<div id="CenteredContent">
<h2>Winkelwagen</h2>

<?php
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {

    //Kijk of er op de remove from cart is geklikt.
    if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['action'])){
        switch ($_POST['action']){
            case "remove_from_cart":
                if(isset($_POST['productID'])){
                    $id = $_POST['productID'];
                    $index = array_search(['productID' => $id], $_SESSION['cart']);

                    if ($index !== null) {
                        unset($_SESSION['cart'][$index]);
                        header("Refresh: 0");

                    }else{
                        echo "<script>alert('Er is iets fout gegaan. Herlaad de pagina en probeer opnieuw.')</script>";
                    }
                }
                break;
            case "change_quantity":
                if(isset($_POST['productID'])){
                    $id = $_POST['productID'];
                    $quantity = $_POST['quantity'];

                    $count = 0;
                    foreach ($_SESSION['cart'] as $key => $item) {
                        if($item['productID'] == $id){
                            $count++;
                        };

                    }

                    $diff = $quantity - $count;
                    for($i = 0; $i < $diff; $i++){
                        $_SESSION['cart'][] = array(
                            'productID' => $id,
                        );
                    }

                }
                header("Refresh: 0");
                break;


        }
    }

    // Een loopje om door de sessie array 'cart' heen te gaan, om inplaats van dubbele items te weergeven, ze bij elkaar op te tellen met een aantal erbij.
    $groupedCart = [];
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

    // Nu alles gesorteerd per item is, kun je door de items heen loopen en laten zien wat je wil tlaten zien.
    foreach ($groupedCart as $item) {
        $StockItemImage = getStockItemImage($item['productID'], $databaseConnection);
        $StockItem = getStockItem($item['productID'], $databaseConnection);
        $totalPrice += $StockItem['SellPrice'] * $item['quantity'];

        //Item
        echo "<div class='cart-item'>";

        //Afbeelding
        echo "<div class='product-image'>";
        if($StockItemImage){
            echo "<img src='Public/StockItemIMG/" . $StockItemImage[0]['ImagePath'] . "' width='200px'>"; //Item afbeelding
        }else{
            echo "<img src='Public/StockGroupIMG/" . $StockItem['BackupImagePath'] . "' width='200px'>"; // Group afbeelding
        }
        echo "</div>";

        //Product informatie
        echo "<div class='product-information'>";
        echo "<p><strong>{$StockItem['StockItemName']}</strong></p>";
        echo "<p>{$StockItem['SearchDetails']}</p>";
        echo "<form method='POST'>";
        echo "<input type='hidden' name='action' value='change_quantity'>";
        echo "<input type='hidden' name='productID' value='" . $StockItem['StockItemID'] . "'>";
        echo "<p>Aantal: 
                    <input type='number' style='width: 65px; height:35px !important' value='{$item['quantity']}' name='quantity' min='1'> 
                    <button type='submit' class='change_quantity'>&#10003;</button
               </p> ";
        echo "</form>";
        echo "</div>";

        //Prijs
        echo "<div class='product-price'>";
        echo "<p>€ " . number_format($StockItem['SellPrice'] * $item['quantity'], 2) . "</p>";
        if($item['quantity'] > 1){
            echo "<p>( " . $item['quantity'] . " x €" . number_format($StockItem['SellPrice'], 2) . ")</p>";
        }
        echo "</div>";

        echo "<div class='product-remove'>";
        echo "<form method='POST'>";
        echo "<input type='hidden' name='action' value='remove_from_cart'>";
        echo "<input type='hidden' name='productID' value='" . $StockItem['StockItemID'] . "'>";
        echo "<button type='submit' class='remove-item-button'>&#10007;</button>";
        echo "</form>";
        echo "</div>";

        echo "</div>";
    }

    //Totaal prijs
    echo "<hr style='background: white'>";
    echo "<p><strong>Totaal prijs:</strong> &euro; " . number_format($totalPrice, 2);

    //Verder winkelen of betalen knoppen
    echo "<div class='button-container'>";
    echo "<a href='browse.php' class='button'>Verder winkelen</a>";
    echo "<a href='' class='button'>Betalen</a>";
}else{
    echo "Je winkelwagen is nog leeg.";
}
?>



</div>

<style>
    .cart-item {
        display: flex;
        border: 1px solid #ddd;
        padding: 10px;
        margin-bottom: 10px;
    }

    .product-image img {
        width: 120px;
        height: 120px;
        object-fit: cover; /* Maintain aspect ratio and cover the container */
        margin-right: 15px;
        border-radius: 8px;
    }

    .product-information {
        flex-grow: 1;
    }

    .product-information p {
        margin: 0;
        font-size: 16px;
    }

    .product-price {
        text-align: right;
    }

    .product-price p {
        margin: 0;
        font-size: 18px;
        font-weight: bold;
    }

    .button-container {
        margin-top: 10px;
    }

    .button {
        display: inline-block;
        margin-right: 10px;
        padding: 10px 15px;
        background-color: #4CAF50;
        color: white;
        text-decoration: none;
        border-radius: 5px;
    }

    .button:hover {
        background-color: #45a049;
        color: white;
    }

    .product-remove {
        text-align: right;
    }

    .product-remove button{
        margin-top: 40px;
        margin-left: 20px;
        font-size: 18px;
        font-weight: bold;
        color: white;
        background-color: red;
        border: 0;
        border-radius: 3px;
    }
    .change_quantity{
        font-size: 18px;
        font-weight: bold;
        color: white;
        background-color: #4CAF50;
        border: 0;
        border-radius: 3px;
    }
</style>

