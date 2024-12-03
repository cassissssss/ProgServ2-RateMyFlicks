<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($currentLang, ENT_QUOTES, 'UTF-8'); ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="./styles/style.css" rel="stylesheet">
    <title>RateMyFlicks</title>
</head>

<body>
    <div class="navbar">
        <a class="<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ""); ?>" href="./index.php"><?php echo t('home'); ?></a>
        <?php if (isset($_SESSION["isConnected"]) && $_SESSION["isConnected"]) { ?>
            <a href="./espacemembre.php"><?php echo t('memberArea'); ?></a>
            <form id="disconnect-form" method="post" action="./index.php" class="nav-right">
                <input id="disconnect" type="submit" name="disconnect" value="<?php echo t('logout'); ?>">
            </form>
        <?php } else { ?>
            <a href="./connexion.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'connexion.php' ? 'active' : ""); ?>"><?php echo t('loginTitle'); ?></a>
            <a href="./creercompte.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'creercompte.php' ? 'active' : ""); ?>"><?php echo t('createAccount'); ?></a>
            <div class="nav-right">
                <?php if (getLanguage() === 'fr') { ?>
                    <a href="?lang=en">EN</a>
                <?php } else { ?>
                    <a href="?lang=fr">FR</a>
                <?php } ?>
            </div>
        <?php } ?>
    </div>