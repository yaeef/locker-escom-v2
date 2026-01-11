<?php require APPROOT . '/Views/layout/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <?php require APPROOT . '/Views/layout/sidebar.php'; ?>
        
        <main class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <div class="pt-3 pb-2 mb-4 border-bottom">
                        <h1 class="h2 fw-bold" style="color: var(--escom-blue);">Validación de Documentos</h1>
                    </div>
                    <p class="text-muted">Revisión de credenciales y horarios para casilleros reservados (Amarillos).</p>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div style="height: 6px; background: var(--ipn-maroon);"></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Casillero</th>
                                    <th>Alumno / Boleta</th>
                                    <th>Carrera</th>
                                    <th>Documentos</th>
                                    <th class="text-end pe-4">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($data['solicitudes'])): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="fas fa-check-double fa-3x mb-3"></i>
                                            <p>No hay solicitudes pendientes de revisión.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach($data['solicitudes'] as $sol): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-warning text-dark rounded px-2 py-1 me-2 fw-bold small">
                                                    <?= substr($sol->numero_locker, 0, 1); ?>
                                                </div>
                                                <div>
                                                    <span class="fw-bold">Locker <?= $sol->numero_locker; ?></span>
                                                    <div class="small text-muted">Nivel <?= $sol->nivel; ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-bold"><?= $sol->nombre . ' ' . $sol->paterno; ?></div>
                                            <span class="badge bg-light text-dark border"><?= $sol->boleta; ?></span>
                                        </td>
                                        <td><small><?= $sol->carrera; ?></small></td>
                                        <td>
                                            <span class="badge bg-info"><i class="fas fa-id-card"></i> Credencial</span>
                                            <span class="badge bg-info"><i class="fas fa-calendar-alt"></i> Horario</span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <button class="btn btn-primary btn-sm px-3 shadow-sm" 
                                                    onclick="gestionarSolicitud('<?= $sol->id_casillero; ?>', '<?= $sol->edificio; ?>')">
                                                <i class="fas fa-tasks me-2"></i> Gestionar
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modalGestion" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg"> 
                    <div class="modal-content">
                        <div class="modal-header bg-dark text-white">
                            <h5 class="modal-title">Revisión de Solicitud <span id="lblLockerHeader" class="text-warning fw-bold"></span></h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 border-end">
                                    <h6 class="fw-bold text-primary"><i class="fas fa-user-graduate me-2"></i>Datos del Alumno</h6>
                                    <p class="mb-1"><strong>Nombre:</strong> <span id="lblNombre"></span></p>
                                    <p class="mb-1"><strong>Carrera:</strong> <span id="lblCarrera"></span></p>
                                    <p class="mb-3"><strong>Estatura:</strong> <span id="lblEstatura"></span> m</p>
                                    
                                    <hr>
                                    
                                    <h6 class="fw-bold text-primary"><i class="fas fa-folder-open me-2"></i>Documentación</h6>
                                    <div class="d-grid gap-2">
                                        <a href="#" id="btnCredencial" target="_blank" class="btn btn-outline-dark btn-sm text-start">
                                            <i class="fas fa-id-card me-2"></i> Ver Credencial
                                        </a>
                                        <a href="#" id="btnHorario" target="_blank" class="btn btn-outline-dark btn-sm text-start">
                                            <i class="fas fa-calendar-alt me-2"></i> Ver Horario
                                        </a>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="alert alert-warning small mb-3">
                                        <i class="fas fa-info-circle me-1"></i> Si el casillero no es adecuado por la estatura, puedes reasignarlo aquí.
                                    </div>

                                    <h6 class="fw-bold"><i class="fas fa-exchange-alt me-2"></i>Reasignar Casillero</h6>
                                    <form id="formCambio" action="<?= URLROOT; ?>/admin/cambiar_asignacion" method="POST">
                                        <input type="hidden" name="id_locker_viejo" id="inputLockerViejo">
                                        <input type="hidden" name="redirect_to" value="solicitudes"> 

                                        <div class="mb-3">
                                            <label class="small text-muted">Disponibles en Edificio <span id="lblEdificioActual"></span>:</label>
                                            <select name="id_locker_nuevo" id="selectLockers" class="form-select form-select-sm"></select>
                                        </div>
                                        <button type="submit" class="btn btn-warning btn-sm w-100 fw-bold">
                                            Aplicar Cambio de Locker
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal-footer bg-light d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-danger" onclick="procesar('G')">
                                <i class="fas fa-times me-2"></i>Rechazar y Liberar
                            </button>
                            
                            <button type="button" class="btn btn-success px-4 fw-bold" onclick="procesar('B')">
                                <i class="fas fa-check me-2"></i>Aprobar Documentos
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <form id="formProcesar" action="<?= URLROOT; ?>/admin/procesar_estado_final" method="POST">
                <input type="hidden" name="id_casillero" id="inputIdCasilleroAction">
                <input type="hidden" name="nuevo_estado" id="inputNuevoEstado">
                <input type="hidden" name="redirect_to" value="solicitudes">
            </form>

        </main>
    </div>
</div>

<script>
    // Función Maestra: Carga datos del alumno y lockers disponibles
    function gestionarSolicitud(idCasillero, edificio) {
        
        // 1. Cargar Info del Alumno (AJAX)
        fetch('<?= URLROOT; ?>/admin/info_casillero/' + idCasillero)
            .then(res => res.json())
            .then(data => {
                if(!data) return alert("Error al cargar datos");

                // Llenar textos
                document.getElementById('lblLockerHeader').innerText = data.numero_locker;
                document.getElementById('lblNombre').innerText = data.nombre + ' ' + data.paterno;
                document.getElementById('lblCarrera').innerText = data.carrera;
                document.getElementById('lblEstatura').innerText = data.estatura;
                document.getElementById('lblEdificioActual').innerText = edificio;

                // Llenar IDs para formularios
                document.getElementById('inputLockerViejo').value = idCasillero;
                document.getElementById('inputIdCasilleroAction').value = idCasillero;

                // Configurar Botones PDF
                // Usamos basename en JS para limpiar ruta si viene sucia
                const cred = data.url_credencial ? data.url_credencial.split('/').pop() : '';
                const hor = data.url_horario ? data.url_horario.split('/').pop() : '';

                // Usamos 'ver_pdf' porque estos documentos NO están en la carpeta de pagos
document.getElementById('btnCredencial').href = cred ? '<?= URLROOT; ?>/admin/ver_pdf/' + cred : '#';
document.getElementById('btnHorario').href = hor ? '<?= URLROOT; ?>/admin/ver_pdf/' + hor : '#';
                // 2. Cargar Lockers Disponibles en el mismo edificio (AJAX)
                return fetch('<?= URLROOT; ?>/admin/get_disponibles/' + edificio);
            })
            .then(res => res.json())
            .then(disponibles => {
                const select = document.getElementById('selectLockers');
                select.innerHTML = ''; // Limpiar
                
                if(disponibles.length === 0) {
                    const opt = document.createElement('option');
                    opt.text = "No hay otros lockers libres en este edificio";
                    select.appendChild(opt);
                } else {
                    disponibles.forEach(l => {
                        const opt = document.createElement('option');
                        opt.value = l.id_casillero;
                        opt.text = `Locker ${l.numero_locker} (Nivel ${l.nivel})`;
                        select.appendChild(opt);
                    });
                }

                // 3. Mostrar Modal
                new bootstrap.Modal(document.getElementById('modalGestion')).show();
            })
            .catch(err => console.error(err));
    }

    function procesar(estado) {
        const msg = (estado === 'B') 
            ? "¿Documentos correctos? El alumno pasará a la etapa de PAGO." 
            : "¿Rechazar solicitud? El casillero quedará LIBRE nuevamente.";
        
        if(confirm(msg)) {
            document.getElementById('inputNuevoEstado').value = estado;
            document.getElementById('formProcesar').submit();
        }
    }
</script>

<?php require APPROOT . '/Views/layout/footer.php'; ?>