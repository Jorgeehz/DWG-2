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
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $nombre = $data['nombre'];
    $edad = $data['edad'];
    $correo = $data['correo'];
    $direccion = $data['direccion'];

    $sql = "INSERT INTO usuarios (nombre, edad, correo, direccion) VALUES (:nombre, :edad, :correo, :direccion)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':edad', $edad);
    $stmt->bindParam(':correo', $correo);
    $stmt->bindParam(':direccion', $direccion);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(array("message" => "Usuario creado correctamente"));
    } else {
        http_response_code(500);
        echo json_encode(array("message" => "Error al crear usuario"));
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $sql = "SELECT * FROM usuarios WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($usuario) {
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode($usuario);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Usuario no encontrado"));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Se requiere un ID para leer un usuario"));
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);

    $id = $data['id'];
    $nombre = $data['nombre'];
    $edad = $data['edad'];
    $correo = $data['correo'];
    $direccion = $data['direccion'];

    $sql = "UPDATE usuarios SET nombre = :nombre, edad = :edad, correo = :correo, direccion = :direccion WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':edad', $edad);
    $stmt->bindParam(':correo', $correo);
    $stmt->bindParam(':direccion', $direccion);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(array("message" => "Usuario actualizado correctamente"));
    } else {
        http_response_code(500);
        echo json_encode(array("message" => "Error al actualizar usuario"));
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);

    $id = $data['id'];

    $sql = "DELETE FROM usuarios WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(array("message" => "Usuario eliminado correctamente"));
    } else {
        http_response_code(500);
        echo json_encode(array("message" => "Error al eliminar usuario"));
    }
}

?>
