<?php
// 1. Crea una cartella "sessioni" nel tuo spazio web e definisci il percorso
date_default_timezone_set('Europe/Rome');
$dir_sessioni = __DIR__ . '/sessioni_private';
if (!file_exists($dir_sessioni)) {
    mkdir($dir_sessioni, 0700, true);
}
ini_set('session.save_path', $dir_sessioni);

// 2. Imposta la durata a 1 anno (31.536.000 secondi)
$durata = 31536000;
ini_set('session.gc_maxlifetime', $durata);

session_set_cookie_params([
    'lifetime' => $durata,
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();
?>