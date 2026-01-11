<?php require APPROOT . '/Views/layout/header.php'; ?>

<div class="container flex-grow-1 d-flex justify-content-center align-items-center py-5">
    
    <div style="width: 100%; max-width: 450px;" class="animate__animated animate__fadeIn">
        
        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-start border-4 border-danger mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-3 fa-lg"></i>
                    <div class="small">
                        <strong class="d-block">Error de acceso</strong>
                        <?php 
                            switch($_GET['error']) {
                                case 'no_existe': echo "El usuario no está registrado en el sistema."; break;
                                case 'password_incorrecto': echo "La contraseña es incorrecta."; break;
                                default: echo "Ocurrió un problema al iniciar sesión.";
                            }
                        ?>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'token_enviado'): ?>
            <div class="alert alert-info alert-dismissible fade show shadow-sm border-start border-4 border-info mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-envelope-open-text me-3 fa-lg"></i>
                    <div class="small">
                        <strong class="d-block">Correo enviado</strong>
                        Se ha enviado un enlace de recuperación a tu bandeja de entrada.
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['status']) && $_GET['status'] == 'reg_ok'): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-start border-4 border-success mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle me-3 fa-lg"></i>
                    <div class="small">
                        <strong class="d-block">¡Registro Exitoso!</strong>
                        Tu solicitud ha sido enviada (Estado A). Ya puedes ingresar.
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            <div style="height: 6px; background: linear-gradient(90deg, #621132, var(--escom-blue));"></div>
            
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="bg-light d-inline-block p-3 rounded-circle mb-3">
                        <i class="fas fa-user-shield fa-3x text-primary"></i>
                    </div>
                    <h3 class="fw-bold m-0" style="color: var(--escom-blue);">Iniciar Sesión</h3>
                    <p class="text-muted small">Ingresa tus credenciales institucionales</p>
                </div>

                <form action="<?= URLROOT; ?>/auth/validar" method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Usuario o Correo</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="fas fa-user text-muted"></i></span>
                            <input type="text" name="usuario" class="form-control bg-light border-0" placeholder="Boleta o email" required>
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <label class="form-label small fw-bold text-muted">Contraseña</label>
                            <a href="<?= URLROOT; ?>/auth/recuperar" class="text-decoration-none small fw-bold" style="color: #621132;">¿La olvidaste?</a>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="fas fa-lock text-muted"></i></span>
                            <input type="password" name="password" class="form-control bg-light border-0" placeholder="••••••••" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-ipn w-100 py-3 fw-bold shadow-sm mb-3 mt-3">
                        Ingresar al Sistema <i class="fas fa-sign-in-alt ms-2"></i>
                    </button>
                </form>

                <div class="text-center mt-4">
                    <p class="small text-muted mb-0">¿Eres nuevo en la plataforma?</p>
                    <a href="<?= URLROOT; ?>/auth/registro" class="text-decoration-none fw-bold" style="color: var(--escom-blue);">
                        Crea una cuenta de alumno aquí
                    </a>
                </div>
            </div>
        </div>

        <div class="text-center mt-4 text-muted opacity-50">
            <small>Escuela Superior de Cómputo</small>
        </div>
    </div>
</div>

<?php require APPROOT . '/Views/layout/footer.php'; ?>