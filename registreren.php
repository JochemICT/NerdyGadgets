<?php
include __DIR__ . "/header.php";
$databaseConnection = connectToDatabase();
?>
<div id="CenteredContent">
<h2>Registreren</h2>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Hier worden de values in de invulvelden automatisch ingevuld
    $voornaam = $_POST["voornaam"];
    $achternaam = $_POST["achternaam"];
    $email = $_POST["email"];
    $wachtwoord = $_POST["wachtwoord"];
    $herhaalww = $_POST["herhaalww"];
    $straatnaam = $_POST["straat"];
    $huisnummer = $_POST["huisnr"];
    $postcode = $_POST["postcode"];
    $woonplaats = $_POST["woonplaats"];
    $land = $_POST["land"];

    $getEmail = getEmail($email, $databaseConnection);

    if ($wachtwoord != $herhaalww) {
        $ww_error = "Wachtwoorden komen niet overeen!";
    }
    elseif ($email == $getEmail){ // Als de email al bekend is in de database krijg je een melding
        $email_error = "Een account met deze email bestaat al!";
    }
    else{ //Als de registratie is goedgekeurd wordt de data van de nieuwe klant in de database toegevoegd
        $wachtwoord = hashPassword($wachtwoord);
        if(insertRegistration($voornaam, $achternaam, $email, $wachtwoord, $straatnaam, $huisnummer, $postcode, $woonplaats, $land, $databaseConnection)){
            $status_message = "Registreren gelukt!";
            // Het invulformulier wordt leeg gemaakt
            $voornaam = "";
            $achternaam = "";
            $email = "";
            $wachtwoord = "";
            $herhaalww = "";
            $straatnaam = "";
            $huisnummer = "";
            $postcode = "";
            $woonplaats = "";
            $land = "";
        }
        else{
            $status_message = "Er is iets fout gegaan, probeer opnieuw";
        }


    }
}
else{ // De registratiepagina is leeg bij het eerst opstarten van de registratiepagina
    $voornaam = "";
    $achternaam = "";
    $email = "";
    $wachtwoord = "";
    $herhaalww = "";
    $straatnaam = "";
    $huisnummer = "";
    $postcode = "";
    $woonplaats = "";
    $land = "";
}

echo   "
<div class='flex-container'>
    <div>
        <form action='registreren.php' method='post'>
        Voornaam: <br><input type='text' name='voornaam' value='$voornaam' required><br>
        Achternaam: <br><input type='text' name='achternaam' value='$achternaam' required><br>
        E-mail adres: <br><input type='email' name='email' value='$email' required><br>
        Wachtwoord: <br><input type='password' name='wachtwoord' value='$wachtwoord' minlength='6' required><br>
        Wachtwoord herhalen: <br><input type='password' name='herhaalww' value='$herhaalww' required>
    </div>
    <div>
        Straatnaam: <br><input type='text' name='straat' value='$straatnaam' required><br>
        Huisnummer: <br><input type='text' name='huisnr' value='$huisnummer' required><br>
        Postcode: <br><input type='text' name='postcode' value='$postcode' required><br>
        Woonplaats: <br><input type='text' name='woonplaats' value='$woonplaats' required><br>
        Land: <br><input type='text' name='land' value='$land' required>
    </div>
    <div>
        <br><input type='submit' value='Registreren'></form>";
// De verschillende errors worden hier weergegeven
    if(isset($ww_error)){
        echo "<p>$ww_error</p>";
    }
    if(isset($email_error)){
        echo"<p>$email_error</p>";
    }
    if(isset($status_message)){
        echo "<p>$status_message</p>";
    }
echo "
    </div>
</div>
";
?>
</div>

<style>
    input[type=password] {
        width: 300px;
        padding: 12px 20px;
        margin: 8px 0;
        box-sizing: border-box;
        <?php
        if(isset($ww_error)){
            echo "border: 3px solid red;";
        }
        else{
            echo "border: 3px solid #ccc;";
        }
        ?>
        -webkit-transition: 0.5s;
        transition: 0.5s;
        outline: none;
    }

    input[type=email]{
        width: 300px;
        padding: 12px 20px;
        margin: 8px 0;
        box-sizing: border-box;
        <?php
        if(isset($email_error)){
            echo "border: 3px solid red;";
        }
        else{
            echo "border: 3px solid #ccc;";
        }
        ?>
        -webkit-transition: 0.5s;
        transition: 0.5s;
        outline: none;
    }

    input[type=text]{
        width: 300px;
        padding: 12px 20px;
        margin: 8px 0;
        box-sizing: border-box;
        border: 3px solid #ccc;
        -webkit-transition: 0.5s;
        transition: 0.5s;
        outline: none;
    }

    input[type=text]:focus, input[type=password]:focus{
        border: 3px solid #373766;
    }

    .flex-container{
        display: flex;
        justify-content: space-around;
        padding-bottom: 40px;
    }

    input[type=submit] {
        width: 300px;
        background-color: #4CAF50;
        color: white;
        padding: 12px 20px;
        margin: 8px 0;
        box-sizing: border-box;
        border: 3px solid #ccc;
        -webkit-transition: 0.2s;
        transition: 0.2s;
        outline: none;
    }

    input[type=submit]:focus{
        background-color: #1c7430;
    }
</style>