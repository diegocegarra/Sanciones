<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login/login.php");
    exit();
}

require_once(__DIR__ . '/../../../back_end/config.php');
require_once(BARRA_LATERAL);
require_once(ANALITICAS);

// Instancia de la clase y obtención de datos
$analiticas = new Analiticas();
$valores = $analiticas->mostrarDatosAnaliticas($pdo);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analíticas</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../publico/style_barra_tareas.css">
    
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<main class="main-content">
    <div class="header-section d-flex flex-column flex-md-row justify-content-between align-items-md-center">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Panel de Analíticas</h1>
            <p class="text-muted small">Visualización en tiempo real de métricas y rendimiento.</p>
        </div>
        <div class="mt-3 mt-md-0">
            <button class="btn btn-white shadow-sm border me-2" onclick="window.location.reload();">
                <i class="fas fa-sync-alt text-muted"></i>
            </button>
            <!-- <button class="btn btn-primary shadow-sm">
                <i class="fas fa-download fa-sm text-white-50 me-2"></i> Exportar
            </button> -->
        </div>
    </div>


    <!-- Graficos -->

    <div class="row g-4 mb-4">
        <div class="col-xl-4 col-lg-12">
            <div class="card stat-card p-3">
                <div class="card-title mb-3">
                    Sanciones Finalizadas <i class="fas fa-check-circle text-primary"></i>
                </div>
                <div class="chart-container">
                    <canvas id="graficoTareas"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-lg-2">
            <div class="card stat-card p-3">
                <div class="card-title mb-3">
                    UT Egresos <i class="fas fa-arrow-down text-danger"></i>
                </div>
                <div class="chart-container">
                    <canvas id="graficoFacturacion_egresos"></canvas>
                </div>
            </div>
        </div>


        
        <div class="col-xl-4 col-lg-2">
            <div class="card stat-card p-2">
                <div class="card-title mb-2">
                    UT Ingresos <i class="fas fa-arrow-up text-success"></i>
                </div>
                <div class="chart-container">
                    <canvas id="graficoFacturacion"></canvas>
                </div>
            </div>
        </div>

        
    </div>





    <!-- tabla -->

    <div class="table-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="m-0 font-weight-bold text-gray-800">Historial de Operaciones</h5>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover custom-table align-middle">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">#</th>
                        <th>UT Ingresos</th>
                        <th>UT Egresos</th>
                        <th>Fecha de Registro</th>
                        <th>Balance (Total UT)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $contador = 1;
                    if (isset($valores['facturacion']['labels'])):
                        foreach ($valores['facturacion']['labels'] as $i => $periodo):
                            // Cálculo del total
                            $ingresos = $valores['facturacion']['values'][$i] ?? 0;
                            $egresos = $valores['facturacion']['values_egresos'][$i] ?? 0;
                            $sumaUt = $ingresos + $egresos;
                    ?>
                        <tr>
                            <td class="text-center text-muted fw-bold"><?= $contador++ ?></td>
                            <td>
                                <span class="text-success fw-semibold">
                                    <i class="fas fa-plus-circle me-1 small"></i> <?= $ingresos ?>
                                </span>
                            </td>
                            <td>
                                <span class="text-danger fw-semibold">
                                    <i class="fas fa-minus-circle me-1 small"></i> <?= $egresos ?>
                                </span>
                            </td>
                            <td class="text-muted">
                                <i class="far fa-calendar-alt me-2"></i><?= $periodo ?>
                            </td>
                            <td>
                                <span class="badge-ut"><?= $sumaUt ?> UT</span>
                            </td>
                        </tr>
                    <?php 
                        endforeach; 
                    else:
                    ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No se encontraron datos disponibles.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script src="../../js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const DATA_GRAFICO = {
        labels: <?= json_encode($valores['tareas_estatus']['labels'] ?? []); ?>,
        values: <?= json_encode($valores['tareas_estatus']['values'] ?? []); ?>,
        fecha: <?= json_encode($valores['tareas_estatus']['fecha'] ?? []); ?>
    };

    const DATA_GRAFICO_FACTURACION = {
        labels: <?= json_encode($valores['facturacion']['labels'] ?? []); ?>,
        values: <?= json_encode($valores['facturacion']['values'] ?? []); ?>,
        values_egresos: <?= json_encode($valores['facturacion']['values_egresos'] ?? []); ?>
    };
    
    console.log("Analytics Data Ready.");
</script>

<script src="analiticas.js"></script>

</body>
</html>