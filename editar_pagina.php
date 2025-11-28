<?php
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Admin') {
    echo "<script>alert('Acceso denegado: solo administradores.'); window.location.href='login.php';</script>";
    exit;
}
?>
<?php
require_once 'conexion.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID inválido.");
}

$stmt = $conn->prepare("SELECT * FROM paginas_estaticas WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Página no encontrada.");
}

$pagina = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar: <?= htmlspecialchars($pagina['titulo']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

	<link rel="apple-touch-icon" sizes="180x180" href="favicon_io/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="favicon_io/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="favicon_io/favicon-16x16.png">
  <link rel="manifest" href="favicon_io/site.webmanifest">


    <!-- TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/i7lemtqouo2i1wn8r5wq9l0w64qjiqa4u56o4xdoa6cmjep0/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: 'textarea#contenidoEditor',
            height: 600,
            menubar: true,
            plugins: [
                'advlist autolink lists link image charmap preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table code help wordcount'
            ],
            toolbar: 'undo redo | formatselect | ' +
                     'bold italic underline | alignleft aligncenter ' +
                     'alignright alignjustify | bullist numlist outdent indent | ' +
                     'removeformat | help',
            forced_root_block: 'p',
            force_br_newlines: false,
            force_p_newlines: true,
            remove_trailing_brs: true
        });
    </script>

    <style>
        .btn-guardar { background-color: #f57a3a; color: white; border: none; }
        .btn-guardar:hover { background-color: #e8632a; }
        .btn-cancelar { background-color: #6b6b6b; color: white; border: none; }
        .btn-cancelar:hover { background-color: #555; }
    </style>
</head>

<body class="p-4">

<h2>Editar página: <?= htmlspecialchars($pagina['titulo']) ?></h2>

<form action="guardar_pagina.php" method="POST">
    <input type="hidden" name="id" value="<?= $pagina['id'] ?>">

    <div class="mb-3">
        <label for="contenidoEditor" class="form-label">Contenido</label>
        <textarea id="contenidoEditor" name="contenido" class="form-control"><?= htmlspecialchars($pagina['contenido']) ?></textarea>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-guardar">Guardar cambios</button>
        <a href="admin.php?seccion=paginas" class="btn btn-cancelar">Cancelar</a>
    </div>
</form>

</body>
</html>
