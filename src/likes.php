<?php

$host = "localhost";
$dbname = "ejemplo1";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $usuario_id = $data['usuario_id'];
    $publicacion_id = $data['publicacion_id'];

    $sql = "INSERT INTO likes (usuario_id, publicacion_id) VALUES (:usuario_id, :publicacion_id)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':usuario_id', $usuario_id);
    $stmt->bindParam(':publicacion_id', $publicacion_id);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(array("message" => "Like creado correctamente"));
    } else {
        http_response_code(500);
        echo json_encode(array("message" => "Error al crear like"));
    }
} if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $sql = "SELECT usuario_id, publicacion_id FROM likes WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $like = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($like) {
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode($like);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Like no encontrado"));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Se requiere un ID para leer un like"));
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);

    $id = $data['id'];
    $usuario_id = $data['usuario_id'];
    $publicacion_id = $data['publicacion_id'];

    $sql = "UPDATE likes SET usuario_id = :usuario_id, publicacion_id = :publicacion_id WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':usuario_id', $usuario_id);
    $stmt->bindParam(':publicacion_id', $publicacion_id);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(array("message" => "Like actualizado correctamente"));
    } else {
        http_response_code(500);
        echo json_encode(array("message" => "Error al actualizar like"));
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);

    $id = $data['id'];

    $sql = "DELETE FROM likes WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(array("message" => "Like eliminado correctamente"));
    } else {
        http_response_code(500);
        echo json_encode(array("message" => "Error al eliminar like"));
    }
}

?>
