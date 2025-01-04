<?php

session_start();
//phpinfo();
require_once('./config/autoload.php');

use functionnalities\DbManagerCRUD;

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION["isConnected"]) || !$_SESSION["isConnected"]) {
    header("Location: ./connexion.php");
    exit();
}

// Récupération des informations de l'utilisateur
$db = new DbManagerCRUD();
$user = $db->rendPersonneEmail($_SESSION["userEmail"]);

// Inclusion du header
require_once('lang' . DIRECTORY_SEPARATOR . 'lang_func.php');
$currentLang = getLanguage();
include "./composants/header/header.php";
?>

<div class="main">
    <div class="profile-container">
        <h1><?php echo t('profileTitle'); ?></h1>
        
        <!-- Informations du profil -->
        <div class="profile-info">
            <h2><?php echo t('personalInfo'); ?></h2>
            <p><strong><?php echo t('fullName'); ?>:</strong> 
                <?php echo htmlspecialchars($user[0]->rendPrenom() . ' ' . $user[0]->rendNom()); ?>
            </p>
            <p><strong><?php echo t('email'); ?>:</strong> 
                <?php echo htmlspecialchars($user[0]->rendEmail()); ?>
            </p>
            <p><strong><?php echo t('phone'); ?>:</strong> 
                <?php echo htmlspecialchars($user[0]->rendNoTel()); ?>
            </p>
        </div>

        <!-- Changer le mot de passe -->
        <div class="change-password">
            <form method="post" action="./changermdp.php">
                <input type="submit" name="changePassword" value="<?php echo t('Mdp oublié ?'); ?>" class="change-password-btn">
            </form>
        </div>

        <!-- Films notés/aimés -->
        <div class="rated-movies">
            <h2><?php echo t('ratedMovies'); ?></h2>
            <?php
            $films = $db->rendFilmsNotesParUtilisateur($user[0]->rendId());
            if (!empty($films)) {
                echo '<ul class="movies-list">';
                foreach ($films as $film) {
                    echo '<li class="movie-item">';
                    echo '<h3>' . htmlspecialchars($film->rendTitre()) . '</h3>';
                    echo '<p>' . t('director') . ': ' . htmlspecialchars($film->rendDirecteur()) . '</p>';
                    echo '<p>' . t('year') . ': ' . htmlspecialchars($film->rendAnnee()) . '</p>';
                    // Ajoutez ici la note donnée par l'utilisateur si disponible
                    echo '</li>';
                }
                echo '</ul>';
            } else {
                echo '<p>' . t('noRatedMovies') . '</p>';
            }
            ?>
        </div>

        <!-- Bouton de déconnexion -->
        <div class="logout-section">
            <form method="post" action="./index.php">
                <input type="submit" name="disconnect" value="<?php echo t('logout'); ?>" class="logout-btn">
            </form>
        </div>
    </div>
</div>

</body>
</html>