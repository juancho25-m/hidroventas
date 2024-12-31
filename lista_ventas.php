<?php
session_start();
require_once "../conexion.php";
$id_user = $_SESSION['idUser'];
$permiso = "ventas";
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");
$existe = mysqli_fetch_all($sql);
if (empty($existe) && $id_user != 1) {
    header('Location: permisos.php');
}
// Consulta para obtener las ventas del día
$total_ventas_query = mysqli_query($conexion, "SELECT SUM(total) AS total_ventas FROM ventas WHERE DATE(fecha) = CURDATE()");
$total_ventas_row = mysqli_fetch_assoc($total_ventas_query);
$total_ventas = $total_ventas_row['total_ventas'] ? $total_ventas_row['total_ventas'] : 0;
$query = mysqli_query($conexion, "SELECT v.*, c.idcliente, c.nombre, v.tipo_pago FROM ventas v INNER JOIN cliente c ON v.id_cliente = c.idcliente");
include_once "includes/header.php";
// Consulta para obtener las ventas del mes actual
$total_ventas_query = mysqli_query($conexion, "SELECT SUM(total) AS total_ventas FROM ventas WHERE MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())");

if ($total_ventas_query === false) {
    die("Error en la consulta de total de ventas mensuales: " . mysqli_error($conexion));
}

$total_ventas_row = mysqli_fetch_assoc($total_ventas_query);
$total_ventas = $total_ventas_row['total_ventas'] ? $total_ventas_row['total_ventas'] : 0;

// Consulta para obtener las ventas del mes actual
$query = mysqli_query($conexion, "SELECT v.*, c.idcliente, c.nombre FROM ventas v INNER JOIN cliente c ON v.id_cliente = c.idcliente WHERE MONTH(v.fecha) = MONTH(CURDATE()) AND YEAR(v.fecha) = YEAR(CURDATE())");

if ($query === false) {
    die("Error en la consulta de ventas mensuales: " . mysqli_error($conexion));
}

include_once "includes/header.php";
?>
<div class="card">
    <div class="card-header">
        Historial ventas
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <strong>Total de ventas del día: </strong> $<?php echo number_format($total_ventas, 2); ?>
        </div>
        <div class="table-responsive">
            <table class="table table-light" id="tbl">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Fecha</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($query)){ ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['nombre']; ?></td>
                            <td><?php echo $row['total']; ?></td>
                            <td><?php echo $row['fecha']; ?></td>
                            <td>
                                <a href="pdf/generar.php?cl=<?php echo $row['id_cliente'] ?>&v=<?php echo $row['id'] ?>" target="_blank" class="btn btn-danger"><i class="fas fa-file-pdf"></i></a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include_once "includes/footer.php"; ?>