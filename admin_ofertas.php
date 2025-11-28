<?php
require_once "conexion.php";

$sql = "SELECT o.oferta_id, o.precio_oferta, o.fecha_inicio, o.fecha_fin, o.activo,
        p.nombre AS nombre_producto
        FROM ofertas o
        JOIN productos p ON o.producto_id = p.producto_id";

$result = $conn->query($sql);
?>

<h2>Gestión de Ofertas</h2>

<a href="admin_ofertas_nueva.php" class="btn btn-agregar">+ Crear nueva oferta</a>

	
<table border="1" cellpadding="10" style="margin-top:20px;">
    <tr>
        <th>ID</th>
        <th>Producto</th>
        <th>Precio Oferta</th>
        <th>Inicio</th>
        <th>Fin</th>
        <th>Activo</th>
        <th>Acciones</th>
    </tr>

<?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['oferta_id']}</td>";
        echo "<td>{$row['nombre_producto']}</td>";
        echo "<td>{$row['precio_oferta']}</td>";
        echo "<td>{$row['fecha_inicio']}</td>";
        echo "<td>{$row['fecha_fin']}</td>";
        echo "<td>" . ($row['activo'] ? "Sí" : "No") . "</td>";
        echo "<td style='display:grid; align-items:center; gap:1rem;'>
                <a class='btn btn-editar' href='admin_ofertas_editar.php?id={$row['oferta_id']}'>Editar</a>
                <a class='btn btn-eliminar' href='admin_ofertas_eliminar.php?id={$row['oferta_id']}'>Desactivar</a>
              </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='7'>No hay ofertas registradas.</td></tr>";
}
?>
</table>
