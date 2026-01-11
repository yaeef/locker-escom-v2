<?php require APPROOT . '/Views/layout/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <?php require APPROOT . '/Views/layout/sidebar.php'; ?>

        <main class="col-md-9 px-md-4">
            <div class="pt-3 pb-2 mb-4 border-bottom">
                <h1 class="h2 fw-bold" style="color: var(--escom-blue);">Resumen de Gestión</h1>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm bg-info text-white rounded-4">
                        <div class="card-body p-4">
                            <h6 class="card-title fw-bold opacity-75">En Revisión (A)</h6>
                            <h2 class="display-6 fw-bold mb-0"><?= $data['stats']['A']; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm bg-warning text-dark rounded-4">
                        <div class="card-body p-4">
                            <h6 class="card-title fw-bold opacity-75">Esperando Locker (D)</h6>
                            <h2 class="display-6 fw-bold mb-0"><?= $data['stats']['D']; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm bg-success text-white rounded-4">
                        <div class="card-body p-4">
                            <h6 class="card-title fw-bold opacity-75">Casilleros Activos (H)</h6>
                            <h2 class="display-6 fw-bold mb-0"><?= $data['stats']['H']; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm bg-danger text-white rounded-4">
                        <div class="card-body p-4">
                            <h6 class="card-title fw-bold opacity-75">Rechazados (C)</h6>
                            <h2 class="display-6 fw-bold mb-0"><?= $data['stats']['C']; ?></h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                        <div style="height: 6px; background: var(--ipn-maroon);"></div>
                        <div class="card-header bg-white fw-bold py-3">
                            <i class="fas fa-list-ol me-2 text-primary"></i>Próximos en Cola de Asignación
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">Boleta</th>
                                            <th>Alumno</th>
                                            <th>Estatura</th>
                                            <th class="text-end pe-4">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($data['cola_espera'] as $alumno): ?>
                                        <tr>
                                            <td class="ps-4 fw-bold"><?= $alumno->boleta; ?></td>
                                            <td><?= $alumno->nombre . ' ' . $alumno->paterno; ?></td>
                                            <td><?= $alumno->estatura; ?> m</td>
                                            <td class="text-end pe-4">
                                                <a href="<?= URLROOT; ?>/admin/casilleros" class="btn btn-sm btn-outline-primary rounded-pill px-3">Asignar</a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php if(empty($data['cola_espera'])): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-check-circle text-success fa-2x mb-2"></i>
                                    <p class="text-muted small">No hay alumnos esperando asignación.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-header bg-danger text-white py-3">
                            <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Zona de Administración de Ciclo</h5>
                        </div>
                        <div class="card-body p-4">
                            <h5 class="fw-bold">Transición de Semestre</h5>
                            <p class="text-muted small">
                                Estas acciones son irreversibles. Selecciona la estrategia para el nuevo ciclo escolar.
                            </p>
                            
                            <div class="d-flex gap-3 mt-3">
                                <button class="btn btn-outline-danger rounded-pill px-4 fw-bold" onclick="confirmarReset('G')">
                                    <i class="fas fa-power-off me-2"></i> Liberación Total (Estado G)
                                </button>
                                
                                <button class="btn btn-outline-warning text-dark rounded-pill px-4 fw-bold" onclick="confirmarReset('I')">
                                    <i class="fas fa-sync-alt me-2"></i> Iniciar Renovaciones (Estado I)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form id="formReset" action="<?= URLROOT; ?>/admin/reset_semestre" method="POST">
                <input type="hidden" name="tipo_reset" id="inputTipoReset">
            </form>

        </main>
    </div>
</div>

<script>
    function confirmarReset(tipo) {
        let mensaje = "";
        const advertencia = "ADVERTENCIA DE SEGURIDAD:\n";

        if(tipo === 'G') {
            mensaje = advertencia + 
                      "1. Se BORRARÁN todas las asignaciones.\n" +
                      "2. Todos los casilleros quedarán LIBRES.\n" +
                      "3. Los alumnos deberán concursar de nuevo.\n\n" + 
                      "¿Confirmar Liberación Total?";
        } 
        else if (tipo === 'I') {
            mensaje = advertencia + 
                      "1. Los alumnos CONSERVARÁN su casillero actual.\n" +
                      "2. Se les pedirá subir un NUEVO comprobante de pago.\n" +
                      "3. Los casilleros pasarán a 'Reservado' temporalmente.\n\n" + 
                      "¿Confirmar Inicio de Renovaciones?";
        }

        if(confirm(mensaje)) {
            if(confirm("¿Estás 100% seguro? Esta acción afecta a TODOS los usuarios del sistema.")) {
                document.getElementById('inputTipoReset').value = tipo;
                document.getElementById('formReset').submit();
            }
        }
    }
</script>

<?php if(isset($_GET['msg'])): ?>
    <script>
        <?php if($_GET['msg'] == 'reset_g_ok'): ?>
            alert('Semestre reiniciado: Todos los casilleros han sido liberados.');
        <?php elseif($_GET['msg'] == 'reset_i_ok'): ?>
            alert('Proceso de Renovación iniciado: Los alumnos ahora deben subir su nuevo pago.');
        <?php endif; ?>
    </script>
<?php endif; ?>

<?php require APPROOT . '/Views/layout/footer.php'; ?>