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

    $contenido = $data['contenido'];
    $fecha_comentario = $data['fecha_comentario'];
    $usuario_id = $data['usuario_id'];
    $publicacion_id = $data['publicacion_id'];

    $sql = "INSERT INTO comentarios (contenido, fecha_comentario, usuario_id, publicacion_id) VALUES (:contenido, :fecha_comentario, :usuario_id, :publicacion_id)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':contenido', $contenido);
    $stmt->bindParam(':fecha_comentario', $fecha_comentario);
    $stmt->bindParam(':usuario_id', $usuario_id);
    $stmt->bindParam(':publicacion_id', $publicacion_id);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(array("message" => "Comentario creado correctamente"));
    } else {
        http_response_code(500);
        echo json_encode(array("message" => "Error al crear comentario"));
    }
} if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Aquí tenemos la consulta que requiere incluir 3 campos de las tablas 2 y 3
    if (isset($_GET['publicacion_id'])) {
        $publicacion_id = $_GET['publicacion_id'];
        $sql = "SELECT c.contenido, c.fecha_comentario, u.nombre FROM comentarios c
                INNER JOIN usuarios u ON c.usuario_id = u.id
                WHERE c.publicacion_id = :publicacion_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':publicacion_id', $publicacion_id);
        $stmt->execute();
        $comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($comentarios) {
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode($comentarios);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "No se encontraron comentarios para esta publicación"));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Se necesita un ID de publicación para obtener los comentarios"));
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);

    $id = $data['id'];
    $contenido = $data['contenido'];
    $fecha_comentario = $data['fecha_comentario'];
    $usuario_id = $data['usuario_id'];
    $publicacion_id = $data['publicacion_id'];

    $sql = "UPDATE comentarios SET contenido = :contenido, fecha_comentario = :fecha_comentario, usuario_id = :usuario_id, publicacion_id = :publicacion_id WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':contenido', $contenido);
    $stmt->bindParam(':fecha_comentario', $fecha_comentario);
    $stmt->bindParam(':usuario_id', $usuario_id);
    $stmt->bindParam(':publicacion_id', $publicacion_id);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(array("message" => "Comentario actualizado correctamente"));
    } else {
        http_response_code(500);
        echo json_encode(array("message" => "Error al actualizar comentario"));
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);

    $id = $data['id'];

    $sql = "DELETE FROM comentarios WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(array("message" => "Comentario eliminado correctamente"));
    } else {
        http_response_code(500);
        echo json_encode(array("message" => "Error al eliminar comentario"));
    }
}

?>
