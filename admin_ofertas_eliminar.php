<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Admin') exit("Acceso denegado.");
if (!isset($_GET['id'])) exit("Oferta no especificada.");

$id = intval($_GET['id']);

$stmt = $conn->prepare("UPDATE ofertas SET activo = 0 WHERE oferta_id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: admin.php?seccion=ofertas");
    exit;
} else {
    echo "Error al desactivar la oferta.";
}
