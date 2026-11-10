<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login/login.php");
    exit();
}

require_once(__DIR__ . '/../../../back_end/config.php');
require_once(BARRA_LATERAL);
require_once(FUNCIONES_LOGS);
!

$logs= mostrarLogs($pdo);

?>
<style>
    table.dataTable {
    width: 100% !important;
    margin: 0 auto;
}
</style>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- datatables -->
<link rel="stylesheet" href="../../../node_modules/datatables.net-dt/css/dataTables.dataTables.min.css">

<script src="../../../node_modules/jquery/dist/jquery.min.js"></script>

<script src="../../../node_modules/datatables.net/js/dataTables.min.js"></script>


     <link href="../../css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Logs</title>

</head>
<body>
    <h5 class="" style="margin-left:20rem;">Logs</h5> <br>

<div class="d-flex justify-content-center">
    <div class="table-responsive-sm ">
    <table class="table" style="border-collapse: separate; border-spacing: 0 10px;" id="tablaLog">
    <thead>
        <tr>
        <th  class="px-5 text-center text-muted fw-bold">ID usuario</th>
        <th class="px-4 text-center text-muted fw-bold">Accion</th>
        <th class="px-6 text-center text-muted fw-bold">Detalle</th>
        <th class="text-center text-muted fw-bold">IP</th>
        <th class="text-center text-muted fw-bold">Fecha</th>

        </tr>
    </thead>
    <tbody>
        <?php foreach($logs as $logs):?>  
    <tr class="shadow-sm">
        
            <td class=" border-0 "><h5><?= htmlspecialchars($logs['usuario_id'])?></h5></td>
            <td class="badge bg-primary-subtle text-primary border-0 " ><?= htmlspecialchars($logs['accion'])?></td>
            <td class="text-muted fw-bold border-0 "><?= htmlspecialchars($logs['detalle'])?></td>
            <td class="text-muted fw-bold border-0 "><?= htmlspecialchars($logs['ip_address'])?></td>
            <td class="border-0 "><?= htmlspecialchars($logs['fecha_registro'])?></td>
        
        
        </tr>
            <?php endforeach; ?>
    </tbody>
    </table>
    </div>
</div>

  <script src="../../js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

<script>
    $(document).ready(function() {
    $('#tablaLog').DataTable({

        language: {
            "decimal": "",
            "emptyTable": "No hay información",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ entradas",
            "infoEmpty": "Mostrando 0 a 0 de 0 entradas",
            "infoFiltered": "(Filtrado de _MAX_ total entradas)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "Mostrar _MENU_ entradas",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar:",
            "zeroRecords": "Sin resultados encontrados",
            "paginate": {
                "first": "Primero",
                "last": "Ultimo",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        }
    });
});
 </script>

</body>
</html>
