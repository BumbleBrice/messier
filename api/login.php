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

    if (empty($data->email) || empty($data->password)) {
        echo json_encode(["message" => "All fields are required"]);
        exit();
    }

    // Vérifier si l'email existe
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$data->email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($data->password, $user['password_hash'])) {
        echo json_encode(["message" => "Invalid email or password"]);
        exit();
    }

    // Création du token JWT
    $issuedAt = time();
    $expirationTime = $issuedAt + 3600;  // jwt valide pendant 1 heure
    $payload = array(
        "iat" => $issuedAt,
        "exp" => $expirationTime,
        "username" => $user['username']
    );

    // Clé secrète pour signer le JWT
    $secretKey = "your_secret_key";  
    $jwt = JWT::encode($payload, $secretKey);

    echo json_encode([
        "message" => "Login successful",
        "jwt" => $jwt
    ]);
}
?>
