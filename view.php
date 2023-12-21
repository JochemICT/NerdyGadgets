<!-- dit bestand bevat alle code voor de pagina die één product laat zien -->
<?php
include __DIR__ . "/header.php";
$StockItem = getStockItem($_GET['id'], $databaseConnection);
$StockItemImage = getStockItemImage($_GET['id'], $databaseConnection);
$Reviews = getProductReviews($_GET['id'], $databaseConnection);

//Informatie over de sterren
$chilledStockTemperature = getTemperatureAndIsChillerStock($databaseConnection, $_GET['id']);

if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['productID'])){
    $_SESSION['cart'][] = array(
        'productID' => $_POST['productID'],
    );
    echo "<script>alert('Product successvol toegevoegd aan het winkelmandje.')</script>";
    header("Refresh: 0");

}

?>

<?php
$ColdRoomSensorNumber = 5;
?>
<div id="CenteredContent">
    <?php
    if ($StockItem != null) {
        ?>
        <?php
        if (isset($StockItem['Video'])) {
            ?>
            <div id="VideoFrame">
                <?php print $StockItem['Video']; ?>
            </div>
        <?php }
        ?>


        <div id="ArticleHeader">
            <?php
            if (isset($StockItemImage)) {
                // één plaatje laten zien
                if (count($StockItemImage) == 1) {
                    ?>
                    <div id="ImageFrame"
                         style="background-image: url('Public/StockItemIMG/<?php print $StockItemImage[0]['ImagePath']; ?>'); background-size: 300px; background-repeat: no-repeat; background-position: center;"></div>
                    <?php
                } else if (count($StockItemImage) >= 2) { ?>
                    <!-- meerdere plaatjes laten zien -->
                    <div id="ImageFrame">
                        <div id="ImageCarousel" class="carousel slide" data-interval="false">
                            <!-- Indicators -->
                            <ul class="carousel-indicators">
                                <?php for ($i = 0; $i < count($StockItemImage); $i++) {
                                    ?>
                                    <li data-target="#ImageCarousel"
                                        data-slide-to="<?php print $i ?>" <?php print (($i == 0) ? 'class="active"' : ''); ?>></li>
                                    <?php
                                } ?>
                            </ul>

                            <!-- slideshow -->
                            <div class="carousel-inner">
                                <?php for ($i = 0; $i < count($StockItemImage); $i++) {
                                    ?>
                                    <div class="carousel-item <?php print ($i == 0) ? 'active' : ''; ?>">
                                        <img src="Public/StockItemIMG/<?php print $StockItemImage[$i]['ImagePath'] ?>">
                                    </div>
                                <?php } ?>
                            </div>

                            <!-- knoppen 'vorige' en 'volgende' -->
                            <a class="carousel-control-prev" href="#ImageCarousel" data-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </a>
                            <a class="carousel-control-next" href="#ImageCarousel" data-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </a>
                        </div>
                    </div>
                    <?php
                }
            } else {
                ?>
                <div id="ImageFrame"
                     style="background-image: url('Public/StockGroupIMG/<?php print $StockItem['BackupImagePath']; ?>'); background-size: cover;"></div>
                <?php
            }
            ?>



            <h1 class="StockItemID">Artikelnummer: <?php print $StockItem["StockItemID"]; ?></h1>
            <h2 class="StockItemNameViewSize StockItemName">
                <?php print $StockItem['StockItemName']; ?>
            </h2>
            <div class="QuantityText"><?php print $StockItem['QuantityOnHand']; ?></div>
            <div id="StockItemHeaderLeft">
                <div class="CenterPriceLeft">
                    <div class="CenterPriceLeftChild">
                        <p class="StockItemPriceText"><b><?php print sprintf("€ %.2f", $StockItem['SellPrice']); ?></b></p>
                        <h6> Inclusief BTW </h6>
                        <form method="post">
                            <input type="hidden" name="productID" value="<?php print $StockItem["StockItemID"]; ?>">
                            <div class="button-container">
                            <button type="submit" class="button">Voeg toe</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>



        <div id="StockItemDescription">
            <h3>Artikel beschrijving</h3>
            <p><?php print $StockItem['SearchDetails']; ?></p>
        </div>
        <div id="StockItemSpecifications">
            <h3>Artikel specificaties</h3>
            <?php
            $CustomFields = json_decode($StockItem['CustomFields'], true);
            if (is_array($CustomFields)) { ?>
                <table>
                <thead>
                <th>Naam</th>
                <th>Data</th>
                </thead>
                <tr>
                    <td>Temperatuur</td>
                    <td><?php
                        $stockItemId = $StockItem['StockItemID'] ?? null;
                        $tempAndIsChillerStock = getTemperatureAndIsChillerStock($databaseConnection, $stockItemId);
                        if ($tempAndIsChillerStock !== false && isset($tempAndIsChillerStock['Temperature'])) {
                            $chillerStockTemperature = $tempAndIsChillerStock['Temperature'];

                            // Check of chillerstock 1 is
                            if ($StockItem['IsChillerStock'] == 1) {
                                // laat temp zien wanneer chillerstock 1 is
                                echo round($chillerStockTemperature, 1) . ' °C';
                            } else {
                                // Wanneer chillerstock niet gelijk aan 1 is of niet beschikbaar is
                                echo 'Er is geen temperatuur beschikbaar voor dit product';
                            }
                        } else {
                            echo 'Temporarily unavailable';
                        }?>
                    </td>
                </tr>
                <?php
                foreach ($CustomFields as $SpecName => $SpecText) { ?>
                    <tr>
                        <td>
                            <?php print $SpecName; ?>
                        </td>
                        <td>
                            <?php
                            if (is_array($SpecText)) {
                                foreach ($SpecText as $SubText) {
                                    print $SubText . " ";
                                }
                            } else {
                                print $SpecText;
                            }
                            ?>
                        </td>
                    </tr>
                <?php } ?>
                </table><?php
            } else { ?>

                <p><?php print $StockItem['CustomFields']; ?>.</p>
                <?php
            }
            ?>
        </div>
        <hr>
        <div id="StockItemReviews">
            <h2>Reviews</h2>
            <div class="average">
                <?php
                if (sizeof($Reviews) != 0) {
                    $averageRating = $Reviews['average'];
                    $filledStars = floor($averageRating);
                    $remainingPercentage = ($averageRating - $filledStars) * 100;

                    // Display filled stars
                    for ($i = 0; $i < $filledStars; $i++) {
                        echo '<span class="filled-star">&#9733;</span>';
                    }

                    // Display empty stars
                    $emptyStars = 5 - $filledStars; // Round down
                    for ($i = 0; $i < $emptyStars; $i++) {
                        echo '<span class="empty-star">&#9733;</span>';
                    }

                    echo "<span class='avg-text'>(" . $averageRating . ")</span>";
                }


                ?>
            </div>
            <hr>

            <div class="reviews">
                <?php
                if (sizeof($Reviews) != 0) {
                    $totalStars = 0;
                    foreach ($Reviews['reviews'] as $review) {

                        echo "<b>" . $review['Title'] . "</b> (" . $review['FullName'] . ", " . date('d-m-Y', strtotime($review['Created_at'])) . ")";
                        echo "<br>";

                        // Display stars
                        $filledStars = round($review['Amount']);
                        for ($i = 0; $i < 5; $i++) {
                            echo '<span class="fa fa-star' . (($i < $filledStars) ? ' checked' : '') . '"></span>';
                        }
                        echo " (" . $review['Amount'] . ")";
                        echo "<br>";

                        // Display plus points
                        $plusPoints = array($review['PlusPoint1'], $review['PlusPoint2'], $review['PlusPoint3']);
                        $plusPoints = array_filter($plusPoints); // Remove empty values
                        if (!empty($plusPoints)) {
                            echo '<div class="row">';
                            echo '<div class="col-md-2">';
                            foreach ($plusPoints as $point) {
                                echo '<span style="color: green"> + </span>' . $point . '<br>';
                            }
                            echo '</div>';
                            echo '</div>';
                        }

                        // Display minus points
                        $minusPoints = array($review['MinusPoint1']);
                        $minusPoints = array_filter($minusPoints); // Remove empty values
                        if (!empty($minusPoints)) {
                            echo '<div class="row">';
                            echo '<div class="col-md-2">';
                            foreach ($minusPoints as $point) {
                                echo '<span style="color: red"> - </span>' . $point . '<br>';
                            }
                            echo '</div>';
                            echo '</div>';
                        }

                        echo "<br>";

                        echo "<p style='width: 50%;'>" . nl2br($review['Description']) . "</p>";
                        echo "<hr>";
                    }
                }
                ?>

            </div>
        </div>
        <?php
        echo "<div class='ri-container'>";
        $recItems = getRecommendedItems($databaseConnection, $StockItem['StockItemID']);
        if(sizeof($recItems) > 0){
            for($i = 0; $i < sizeof($recItems); $i++){
                $StockItem = getStockItem($recItems[$i]['StockItemID'], $databaseConnection);
                echo "<div class='recommended-items'>";

                $StockItemImage = getStockItemImage($StockItem['StockItemID'], $databaseConnection);
                echo $StockItem['StockItemName'] . "<br>";

                echo "<div class='product-image'>";
                if($StockItemImage){
                    echo "<img src='Public/StockItemIMG/" . $StockItemImage[0]['ImagePath'] . "' width='200px'>";
                }else{
                    echo "<img src='Public/StockGroupIMG/" . $StockItem['BackupImagePath'] . "' width='200px'>"; // Group afbeelding
                }
                echo "</div>";
                echo "<div class='product-price'>";
                echo sprintf("€ %.2f", ($StockItem['SellPrice'])) . "<br>";
                echo "</div>";
                ?>
                <form method="post" id="myForm">
                    <input type="hidden" name="productID" value="<?php print $StockItem["StockItemID"]; ?>">
                    <div class="button-container">
                        <button class="button" type="submit">Voeg toe</button>
                    </div>
                </form>

                <?php
                echo "</div>";
            }
        }
        echo "</div>";
    } else {
        ?><h2 id="ProductNotFound">Het opgevraagde product is niet gevonden.</h2><?php
    } ?>
</div>

<style>
    .ri-container {
        margin-bottom: 3%;
        margin-top: 2%;
    }

    .recommended-items {
        display: inline-block;
        margin-block: 3%;
        text-align: center;
        align-items: center;
        border: 1px solid #ddd;
        width: 23%; /* Set a fixed width for each item */
        height: 23%;
        margin: 1%; /* Adjust the margin as needed */
    }

    .product-image img {
        width: 130px;
        height: 130px;
        object-fit: cover; /* Maintain aspect ratio and cover the container */
        margin-right: 15px;
        border-radius: 8px;
    }

    .product-price {
        text-align: center;
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

    .message {
        display: none;
        text-align: ;
        color: white;
        position: fixed;
        top: 130px;
        bottom: 50px;
        left: 10px;
        right: 10px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        z-index: 9999;
    }

</style>