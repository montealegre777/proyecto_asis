<?php
session_start();

// 🛡️ CONTROL DE SEGURIDAD
if (!isset($_SESSION['admin_id']) || intval($_SESSION['id_tip_user'] ?? 0) !== 1) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/funciones.php';

if (!isset($pdo)) {
    if (class_exists('Database')) {
        $db  = new Database();
        $pdo = $db->conectar();
    } else {
        global $pdo;
    }
}

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
    <title>Dashboard Administrativo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/styles_index.css">
    <link rel="stylesheet" href="../css/styles_dashboard.css">
    <link rel="stylesheet" href="../css/styles_footer.css">
</head>

<body>

    <!-- Header exacto del index -->
    <header class="site-header">
        <div class="site-header__inner">
            <div class="site-header__brand">
                <div class="site-header__logo">
                    <i class="fa-solid fa-building-columns"></i>
                </div>
                <div>
                    <span class="site-header__name">Sistema de Asistencias</span>
                    <span class="site-header__sub">Gestión de Personal</span>
                </div>
            </div>
            <div class="site-header__date">
                <i class="fa-regular fa-calendar" style="margin-right:6px;color:var(--cyan)"></i>
                <?php echo date('d/m/Y'); ?>
            </div>
        </div>
    </header>

    <!-- Barra de acciones del admin -->
    <div class="admin-toolbar">
        <div class="admin-toolbar__inner">
            <div class="admin-toolbar__left">
                <a href="dashboard_admin.php" class="admin-toolbar__link active">
                    <i class="fa-solid fa-house me-1"></i> Inicio
                </a>
                <a href="empleados_crud.php" class="admin-toolbar__link">
                    <i class="fa-solid fa-users-gear me-1"></i> CRUD Empleados
                </a>
            </div>
            <div class="admin-toolbar__right">
                <span class="admin-toolbar__user">
                    <i class="fa-solid fa-user-shield me-1"></i>
                    <?= htmlspecialchars($_SESSION['nombres'] ?? 'Administrador') ?>
                </span>
                <a href="../logout.php" class="admin-toolbar__logout">
                    <i class="fa-solid fa-right-from-bracket me-1"></i> Cerrar Sesión
                </a>
            </div>
        </div>
    </div>

    <div class="container my-5">


        <div class="row gx-4">

            <article class="col-md-8">

                <div class="content-box card p-4 shadow-sm mb-4 bg-white border-0">
                    <h2 class="h4 mb-3 text-dark d-flex align-items-center">
                        <i class="fa-solid fa-calendar-check me-2 text-primary"></i> Reporte de Asistencias
                    </h2>
                    <form method="GET" class="row g-2 align-items-center">
                        <div class="col-auto">
                            <input type="date" name="fecha" class="form-control"
                                value="<?= htmlspecialchars($fechaSeleccionada) ?>">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fa-solid fa-filter me-1"></i> Filtrar
                            </button>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover mt-3 align-middle">
                            <thead class="table-dark">
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
                                    <td colspan="6" class="text-center text-muted py-3">Sin asistencias para esta fecha.
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($asistencias as $a): ?>
                                <tr>
                                    <td class="fw-semibold"><?= htmlspecialchars($a['nombre_completo']) ?></td>
                                    <td><?= htmlspecialchars($a['documento']) ?></td>
                                    <td><span
                                            class="badge bg-secondary"><?= htmlspecialchars($a['area'] ?? '—') ?></span>
                                    </td>
                                    <td><i
                                            class="fa-regular fa-clock text-success me-1"></i><?= date('H:i', strtotime($a['fecha_entrada'])) ?>
                                    </td>
                                    <td>
                                        <?php if ($a['fecha_salida']): ?>
                                        <i
                                            class="fa-regular fa-clock text-danger me-1"></i><?= date('H:i', strtotime($a['fecha_salida'])) ?>
                                        <?php else: ?>
                                        <span class="badge bg-warning text-dark"><i
                                                class="fa-solid fa-spinner fa-spin me-1"></i>En curso</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="fw-bold text-primary">
                                        <?= $a['horas_trabajadas'] ? number_format($a['horas_trabajadas'], 2) . 'h' : '—' ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="content-box card p-4 shadow-sm bg-white border-0">
                    <h2 class="h4 mb-3 text-dark d-flex align-items-center">
                        <i class="fa-solid fa-users me-2 text-success"></i> Lista General de Empleados
                    </h2>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
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
                                    <td colspan="5" class="text-center text-muted py-3">No hay empleados registrados.
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($empleados as $e): ?>
                                <tr>
                                    <td class="fw-semibold"><?= htmlspecialchars($e['nombre_completo']) ?></td>
                                    <td><?= htmlspecialchars($e['documento']) ?></td>
                                    <td><span
                                            class="badge bg-light text-dark border"><?= htmlspecialchars($e['area'] ?? '—') ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($e['tipo_usuario'] ?? '—') ?></td>
                                    <td><?= isset($e['fecha_creacion']) ? date('d/m/Y', strtotime($e['fecha_creacion'])) : '—' ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </article>

            <aside class="col-md-4 sidebar mt-4 mt-md-0">
                <div class="card p-4 shadow-sm bg-white border-0">
                    <h3 class="h4 mb-3 text-dark d-flex align-items-center">
                        <i class="fa-solid fa-layer-group me-2 text-warning"></i> Áreas de la Empresa
                    </h3>
                    <ul class="list-group list-group-flush">
                        <?php if (empty($areas)): ?>
                        <li class="list-group-item text-muted">Sin áreas registradas.</li>
                        <?php else: ?>
                        <?php foreach ($areas as $area): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center ps-0">
                            <span><i
                                    class="fa-solid fa-chevron-right text-muted me-2 small"></i><?= htmlspecialchars($area['nom_area']) ?></span>
                        </li>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </aside>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include '../includes/footer.php'; ?>

</body>

</html>