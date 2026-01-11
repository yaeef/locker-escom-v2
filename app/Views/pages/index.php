<?php require APPROOT . '/Views/layout/header.php'; ?>

<div class="container py-5">
    <div class="row g-4">
        
        <div class="col-lg-8">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-primary p-2 rounded-3 me-3 text-white">
                    <i class="fas fa-newspaper fa-lg"></i>
                </div>
                <h3 class="fw-bold m-0">Noticias y Avisos <span class="text-muted fw-light small">ESCOM</span></h3>
            </div>

            <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                <div class="row g-0">
                    <div class="col-md-5 bg-dark" style="background: url('https://www.escom.ipn.mx/assets/img/identidad/banner_escom.jpg') center/cover;">
                        <div style="height: 200px;"></div>
                    </div>
                    <div class="col-md-7 p-4">
                        <span class="badge bg-danger mb-2">Urgente</span>
                        <h4 class="fw-bold">Proceso de Asignación 2026-1</h4>
                        <p class="text-muted small">Se les informa que el periodo para solicitar casilleros inicia este lunes. Asegúrate de tener tu credencial vigente en PDF.</p>
                        <a href="#" class="text-primary fw-bold text-decoration-none small">Leer más <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm border-0 rounded-4 p-3">
                        <small class="text-primary fw-bold mb-1">Mantenimiento</small>
                        <h6 class="fw-bold">Limpieza de Edificio 1</h6>
                        <p class="text-muted small mb-0">Los casilleros del nivel 2 entrarán en mantenimiento el próximo viernes.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm border-0 rounded-4 p-3">
                        <small class="text-success fw-bold mb-1">Académico</small>
                        <h6 class="fw-bold">Horarios de Ventanilla</h6>
                        <p class="text-muted small mb-0">Atención presencial para dudas de 10:00 AM a 4:00 PM.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden sticky-top" style="top: 100px;">
                <div class="bg-primary p-4 text-center text-white">
                    <h5 class="fw-bold mb-0">Gestión de Casilleros</h5>
                    <small class="opacity-75">Accede a tu cuenta</small>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted small text-center mb-4">Inicia sesión para solicitar un espacio o revisar tu estatus actual.</p>
                    
                    <div class="d-grid gap-3">
                        <a href="<?= URLROOT; ?>/auth/login" class="btn btn-primary py-2 fw-bold rounded-pill shadow-sm">
                            <i class="fas fa-sign-in-alt me-2"></i> Iniciar Sesión
                        </a>
                        <a href="<?= URLROOT; ?>/auth/registro" class="btn btn-outline-dark py-2 fw-bold rounded-pill">
                            <i class="fas fa-user-plus me-2"></i> Registrarse
                        </a>
                    </div>

                    <hr class="my-4 opacity-10">

                    <div class="bg-light p-3 rounded-3">
                        <h6 class="fw-bold small mb-2"><i class="fas fa-info-circle me-1 text-primary"></i> Recordatorio</h6>
                        <p class="mb-0 text-muted" style="font-size: 0.8rem;">
                            Debes ser alumno activo para solicitar un casillero. Se requiere tu boleta y comprobante de inscripción.
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require APPROOT . '/Views/layout/footer.php'; ?>