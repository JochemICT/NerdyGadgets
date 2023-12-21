<!-- dit bestand bevat alle code die verbinding maakt met de database -->
<?php

include __DIR__ . "/SQL_queries.php";

function connectToDatabase() {
    $Connection = null;

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Set MySQLi to throw exceptions
    try {
        $Connection = mysqli_connect("localhost", "root", "", "nerdygadgets");
        mysqli_set_charset($Connection, 'latin1');
        $DatabaseAvailable = true;
    } catch (mysqli_sql_exception $e) {
        $DatabaseAvailable = false;
    }
    if (!$DatabaseAvailable) {
        ?><h2>Website wordt op dit moment onderhouden.</h2><?php
        die();
    }

    return $Connection;
}

function getHeaderStockGroups($databaseConnection) {
    $Query = "
                SELECT StockGroupID, StockGroupName, ImagePath
                FROM stockgroups 
                WHERE StockGroupID IN (
                                        SELECT StockGroupID 
                                        FROM stockitemstockgroups
                                        ) AND ImagePath IS NOT NULL
                ORDER BY StockGroupID ASC";
    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_execute($Statement);
    $HeaderStockGroups = mysqli_stmt_get_result($Statement);
    return $HeaderStockGroups;
}

function getStockGroups($databaseConnection) {
    $Query = "
            SELECT StockGroupID, StockGroupName, ImagePath
            FROM stockgroups 
            WHERE StockGroupID IN (
                                    SELECT StockGroupID 
                                    FROM stockitemstockgroups
                                    ) AND ImagePath IS NOT NULL
            ORDER BY StockGroupID ASC";
    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_execute($Statement);
    $Result = mysqli_stmt_get_result($Statement);
    $StockGroups = mysqli_fetch_all($Result, MYSQLI_ASSOC);
    return $StockGroups;
}

function getStockItem($id, $databaseConnection) {
    $Result = null;

    $Query = " 
           SELECT SI.StockItemID, 
            (RecommendedRetailPrice*(1+(TaxRate/100))) AS SellPrice, 
            StockItemName,
            CONCAT('Voorraad: ',QuantityOnHand)AS QuantityOnHand,
            SearchDetails, 
            (CASE WHEN (RecommendedRetailPrice*(1+(TaxRate/100))) > 50 THEN 0 ELSE 6.95 END) AS SendCosts, MarketingComments, CustomFields, SI.Video,
            (SELECT ImagePath FROM stockgroups JOIN stockitemstockgroups USING(StockGroupID) WHERE StockItemID = SI.StockItemID LIMIT 1) as BackupImagePath   
            FROM stockitems SI 
            JOIN stockitemholdings SIH USING(stockitemid)
            JOIN stockitemstockgroups ON SI.StockItemID = stockitemstockgroups.StockItemID
            JOIN stockgroups USING(StockGroupID)
            WHERE SI.stockitemid = ?
            GROUP BY StockItemID";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "i", $id);
    mysqli_stmt_execute($Statement);
    $ReturnableResult = mysqli_stmt_get_result($Statement);
    if ($ReturnableResult && mysqli_num_rows($ReturnableResult) == 1) {
        $Result = mysqli_fetch_all($ReturnableResult, MYSQLI_ASSOC)[0];
    }

    return $Result;
}

function getStockItemImage($id, $databaseConnection) {

    $Query = "
                SELECT ImagePath
                FROM stockitemimages 
                WHERE StockItemID = ?";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "i", $id);
    mysqli_stmt_execute($Statement);
    $R = mysqli_stmt_get_result($Statement);
    $R = mysqli_fetch_all($R, MYSQLI_ASSOC);

    return $R;
}

function getEmail($email, $databaseConnection) {
    $query = "
        SELECT LogonName
        FROM people
        WHERE LogonName = '$email';
    ";

    $result = mysqli_query($databaseConnection, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        if ($row) {
            return $row['LogonName'];
        }
    }
    return null;
}

function getPassword($email, $databaseConnection){
    $query = "
        SELECT HashedPassword
        FROM people
        WHERE LogonName = '$email';
    ";

    $result = mysqli_query($databaseConnection, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        if ($row) {
            return $row['HashedPassword'];
        }
    }
    return null;
}

function hashPassword($password){
    $password = hash('sha256', $password);
    return $password;
}

function insertRegistration($voornaam, $achternaam, $email, $wachtwoord, $straatnaam, $huisnummer, $postcode, $woonplaats, $land, $databaseConnection) {
    mysqli_query($databaseConnection, PeopleQuery($voornaam, $achternaam, $email, $wachtwoord));
    $personID = PersonID($databaseConnection, $email);
    mysqli_query($databaseConnection, CustomersQuery($voornaam, $achternaam, $personID, $huisnummer, $straatnaam, $postcode, $woonplaats, $land));
    return true;
}

function wijzigVoorraad($id, $aantal, $databaseConnection){
    $query = "
        UPDATE stockitemholdings
        SET QuantityOnHand=QuantityOnHand - ?
        WHERE StockItemID=?";

    $Statement = mysqli_prepare($databaseConnection, $query);
    mysqli_stmt_bind_param($Statement, "ii", $aantal, $id);
    $result = mysqli_stmt_execute($Statement);


    return $result;
}

function addReview($StockItemID, $CustomerID, $Title, $Description, $Amount, $PlusPoint1, $PlusPoint2, $PlusPoint3, $MinusPoint1, $MinusPoint2, $MinusPoint3){
    $databaseConnection = connectToDatabase();

    $query = "INSERT INTO reviews (StockItemID, CustomerID, Title, Description, Amount, PlusPoint1, PlusPoint2, PlusPoint3, MinusPoint1, MinusPoint2, MinusPoint3) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $Statement = mysqli_prepare($databaseConnection, $query);
    mysqli_stmt_bind_param($Statement, "iississssss", $StockItemID, $CustomerID, $Title, $Description, $Amount, $PlusPoint1, $PlusPoint2, $PlusPoint3, $MinusPoint1, $MinusPoint2, $MinusPoint3);
    mysqli_stmt_execute($Statement);
    $result = mysqli_stmt_get_result($Statement);

    return $result;

}