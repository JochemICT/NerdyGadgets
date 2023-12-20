<!-- de inhoud van dit bestand wordt bovenaan elke pagina geplaatst -->
<?php
session_start();
include "database.php";
$databaseConnection = connectToDatabase();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>NerdyGadgets</title>
<!--test-->
    <!-- Javascript -->
    <script src="Public/JS/fontawesome.js"></script>
    <script src="Public/JS/jquery.min.js"></script>
    <script src="Public/JS/bootstrap.min.js"></script>
    <script src="Public/JS/popper.min.js"></script>
    <script src="Public/JS/resizer.js"></script>

    <!-- Style sheets-->
    <link rel="stylesheet" href="Public/CSS/style.css" type="text/css">
    <link rel="stylesheet" href="Public/CSS/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="Public/CSS/typekit.css">
</head>
<body>
<div class="Background">
    <div class="row" id="Header">
        <div class="col-2"><a href="./" id="LogoA">
                <div id="LogoImage"></div>
            </a></div>
        <div class="col-8" id="CategoriesBar">
            <ul id="ul-class">
                <?php
                $HeaderStockGroups = getHeaderStockGroups($databaseConnection);

                foreach ($HeaderStockGroups as $HeaderStockGroup) {
                    ?>
                    <li>
                        <a href="browse.php?category_id=<?php print $HeaderStockGroup['StockGroupID']; ?>"
                           class="HrefDecoration"><?php print $HeaderStockGroup['StockGroupName']; ?></a>
                    </li>
                    <?php
                }
                ?>
                <li>
                    <a href="categories.php" class="HrefDecoration">Alle categorieën</a>
                </li>
            </ul>
        </div>
<!-- code voor US3: zoeken -->
        <ul id="ul-class-navigation">
            <li>
                <a href="login.php" class="HrefDecoration"><i style="color: #676eff" class="fa fa-user"></i>
                <?php
                // Wanneer de bezoeker is ingelogd wordt zijn/haar naam weergegeven, zo niet wordt 'inloggen' weergegeven
                $inlog = "Inloggen";
                if(isset($_SESSION['ingelogd']) && $_SESSION['ingelogd'] && $_SESSION['klantInfoArray'][0]["PreferredName"]){
                    $inlog = $_SESSION['klantInfoArray'][0]["PreferredName"];
                }
                echo "$inlog";
                ?>
                </a>
            </li>
            <li>
                <a href="browse.php" class="HrefDecoration"><i class="fas fa-search search"></i> Zoeken</a>
            </li>
            <li class="nav-item">
                <a href="cart.php" class="HrefDecoration">
                    <i style="color: #676eff" class="fas fa-shopping-cart"></i>
                    <?php
                    $cartTotal = 0;
                    if (isset($_SESSION['cart'])) {
                        foreach ($_SESSION['cart'] as $item) {
                            $cartTotal++;
                        }
                    }
                    echo "( " . $cartTotal . " )";
                    ?>
                </a>
            </li>
        </ul>
<!-- einde code voor US3 zoeken -->
    </div>
    <div class="row" id="Content">
        <div class="col-12">
            <div id="SubContent">


