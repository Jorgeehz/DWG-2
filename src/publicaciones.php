<?php

// Conexión a la base de datos
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

    $titulo = $data['titulo'];
    $contenido = $data['contenido'];
    $fecha_publicacion = $data['fecha_publicacion'];
    $usuario_id = $data['usuario_id'];

    $sql = "INSERT INTO publicaciones (titulo, contenido, fecha_publicacion, usuario_id) VALUES (:titulo, :contenido, :fecha_publicacion, :usuario_id)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':titulo', $titulo);
    $stmt->bindParam(':contenido', $contenido);
    $stmt->bindParam(':fecha_publicacion', $fecha_publicacion);
    $stmt->bindParam(':usuario_id', $usuario_id);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(array("message" => "Publicación creada correctamente"));
    } else {
        http_response_code(500);
        echo json_encode(array("message" => "Error al crear publicación"));
    }
} if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Aquí tenemos la consulta que requiere incluir 3 campos de cada tabla
    // y se pasa como parámetro el id de la publicación 
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $sql = "SELECT p.titulo, p.contenido, u.nombre AS nombre_usuario FROM publicaciones p
                INNER JOIN usuarios u ON p.usuario_id = u.id
                WHERE p.id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $publicacion = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($publicacion) {
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode($publicacion);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "No se encontró la publicación"));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Se requiere un ID de publicación para obtener la información"));
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);

    $id = $data['id'];
    $titulo = $data['titulo'];
    $contenido = $data['contenido'];
    $fecha_publicacion = $data['fecha_publicacion'];
    $usuario_id = $data['usuario_id'];

    $sql = "UPDATE publicaciones SET titulo = :titulo, contenido = :contenido, fecha_publicacion = :fecha_publicacion, usuario_id = :usuario_id WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':titulo', $titulo);
    $stmt->bindParam(':contenido', $contenido);
    $stmt->bindParam(':fecha_publicacion', $fecha_publicacion);
    $stmt->bindParam(':usuario_id', $usuario_id);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(array("message" => "Publicación actualizada correctamente"));
    } else {
        http_response_code(500);
        echo json_encode(array("message" => "Error al actualizar publicación"));
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);

    $id = $data['id'];

    $sql = "DELETE FROM publicaciones WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(array("message" => "Publicación eliminada correctamente"));
    } else {
        http_response_code(500);
        echo json_encode(array("message" => "Error al eliminar publicación"));
    }
}

?>
