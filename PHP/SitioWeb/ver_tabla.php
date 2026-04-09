<?php
include("conexion.php");

$tabla = $_GET['tabla'];

$result = $conn->query("SELECT * FROM $tabla");

// total registros
$total = $conn->query("SELECT COUNT(*) as total FROM $tabla")->fetch_assoc();

echo "<h2 class='mb-3'>📊 Tabla: $tabla</h2>";
echo "<p>Total registros: <b>{$total['total']}</b></p>";

// CONTENEDOR CON SCROLL
echo "<div style='max-height:400px; overflow:auto;'>";

echo "<table class='table table-dark table-hover table-striped'>";

// ENCABEZADOS
echo "<thead>";
echo "<tr>";
while($field = $result->fetch_field()) {
    echo "<th>{$field->name}</th>";
}
echo "</tr>";
echo "</thead>";

// DATOS
echo "<tbody>";

if ($result->num_rows > 0) {

    while($row = $result->fetch_assoc()) {
        echo "<tr>";

        foreach($row as $valor) {
            echo "<td>$valor</td>";
        }

        echo "</tr>";
    }

} else {
    echo "<tr><td colspan='100'>⚠️ No hay datos</td></tr>";
}

echo "</tbody>";
echo "</table>";
echo "</div>";
?>