<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

$host = "localhost";
$dbname = "altis";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"));

// ----------------------------------
// GET : récupérer tous les commentaires
// ----------------------------------
if($method === 'GET'){
    $stmt = $pdo->query("SELECT * FROM commentaires ORDER BY date_creation DESC");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// ----------------------------------
// POST : ajouter un commentaire
// ----------------------------------
if($method === 'POST'){
    if(!isset($data->auteur) || !isset($data->contenu)){
        http_response_code(400);
        echo json_encode(["error"=>"Données manquantes"]);
        exit;
    }
    $stmt = $pdo->prepare("INSERT INTO commentaires (auteur, contenu) VALUES (?, ?)");
    $stmt->execute([$data->auteur, $data->contenu]);
    echo json_encode(["message"=>"Commentaire ajouté"]);
    exit;
}

// ----------------------------------
// PUT : modifier un commentaire
// ----------------------------------
if($method === 'PUT'){
    if(!isset($data->id) || !isset($data->contenu)){
        http_response_code(400);
        echo json_encode(["error"=>"Données manquantes pour modification"]);
        exit;
    }
    $stmt = $pdo->prepare("UPDATE commentaires SET contenu = ? WHERE id = ?");
    $stmt->execute([$data->contenu, $data->id]);
    echo json_encode(["message"=>"Commentaire modifié"]);
    exit;
}

// ----------------------------------
// DELETE : supprimer un commentaire
// ----------------------------------
if($method === 'DELETE'){
    if(!isset($data->id)){
        http_response_code(400);
        echo json_encode(["error"=>"ID manquant pour suppression"]);
        exit;
    }
    $stmt = $pdo->prepare("DELETE FROM commentaires WHERE id = ?");
    $stmt->execute([$data->id]);
    echo json_encode(["message"=>"Commentaire supprimé"]);
    exit;
}

http_response_code(405);
echo json_encode(["error"=>"Méthode non autorisée"]);