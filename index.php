<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TiendaPro | Catálogo</title>
    <!-- Bootstrap 5 + FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <section class="container-fluid py-5 bg-light">

        <div class="container">

            <!-- Título principal -->
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold">Sistema de Control de Asistencia</h1>
                <p class="lead text-muted">
                    Gestión eficiente de horarios, asistencia y administración del personal.
                </p>
            </div>

            <!-- Panel principal -->
            <div class="row g-4">
                <!-- Asistencia -->
                <div class="col-lg-6">

                    <div class="card border-0 shadow-lg h-100">

                        <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-5">

                            <div class="mb-4">

                                <div
                                    class="rounded-circle bg-success text-white d-flex justify-content-center align-items-center"
                                    style="width:120px;height:120px;">

                                    <i class="bi bi-clock-history fs-1"></i>

                                </div>

                            </div>

                            <h2 class="fw-bold mb-3">
                                Control de Asistencia
                            </h2>

                            <p class="text-muted mb-4">
                                Registre su entrada y salida laboral de forma
                                rápida y segura.
                            </p>

                            <a
                                href="#"
                                class="btn btn-success rounded-pill px-4 py-2"
                                data-bs-toggle="modal"
                                data-bs-target="#modalAsistencia">

                                Registrar Asistencia

                            </a>

                        </div>

                    </div>

                </div>

                <!-- Administrador -->
                <div class="col-lg-6">

                    <div class="card border-0 shadow-lg h-100">

                        <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-5">

                            <div class="mb-4">

                                <div
                                    class="rounded-circle bg-dark text-white d-flex justify-content-center align-items-center"
                                    style="width:100px;height:100px;">

                                    <i class="bi bi-person-gear fs-1"></i>

                                </div>

                            </div>

                            <h3 class="fw-bold mb-3">
                                Acceso Administrativo
                            </h3>

                            <p class="text-muted mb-4">
                                Gestión de usuarios, áreas y reportes.
                            </p>

                            <a
                                href="admin/login.php"
                                class="btn btn-dark rounded-pill px-4 py-2">

                                Ingresar

                            </a>

                        </div>

                    </div>

                </div>

            </div>

            <!-- Información empresarial -->
            <div class="row mt-5 g-4">

                <div class="col-md-4">

                    <div class="card shadow-sm border-0 h-100">

                        <div class="card-body text-center p-4">

                            <h4 class="fw-bold mb-3">
                                Misión
                            </h4>

                            <p class="text-muted">
                                Optimizar la gestión del talento humano
                                mediante herramientas tecnológicas que
                                faciliten el control de asistencia y
                                la administración empresarial.
                            </p>

                        </div>

                    </div>

                </div>

                <div class="col-md-4">

                    <div class="card shadow-sm border-0 h-100">

                        <div class="card-body text-center p-4">

                            <h4 class="fw-bold mb-3">
                                Visión
                            </h4>

                            <p class="text-muted">
                                Convertirnos en una referencia en sistemas
                                de control laboral, ofreciendo soluciones
                                modernas, seguras y confiables.
                            </p>

                        </div>

                    </div>

                </div>

                <div class="col-md-4">

                    <div class="card shadow-sm border-0 h-100">

                        <div class="card-body text-center p-4">

                            <h4 class="fw-bold mb-3">
                                Valores
                            </h4>

                            <p class="text-muted">
                                Transparencia, responsabilidad,
                                compromiso, innovación y respeto
                                por nuestros colaboradores.
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    <div class="modal fade" id="modalAsistencia" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Registro de Asistencia
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">
                                Documento
                            </label>
                            <input
                                type="number"
                                name="documento"
                                class="form-control"
                                placeholder="Ingrese su documento"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">PIN</label>
                            <input
                                type="password"
                                name="pin"
                                class="form-control"
                                placeholder="Ingrese su PIN"
                                required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">
                                Contraseña
                            </label>
                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                placeholder="Ingrese su contraseña"
                                required>
                        </div>
                        <div class="d-grid gap-2">
                            <button
                                type="submit"
                                name="entrada"
                                class="btn btn-success">
                                Registrar Entrada
                            </button>
                            <button
                                type="submit"
                                name="salida"
                                class="btn btn-danger">
                                Registrar Salida
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>