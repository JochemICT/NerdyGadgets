<?php

function PersonID($databaseConnection, $email){
    $query_personID = "
        SELECT PersonID
        FROM people
        WHERE LogonName = '$email';
    ";
    $result_personID = mysqli_query($databaseConnection, $query_personID);
    if ($result_personID) {
        $row = mysqli_fetch_assoc($result_personID);
        if ($row) {
            $personID = $row['PersonID'];
        }
    }
    return $personID;
} // Deze functie returnt de PersonID van de klant met behulp van de gebruikersnaam

function CustomersQuery($voornaam, $achternaam, $personID, $huisnummer, $straatnaam, $postcode, $woonplaats, $land){
    $fullName = "$voornaam $achternaam";
    
    $query_customers = "
    INSERT INTO customers (
                CustomerName,
                BillToCustomerID,
                CustomerCategoryID,
                BuyingGroupID,
                PrimaryContactPersonID,
                AlternateContactPersonID,
                DeliveryMethodID,
                DeliveryCityID,
                PostalCityID,
                CreditLimit,
                AccountOpenedDate,
                StandardDiscountPercentage,
                IsStatementSent,
                IsOnCreditHold,
                PaymentDays,
                PhoneNumber,
                FaxNumber,
                DeliveryRun,
                RunPosition,
                WebsiteURL,
                DeliveryAddressLine1,
                DeliveryAddressLine2,
                DeliveryPostalCode,
                DeliveryLocation,
                PostalAddressLine1,
                PostalAddressLine2,
                PostalPostalCode,
                LastEditedBy,
                ValidFrom,
                ValidTo
    ) VALUES (
                '$fullName',
                1,
                1,
                NULL,
                $personID,
                NULL,
                1,
                1,
                1,
                1000.00,
                '2023-01-01',
                0.000,
                0,
                0,
                7,
                'telefoonnummer',
                'faxnummer',
                NULL,
                NULL,
                'http://www.microsoft.com',
                '$huisnummer',
                '$straatnaam',
                '$postcode',
                '$woonplaats',
                '$land',
                NULL,
                'Postcode2',
                1, -- Replace with the appropriate LastEditedBy ID
                '2023-01-01 00:00:00', -- Replace with the appropriate ValidFrom datetime
                '9999-12-31 23:59:59' -- Replace with the appropriate ValidTo datetime
            );
    ";
    return $query_customers;
} // Deze functie returnt de query om een entiteit in de 'customers' tabel toe te voegen

function PeopleQuery($voornaam, $achternaam, $email, $wachtwoord){
    $fullName = "$voornaam $achternaam";

    $query_people = "
    INSERT INTO people (
                FullName,
                PreferredName,
                SearchName,
                IsPermittedToLogon,
                LogonName,
                IsExternalLogonProvider,
                HashedPassword,
                IsSystemUser,
                IsEmployee,
                IsSalesperson,
                UserPreferences,
                PhoneNumber,
                FaxNumber,
                EmailAddress,
                Photo,
                CustomFields,
                OtherLanguages,
                LastEditedBy,
                ValidFrom,
                ValidTo
                )
    VALUES (
                '$fullName',
                '$voornaam',
                '$fullName',
                1,
                '$email',
                0,
                '$wachtwoord',
                0,
                0,
                0,
                '',
                'telefoonnummer',
                'faxnummer',
                '$email',
                NULL,
                '',
                '',
                1,
                '2023-01-01 00:00:00',
                '9999-12-31 23:59:59'
                );
    ";

    return $query_people;
} // Deze functie returnt de query om een nieuwe entiteit in de 'people' tabel toe te voegen

function returnKlantInfo($databaseConnection, $email){
    $personID = PersonID($databaseConnection, $email);
    $klantInfoAdres = array();
    $klantInfoNaam = array();

    $adresQuery = "  SELECT DeliveryAddressLine1, DeliveryAddressLine2, DeliveryPostalCode, DeliveryLocation, PostalAddressLine1
                FROM customers
                WHERE PrimaryContactPersonID = $personID;";
    $namenQuery = "   SELECT PreferredName, FullName, LogonName
                FROM people
                WHERE PersonID = $personID;";

    $adres = mysqli_query($databaseConnection, $adresQuery);
    $namen = mysqli_query($databaseConnection, $namenQuery);

    while ($row = mysqli_fetch_assoc($adres)) {
        $klantInfoAdres[] = $row;
    }

    while ($row = mysqli_fetch_assoc($namen)) {
        $klantInfoNaam[] = $row;
    }

    $results = array_merge($klantInfoNaam, $klantInfoAdres);
    return $results;
} // Deze functie returnt een array met verschillende nodige gegevens van de klant

function updateKlantInfo($databaseConnection, $personID, $huisnummer, $straatnaam, $postcode, $woonplaats){
    $updateQuery = "
    UPDATE customers
    SET DeliveryAddressLine1 = $huisnummer, DeliveryAddressLine2 = '$straatnaam', DeliveryPostalCode = '$postcode', DeliveryLocation = '$woonplaats'
    WHERE PrimaryContactPersonID = $personID
    ";

    return mysqli_query($databaseConnection, $updateQuery);
} // Deze functie wordt gebruikt om de gegevens van de klant aan te passen