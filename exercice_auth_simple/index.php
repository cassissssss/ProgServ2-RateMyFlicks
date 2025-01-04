<?php
session_start();
require_once('./config/autoload.php');
require_once('lang' . DIRECTORY_SEPARATOR . 'lang_func.php');

use functionnalities\DbManagerCRUD;

if (filter_has_var(INPUT_POST, "disconnect")) {
  $_SESSION = array();
  session_destroy();
}

$currentLang = getLanguage();
$db = new DbManagerCRUD();

// Récupérer les films les mieux notés, sinon les films récents
$films = $db->rendFilmsMieuxNotes(50);
if (empty($films)) {
    $films = $db->rendFilmsRecents(50);
}

include "./composants/header/header.php";
?>
<!-- Contenu principal -->
<div class="mainfilm">
    <h1><?php echo t('home'); ?></h1>
    
    <div class="movies-grid">
        <?php foreach ($films as $film): ?>
            <div class="movie-card">
                <h3><?php echo htmlspecialchars($film->rendTitle()); ?></h3>
                <div class="movie-info">
                    <p><strong><?php echo t('director'); ?>:</strong> <?php echo htmlspecialchars($film->rendDirector()); ?></p>
                    <p><strong><?php echo t('year'); ?>:</strong> <?php echo htmlspecialchars($film->rendYear()); ?></p>
                    <p><strong><?php echo t('genres'); ?>:</strong> <?php echo htmlspecialchars($film->rendGenres()); ?></p>
                    <p><strong>IMDB:</strong> <?php echo htmlspecialchars($film->rendImdbScore()); ?>/10</p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>