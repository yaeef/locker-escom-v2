<?php require APPROOT . '/Views/layout/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php require APPROOT . '/Views/layout/sidebar.php'; ?>

        <main class="col-md-9 px-4 py-4">
            
            <div class="pt-3 pb-2 mb-4 border-bottom">
                <h1 class="h2 fw-bold" style="color: var(--escom-blue);">Registro de Admin</h1>
            </div>

            <div class="row g-0">
                <div class="col-12">
                    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                        <div style="height: 6px; background: var(--ipn-maroon);"></div>
                        
                        <div class="card-body p-4">
                            <h4 class="fw-bold mb-4">Datos del Nuevo Administrador</h4>
                            
                            <form action="<?= URLROOT; ?>/admin/guardar_admin" method="POST">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">Username</label>
                                        <input type="text" name="username" class="form-control bg-light border-0 py-2" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">Correo Electrónico</label>
                                        <input type="email" name="correo" class="form-control bg-light border-0 py-2" required>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted">Nombre(s)</label>
                                        <input type="text" name="nombre" class="form-control bg-light border-0 py-2" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted">A. Paterno</label>
                                        <input type="text" name="paterno" class="form-control bg-light border-0 py-2" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted">A. Materno</label>
                                        <input type="text" name="materno" class="form-control bg-light border-0 py-2">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-muted">Contraseña</label>
                                        <input type="password" name="password" class="form-control bg-light border-0 py-2" required>
                                    </div>

                                    <div class="col-12 mt-4 text-end">
                                        <button type="submit" class="btn btn-ipn px-5 py-2 fw-bold rounded-pill shadow">
                                            <i class="fas fa-save me-2"></i> Registrar
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php require APPROOT . '/Views/layout/footer.php'; ?>