<?php
include __DIR__ . "/header.php";

?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../library/css/bootstrap.min.css">
    <style>
        h2, h3 {
            margin-top: 0px;
            padding-top: 0px;
        }
        td {
            border-top: none !important;
        }
    </style>
</head>
<body>

<div class="container well">
    <center><h2>Adresgegevens</h2></center>
    <hr>
    <form method="post" action="pay.php" class="form-horizontal">
        <div class="flex-row" style="
            display: flex;
            flex-direction: row;
            justify-content: space-between">
            <div class="col-md-6 well">
                <h3>Persoonsgegevens</h3>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon addon-diff-color">
                            <span class="glyphicon glyphicon-user"></span>
                        </div>
                        <input class="form-control" type="text" id="billing_name" name="billing_name" placeholder="Naam" value="" required>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon addon-diff-color">
                            <span class="glyphicon glyphicon-envelope"></span>
                        </div>
                        <input class="form-control" type="text" id="billing_email" name="billing_email" placeholder="E-mail" value="" required>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon addon-diff-color">
                            <span class="glyphicon glyphicon-earphone"></span>
                        </div>
                        <input class="form-control" type="text" id="billing_tel" name="billing_tel" placeholder="Telefoonnummer" value="" required>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon addon-diff-color">
                            <span class="glyphicon glyphicon-home"></span>
                        </div>
                        <input class="form-control" type="text" id="billing_address" name="billing_address" placeholder="Adres" value="" required>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon addon-diff-color">
                            <span class="glyphicon glyphicon-home"></span>
                        </div>
                        <input class="form-control" type="text" id="billing_city" name="billing_city" placeholder="Stad" value="" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon addon-diff-color">
                                    <span class="glyphicon glyphicon-home"></span>
                                </div>
                                <input class="form-control" type="text" id="billing_state" name="billing_state" placeholder="Provincie" value="" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 col-md-offset-2">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon addon-diff-color">
                                    <span class="glyphicon glyphicon-map-marker"></span>
                                </div>
                                <input class="form-control" type="text" id="billing_zip" name="billing_zip" placeholder="Postcode" value="" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon addon-diff-color">
                            <span class="glyphicon glyphicon-home"></span>
                        </div>
                        <input class="form-control" type="text" id="billing_country" name="billing_country" placeholder="Land" value="" required>
                    </div>
                </div>
            </div>
    </form>
            <div class="float-right">
                <div class="">
                    <h3>Product overzict</h3>
                    <h4><span class="glyphicon glyphicon-shopping-cart"></span></h4>
                    <table class="table">
                        <tbody>
                        <?php
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
                            echo "<p>Aantal: " . $item['quantity'] . " </p> ";
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
                        ?>
                        </tbody>
                    </table>
                    <hr style="border: 1px dotted gray;">
                    <p>Total: <strong>
                            <span>&euro;</span><?=number_format($totalPrice,2)?>
                        </strong>
                    </p>
                </div>
                <div class="text-right">
                    <input type="hidden" name="data" value="value">
                    <input type="submit" value="Verder naar afrekenen" class="btn btn-success btn-block">
                </div>
            </div>
        </div>
    </form>
</div>
</body>


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
        object-fit: contain; /* Maintain aspect ratio and cover the container */
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
</style>
</html>
