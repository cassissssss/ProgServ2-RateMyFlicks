<?php

session_start();
//phpinfo();
require_once('./config/autoload.php');
use functionnalities\DbManagerCRUD;

if (filter_has_var(INPUT_POST, "disconnect")) {
    $_SESSION = array();
    session_destroy();
    header("Location: ./index.php");
    exit();
}

if (isset($_SESSION["isConnected"]) && $_SESSION["isConnected"]) {
    $db = new DbManagerCRUD();

    $users = $db->rendPersonneEmail($_SESSION["userEmail"]);
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="./styles/style.css" rel="stylesheet">
    <title>Espace membre top secret</title>
</head>

<body>
    <!-- Barre de navigation -->
    <div class="navbar">
        <a href="./index.php">Accueil</a>
        <?php
        if (isset($_SESSION["isConnected"]) && $_SESSION["isConnected"]) {
            echo <<<HEREDOC
        <a href="./espacemembre.php" class="active">Espace membre</a>
        <form id="disconnect-form" method="post" action="./index.php">
        <input id="disconnect" type="submit" name="disconnect" value="Déconnexion">
        </form>
        HEREDOC;
        } else {
            echo <<<HEREDOC
        <a href="./connexion.php">Connexion</a>
        <a href="./creercompte.php">Créer un compte</a>
        HEREDOC;
        }
        ?>
    </div>

    <div class="main">
        <?php if (isset($_SESSION["isConnected"]) && $_SESSION["isConnected"]) {
            echo <<<HEREDOC
        <h1>Espace membre top secret</h1>
        <button id="topsecret">Bouton top secret</button>
        <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
        <script>
            const secretButton = document.querySelector("#topsecret")
            secretButton.addEventListener("click", () => {
                confetti({
                    particleCount: 100,
                    startVelocity: 30,
                    spread: 360,
                });
            })
        </script>
        HEREDOC;
        } else {
            echo <<<HEREDOC
        <h1>Vous n'avez pas accès à l'espace membre top secret</h1>
        <a href="./connexion.php">Veuillez vous connecter</a>
        HEREDOC;
        }
        ?>
    </div>
</body>

</html>