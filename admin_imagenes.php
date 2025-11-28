<?php
require_once 'conexion.php';

echo "<h2>Asignar imágenes a productos</h2>";

$porPagina = 20;
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$inicio = ($pagina - 1) * $porPagina;

$totalQuery = $conn->query("SELECT COUNT(*) AS total FROM productos");
$total = $totalQuery->fetch_assoc()['total'];
$paginasTotales = ceil($total / $porPagina);

$sql = "SELECT producto_id, nombre, ImagenText 
        FROM productos 
        ORDER BY producto_id ASC 
        LIMIT $inicio, $porPagina";

$res = $conn->query($sql);

if ($res && $res->num_rows > 0) {

    echo "<table>
            <tr>
                <th>ID</th>
                <th>Producto</th>
                <th>Imagen</th>
                <th>Acción</th>
            </tr>";

    while ($fila = $res->fetch_assoc()) {

        $preview = $fila['ImagenText']
            ? "<img src='img/{$fila['ImagenText']}' style='width:60px;height:60px;border-radius:8px;object-fit:cover;'>"
            : "Sin imagen";

        echo "<tr>
        <td>{$fila['producto_id']}</td>
        <td>{$fila['nombre']}</td>
        <td>$preview</td>
        <td>
            <form onsubmit='return false;' class='form-img' data-id='{$fila['producto_id']}'>
                <input type='file' accept='image/*' class='file-input' hidden>
                <button class='btn btn-editar' type='button' onclick='this.previousElementSibling.click()'>Asignar / Cambiar</button>
            </form>
        </td>
      </tr>";

    }

    echo "</table>";
} else {
    echo "<p>No hay productos registrados.</p>";
}

echo "<div style='text-align:center; margin-top:1rem;'>";

if ($pagina > 1) {
    echo "<a href='?seccion=imagenes&pagina=" . ($pagina - 1) . "' class='btn btn-editar'>Anterior</a>";
}

if ($pagina < $paginasTotales) {
    echo "<a href='?seccion=imagenes&pagina=" . ($pagina + 1) . "' class='btn btn-editar'>Siguiente</a>";
}

echo "</div>";

?>
<script>
document.querySelectorAll(".file-input").forEach(input => {

    input.addEventListener("change", async function () {
        if (!this.files.length) return;

        const file = this.files[0];
        const form = this.closest(".form-img");
        const id = form.dataset.id;

        const formData = new FormData();
        formData.append("imagen", file);
        formData.append("id", id);

        const resp = await fetch("asignar_imagen.php", {
            method: "POST",
            body: formData
        });

        const txt = await resp.text();
        alert(txt);

        location.reload();
    });
});
</script>