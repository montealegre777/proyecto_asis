<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/funciones.php';

$db  = new Database();
$pdo = $db->conectar();

$fechaSeleccionada = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');

$areas       = obtenerAreas($pdo);
$empleados   = obtenerEmpleados($pdo);
$asistencias = obtenerAsistenciasPorFecha($pdo, $fechaSeleccionada);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style/dashboardstyle.css">
</head>

<body>

    <div class="container my-5">
        <h1 class="titulo">Dashboard</h1>

        <div class="row gx-4">

            <article class="col-md-8">

                <!-- Asistencias por fecha -->
                <div class="content-box">
                    <h2>Asistencias</h2>
                    <form method="GET">
                        <input type="date" name="fecha" value="<?= htmlspecialchars($fechaSeleccionada) ?>">
                        <button type="submit">Filtrar</button>
                    </form>
                    <table class="table mt-3">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Documento</th>
                                <th>Área</th>
                                <th>Entrada</th>
                                <th>Salida</th>
                                <th>Horas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($asistencias)): ?>
                            <tr>
                                <td colspan="6">Sin asistencias para esta fecha.</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($asistencias as $a): ?>
                            <tr>
                                <td><?= htmlspecialchars($a['nombre_completo']) ?></td>
                                <td><?= htmlspecialchars($a['documento']) ?></td>
                                <td><?= htmlspecialchars($a['area'] ?? '—') ?></td>
                                <td><?= date('H:i', strtotime($a['fecha_entrada'])) ?></td>
                                <td><?= $a['fecha_salida'] ? date('H:i', strtotime($a['fecha_salida'])) : 'En curso' ?>
                                </td>
                                <td><?= $a['horas_trabajadas'] ? number_format($a['horas_trabajadas'], 2) . 'h' : '—' ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Empleados -->
                <div class="content-box">
                    <h2>Empleados</h2>
                    <table class="table mt-3">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Documento</th>
                                <th>Área</th>
                                <th>Tipo</th>
                                <th>Registro</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($empleados)): ?>
                            <tr>
                                <td colspan="5">No hay empleados registrados.</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($empleados as $e): ?>
                            <tr>
                                <td><?= htmlspecialchars($e['nombre_completo']) ?></td>
                                <td><?= htmlspecialchars($e['documento']) ?></td>
                                <td><?= htmlspecialchars($e['area'] ?? '—') ?></td>
                                <td><?= htmlspecialchars($e['tipo_usuario'] ?? '—') ?></td>
                                <td><?= isset($e['fecha_creacion']) ? date('d/m/Y', strtotime($e['fecha_creacion'])) : '—' ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </article>

            <!-- Áreas -->
            <aside class="col-md-4 sidebar">
                <h3>Áreas</h3>
                <ul>
                    <?php if (empty($areas)): ?>
                    <li>Sin áreas registradas.</li>
                    <?php else: ?>
                    <?php foreach ($areas as $area): ?>
                    <li><?= htmlspecialchars($area['nom_area']) ?></li>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </aside>

        </div>
    </div>

</body>

</html>