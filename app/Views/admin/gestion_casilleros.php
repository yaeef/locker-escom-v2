<?php require APPROOT . '/Views/layout/header.php'; ?>
<div class="container-fluid py-4">
    <div class="row">
        <?php require APPROOT . '/Views/layout/sidebar.php'; ?>

        <main class="col-md-9 px-md-4">
            
            <div class="mt-2">
                <?php if(isset($_GET['msg'])): ?>
                    <?php if($_GET['msg'] == 'eliminado'): ?>
                        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-4" role="alert">
                            <i class="fas fa-trash-alt me-2"></i> Casillero y su historial eliminados correctamente.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php elseif($_GET['msg'] == 'success'): ?>
                        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-4" role="alert">
                            <i class="fas fa-check-circle me-2"></i> Datos del casillero guardados correctamente.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                <h2 class="fw-bold mb-0" style="color: var(--escom-blue);">Administración de Casilleros</h2>
                <button class="btn btn-ipn px-5 py-2 fw-bold rounded-pill shadow" onclick="abrirModal()">
                    <i class="fas fa-plus me-2"></i> Nuevo Locker
                </button>
            </div>

            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div style="height: 6px; background: var(--ipn-maroon);"></div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-uppercase">
                            <tr>
                                <th class="ps-4">Identificador</th>
                                <th>Ubicación</th>
                                <th>Estado</th>
                                <th class="text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['casilleros'] as $c): ?>
                            <tr>
                                <td class="ps-4"><span class="fw-bold fs-5">#<?= $c->numero_locker; ?></span></td>
                                <td>Edificio <?= $c->edificio; ?> <small class="text-muted">(Nivel <?= $c->nivel; ?>)</small></td>
                                <td>
                                    <?php 
                                        $colors = ['disponible'=>'success', 'ocupado'=>'danger', 'reservado'=>'warning', 'mantenimiento'=>'dark'];
                                        $color = $colors[$c->estatus] ?? 'secondary';
                                    ?>
                                    <span class="badge rounded-pill bg-<?= $color; ?> px-3"><?= ucfirst($c->estatus); ?></span>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-light rounded-circle shadow-sm me-2" onclick='editar(<?= json_encode($c); ?>)'>
                                        <i class="fas fa-edit text-primary"></i>
                                    </button>
                                    <a href="<?= URLROOT; ?>/admin/eliminar_casillero/<?= $c->id_casillero; ?>" 
                                       class="btn btn-sm btn-light rounded-circle shadow-sm" 
                                       onclick="return confirm('¿Eliminar definitivamente este locker?');">
                                        <i class="fas fa-trash text-danger"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<div class="modal fade" id="modalLocker" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border-0 rounded-4 shadow" action="<?= URLROOT; ?>/admin/guardar_casillero" method="POST">
            <div class="modal-header border-0 bg-light">
                <h5 class="fw-bold mb-0" id="modalTitle">Gestión de Casillero</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="id_casillero" id="inputId">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="small fw-bold">Número de Locker</label>
                        <input type="text" name="numero_locker" id="inputNum" class="form-control bg-light border-0" required>
                    </div>
                    <div class="col-6">
                        <label class="small fw-bold">Edificio</label>
                        <select name="edificio" id="inputEdi" class="form-select bg-light border-0">
                            <?php for($i=1; $i<=5; $i++) echo "<option value='$i'>$i</option>"; ?>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="small fw-bold">Nivel</label>
                        <select name="nivel" id="inputNiv" class="form-select bg-light border-0">
                            <?php for($i=1; $i<=4; $i++) echo "<option value='$i'>$i</option>"; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="small fw-bold">Estado</label>
                        <select name="estatus" id="inputStatus" class="form-select bg-light border-0">
                            <option value="disponible">Disponible</option>
                            <option value="ocupado">Ocupado</option>
                            <option value="reservado">Reservado</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="submit" class="btn btn-ipn w-100 py-2 rounded-pill fw-bold">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<script>
let myModal;
document.addEventListener('DOMContentLoaded', function() {
    // Inicialización correcta del modal de Bootstrap 5
    myModal = new bootstrap.Modal(document.getElementById('modalLocker'));
});

function abrirModal() {
    document.getElementById('inputId').value = '';
    document.getElementById('modalTitle').innerText = 'Nuevo Casillero';
    document.getElementById('inputNum').value = '';
    document.getElementById('inputStatus').value = 'disponible';
    myModal.show(); // Llamamos al objeto inicializado
}

function editar(locker) {
    document.getElementById('inputId').value = locker.id_casillero;
    document.getElementById('modalTitle').innerText = 'Editar Casillero #' + locker.numero_locker;
    document.getElementById('inputNum').value = locker.numero_locker;
    document.getElementById('inputEdi').value = locker.edificio;
    document.getElementById('inputNiv').value = locker.nivel;
    document.getElementById('inputStatus').value = locker.estatus;
    myModal.show();
}
</script>
<?php require APPROOT . '/Views/layout/footer.php'; ?>