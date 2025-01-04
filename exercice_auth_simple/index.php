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
    $films = $db->rendFilmsRecents(10);
}

include "./composants/header/header.php";
?>
    <h1><?php echo t('home'); ?></h1>

<!-- Contenu principal -->
<div class="mainfilm">
    
    <div class="movies-grid">
        <?php foreach ($films as $film): ?>
            <div class="movie-card">
                <h2><?php echo htmlspecialchars($film->rendTitle()); ?></h2>
                <div class="movie-info">
                    <p><strong><?php echo t('director'); ?>:</strong> <?php echo htmlspecialchars($film->rendDirector()); ?></p>
                    <p><strong><?php echo t('year'); ?>:</strong> <?php echo htmlspecialchars($film->rendYear()); ?></p>
                    <p><strong><?php echo t('genres'); ?>:</strong> <?php echo htmlspecialchars($film->rendGenres()); ?></p>
                    <p><strong>🌟:</strong> <?php echo htmlspecialchars($film->rendImdbScore()); ?>/10</p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>