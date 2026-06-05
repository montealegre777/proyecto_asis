<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema asistencia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../proyecto_asis/style/indexstyle.css">
</head>

<body class="fondo-degradado">

    <section class="container-fluid">
        <div class="container">
            <div class="text-center mb-5 text-white">
                <h1 class="display-4 fw-bold">Sistema de Control de Asistencia</h1>
                <p class="lead">Gestión eficiente de horarios, asistencia y administración del personal.</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card border-0 shadow-lg h-100">
                        <div class="card-body d-flex flex-column align-items-center text-center p-5">
                            <div class="rounded-circle bg-success text-white d-flex justify-content-center align-items-center icon-circle-asistencia mb-4">
                                <i class="fa-solid fa-clock-rotate-left fs-1"></i>
                            </div>
                            <h2 class="fw-bold mb-3">Control de Asistencia</h2>
                            <button type="button" class="btn btn-success rounded-pill px-4 py-2" data-bs-toggle="modal" data-bs-target="#modalAsistencia">
                                Registrar Asistencia
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card border-0 shadow-lg h-100">
                        <div class="card-body d-flex flex-column align-items-center text-center p-5">
                            <div class="rounded-circle bg-dark text-white d-flex justify-content-center align-items-center icon-circle-admin mb-4">
                                <i class="fa-solid fa-user-gear fs-1"></i>
                            </div>
                            <h3 class="fw-bold mb-3">Acceso Administrativo</h3>
                            <a href="login.php" class="btn btn-dark rounded-pill px-4 py-2">Ingresar</a>
                        </div>
                    </div>
                </div>
            </div>
            
            </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>