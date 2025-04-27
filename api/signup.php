<?php
// Permettre les requêtes CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Vérifier la méthode de la requête
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Si la méthode est OPTIONS, répondre avec un code 200
    http_response_code(200);
    exit();
}

require_once 'vendor/autoload.php';
require_once 'db.php'; // Inclure le fichier de connexion
use \Firebase\JWT\JWT;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données envoyées en POST
    $data = json_decode(file_get_contents("php://input"));

    if (empty($data->username) || empty($data->email) || empty($data->password)) {
        echo json_encode(["message" => "All fields are required"]);
        exit();
    }

    // Vérifier si l'email existe déjà
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$data->email]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(["message" => "Email already exists"]);
        exit();
    }

    // Hachage du mot de passe
    $passwordHash = password_hash($data->password, PASSWORD_BCRYPT);

    // Insertion de l'utilisateur dans la base de données
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
    $stmt->execute([$data->username, $data->email, $passwordHash]);

    // Création du token JWT
    $issuedAt = time();
    $expirationTime = $issuedAt + 3600;  // jwt valide pendant 1 heure
    $payload = array(
        "iat" => $issuedAt,
        "exp" => $expirationTime,
        "username" => $data->username
    );

    // Clé secrète pour signer le JWT
    $secretKey = "your_secret_key";  
    $jwt = JWT::encode($payload, $secretKey);

    echo json_encode([
        "message" => "User created successfully",
        "jwt" => $jwt
    ]);
}
?>
