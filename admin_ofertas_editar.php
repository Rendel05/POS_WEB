<?php
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Admin') {
    echo "<script>alert('Acceso denegado: solo administradores.'); window.location.href='login.php';</script>";
    exit;
}

require_once "conexion.php";

if (!isset($_GET['id'])) {
    echo "<script>alert('Oferta no especificada'); window.location.href='admin.php?seccion=ofertas';</script>";
    exit;
}

$oferta_id = intval($_GET['id']);

$sqlOferta = $conn->prepare("SELECT * FROM ofertas WHERE oferta_id = ?");
$sqlOferta->bind_param("i", $oferta_id);
$sqlOferta->execute();
$oferta = $sqlOferta->get_result()->fetch_assoc();

if (!$oferta) {
    echo "<script>alert('La oferta no existe'); window.location.href='admin.php?seccion=ofertas';</script>";
    exit;
}

// Obtener productos activos
$sql = "SELECT producto_id, nombre, precio_venta FROM productos WHERE activo = 1";
$productos = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar oferta</title>
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
		<a href="admin.php?seccion=ofertas">Ofertas</a>

        <form action="logout.php" method="post" style="display:inline;">
            <button type="submit" class="logout">Cerrar sesión</button>
        </form>
    </nav>
</header>

<main style="display: grid !important; justify-content:center !important;">

    <form action="admin_ofertas_guardar.php" method="POST">
        <h1>Editar oferta</h1>

        <input type="hidden" name="oferta_id" value="<?= $oferta_id ?>">

        <label for="producto_id">Producto:</label> 
        <select name="producto_id" id="producto_id" required>
            <option value="">Selecciona un producto</option>
            <?php
            $lista = [];
            while ($p = $productos->fetch_assoc()) :
                $lista[$p['producto_id']] = $p['precio_venta'];
            ?>
                <option value="<?= $p['producto_id'] ?>" 
                    <?= $p['producto_id'] == $oferta['producto_id'] ? 'selected' : '' ?>>
                    <?= $p['nombre'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <p id="precioRegular" style="margin-top:0;">Precio regular: —</p>

        <script>
            const precios = <?= json_encode($lista) ?>;

            function actualizarPrecio() {
                const id = document.getElementById("producto_id").value;
                const salida = document.getElementById("precioRegular");

                if (precios[id]) {
                    salida.textContent = "Precio regular: $" + precios[id];
                } else {
                    salida.textContent = "Precio regular: —";
                }
            }

            document.getElementById("producto_id").addEventListener("change", actualizarPrecio);
            window.onload = actualizarPrecio;
        </script>

        <label for="precio_oferta">Precio de oferta:</label>
        <input type="number" step="0.01" name="precio_oferta" required 
               value="<?= $oferta['precio_oferta'] ?>">

        <label>Fecha de inicio:</label>
        <input type="date" name="fecha_inicio" required 
               value="<?= $oferta['fecha_inicio'] ?>">

        <label>Fecha de fin:</label>
        <input type="date" name="fecha_fin" required 
               value="<?= $oferta['fecha_fin'] ?>">

        <label>Activo:</label>
        <select name="activo">
            <option value="1" <?= $oferta['activo'] == 1 ? 'selected' : '' ?>>Sí</option>
            <option value="0" <?= $oferta['activo'] == 0 ? 'selected' : '' ?>>No</option>
        </select>

        <button type="submit">Actualizar oferta</button>
        <button type="button" class="btn btn-eliminar" onclick="window.location.replace('admin.php?seccion=ofertas');">Cancelar</button>

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
