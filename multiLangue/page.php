<?php
session_start();
require_once('lang' . DIRECTORY_SEPARATOR . 'lang_func.php');

if (getLanguage() === 'en') {
    echo '<a href="' . $_SERVER['PHP_SELF'] . '?lang=fr">Français</a>';
} else {
    echo '<a href="' . $_SERVER['PHP_SELF'] . '?lang=en">English</a>';
}

echo '<h1>' . t('loveProg') . '</h1>';
