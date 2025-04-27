<?php
// Informations de connexion
$host = 'localhost';
$dbname = 'messier_catalog';
$user = 'root';
$password = '';

try {
    // Connexion au serveur sans base sélectionnée
    $pdo = new PDO("mysql:host=$host", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Création de la base de données si elle n'existe pas
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    echo "Database '$dbname' checked/created.\n";

    // Se reconnecter en sélectionnant la base
    $pdo->exec("USE `$dbname`");

    // Liste des tables et leur SQL de création
    $tables = [
        'users' => "
            CREATE TABLE IF NOT EXISTS users (
                id INT PRIMARY KEY AUTO_INCREMENT,
                username VARCHAR(50) NOT NULL UNIQUE,
                email VARCHAR(100) NOT NULL UNIQUE,
                password_hash VARCHAR(255) NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ",
        'messier_objects' => "
            CREATE TABLE IF NOT EXISTS messier_objects (
                id INT PRIMARY KEY AUTO_INCREMENT,
                messier_number VARCHAR(5) NOT NULL UNIQUE,
                name VARCHAR(100),
                type VARCHAR(50),
                constellation VARCHAR(50),
                ra VARCHAR(20),
                declination VARCHAR(20)
            );
        ",
        'user_photos' => "
            CREATE TABLE IF NOT EXISTS user_photos (
                id INT PRIMARY KEY AUTO_INCREMENT,
                user_id INT NOT NULL,
                messier_object_id INT NOT NULL,
                photo_url VARCHAR(255),
                taken_at DATE,
                equipment TEXT,
                notes TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (messier_object_id) REFERENCES messier_objects(id) ON DELETE CASCADE,
                UNIQUE (user_id, messier_object_id)
            );
        "
    ];

    // Vérification/création de chaque table
    foreach ($tables as $name => $sql) {
        $pdo->exec($sql);
        echo "Table '$name' checked/created.\n";
    }

    echo "✅ Installation completed.\n";

} catch (PDOException $e) {
    die("Connection or creation error: " . $e->getMessage());
}
?>
