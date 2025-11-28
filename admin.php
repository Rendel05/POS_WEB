<?php
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Admin') {
    echo "<script>alert('Acceso denegado: solo administradores.'); window.location.href='login.php';</script>";
    exit;
}

require_once 'conexion.php';
$seccion = $_GET['seccion'] ?? 'usuarios';
date_default_timezone_set('America/Mexico_City');
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
        <a href="?seccion=usuarios">Usuarios</a>
        <a href="?seccion=cortes">Cortes de caja</a>
		<a href="?seccion=paginas">Panel de edición</a>
		<a href="?seccion=imagenes">Imágenes</a>
		<a href="?seccion=ofertas">Crear oferta</a>

        <form action="logout.php" method="post" style="display:inline;">
            <button type="submit" class="logout">Cerrar sesión</button>
        </form>
    </nav>
</header>
<main>
<?php
if ($seccion === 'usuarios') {

    $sql = "SELECT usuario_id, alias, rol, activo FROM usuarios";
    $res = $conn->query($sql);

    echo "<h2>Usuarios registrados</h2>";

    echo "<a href='agregar_usuario.php' class='btn btn-agregar'>+ Agregar usuario</a>";

    if ($res && $res->num_rows > 0) {
        echo "<table>
                <tr><th>ID</th><th>Alias</th><th>Rol</th><th>Activo</th><th>Acciones</th></tr>";
        while ($fila = $res->fetch_assoc()) {
            $estado = $fila['activo'] ? 'Sí' : 'No';
            echo "<tr>
                    <td data-label='ID'>{$fila['usuario_id']}</td>
                    <td data-label='Alias'>{$fila['alias']}</td>
                    <td data-label='Rol'>{$fila['rol']}</td>
                    <td data-label='Activo'>{$estado}</td>
                    <td>
                        <a href='editar_usuario.php?id={$fila['usuario_id']}' class='btn btn-editar'>Editar</a>
                        <a href='eliminar_usuario.php?id={$fila['usuario_id']}' class='btn btn-eliminar' onclick='return confirm(\"¿Eliminar este usuario?\");'>Eliminar</a>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay usuarios registrados.</p>";
    }

} elseif ($seccion === 'paginas') {

    include 'admin_paginas.php';
}
elseif ($seccion === 'imagenes') {
	include'admin_imagenes.php';
} 
elseif ($seccion === 'ofertas') {
	include'admin_ofertas.php';
}
elseif ($seccion === 'cortes') {
    echo "<h2>Resumen de ventas y retiros del día</h2>";

    $queryVentas = "SELECT IFNULL(SUM(total), 0) AS total_ventas FROM ventas WHERE DATE(fecha_hora) = CURDATE()";
    $queryRetiros = "SELECT IFNULL(SUM(monto), 0) AS total_retiros FROM retiros_caja WHERE DATE(fecha_hora) = CURDATE()";

    $resVentas = $conn->query($queryVentas);
    $resRetiros = $conn->query($queryRetiros);

    $totalVentas = $resVentas->fetch_assoc()['total_ventas'] ?? 0;
    $totalRetiros = $resRetiros->fetch_assoc()['total_retiros'] ?? 0;
    $saldoFinal = $totalVentas - $totalRetiros;

    echo "<div class='resumen'>
            <p><strong>Ventas del día:</strong> $" . number_format($totalVentas, 2) . "</p>
            <p><strong>Retiros del día:</strong> $" . number_format($totalRetiros, 2) . "</p>
            <p><strong>Saldo final (provisorio):</strong> $" . number_format($saldoFinal, 2) . "</p>
            <p style='color:gray; font-size:0.9em;'>* Los valores son provisorios, aún no se ha realizado corte de caja.</p>
          </div>";

    echo "<h3>Ventas registradas hoy</h3>";
    $ventasHoy = $conn->query("SELECT venta_id, fecha_hora, usuario_id, total FROM ventas WHERE DATE(fecha_hora) = CURDATE()");
    if ($ventasHoy && $ventasHoy->num_rows > 0) {
        echo "<table><tr><th>ID Venta</th><th>Fecha</th><th>Usuario</th><th>Total</th></tr>";
        while ($fila = $ventasHoy->fetch_assoc()) {
            echo "<tr>
                    <td>{$fila['venta_id']}</td>
                    <td>{$fila['fecha_hora']}</td>
                    <td>{$fila['usuario_id']}</td>
                    <td>$" . number_format($fila['total'], 2) . "</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay ventas registradas hoy.</p>";
    }

    echo "<h3>Retiros registrados hoy</h3>";
    $retirosHoy = $conn->query("SELECT retiro_id, fecha_hora, usuario_id, monto, descripcion FROM retiros_caja WHERE DATE(fecha_hora) = CURDATE()");
    if ($retirosHoy && $retirosHoy->num_rows > 0) {
        echo "<table><tr><th>ID Retiro</th><th>Fecha</th><th>Usuario</th><th>Monto</th><th>Descripción</th></tr>";
        while ($fila = $retirosHoy->fetch_assoc()) {
            echo "<tr>
                    <td>{$fila['retiro_id']}</td>
                    <td>{$fila['fecha_hora']}</td>
                    <td>{$fila['usuario_id']}</td>
                    <td>$" . number_format($fila['monto'], 2) . "</td>
                    <td>{$fila['descripcion']}</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay retiros registrados hoy.</p>";
    }
}
$conn->close();
?>
</main>
<footer>
    <p>Panel Admin · v1.0 · Servidor: <?php echo date("d/m/Y H:i"); ?></p>
    <p>Sesión iniciada como: <?php echo $_SESSION['rol']; ?> (<?php echo $_SESSION['alias']; ?>)</p>
    <p>Desarrollado por <a href="https://github.com/Rendel05">Pedro M</a> · 2025</p>
    <p>© 2025 Tienda Olly — Todos los derechos reservados.</p>
</footer>

</body>
</html>  