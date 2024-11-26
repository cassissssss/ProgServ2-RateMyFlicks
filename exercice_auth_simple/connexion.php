<?php

session_start();

//phpinfo();
require_once('./config/autoload.php');
use functionnalities\DbManagerCRUD;

$msg = [];
$err = [];

if (filter_has_var(INPUT_GET, "activated")) {
  $msg[] = "Le compte a été activé";
}

if (filter_has_var(INPUT_POST, "disconnect")) {
  $_SESSION = array();
  session_destroy();
  header("Location: ./index.php");
  exit();
}

if (filter_has_var(INPUT_POST, "submit")) {
  $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);
  $pwd = filter_input(INPUT_POST, "password", FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,25}$/')));

  if (!$email) {
    $err[] = "Veuillez renseigner votre adresse mail";
  }
  if (!$pwd) {
    $err[] = "Veuillez renseigner un mot de passe d'une longueur comprise entre 8 et 25 caractères, contenant au minimum 1 majuscule, 1 minuscule, 1 chiffre et 1 caractère spécial ( @$!%*?& )";
  }

  if (!$err) {
    $db = new DbManagerCRUD();

    $user = $db->rendPersonneEmail($email);

    if ($user) {
      $isAccountActivated = $db->rendPersonneTokenId($user[0]->rendId()) == "" ? true:false;
      $isPasswordOk = password_verify($pwd, $user[0]->rendMdp());
      if ($isPasswordOk && $isAccountActivated) {
        $_SESSION["isConnected"] = true;
        $_SESSION["userEmail"] = $user[0]->rendEmail();
        header("Location: ./index.php");
        exit();
      } else {
        $err[] = "Le mot de passe n'est pas correct";
      }
    } else {
      $err[] = "Le compte n'existe pas";
    }

  }
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
    <a href="./index.php">Accueil</a>
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
        <a href="./connexion.php" class="active">Connexion</a>
        <a href="./creercompte.php">Créer un compte</a>
        HEREDOC;
    }
    ?>
  </div>

  <!-- Contenu principal -->
  <div class="main">
    <h1>Connexion</h1>

    <div class="err" <?php if (!$err)
      echo "style='display: none';"; ?>>
      <?php
      if ($err) {
        foreach ($err as $erreur) {
          echo "<p>" . $erreur . "</p>";
        }
      }
      ?>
    </div>

    <div class="msg" <?php if (!$msg)
      echo "style='display: none';"; ?>>
      <?php
      if ($msg) {
        foreach ($msg as $message) {
          echo "<p>" . $message . "</p>";
        }
      }
      ?>
    </div>


    <!-- Formulaire de création de compte -->
    <div class="form-container">
      <form action="connexion.php" method="post">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required placeholder="john.doe@gmail.com">

        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" required>

        <input type="submit" name="submit" value="envoyer">
      </form>
    </div>

  </div>

  </div>

</body>

</html>