<?php
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Admin') {
    echo "<script>alert('Acceso denegado: solo administradores.'); window.location.href='login.php';</script>";
    exit;
}
include 'conexion.php';

// Validar ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de usuario inválido.");
}

$id = intval($_GET['id']);

// Obtener los datos del usuario actual
$sql = "SELECT usuario_id, alias, rol, activo FROM usuarios WHERE usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if (!$usuario) {
    die("Usuario no encontrado.");
}

$stmt->close();

// Procesar actualización
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $alias = $_POST["alias"];
    $rol = $_POST["rol"];
    $activo = isset($_POST["activo"]) ? 1 : 0;
    $password = $_POST["password"];

    if (!empty($password)) {
        // Si se proporciona una nueva contraseña, se hashea y se actualiza
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $sql = "UPDATE usuarios SET alias = ?, password_hash = ?, rol = ?, activo = ? WHERE usuario_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssii", $alias, $passwordHash, $rol, $activo, $id);
    } else {
        // Si no se proporciona, se omite la contraseña
        $sql = "UPDATE usuarios SET alias = ?, rol = ?, activo = ? WHERE usuario_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssii", $alias, $rol, $activo, $id);
    }

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: admin.php?msg=" . urlencode("Usuario actualizado correctamente."));
        exit;
    } else {
        $error = "Error al actualizar el usuario: " . $stmt->error;
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="stylesAdmin.css">
	  <link rel="apple-touch-icon" sizes="180x180" href="favicon_io/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="favicon_io/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="favicon_io/favicon-16x16.png">
  <link rel="manifest" href="favicon_io/site.webmanifest">
</head>
<body>

<header>
    <div class="header-left">
        <img src="logo.png" alt="Logo de la Aplicación" />
        <h1>Panel de consulta para administradores</h1>
    </div>
    <nav>
        <a href="admin.php?seccion=usuarios">Usuarios</a>
        <a href="admin.php?seccion=cortes">Cortes de caja</a>
		<a href="admin.php?seccion=paginas">Panel de edición</a>
		<a href="admin.php?seccion=imagenes">Imágenes</a>
		<a href="admin.php?seccion=ofertas">Crear oferta</a>

        <form action="logout.php" method="post" style="display:inline;">
            <button type="submit" class="logout">Cerrar sesión</button>
        </form>
    </nav>
</header>
<main>
    <h2>Editar Usuario</h2>

    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <form method="POST">
        <label>Alias:</label><br>
        <input type="text" name="alias" value="<?php echo htmlspecialchars($usuario['alias']); ?>" required><br><br>

        <label>Nueva contraseña (opcional):</label><br>
        <input type="password" name="password" placeholder="Dejar en blanco para no cambiar"><br><br>

        <label>Rol:</label><br>
        <select name="rol" required>
            <option value="admin" <?php if ($usuario['rol'] === 'admin') echo 'selected'; ?>>Admin</option>
            <option value="empleado" <?php if ($usuario['rol'] === 'empleado') echo 'selected'; ?>>Empleado</option>
        </select><br><br>

        <label>Activo:</label>
        <input type="checkbox" name="activo" <?php if ($usuario['activo']) echo 'checked'; ?>><br><br>

        <button type="submit">Actualizar</button>
        <a href="admin.php">Cancelar</a>
    </form>
</main>
<footer>
    <p>Panel Admin · v1.0 · Servidor: <?php echo date("d/m/Y H:i"); ?></p>
    <p>Sesión iniciada como: <?php echo $_SESSION['rol']; ?> (<?php echo $_SESSION['alias']; ?>)</p>
    <p>Desarrollado por <a href="https://github.com/Rendel05">Pedro M</a> · 2025</p>
    <p>© 2025 Tienda Olly — Todos los derechos reservados.</p>
</footer>

</body>

</html>
