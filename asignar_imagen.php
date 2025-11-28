<?php
session_start();
header('Content-Type: text/plain; charset=utf-8');
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Admin') {
    http_response_code(403);
    echo 'Acceso denegado';
    exit;
}
require_once 'conexion.php';
if (!isset($_POST['id']) || !isset($_FILES['imagen'])) {
    http_response_code(400);
    echo 'Solicitud inválida';
    exit;
}
$id = intval($_POST['id']);
$file = $_FILES['imagen'];
if ($file['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo 'Error al cargar archivo';
    exit;
}
$maxSize = 2 * 1024 * 1024;
if ($file['size'] > $maxSize) {
    http_response_code(400);
    echo 'El archivo es demasiado grande (máx 2MB)';
    exit;
}
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($file['tmp_name']);
$ext = '';
if ($mime === 'image/jpeg') $ext = '.jpg';
elseif ($mime === 'image/png') $ext = '.png';
elseif ($mime === 'image/gif') $ext = '.gif';
else {
    http_response_code(400);
    echo 'Tipo de archivo no permitido';
    exit;
}
$dir = __DIR__ . '/img';
if (!is_dir($dir)) mkdir($dir, 0755, true);
$baseName = bin2hex(random_bytes(8)) . time() . $ext;
$dest = $dir . '/' . $baseName;
if (!move_uploaded_file($file['tmp_name'], $dest)) {
    http_response_code(500);
    echo 'No se pudo guardar el archivo';
    exit;
}
$stmt = $conn->prepare("UPDATE productos SET ImagenText = ? WHERE producto_id = ?");
if (!$stmt) {
    http_response_code(500);
    echo 'Error en base de datos';
    exit;
}
$stmt->bind_param('si', $baseName, $id);
if ($stmt->execute()) {
    echo 'OK';
} else {
    http_response_code(500);
    echo 'Error al actualizar la base de datos';
}
$stmt->close();
$conn->close();
