<?php
// Iniciar sesión para poder leer los datos del usuario logueado
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Validamos si inició sesión y si su ID de tipo de usuario corresponde a Admin (1)
if (!isset($_SESSION['documento']) || intval($_SESSION['id_tip_user'] ?? 0) !== 1) {
    header("Location: ../index.php?error=acceso_denegado");
    exit();
}

// Conexión y funciones
require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/includes/funciones.php';

$db = new Database();
$pdo = $db->conectar();
if (!$pdo) {
    die('<div class="alert alert-danger text-center mt-5"> Error de conexión a la base de datos</div>');
}

// Carga exacta de roles desde 'type_user'
$tipos = $pdo->query("SELECT id_tip_user AS id, nom_tip AS nombre FROM type_user ORDER BY nom_tip ASC")->fetchAll(PDO::FETCH_ASSOC);

// Carga de áreas desde tu función corregida
$areas = obtenerAreas($pdo);

$accion = $_GET['accion'] ?? 'menu';
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_POST['crear'])) {
        $pin_plano  = trim($_POST['pin']);
        $pass_plano = trim($_POST['password']);
        
        $pin_hash  = password_hash($pin_plano, PASSWORD_DEFAULT);
        $pass_hash = password_hash($pass_plano, PASSWORD_DEFAULT);

        $datos = [
            'documento'       => trim($_POST['documento']),
            'nombre_completo' => trim($_POST['nombre_completo']),
            'pin'             => $pin_plano, // Nota: Si manejas PIN entero en BD cambialo a intval si es necesario
            'password'        => $pass_hash,
            'tipo_usuario_id' => $_POST['tipo_usuario_id'] !== '' ? intval($_POST['tipo_usuario_id']) : null,
            'area_id'         => $_POST['area_id'] !== '' ? intval($_POST['area_id']) : null
        ];

        if ($datos['documento'] !== '' && $datos['nombre_completo'] !== '' && $pin_plano !== '' && $pass_plano !== '') {
            $res = crearEmpleado($pdo, $datos);
            $mensaje = $res ? "Empleado creado correctamente." : "Error al insertar el empleado.";
        } else {
            $mensaje = "Todos los campos obligatorios son requeridos.";
        }
    }
    
    elseif (isset($_POST['actualizar'])) {
        $documento = trim($_POST['documento']);
        $datos = [
            'nombre_completo' => trim($_POST['nombre_completo']),
            'tipo_usuario_id' => $_POST['tipo_usuario_id'] !== '' ? intval($_POST['tipo_usuario_id']) : null,
            'area_id'         => $_POST['area_id'] !== '' ? intval($_POST['area_id']) : null
        ];

        if ($documento !== '' && $datos['nombre_completo'] !== '') {
            $ok = actualizarEmpleado($pdo, $documento, $datos);
            $mensaje = $ok ? "Empleado actualizado con éxito." : "Error al actualizar el empleado.";
        } else {
            $mensaje = "Datos inválidos para actualizar.";
        }
    }
    
    elseif (isset($_POST['eliminar'])) {
        $documento = trim($_POST['documento']);
        if ($documento !== '') {
            $res = eliminarEmpleado($pdo, $documento);
            $mensaje = $res ? "Empleado eliminado correctamente." : "Error al eliminar el empleado.";
        }
    }
}

if ($accion === "listar") {
    $empleados = obtenerEmpleados($pdo);
}    

if ($accion === "reportes") {
    if (function_exists('obtenerReportesAsistencia')) {
        $asistencias = obtenerReportesAsistencia($pdo);
    } else {
        $sqlAsistencia = "SELECT 
                            a.id_asistencia, 
                            u.documento, 
                            u.nombre_completo, 
                            DATE(a.fecha_entrada) AS fecha, 
                            TIME(a.fecha_entrada) AS hora_entrada, 
                            TIME(a.fecha_salida) AS hora_salida,
                            a.horas_trabajadas
                          FROM asistencias a 
                          INNER JOIN usuario u ON a.id_empleado = u.documento 
                          ORDER BY a.fecha_entrada DESC";
        $asistencias = $pdo->query($sqlAsistencia)->fetchAll(PDO::FETCH_ASSOC);
    }
}

$empleado_editar = null;
if ($accion === 'editar_form') {
    $id_buscar = trim($_GET['id'] ?? '');
    if ($id_buscar !== '') {
        $empleado_editar = obtenerEmpleadoPorId($pdo, $id_buscar);
        if (!$empleado_editar) {
            $mensaje = "No se encontró ningún empleado con ese documento.";
        }
    }
} 
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Empleados & Reportes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-light">

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded shadow-sm border">
            <div class="d-flex align-items-center">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; font-size:1.2rem; font-weight:bold;">
                    <?php 
                    $iniciales = strtoupper(substr($_SESSION['nombres'] ?? 'A', 0, 1));
                    echo $iniciales;
                    ?>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold"><?= htmlspecialchars($_SESSION['nombres'] ?? 'Administrador') ?></h6>
                    <small class="text-muted">
                        Documento: <?= htmlspecialchars($_SESSION['documento'] ?? '') ?> |
                        <span class="badge bg-danger text-white">Panel de Control</span>
                    </small>
                </div>
            </div>
            <a href="../logout.php" class="btn btn-outline-danger btn-sm px-3">
                <i class="fas fa-sign-out-alt me-1"></i> Cerrar sesión
            </a>
        </div>

        <h1 class="mb-4 text-center">Panel de Administración de Personal</h1>

        <?php if ($mensaje): ?>
            <div class="alert alert-warning text-center shadow-sm">
                <p class="mb-2"><strong><?= $mensaje ?></strong></p>
                <a href="?accion=menu" class="btn btn-primary btn-sm">- Volver al menú</a>
            </div>
        <?php else: ?>

            <?php if ($accion === 'menu'): ?>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Seleccione una opción del sistema:</h5>
                        <div class="list-group list-group-flush">
                            <a href="?accion=listar" class="list-group-item list-group-item-action py-3"><i class="fa-solid fa-users text-primary me-3"></i> Listar empleados</a>
                            <a href="?accion=crear_form" class="list-group-item list-group-item-action py-3"><i class="fa-solid fa-user-plus text-success me-3"></i> Crear empleado</a>
                            <a href="?accion=editar_form" class="list-group-item list-group-item-action py-3"><i class="fa-solid fa-user-pen text-warning me-3"></i> Actualizar empleado</a>
                            <a href="?accion=reportes" class="list-group-item list-group-item-action py-3 bg-light fw-bold"><i class="fa-solid fa-file-invoice text-danger me-3"></i> Ver Reportes de Asistencia</a>
                        </div>
                    </div>
                </div>

            <?php elseif ($accion === 'listar'): ?>
                <h2 class="mb-3">Listado de empleados</h2>
                <?php if (count($empleados ?? []) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Documento</th>
                                    <th>Nombre Completo</th>
                                    <th>Área</th>
                                    <th>Tipo Usuario</th>
                                    <th>Fecha Creación</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($empleados as $emp): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($emp['documento']) ?></td>
                                        <td><?= htmlspecialchars($emp['nombre_completo']) ?></td>
                                        <td><?= htmlspecialchars($emp['area'] ?? 'Sin área') ?></td>
                                        <td><?= htmlspecialchars($emp['tipo_usuario'] ?? 'Sin tipo') ?></td>
                                        <td><?= htmlspecialchars($emp['fecha_creacion']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">No hay empleados registrados.</div>
                <?php endif; ?>
                <p><a href="?accion=menu" class="btn btn-secondary mt-3">- Volver al menú</a></p>

            <?php elseif ($accion === 'reportes'): ?>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2>Historial de Asistencias Real <i class="fa-solid fa-clock text-danger"></i></h2>
                    <a href="?accion=menu" class="btn btn-secondary btn-sm"><i class="fa-solid fa-arrow-left"></i> Volver</a>
                </div>

                <?php if (count($asistencias ?? []) > 0): ?>
                    <div class="card shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered mb-0 align-middle">
                                    <thead class="table-danger">
                                        <tr>
                                            <th>ID Registro</th>
                                            <th>Documento</th>
                                            <th>Empleado</th>
                                            <th>Fecha</th>
                                            <th>Hora Entrada</th>
                                            <th>Hora Salida</th>
                                            <th>Horas Totales</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($asistencias as $asist): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($asist['id_asistencia']) ?></td>
                                                <td class="fw-bold"><?= htmlspecialchars($asist['documento']) ?></td>
                                                <td><?= htmlspecialchars($asist['nombre_completo']) ?></td>
                                                <td><?= htmlspecialchars($asist['fecha']) ?></td>
                                                <td class="text-success fw-bold">
                                                    <i class="fa-solid fa-right-to-bracket me-1"></i> <?= htmlspecialchars($asist['sample'] ?? $asist['hora_entrada']) ?>
                                                </td>
                                                <td class="text-danger fw-bold">
                                                    <i class="fa-solid fa-right-from-bracket me-1"></i> 
                                                    <?= $asist['hora_salida'] ? htmlspecialchars($asist['hora_salida']) : '<span class="text-muted fw-normal">En turno (Sin salida)</span>' ?>
                                                </td>
                                                <td>
                                                    <?php if ($asist['horas_trabajadas'] !== null): ?>
                                                        <span class="badge bg-primary"><?= htmlspecialchars($asist['horas_trabajadas']) ?> hrs</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Calculando...</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning shadow-sm text-center">
                        <i class="fa-solid fa-triangle-exclamation fa-2x mb-2"></i>
                        <p class="mb-0">Aún no se han generado registros de asistencia en la tabla asistencias.</p>
                    </div>
                <?php endif; ?>
                <p class="mt-3"><a href="?accion=menu" class="btn btn-secondary">- Volver al menú</a></p>

            <?php elseif ($accion === 'crear_form'): ?>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title mb-3">Nuevo empleado</h2>
                        <form action="" method="post">
                            <div class="mb-3">
                                <label class="form-label">Documento</label>
                                <input type="number" name="documento" required class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nombre completo</label>
                                <input type="text" name="nombre_completo" required class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">PIN (Solo Números enteros)</label>
                                <input type="number" name="pin" required class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Contraseña (Acceso al sistema)</label>
                                <input type="password" name="password" required class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Área</label>
                                <select name="area_id" class="form-select">
                                    <option value="">-- Sin área --</option>
                                    <?php foreach ($areas as $a): ?>
                                        <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nombre']) ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tipo de usuario</label>
                                <select name="tipo_usuario_id" class="form-select" required>
                                    <option value="">-- Seleccione un rol --</option>
                                    <?php foreach ($tipos as $t): ?>
                                        <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <button type="submit" name="crear" class="btn btn-primary">Guardar</button>
                            <a href="?accion=menu" class="btn btn-outline-secondary">Cancelar</a>
                        </form>
                    </div>
                </div>
                
            <?php elseif ($accion === 'editar_form' && !$empleado_editar): ?>
                <div class="card shadow-sm p-4">
                    <h2>Actualizar Empleado <i class="fa-solid fa-pen"></i></h2>
                    <p class="text-muted">Ingrese el documento del empleado que desea editar:</p>
                    <form action="" method="GET" class="row g-3 align-items-center">
                        <input type="hidden" name="accion" value="editar_form">
                        <div class="col-sm-6">
                            <input type="number" name="id" class="form-control" placeholder="Número de documento" required min="1">
                        </div>
                        <div class="col-sm-6">
                            <button type="submit" class="btn btn-success"><i class="fa-solid fa-magnifying-glass"></i> Buscar</button>
                        </div>
                    </form>
                    <p class="mt-3"><a href="?accion=menu" class="btn btn-secondary">- Volver al menú</a></p>
                
            <?php elseif ($empleado_editar): ?>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title mb-3">Editar empleado: <?= htmlspecialchars($empleado_editar['nombre_completo']) ?></h2>
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Documento del empleado: </label>
                                <input type="number" class="form-control" name="documento" value="<?= htmlspecialchars($empleado_editar['documento']) ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nombre completo</label>
                                <input type="text" class="form-control" name="nombre_completo" value="<?= htmlspecialchars($empleado_editar['nombre_completo']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Área</label>
                                <select name="area_id" class="form-select">
                                    <option value="">-- Sin área --</option>
                                    <?php foreach ($areas as $a): ?>
                                        <option value="<?= $a['id'] ?>" <?= $empleado_editar['id_area'] == $a['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($a['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tipo de usuario</label>
                                <select name="tipo_usuario_id" class="form-select" required>
                                    <option value="">-- Seleccione un rol --</option>
                                    <?php foreach ($tipos as $t): ?>
                                        <option value="<?= $t['id'] ?>" <?= $empleado_editar['id_tip_user'] == $t['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($t['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?> 
                                </select>
                            </div>
                            <button type="submit" name="actualizar" class="btn btn-warning">Actualizar</button>
                            <a href="?accion=menu" class="btn btn-outline-secondary">Cancelar</a>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</body>
</html>