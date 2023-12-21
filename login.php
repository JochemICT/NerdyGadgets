<?php
include __DIR__ . "/header.php";
$databaseConnection = connectToDatabase();
?>
<div id="CenteredContent">

<?php
if(isset($_SESSION['ingelogd']) && $_SESSION['ingelogd']){ // Als de bezoeker ingelogd is, word de pagina met gegevens weergegeven
    loggedIn();
}
else { // Als de bezoeker niet is ingelogd wordt er een login pagina weergegeven
    echo "<h2>Login</h2>";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST["email"];
        $password = $_POST["wachtwoord"];
        $password = hashPassword($password); // Het ingevoerde wachtwoord wordt gehashed om te vergelijken
        $_SESSION['getEmail'] = getEmail($email, $databaseConnection); // Er wordt gecheckt of het emailadres in de database staat
        $getPassword = getPassword($_SESSION['getEmail'], $databaseConnection); // Het gehashte wachtwoord van de overeenkomende email wordt uit de database gehaald
        if ($email == $_SESSION['getEmail'] && $password == $getPassword) { // Als het email en wachtwoord overeenkomen wordt de bezoker ingelogd
            $message = "Login succesvol";
            $_SESSION['ingelogd'] = true;
            unset($_SESSION['cart']);
            $_SESSION['klantInfoArray'] = returnKlantInfo($databaseConnection, $_SESSION['getEmail']); // Een array met de klantdata wordt aangemaakt
        } else { //Wanneer het emailadres niet bekend is of het wachtwoord niet overeenkomt met het wachtwoord uit de database wordt er een error weergegeven
            $error = "Error, incorrecte email of wachtwoord";
        }
    } else {
        $email = '';
    }

    if (isset($message)) { //Nadat er is succesvol ingelogd is, word de gebruiker meteen verwezen naar de gegevens pagina
        loggedIn();
    } else { //Anders wordt de error weergegeven
        if (isset($error)) {
            echo "<p>$error</p>";
        }
        echo "<form method='post' action='login.php'>
        E-mailadres:    <br><input type='text' name='email' value='$email' required><br>
        Wachtwoord:     <br><input type='password' name='wachtwoord' required><br>
                        <input type='submit' value='Inloggen'<br><br>
                         
        </form>";
        echo "<br><h4>Ik ben nieuw hier</h4>";
        echo "<a href='registreren.php' class='button'>Registreren</a><br>";
    }
}

function loggedIn(){
    $databaseConnection = connectToDatabase();
    // Welkom en de naam van de klant worden weergegeven
    echo '<h3>Welkom ';
    if(isset($_SESSION['klantInfoArray'])) {
        echo $_SESSION['klantInfoArray'][0]["PreferredName"];
        echo "</h3><br>";
        //De klantgegevens worden uit de array gehaald
        $email = $_SESSION['klantInfoArray'][0]['LogonName'];
        $hn = $_SESSION['klantInfoArray'][1]['DeliveryAddressLine1'];
        $sn = $_SESSION['klantInfoArray'][1]['DeliveryAddressLine2'];
        $pc = $_SESSION['klantInfoArray'][1]['DeliveryPostalCode'];
        $wp = $_SESSION['klantInfoArray'][1]['DeliveryLocation'];
        echo "<div>
        <form method='post'>
        Straatnaam: <br><input type='text' name='straatnaam' value='$sn'><br>
        Huisnummer: <br><input type='text' name='huisnummer' value='$hn'><br>
        Postcode: <br><input type='text' name='postcode' value='$pc'><br>
        Woonplaats: <br><input type='text' name='woonplaats' value='$wp'><br>
        <input type='submit' name='update' value='Gegevens updaten'><br>
        </form>
        </div>";
        if (isset($_POST['update'])) {//De aangepaste gegevens worden aan de hand van een functie in de database aangepast
            if (updateKlantInfo($databaseConnection, PersonID($databaseConnection, $email), $_POST['huisnummer'], $_POST['straatnaam'], $_POST['postcode'], $_POST['woonplaats'])) {
                $_SESSION['klantInfoArray'] = returnKlantInfo($databaseConnection, $email); //De geupdate klantgegevens worden in de form aangepast
                echo "Gegevens sucessvol aangepast";
            } else {
                echo "Error, er is iets mis gegaan.";
            }
        }
    }

    echo "<form method='post'>";
    echo "<input type='submit' name='submitLogout' value='Uitloggen' class='logOut'>";
    echo "</form>";

    if (isset($_POST['submitLogout'])) { //Bij het uitloggen worden de klantgegevens en de inhoud van het winkelmandje ge-unset
        $_SESSION['ingelogd'] = false;
        unset($_SESSION['klantInfoArray']);
        unset($_SESSION['personID']);
        unset($_SESSION['cart']);
        echo "Sucessvol uitgelogd";
    }
}
?>
</div>

<style>
    input[type=text], input[type=password] {
        width: 300px;
        padding: 12px 20px;
        margin: 8px 0;
        box-sizing: border-box;
        border: 3px solid #ccc;
        -webkit-transition: 0.5s;
        transition: 0.5s;
        outline: none;
    }

    input[type=submit] {
        width: 300px;
        padding: 12px 20px;
        margin: 8px 0;
        box-sizing: border-box;
        border: 3px solid #ccc;
        -webkit-transition: 0.2s;
        transition: 0.2s;
        outline: none;
    }

    input[type=text]:focus, input[type=password]:focus{
        border: 3px solid #373766;
    }

    input[type=submit]:focus{
        background-color: #4CAF50;
        color: white;
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

    .logOut {
        display: inline-block;
        margin-right: 10px;
        padding: 10px 15px;
        background-color: darkred;
        color: white;
        text-decoration: none;
    }

    input[class='logOut']:focus{
        background-color: red;
        color: white;
    }
</style>