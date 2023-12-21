<?php
include __DIR__ . "/header.php";


$naam = $_SESSION['klantInfoArray'][0]["PreferredName"] ?? "";
$email = $_SESSION['klantInfoArray'][0]['LogonName'] ?? "";
$hn = $_SESSION['klantInfoArray'][1]['DeliveryAddressLine1'] ?? "";
$sn = $_SESSION['klantInfoArray'][1]['DeliveryAddressLine2'] ?? "";
$pc = $_SESSION['klantInfoArray'][1]['DeliveryPostalCode'] ?? "";
$wp = $_SESSION['klantInfoArray'][1]['DeliveryLocation'] ?? "";

?>

<div id="CenteredContent">
    <h2>Bestelgegevens</h2>

    <form method="post" action="checkout.php">
        <div class="personal_info">
            <label for="name">Naam:</label>
            <input type="text" id="name" name="name" value="<?php echo $naam?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo $email?>" required>

            <label for="street">Straat:</label>
            <input type="text" id="street" name="street" value="<?php echo $sn?>" required>

            <label for="house_number">Huisnummer:</label>
            <input type="number" id="house_number" name="house_number" value="<?php echo $hn?>" required>

            <label for="postal_code">Postcode:</label>
            <input type="text" id="postal_code" name="postal_code" pattern="^[1-9][0-9]{3}[[:space:]]{0,1}(?!SA|SD|SS)[A-Z]{2}$" value="<?php echo $pc?>" required>

            <label for="city">Woonplaats:</label>
            <input type="text" id="city" name="city" value="<?php echo $wp?>" required>
        </div>

        <br>
        <button type="submit" class="button">Bestel</button>
    </form>
</div>

