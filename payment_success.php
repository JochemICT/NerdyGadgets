<?php

include __DIR__ . "/header.php";

require_once 'vendor/stripe/stripe-php/init.php';

$sck = "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";
\Stripe\Stripe::setApiKey($sck);

$session_id = $_GET['session_id']; // Get the session ID from the query parameters
if(!$session_id){
    header("Location: cart.php");
}

$session = \Stripe\Checkout\Session::retrieve($session_id);
$payment_intent = $session->payment_intent;
$payment_intent_obj = \Stripe\PaymentIntent::retrieve($payment_intent);



foreach($_SESSION['order_info']['products'] as $product){
    $id = $product['productID'];
    $aantal = $product['quantity'];

    wijzigVoorraad($id, $aantal, $databaseConnection);
}

?>

<div id="CenteredContent">
    <h2>Bedankt voor je bestelling.</h2>

<?php
    foreach ($_SESSION['order_info']['products'] as $product) {
    $aantal = $product['quantity'];
    $StockItemImage = getStockItemImage($product['productID'], $databaseConnection);
    $StockItem = getStockItem($product['productID'], $databaseConnection);

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
        echo "<p><strong>{$StockItem['StockItemName']}</strong> x{$aantal}</p>";
        echo "<p>{$StockItem['SearchDetails']}</p>";
        echo "</div>";

        echo "<button type='button' class='btn btn-primary' data-toggle='modal' data-target='#reviewModal{$StockItem['StockItemID']}'>
            Review schrijven
        </button>";

        echo "<div class='modal fade' id='reviewModal{$StockItem['StockItemID']}' tabindex='-1' role='dialog' aria-labelledby='reviewModalLabel' aria-hidden='true'>
        <div class='modal-dialog' role='document'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h5 class='modal-title' id='reviewModalLabel'>Review schrijven</h5>
                    <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                        <span aria-hidden='true'>&times;</span>
                    </button>
                </div>
                <div class='modal-body'>
                    <form id='reviewForm{$StockItem['StockItemID']}' action='submit_review.php' method='POST'>
                        <input type='hidden' name='productID' value='" . $StockItem['StockItemID'] . "'>
                        <label for='review_title' style='color: black;'>Titel:</label>
                        <input type='text' name='review_title' required><br>
                        <label for='review_description' style='color: black;'>Beschrijving:</label>
                        <textarea name='review_description' required></textarea><br>
                        <label for='review_pros_1' style='color: black;'>Pluspunt 1:</label>
                        <input type='text' name='review_pros_1' maxlength='100'><br>
                        <label for='review_pros_2' style='color: black;'>Pluspunt 2:</label>
                        <input type='text' name='review_pros_2' maxlength='100'><br>
                        <label for='review_pros_3' style='color: black;'>Pluspunt 3:</label>
                        <input type='text' name='review_pros_3' maxlength='100'><br>
                        <label for='review_cons_1' style='color: black;'>Minpunt 1:</label>
                        <input type='text' name='review_cons_1' maxlength='100'><br>
                        <label for='review_cons_2' style='color: black;'>Minpunt 2:</label>
                        <input type='text' name='review_cons_2' maxlength='100'><br>
                        <label for='review_cons_3' style='color: black;'>Minpunt 3:</label>
                        <input type='text' name='review_cons_3' maxlength='100'><br>
                        
    <label for='review_ranking' style='color: black;'>Ranking (1 tot 5):</label>
    <div class='form-check form-check-inline'>
        <input class='form-check-input' type='radio' name='review_ranking' value='1' required>
        <label class='form-check-label' style='color: black;'>1</label>
    </div>

    <div class='form-check form-check-inline'>
        <input class='form-check-input' type='radio' name='review_ranking' value='2'>
        <label class='form-check-label' style='color: black;'>2</label>
    </div>

    <div class='form-check form-check-inline'>
        <input class='form-check-input' type='radio' name='review_ranking' value='3'>
        <label class='form-check-label' style='color: black;'>3</label>
    </div>

    <div class='form-check form-check-inline'>
        <input class='form-check-input' type='radio' name='review_ranking' value='4'>
        <label class='form-check-label' style='color: black;'>4</label>
    </div>

    <div class='form-check form-check-inline'>
        <input class='form-check-input' type='radio' name='review_ranking' value='5'>
        <label class='form-check-label' style='color: black;'>5</label>
    </div>
                        
                    </form>                <button type='button' class='btn btn-primary' onclick='submitReview({$StockItem['StockItemID']})'>Plaats review</button>

                </div>
            </div>
        </div>
    </div>";

    echo "</div>";
    }
?>
</div>

<script>
    function submitReview(stockItemID) {
        var title = $('#reviewForm' + stockItemID + ' [name="review_title"]').val();
        var description = $('#reviewForm' + stockItemID + ' [name="review_description"]').val();
        var rating = $('#reviewForm' + stockItemID + ' [name="review_ranking"]:checked').val();

        if (!title || !description || !rating) {
            alert('De titel, beschrijving en rating zijn verplichte velden!');
            return;
        }

        $.ajax({
            type: 'POST',
            url: 'submit_review.php', // Vervang dit door de juiste URL voor de verwerking van de review op de server
            data: $('#reviewForm' + stockItemID).serialize(),
            success: function(response) {
                // Voeg hier eventuele logica toe voor succesvolle verwerking
                console.log(response);

                // Sluit het Bootstrap-popupvenster
                $('#reviewModal' + stockItemID).modal('hide');
                alert("Review successvol toegevoegd.");
            },
            error: function(error) {
                // Voeg hier eventuele logica toe voor fouten
                console.error(error);
            }
        });
    }
</script>

<style>
    .review-title {
        font-size: 16px;
        margin: 15px 0;
    }

    label {
        display: block;
        margin-bottom: 5px;
    }

    textarea {
        width: 100%;
        height: 80px;
        resize: vertical;
    }

    .submit-review-btn {
        padding: 10px 15px;
        background-color: #007BFF;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .submit-review-btn:hover {
        background-color: #0056b3;
    }
</style>