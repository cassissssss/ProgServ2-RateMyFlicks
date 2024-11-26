<?php
session_start();
//phpinfo();
require_once('./config/autoload.php');

if (filter_has_var(INPUT_POST, "disconnect")) {
  $_SESSION = array();
  session_destroy();
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="./styles/style.css" rel="stylesheet">
  <title>Exercice Authentification Simple</title>
</head>

<body>

  <!-- Barre de navigation -->
  <div class="navbar">
    <a href="./index.php" class="active">Accueil</a>
    <?php
    if (isset($_SESSION["isConnected"]) && $_SESSION["isConnected"]) {
      echo <<<HEREDOC
        <a href="./espacemembre.php">Espace membre</a>
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

  <!-- Contenu principal -->
  <div class="main">
    <h1>Bienvenue sur notre site</h1>
    <p>Cliquez sur les liens ci-dessus pour naviguer.</p>
  </div>

</body>

</html>