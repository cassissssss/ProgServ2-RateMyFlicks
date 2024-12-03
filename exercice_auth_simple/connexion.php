<?php
session_start();

// Inclure les fichiers nécessaires
require_once('./config/autoload.php');

use functionnalities\DbManagerCRUD;

require_once('lang' . DIRECTORY_SEPARATOR . 'lang_func.php');

$currentLang = getLanguage();

$msg = [];
$err = [];

// Vérifier si le compte a été activé
if (filter_has_var(INPUT_GET, "activated")) {
  $msg[] = t('activatedAccount'); // Utilisation de la clé dans le fichier de langue
}

// Déconnexion de l'utilisateur
if (filter_has_var(INPUT_POST, "disconnect")) {
  $_SESSION = array();
  session_destroy();
  header("Location: ./index.php");
  exit();
}

// Traitement du formulaire de connexion
if (filter_has_var(INPUT_POST, "submit")) {
  $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);
  $pwd = filter_input(INPUT_POST, "password", FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,25}$/')));

  if (!$email) {
    $err[] = t('invalidEmail');
  }
  if (!$pwd) {
    $err[] = t('invalidPassword');
  }

  if (!$err) {
    $db = new DbManagerCRUD();
    $user = $db->rendPersonneEmail($email);

    if ($user) {
      $isAccountActivated = $db->rendPersonneTokenId($user[0]->rendId()) == "" ? true : false;
      $isPasswordOk = password_verify($pwd, $user[0]->rendMdp());
      if ($isPasswordOk && $isAccountActivated) {
        $_SESSION["isConnected"] = true;
        $_SESSION["userEmail"] = $user[0]->rendEmail();
        header("Location: ./index.php");
        exit();
      } else {
        $err[] = t('incorrectPassword');
      }
    } else {
      $err[] = t('accountNotFound');
    }
  }
}

include "./composants/header/header.php";
?>

<div class="main">
  <div>
    <div>
      <h1 class="TitleWelcome"><?php echo t('login');
                                ?></h1>
      <div class="err" <?php if (!$err) echo "style='display: none';"; ?>>
        <?php
        foreach ($err as $erreur) {
          echo "<p>" . $erreur . "</p>";
        }
        ?>
      </div>

      <div class="msg" <?php if (!$msg) echo "style='display: none';"; ?>>
        <?php
        foreach ($msg as $message) {
          echo "<p>" . $message . "</p>";
        }
        ?>
      </div>
    </div>
    <div class="form-container">

      <form action="connexion.php" method="post">
        <label for="email"><?php echo t('email');
                            ?></label>
        <input type="email" id="email" name="email" required placeholder="john.doe@gmail.com">

        <label for="password"><?php echo t('password');
                              ?></label>
        <input type="password" id="password" name="password" required>

        <input type="submit" name="submit" value="<?php echo t('submit');
                                                  ?>">
      </form>
    </div>
  </div>

</div>
</body>

</html>