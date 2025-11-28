<?php
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Admin') {
    exit("Acceso denegado.");
}

require_once "conexion.php";

if (
    !isset($_POST['producto_id']) ||
    !isset($_POST['precio_oferta']) ||
    !isset($_POST['fecha_inicio']) ||
    !isset($_POST['fecha_fin']) ||
    !isset($_POST['activo'])
) {
    exit("Solicitud incompleta.");
}

$producto_id   = intval($_POST['producto_id']);
$precio_oferta = floatval($_POST['precio_oferta']);
$fecha_inicio  = $_POST['fecha_inicio'];
$fecha_fin     = $_POST['fecha_fin'];
$activo        = intval($_POST['activo']);

$check = $conn->prepare(
    "SELECT oferta_id FROM ofertas 
     WHERE producto_id = ? AND activo = 1
     LIMIT 1"
);
$check->bind_param("i", $producto_id);
$check->execute();
$res = $check->get_result();

if ($res->num_rows > 0 && $activo == 1) {
    echo "<script>
            alert('Este producto ya tiene una oferta activa. Desactívela antes de crear una nueva.');
            history.back();
          </script>";
    exit;
}
$check->close();

$stmt = $conn->prepare(
    "INSERT INTO ofertas (producto_id, precio_oferta, fecha_inicio, fecha_fin, activo)
     VALUES (?, ?, ?, ?, ?)"
);

$stmt->bind_param("idssi", $producto_id, $precio_oferta, $fecha_inicio, $fecha_fin, $activo);

if ($stmt->execute()) {
    echo "<script>
            alert('Oferta creada correctamente.');
            window.location.href = 'https://equipo6.grupoahost.com/admin.php?seccion=ofertas';
          </script>";
} else {
    echo "Error al guardar oferta.";
}

$stmt->close();
$conn->close();
?>
