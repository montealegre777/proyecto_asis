<?php session_start(); ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Control de Asistencia</title>
    <link rel="stylesheet" href="../proyecto_asis/css/styles_index.css">
    <link rel="stylesheet" href="../proyecto_asis/css/styles_footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

    <!-- Encabezado principal -->
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

    <div class="page-wrapper">

        <!-- Título del cuerpo -->
        <div class="body-header">
            <h1>Control de <span>Asistencia</span></h1>
            <p>Gestión eficiente de horarios, asistencia y administración del personal en tiempo real.</p>
        </div>

        <!-- Tarjetas principales -->
        <div class="main-grid">

            <div class="main-card">
                <div class="card-icon icon-green">
                    <i class="fa-regular fa-clock"></i>
                </div>
                <h2>Control de Asistencia</h2>
                <p>Registre su entrada y salida laboral de forma rápida y segura con su documento y PIN.</p>
                <button class="btn-primary-cyan"
                    onclick="document.getElementById('modalAsistencia').classList.add('active')">
                    <i class="fa-solid fa-fingerprint" style="margin-right:8px"></i>Registrar Asistencia
                </button>
            </div>

            <div class="main-card">
                <div class="card-icon icon-dark">
                    <i class="fa-solid fa-user-gear" style="color:var(--cyan)"></i>
                </div>
                <h2>Acceso Administrativo</h2>
                <p>Gestión completa de usuarios, áreas, reportes y configuración del sistema.</p>
                <a href="admin/login.php" class="btn-outline-cyan">
                    <i class="fa-solid fa-right-to-bracket" style="margin-right:8px"></i>Ingresar
                </a>
            </div>

        </div>

        <!-- Info cards -->
        <div class="info-grid">

            <div class="info-card">
                <span class="info-icon"><i class="fa-solid fa-bullseye"></i></span>
                <h4>Misión</h4>
                <p>Optimizar la gestión del talento humano mediante herramientas tecnológicas que faciliten el control
                    de asistencia.</p>
            </div>

            <div class="info-card">
                <span class="info-icon"><i class="fa-solid fa-eye"></i></span>
                <h4>Visión</h4>
                <p>Convertirnos en referencia en sistemas de control laboral, ofreciendo soluciones modernas, seguras y
                    confiables.</p>
            </div>

            <div class="info-card">
                <span class="info-icon"><i class="fa-solid fa-star"></i></span>
                <h4>Valores</h4>
                <p>Transparencia, responsabilidad, compromiso, innovación y respeto por nuestros colaboradores.</p>
            </div>

        </div>

    </div>

    <!-- Modal Asistencia -->
    <div class="modal-overlay" id="modalAsistencia" onclick="if(event.target===this)this.classList.remove('active')">
        <div class="modal-box">
            <div class="modal-header">
                <h5>
                    <i class="fa-solid fa-clock-rotate-left" style="color:var(--cyan);margin-right:10px"></i>
                    Registro de Asistencia
                </h5>
                <button class="modal-close"
                    onclick="document.getElementById('modalAsistencia').classList.remove('active')">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <form method="POST">
                <div class="form-group">
                    <label>Documento</label>
                    <input type="number" name="documento" required>
                </div>
                <div class="form-group">
                    <label>PIN</label>
                    <input type="password" name="pin" required>
                </div>
                <div class="form-group">
                    <label>Contraseña</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-actions">
                    <button type="submit" name="entrada" class="btn-entrada">
                        <i class="fa-solid fa-arrow-right-to-bracket" style="margin-right:6px"></i>Entrada
                    </button>
                    <button type="submit" name="salida" class="btn-salida">
                        <i class="fa-solid fa-arrow-right-from-bracket" style="margin-right:6px"></i>Salida
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

</body>

</html>