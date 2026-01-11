<?php require APPROOT . '/Views/layout/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <?php require APPROOT . '/Views/layout/sidebar.php'; ?>
        <main class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="pt-3 pb-2 mb-4 border-bottom">
                    <h1 class="h2 fw-bold" style="color: var(--escom-blue);">Validación de Pagos</h1>
                </div>
                <span class="text-muted small">Estado F: Pendientes de confirmación</span>
            </div>

            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div style="height: 6px; background: var(--ipn-maroon);"></div>
                <div class="card-header bg-white fw-bold py-3">
                    <i class="fas fa-clock me-2 text-warning"></i> Solicitudes Pendientes
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Alumno</th>
                                    <th>Carrera</th>
                                    <th>Locker Asignado</th>
                                    <th>Comprobante</th>
                                    <th class="text-end pe-4">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($data['pendientes'])): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                                            <p class="mb-0">No hay pagos pendientes de revisión.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach($data['pendientes'] as $p): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold"><?= $p->nombre . ' ' . $p->paterno; ?></div>
                                            <small class="text-muted badge bg-light text-dark border"><?= $p->boleta; ?></small>
                                        </td>
                                        <td><?= $p->carrera; ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px;">
                                                    <small><?= substr($p->numero_locker, 0, 1); ?></small>
                                                </div>
                                                <span class="fw-bold"><?= $p->numero_locker; ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if($p->url_pago): ?>
                                                <a href="<?= URLROOT; ?>/admin/ver_archivo/<?= basename($p->url_pago); ?>" target="_blank" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye me-1"></i> Ver Archivo
                                                </a>
                                            <?php else: ?>
                                                <span class="badge bg-danger">No subido</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="d-flex justify-content-end gap-2">
                                                <form action="<?= URLROOT; ?>/admin/rechazar_pago/<?= $p->id_usuario; ?>" method="POST" onsubmit="return confirm('¿Rechazar pago y devolver al alumno?');">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Rechazar">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>

                                                <form action="<?= URLROOT; ?>/admin/validar_pago/<?= $p->id_usuario; ?>" method="POST" onsubmit="return confirm('¿Validar pago y asignar locker?');">
                                                    <button type="submit" class="btn btn-sm btn-success text-white fw-bold" title="Validar">
                                                        <i class="fas fa-check me-1"></i> Aceptar
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php require APPROOT . '/Views/layout/footer.php'; ?>