<?php

session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Admin') {
    echo "<script>alert('Acceso denegado: solo administradores.'); window.location.href='login.php';</script>";
    exit;
}

require_once "conexion.php";

$sql = "SELECT producto_id, nombre, precio_venta FROM productos WHERE activo = 1";
$productos = $conn->query($sql);
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

<main style="display: grid !important; justify-content:center !important;">


    <form action="admin_ofertas_guardar.php" method="POST">
        <h1>Crear nueva oferta</h1>

        <label for="producto_id">Producto:</label> 
        <select name="producto_id" id="producto_id" required>
            <option value="">Selecciona un producto</option>
            <?php 
            $lista = [];
            while ($p = $productos->fetch_assoc()) : 
                $lista[$p['producto_id']] = $p['precio_venta'];
            ?>
                <option value="<?= $p['producto_id'] ?>">
                    <?= $p['nombre'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <p id="precioRegular" style="margin-top:0;">
            Precio regular: —
        </p>

        <script>
            const precios = <?= json_encode($lista) ?>;

            document.getElementById("producto_id").addEventListener("change", function() {
                const id = this.value;
                const salida = document.getElementById("precioRegular");

                if (precios[id]) {
                    salida.textContent = "Precio regular: $" + precios[id];
                } else {
                    salida.textContent = "Precio regular: —";
                }
            });
        </script>

        <label for="precio_oferta">Precio de oferta:</label>
        <input type="number" step="0.01" name="precio_oferta" required>

        <label>Fecha de inicio:</label>
        <input type="date" name="fecha_inicio" required>

        <label>Fecha de fin:</label>
        <input type="date" name="fecha_fin" required>

        <label>Activo:</label>
        <select name="activo">
            <option value="1">Sí</option>
            <option value="0">No</option>
        </select>

        <button type="submit">Guardar oferta</button>
        <button	class="btn btn-eliminar" type="button" onclick="window.location.replace('https://equipo6.grupoahost.com/admin.php?seccion=ofertas');">Cancelar</button>

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
