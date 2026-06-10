<?php
// ============================================================
// includes/funciones.php
// Responsabilidad: Centralizar todas las consultas a la BD.
// Cada función recibe $pdo (la conexión) y retorna datos o
// un booleano indicando éxito/fracaso.
// ============================================================


// ── ÁREAS ──
// Retorna todas las áreas de la empresa para llenar los selects del formulario
function obtenerAreas($pdo) {
    $stmt = $pdo->query("SELECT id_area, nom_area FROM area ORDER BY nom_area ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// ── EMPLEADOS ──

// Retorna todos los empleados con su área y tipo de usuario (usando JOINs)
function obtenerEmpleados($pdo) {
    $stmt = $pdo->query("
        SELECT u.documento, u.nombre_completo, u.fecha_creacion,
               a.nom_area AS area, t.nom_tip AS tipo_usuario
        FROM usuario u
        LEFT JOIN area a ON u.id_area = a.id_area          -- une con la tabla de áreas
        LEFT JOIN type_user t ON u.id_tip_user = t.id_tip_user  -- une con la tabla de roles
        ORDER BY u.nombre_completo ASC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Busca un empleado específico por su número de documento
function obtenerEmpleadoPorId($pdo, $documento) {
    $stmt = $pdo->prepare("
        SELECT u.*, a.nom_area AS area, t.nom_tip AS tipo_usuario
        FROM usuario u
        LEFT JOIN area a ON u.id_area = a.id_area
        LEFT JOIN type_user t ON u.id_tip_user = t.id_tip_user
        WHERE u.documento = ?
        LIMIT 1
    ");
    $stmt->execute([$documento]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Crea un nuevo empleado. Primero verifica que el documento no exista (evita duplicados)
function crearEmpleado($pdo, $datos) {
    // Verificar si el documento ya está registrado
    $check = $pdo->prepare("SELECT documento FROM usuario WHERE documento = ? LIMIT 1");
    $check->execute([$datos['documento']]);
    if ($check->fetch()) return false; // Retorna false si ya existe

    $stmt = $pdo->prepare("
        INSERT INTO usuario (documento, pin, password, nombre_completo, id_tip_user, id_area, fecha_creacion)
        VALUES (?, ?, ?, ?, ?, ?, CURDATE())
    ");
    return $stmt->execute([
        $datos['documento'],
        $datos['pin'],
        password_hash($datos['password'], PASSWORD_DEFAULT), // La contraseña se guarda cifrada, nunca en texto plano
        $datos['nombre_completo'],
        $datos['id_tip_user'],
        $datos['id_area']
    ]);
}

// Actualiza nombre, área y tipo de usuario de un empleado (el documento no se puede cambiar)
function actualizarEmpleado($pdo, $documento, $datos) {
    $stmt = $pdo->prepare("
        UPDATE usuario SET nombre_completo = ?, id_area = ?, id_tip_user = ?
        WHERE documento = ?
    ");
    return $stmt->execute([
        $datos['nombre_completo'],
        $datos['id_area'],
        $datos['id_tip_user'],
        $documento
    ]);
}

// Elimina permanentemente un empleado por su documento
function eliminarEmpleado($pdo, $documento) {
    $stmt = $pdo->prepare("DELETE FROM usuario WHERE documento = ?");
    return $stmt->execute([$documento]);
}


// ── ASISTENCIAS ──

// FUNCIÓN PRINCIPAL: Registra entrada o salida de un empleado.
// Lógica de decisión automática:
//   - Si el empleado NO tiene una entrada abierta hoy → registra ENTRADA
//   - Si el empleado YA tiene entrada abierta hoy      → registra SALIDA y calcula horas
function registrarAsistencia($pdo, $documento, $pin) {

    // Paso 1: Verificar que el documento pertenece a un empleado (no a un admin)
    $stmt = $pdo->prepare("
        SELECT u.* FROM usuario u
        INNER JOIN type_user t ON u.id_tip_user = t.id_tip_user
        WHERE u.documento = ? AND t.nom_tip = 'empleado'
        LIMIT 1
    ");
    $stmt->execute([$documento]);
    $empleado = $stmt->fetch(PDO::FETCH_ASSOC);

    // Paso 2: Validar que el PIN ingresado coincide con el PIN del empleado en BD
    if (!$empleado || (int)$pin !== (int)$empleado['pin']) {
        return ['ok' => false, 'mensaje' => 'Documento o PIN incorrecto.'];
    }

    // Paso 3: Buscar si ya tiene una entrada abierta hoy (sin salida registrada)
    $hoy = date('Y-m-d');
    $check = $pdo->prepare("
        SELECT id_asistencia FROM asistencias
        WHERE id_empleado = ? AND DATE(fecha_entrada) = ? AND fecha_salida IS NULL
        LIMIT 1
    ");
    $check->execute([$empleado['documento'], $hoy]);
    $abierto = $check->fetch(PDO::FETCH_ASSOC);

    // Paso 4: Decidir si es entrada o salida
    if (!$abierto) {
        // No hay entrada abierta → crear nuevo registro de ENTRADA
        $pdo->prepare("INSERT INTO asistencias (id_empleado, fecha_entrada) VALUES (?, NOW())")
            ->execute([$empleado['documento']]);
        return ['ok' => true, 'mensaje' => 'Entrada registrada correctamente.'];
    } else {
        // Ya hay entrada abierta → cerrarla como SALIDA y calcular horas trabajadas
        $pdo->prepare("
            UPDATE asistencias
            SET fecha_salida = NOW(),
                horas_trabajadas = TIMESTAMPDIFF(MINUTE, fecha_entrada, NOW()) / 60
            WHERE id_asistencia = ?
        ")->execute([$abierto['id_asistencia']]);
        return ['ok' => true, 'mensaje' => 'Salida registrada correctamente.'];
    }
}


// ── REPORTES ──

// Retorna todas las asistencias de una fecha específica para el dashboard del admin
function obtenerAsistenciasPorFecha($pdo, $fecha) {
    $stmt = $pdo->prepare("
        SELECT u.nombre_completo, u.documento, a.nom_area AS area,
               ast.fecha_entrada, ast.fecha_salida,
               ROUND(ast.horas_trabajadas, 2) AS horas_trabajadas
        FROM asistencias ast
        INNER JOIN usuario u ON ast.id_empleado = u.documento
        LEFT JOIN area a ON u.id_area = a.id_area
        WHERE DATE(ast.fecha_entrada) = ?
        ORDER BY ast.fecha_entrada ASC
    ");
    $stmt->execute([$fecha]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}