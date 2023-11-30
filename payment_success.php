<?php

include __DIR__ . "/header.php";
session_destroy();

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

?>

<div id="CenteredContent">
    <h2>Bedankt voor je bestelling</h2>

            <table style="margin-bottom: 50px; margin-left: -9px">
                <?php
                if (isset($session->customer_details)) {
                    $shippingAddress = $session->customer_details->address->line1;
                    $shippingCity = $session->customer_details->address->city;
                    $shippingPostalCode = $session->customer_details->address->postal_code;

                    echo '<tr>';
                    echo '<th>Adres</th>';
                    echo '<td>' . $shippingAddress . '</td>';
                    echo '</tr>';

                    echo '<tr>';
                    echo '<th>Stad</th>';
                    echo '<td>' . $shippingCity . '</td>';
                    echo '</tr>';

                    echo '<tr>';
                    echo '<th>Postcode</th>';
                    echo '<td>' . $shippingPostalCode . '</td>';
                    echo '</tr>';
                }
                ?>
            </table>

            <table style="margin-left: -9px">
                <tr>
                    <th>Betaalmethode</th>
                    <td><?php echo $payment_intent_obj->payment_method_types[0] ?></td>
                </tr>

                <tr>
                    <th>Status</th>
                    <td><?php
                        if($payment_intent_obj->status == 'succeeded'){
                            echo "betaald";
                        } else {
                            echo "in behandeling.";
                        } ?></td>
                </tr>
            </table>
</div>