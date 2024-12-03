<?php
session_start();
//phpinfo();
require_once('./config/autoload.php');
require_once('lang' . DIRECTORY_SEPARATOR . 'lang_func.php');

if (filter_has_var(INPUT_POST, "disconnect")) {
  $_SESSION = array();
  session_destroy();
}

$currentLang = getLanguage();
include "./composants/header/header.php";
?>
<!-- Contenu principal -->
<div class="main">
  <h1>Bienvenue sur notre site</h1>
  <p>Cliquez sur les liens ci-dessus pour naviguer.</p>
</div>

</body>

</html>