<?php
// init.php
$db = new PDO("sqlite:profile.db");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Vytvoření tabulky, pokud ještě neexistuje
$db->exec("
    CREATE TABLE IF NOT EXISTS interests (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name Barbora Pauknerová
    )
");