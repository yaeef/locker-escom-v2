<?php require APPROOT . '/Views/layout/header.php'; ?>
<div class="container-fluid py-4">
    <div class="row">
        <?php require APPROOT . '/Views/layout/sidebar.php'; ?>
        
        <main class="col-md-9 px-md-4">
            <?php if(isset($_GET['msg']) && $_GET['msg'] == 'eliminado'): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-4 mb-4" role="alert">
                    <i class="fas fa-user-slash me-2"></i> El alumno y sus datos relacionados han sido eliminados.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                <h2 class="fw-bold mb-0" style="color: var(--escom-blue);">Gestión de Alumnos</h2>
            </div>

            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div style="height: 6px; background: var(--ipn-maroon);"></div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-uppercase">
                            <tr>
                                <th class="ps-4">Boleta</th>
                                <th>Nombre Completo</th>
                                <th>Carrera</th>
                                <th>Contacto</th>
                                <th class="text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['alumnos'] as $a): ?>
                            <tr>
                                <td class="ps-4 fw-bold"><?= $a->boleta; ?></td>
                                <td>
                                    <?= $a->nombre . ' ' . $a->paterno . ' ' . $a->materno; ?><br>
                                    <small class="text-muted"><?= $a->correo; ?></small>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= $a->carrera; ?></span></td>
                                <td><small><i class="fas fa-phone me-1"></i><?= $a->telefono; ?></small></td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-light rounded-circle shadow-sm me-2" 
                                            onclick='editar(<?= json_encode($a); ?>)'>
                                        <i class="fas fa-edit text-primary"></i>
                                    </button>
                                    <a href="<?= URLROOT; ?>/admin/eliminar_alumno/<?= $a->id_usuario; ?>" 
                                       class="btn btn-sm btn-light rounded-circle shadow-sm" 
                                       onclick="return confirm('¿Eliminar definitivamente a este alumno? Se perderá su historial.');">
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

<div class="modal fade" id="modalAlumno" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form class="modal-content border-0 rounded-4 shadow" action="<?= URLROOT; ?>/admin/guardar_alumno" method="POST">
            <div class="modal-header bg-light border-0">
                <h5 class="fw-bold mb-0" id="modalTitle">Editar Datos del Alumno</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="id_usuario" id="inputId">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="small fw-bold text-muted">Nombre(s)</label>
                        <input type="text" name="nombre" id="inputNombre" class="form-control bg-light border-0" required>
                    </div>
                    <div class="col-md-4">
                        <label class="small fw-bold text-muted">A. Paterno</label>
                        <input type="text" name="paterno" id="inputPaterno" class="form-control bg-light border-0" required>
                    </div>
                    <div class="col-md-4">
                        <label class="small fw-bold text-muted">A. Materno</label>
                        <input type="text" name="materno" id="inputMaterno" class="form-control bg-light border-0">
                    </div>

                    <div class="col-md-6">
                        <label class="small fw-bold text-muted">Boleta</label>
                        <input type="text" name="boleta" id="inputBoleta" class="form-control bg-light border-0" required>
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-bold text-muted">Carrera</label>
                        <select name="carrera" id="inputCarrera" class="form-select bg-light border-0">
                            <option value="ISC">Ingeniería en Sistemas Computacionales</option>
                            <option value="LCD">Licenciatura en Ciencia de Datos</option>
                            <option value="LIA">Licenciatura en Inteligencia Artificial</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="small fw-bold text-muted">Estatura (m)</label>
                        <input type="number" step="0.01" name="estatura" id="inputEstatura" class="form-control bg-light border-0" required>
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-bold text-muted">Teléfono</label>
                        <input type="tel" name="telefono" id="inputTelefono" class="form-control bg-light border-0" required>
                    </div>

                    <div class="col-12">
                        <label class="small fw-bold text-muted">Correo Institucional</label>
                        <input type="email" name="correo" id="inputCorreo" class="form-control bg-light border-0" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4">
                <button type="submit" class="btn btn-ipn w-100 py-2 rounded-pill fw-bold">Actualizar Información</button>
            </div>
        </form>
    </div>
</div>

<script>
let modal;
document.addEventListener('DOMContentLoaded', () => modal = new bootstrap.Modal(document.getElementById('modalAlumno')));

function editar(a) {
    document.getElementById('inputId').value = a.id_usuario;
    document.getElementById('inputNombre').value = a.nombre;
    document.getElementById('inputPaterno').value = a.paterno;
    document.getElementById('inputMaterno').value = a.materno;
    document.getElementById('inputBoleta').value = a.boleta;
    document.getElementById('inputCorreo').value = a.correo;
    document.getElementById('inputCarrera').value = a.carrera;
    document.getElementById('inputEstatura').value = a.estatura;
    document.getElementById('inputTelefono').value = a.telefono;
    modal.show();
}
</script>
<?php require APPROOT . '/Views/layout/footer.php'; ?>