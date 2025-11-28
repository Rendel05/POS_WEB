<?php
require "conexion.php";

$result = $conn->query("SELECT id, slug, titulo, ultima_actualizacion FROM paginas_estaticas ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="stylesAdmin.css">
	  <link rel="apple-touch-icon" sizes="180x180" href="favicon_io/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="favicon_io/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="favicon_io/favicon-16x16.png">
  <link rel="manifest" href="favicon_io/site.webmanifest">
</head>

<h2>Administrar páginas estáticas</h2>

<table border="1" cellpadding="10" style="border-collapse: collapse;">
    <tr>
        <th>ID</th>
        <th>Slug</th>
        <th>Título</th>
        <th>Última actualización</th>
        <th>Acción</th>
    </tr>

    <?php while($p = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $p['id'] ?></td>
            <td><?= htmlspecialchars($p['slug']) ?></td>
            <td><?= htmlspecialchars($p['titulo']) ?></td>
            <td><?= $p['ultima_actualizacion'] ?></td>
            <td><a href="editar_pagina.php?id=<?= $p['id'] ?>">Editar</a></td>
        </tr>
    <?php endwhile; ?>
</table>
</html>  