<?php
try {
    $pdo = new PDO('sqlite:sigrrus.sqlite');
    $stmt = $pdo->query('SELECT * FROM usuarios');
    print_r($stmt->fetchAll());
} catch(Exception $e) {
    echo $e->getMessage();
}
