<?php require APPROOT . '/Views/layout/header.php'; ?>

<div class="container vh-100 d-flex align-items-center justify-content-center">
    <div class="col-md-5 animate__animated animate__fadeIn">
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            <div style="height: 6px; background: #621132;"></div>
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <h3 class="fw-bold">Nueva Contraseña</h3>
                    <p class="text-muted small">Para la cuenta: <strong><?= $data['correo']; ?></strong></p>
                </div>

                <form action="<?= URLROOT; ?>/auth/actualizar_password" method="POST" id="formReset">
                    <input type="hidden" name="token" value="<?= $data['token']; ?>">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Nueva Contraseña</label>
                        <input type="password" name="password" id="pass1" class="form-control bg-light border-0" required minlength="6">
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">Confirmar Contraseña</label>
                        <input type="password" name="confirm_password" id="pass2" class="form-control bg-light border-0" required>
                    </div>

                    <button type="submit" class="btn text-white w-100 py-3 fw-bold rounded-pill shadow-sm" style="background: #621132;">
                        Actualizar Contraseña
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Validación rápida en el cliente
document.getElementById('formReset').onsubmit = function(e) {
    if(document.getElementById('pass1').value !== document.getElementById('pass2').value) {
        alert("Las contraseñas no coinciden.");
        return false;
    }
};
</script>

<?php require APPROOT . '/Views/layout/footer.php'; ?>