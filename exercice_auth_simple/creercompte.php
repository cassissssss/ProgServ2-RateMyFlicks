<?php

session_start();

//phpinfo();
require_once('./config/autoload.php');
require_once('lang' . DIRECTORY_SEPARATOR . 'lang_func.php');


use functionnalities\DbManagerCRUD;
use functionnalities\Personne;
use functionnalities\EmailManager;
use functionnalities\TokenManager;

//Gestion des erreurs
$err = [];
$isAccountCreated = false;
$currentLang = getLanguage();

if (filter_has_var(INPUT_POST, "disconnect")) {
    $_SESSION = array();
    session_destroy();
    header("Location: ./index.php");
    exit();
}

if (filter_has_var(INPUT_POST, "submit")) {
    $lastname = filter_input(INPUT_POST, "nom", FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => '/[A-Za-z]+/')));
    $firstname = filter_input(INPUT_POST, "prenom", FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => '/[A-Za-z]+/')));
    $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);
    $phone = filter_input(INPUT_POST, "tel", FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => '/^[0-9]+$/')));
    $pwd = filter_input(INPUT_POST, "password", FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,25}$/')));

    if (!$lastname) {
        $err[] = "Veuillez renseigner votre nom";
    }
    if (!$firstname) {
        $err[] = "Veuillez renseigner votre prénom";
    }
    if (!$email) {
        $err[] = "Veuillez renseigner votre adresse mail";
    }
    if (!$phone) {
        $err[] = "Veuillez renseigner votre n° de téléphone";
    }
    if (!$pwd) {
        $err[] = "Veuillez renseigner un mot de passe d'une longueur comprise entre 8 et 25 caractères, contenant au minimum 1 majuscule, 1 minuscule, 1 chiffre et 1 caractère spécial ( @$!%*?& )";
    }
    // Vérification des doublons avant d'ajouter l'utilisateur
    $db = new DbManagerCRUD();
    if ($db->checkEmailExists($email)) {
        $err[] = "Cette adresse email est déjà utilisée.";
    }
    if ($db->checkPhoneExists($phone)) {
        $err[] = "Ce numéro de téléphone est déjà utilisé.";
    }

    if (!$err) {

        $token = "";
        $isTokenUpdated = 0;
        $personne = new Personne($firstname, $lastname, $email, $phone, $pwd);

        $id = $db->ajoutePersonne($personne);

        do {
            try {
                $token = TokenManager::generateToken();
                $isTokenUpdated = $db->changePersonneToken($id, $token);
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        } while ($isTokenUpdated != 1);


        if ($id > 0 && $isTokenUpdated > 0) {
            $isAccountCreated = true;
            if ($isTokenUpdated > 0) {
                $personne = $db->rendPersonneEmail($personne->rendEmail());
                EmailManager::sendValidationEmail($personne[0], $token);
                header("./connexion.php");
            }

        }
    }
}

include "./composants/header/header.php";
?>
<!-- Contenu principal -->
<div class="main">
    <h1 class="TitleWelcome">Créer votre compte</h1>

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

    <div class="account-created" <?php if (!$isAccountCreated)
        echo "style='display: none';"; ?>>
        <?php
        if ($isAccountCreated) {
            echo "<p>Le compte a bien été créé</p>";
            echo "<p>Veuillez confirmer la création en cliquant sur le lien envoyé par Email</p>";
        }
        ?>
    </div>

    <!-- Formulaire de création de compte -->
    <div class="form-container" <?php if ($isAccountCreated)
        echo "style='display: none';"; ?>>
        <form action="creercompte.php" method="post">
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" placeholder="Doe"
                value="<?php echo isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : ''; ?>">

            <label for="prenom">Prénom</label>
            <input type="text" id="prenom" name="prenom" placeholder="John"
                value="<?php echo isset($_POST['prenom']) ? htmlspecialchars($_POST['prenom']) : ''; ?>">

            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="john.doe@gmail.com"
                value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">

            <label for="tel">No Portable</label>
            <input type="tel" id="tel" name="tel" pattern="[0-9]{10}" placeholder="079XXXXXXX"
                value="<?php echo isset($_POST['tel']) ? htmlspecialchars($_POST['tel']) : ''; ?>">

            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password"
                value="<?php echo isset($_POST['password']) ? htmlspecialchars($_POST['password']) : ''; ?>">

            <input type="submit" name="submit" value="Envoyer">
        </form>
    </div>

</div>

</body>

</html>