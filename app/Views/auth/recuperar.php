<?php require APPROOT . '/Views/layout/header.php'; ?>

<div class="container vh-100 d-flex align-items-center justify-content-center">
    <div class="col-md-6 animate__animated animate__fadeIn">
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            <div style="height: 6px; background: #621132;"></div>
            <div class="card-body p-5">
                
                <?php if(isset($_GET['success'])): ?>
                    <div class="text-center">
                        <div class="bg-light d-inline-block p-3 rounded-circle mb-3">
                            <i class="fas fa-paper-plane fa-2x text-success"></i>
                        </div>
                        <h3 class="fw-bold">Simulación de Envío</h3>
                        <p class="text-muted small">En un entorno real, este enlace se enviaría por correo. Úsalo para continuar con la prueba:</p>
                        
                        <div class="alert alert-info border-0 shadow-sm p-3 mb-4">
                            <a href="<?= URLROOT; ?>/auth/resetear/<?= $_GET['token']; ?>" class="fw-bold text-break">
                                <?= URLROOT; ?>/auth/resetear/<?= $_GET['token']; ?>
                            </a>
                        </div>
                        
                        <a href="<?= URLROOT; ?>/auth/login" class="btn btn-dark rounded-pill px-4">Ir al Login</a>
                    </div>

                <?php else: ?>
                    <div class="text-center mb-4">
                        <div class="bg-light d-inline-block p-3 rounded-circle mb-3">
                            <i class="fas fa-key fa-2x" style="color: #621132;"></i>
                        </div>
                        <h3 class="fw-bold">Recuperar Acceso</h3>
                        <p class="text-muted small">Ingresa tu correo para generar un token de recuperación.</p>
                    </div>

                    <?php if(isset($_GET['err'])): ?>
                        <div class="alert alert-danger py-2 small border-start border-4 border-danger">
                            El correo no está registrado en el sistema.
                        </div>
                    <?php endif; ?>

                    <form action="<?= URLROOT; ?>/auth/enviar_token" method="POST">
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted">Correo Institucional</label>
                            <input type="email" name="correo" class="form-control bg-light border-0 py-2" placeholder="usuario@ipn.mx" required>
                        </div>

                        <button type="submit" class="btn text-white w-100 py-3 fw-bold rounded-pill shadow-sm" style="background: #621132;">
                            Generar Enlace Temporal
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <a href="<?= URLROOT; ?>/auth/login" class="text-decoration-none small fw-bold" style="color: #621132;">
                            <i class="fas fa-arrow-left me-1"></i> Regresar al login
                        </a>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/Views/layout/footer.php'; ?>