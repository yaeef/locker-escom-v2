<?php require APPROOT . '/Views/layout/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <?php require APPROOT . '/Views/layout/sidebar.php'; ?>
        <main class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <div class="pt-3 pb-2 mb-4 border-bottom">
                        <h1 class="h2 fw-bold" style="color: var(--escom-blue);">Gestión de Casilleros</h1>
                    </div>
                    <?php if(isset($_GET['msg'])): ?>
                        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            Acción realizada correctamente.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    <p class="text-muted">Edificio actual: <strong><?= $data['edificio']; ?></strong></p>
                </div>
                <div class="btn-group shadow-sm">
                    <?php for($i=1; $i<=5; $i++): ?>
                        <a href="<?= URLROOT; ?>/admin/casilleros?edificio=<?= $i; ?>" 
                           class="btn btn-<?= ($data['edificio'] == $i) ? 'primary' : 'outline-primary'; ?> btn-sm">
                           E<?= $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div style="height: 6px; background: var(--ipn-maroon);"></div>
                <div class="card-header bg-white py-3">
                    <div class="d-flex gap-3 small">
                        <span><i class="fas fa-square text-success"></i> Disponible</span>
                        <span><i class="fas fa-square text-warning"></i> Reservado (En Trámite)</span>
                        <span><i class="fas fa-square text-danger"></i> Ocupado (Asignado)</span>
                    </div>
                </div>
                <div class="card-body bg-light">
                    <div class="locker-container d-flex flex-nowrap gap-3 pb-3">
                        <?php 
                        $columnas = array_chunk($data['casilleros'], 4); 
                        foreach($columnas as $col): 
                        ?>
                            <div class="locker-column d-flex flex-column gap-2 flex-shrink-0">
                                <?php foreach($col as $locker): 
                                    // Colores
                                    $colorClass = 'success';
                                    if($locker->estatus == 'reservado') $colorClass = 'warning';
                                    if($locker->estatus == 'ocupado') $colorClass = 'danger';
                                ?>
                                    <div class="card text-center border-<?= $colorClass; ?> locker-card shadow-sm">
                                        <div class="card-header py-1 bg-<?= $colorClass; ?> text-white small-text">
                                            NIVEL <?= $locker->nivel; ?>
                                        </div>
                                        <div class="card-body py-2 px-1">
                                            <span class="d-block fw-bold mb-1 text-dark"><?= substr($locker->numero_locker, -3); ?></span>
                                            
                                            <?php if($locker->estatus == 'reservado'): ?>
                                                <button type="button" 
                                                    class="btn btn-warning btn-xs text-white" 
                                                    title="Gestionar Solicitud"
                                                    onclick="gestionarCambio('<?= $locker->id_casillero ?>')"> 
                                                    <i class="fas fa-exchange-alt"></i>
                                                </button>

                                            <?php elseif($locker->estatus == 'ocupado'): ?>
                                                <button type="button" 
                                                    class="btn btn-danger btn-xs" 
                                                    title="Ver Información"
                                                    onclick="verDetalleOcupado('<?= $locker->id_casillero ?>')"> 
                                                    <i class="fas fa-user-check"></i>
                                                </button>

                                            <?php else: ?>
                                                <span class="text-success small" style="font-size: 0.8rem;">
                                                    <i class="fas fa-check-circle"></i> Libre
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modalLocker" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg"> 
                    <div class="modal-content">
                        <div class="modal-header bg-dark text-white">
                            <h5 class="modal-title">Solicitud Locker <span id="numLockerHeader"></span></h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 border-end">
                                    <h6>Datos del Alumno</h6>
                                    <p class="mb-1"><strong>Nombre:</strong> <span id="nombreAlumno"></span></p>
                                    <p class="mb-1"><strong>Carrera:</strong> <span id="carreraAlumno"></span></p>
                                    <p class="mb-1"><strong>Estatura:</strong> <span id="estaturaAlumno"></span> m</p>
                                    <hr>
                                    <div class="d-grid gap-2 mb-3">
                                        <a href="#" id="linkCredencial" target="_blank" class="btn btn-outline-info btn-sm">Ver Credencial</a>
                                        <a href="#" id="linkHorario" target="_blank" class="btn btn-outline-info btn-sm">Ver Horario</a>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6>Reasignar (Opcional)</h6>
                                    <form id="formCambio" action="<?= URLROOT; ?>/admin/cambiar_asignacion" method="POST">
                                        <input type="hidden" name="id_locker_viejo" id="idLockerInput">
                                        <div class="mb-3">
                                            <select name="id_locker_nuevo" id="selectLockersDisponibles" class="form-select form-select-sm"></select>
                                        </div>
                                        <button type="submit" class="btn btn-warning btn-sm w-100 fw-bold">
                                            <i class="fas fa-exchange-alt me-1"></i> Aplicar Cambio
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light d-flex justify-content-between">
                            <button type="button" class="btn btn-danger" onclick="procesarFinal('G')">Rechazar</button>
                            <button type="button" class="btn btn-success" onclick="procesarFinal('B')">Aprobar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modalDetalleOcupado" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">Locker <span id="lblLockerOcupado"></span></h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body text-center">
                            <div class="avatar-placeholder bg-light rounded-circle d-inline-flex align-items-center justify-content-center border mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-user fa-3x text-secondary"></i>
                            </div>
                            <h4 id="lblNombreOcupado" class="fw-bold"></h4>
                            <p class="text-muted"><span id="lblCarreraOcupado"></span></p>
                            
                            <ul class="list-group list-group-flush text-start mt-3 mb-4">
                                <li class="list-group-item d-flex justify-content-between"><strong>Boleta:</strong> <span id="lblBoletaOcupado"></span></li>
                                <li class="list-group-item d-flex justify-content-between"><strong>Correo:</strong> <span id="lblCorreoOcupado"></span></li>
                            </ul>
                            
                            <div class="d-grid">
                                <a href="#" id="btnVerPago" target="_blank" class="btn btn-primary disabled">
                                    <i class="fas fa-file-invoice-dollar me-2"></i> Ver Comprobante de Pago
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form id="formFinal" action="<?= URLROOT; ?>/admin/procesar_estado_final" method="POST">
                <input type="hidden" name="id_casillero" id="idLockerFinal">
                <input type="hidden" name="nuevo_estado" id="nuevoEstadoInput">
            </form>
        </main>
    </div>
</div>

<script>
    // JS para Modal Amarillos
    function gestionarCambio(id_casillero) {
        Promise.all([
            fetch('<?= URLROOT; ?>/admin/info_casillero/' + id_casillero).then(r => r.json()),
            fetch('<?= URLROOT; ?>/admin/get_disponibles/' + '<?= $data['edificio']; ?>').then(r => r.json())
        ]).then(([info, disponibles]) => {
            if (info) {
                // Llenar datos básicos
                document.getElementById('numLockerHeader').innerText = info.numero_locker;
                document.getElementById('nombreAlumno').innerText = info.nombre + ' ' + info.paterno;
                document.getElementById('carreraAlumno').innerText = info.carrera;
                document.getElementById('estaturaAlumno').innerText = info.estatura;
                
                // IDs para formularios
                document.getElementById('idLockerInput').value = id_casillero;
                document.getElementById('idLockerFinal').value = id_casillero;

                // Llenar select
                const select = document.getElementById('selectLockersDisponibles');
                select.innerHTML = '';
                disponibles.forEach(l => {
                    let opt = document.createElement('option');
                    opt.value = l.id_casillero;
                    opt.text = `Locker ${l.numero_locker} (${l.nivel})`;
                    select.appendChild(opt);
                });

                // --- CORRECCIÓN: Usamos ver_pdf para TODO (Cred y Horarios) ---
                const cred = info.url_credencial ? info.url_credencial.split('/').pop() : '';
                const hor = info.url_horario ? info.url_horario.split('/').pop() : '';
                
                document.getElementById('linkCredencial').href = cred ? '<?= URLROOT; ?>/admin/ver_pdf/' + cred : '#';
                document.getElementById('linkHorario').href = hor ? '<?= URLROOT; ?>/admin/ver_pdf/' + hor : '#';

                new bootstrap.Modal(document.getElementById('modalLocker')).show();
            }
        });
    }

    // JS para Modal Rojos
    function verDetalleOcupado(id_casillero) {
        fetch('<?= URLROOT; ?>/admin/info_casillero/' + id_casillero)
            .then(r => r.json())
            .then(info => {
                if(info) {
                    document.getElementById('lblLockerOcupado').innerText = info.numero_locker;
                    document.getElementById('lblNombreOcupado').innerText = info.nombre + ' ' + info.paterno;
                    document.getElementById('lblCarreraOcupado').innerText = info.carrera;
                    document.getElementById('lblBoletaOcupado').innerText = info.boleta;
                    document.getElementById('lblCorreoOcupado').innerText = info.correo;

                    // LÓGICA BOTÓN DE PAGO
                    const btnPago = document.getElementById('btnVerPago');
                    
                    if(info.url_pago) {
                        let nombreArchivo = info.url_pago.split('/').pop();
                        
                        // --- CORRECCIÓN: Usamos ver_pdf también para el pago ---
                        // El controlador 'ver_pdf' detectará que no es 'cred' ni 'hor' y buscará en comprobantes
                        btnPago.href = '<?= URLROOT; ?>/admin/ver_pdf/' + nombreArchivo;
                        
                        btnPago.classList.remove('disabled', 'btn-secondary');
                        btnPago.classList.add('btn-primary');
                        btnPago.innerHTML = '<i class="fas fa-file-invoice-dollar me-2"></i> Ver Comprobante de Pago';
                    } else {
                        btnPago.href = '#';
                        btnPago.classList.add('disabled', 'btn-secondary');
                        btnPago.classList.remove('btn-primary');
                        btnPago.innerHTML = '<i class="fas fa-times me-2"></i> Sin comprobante';
                    }

                    new bootstrap.Modal(document.getElementById('modalDetalleOcupado')).show();
                }
            });
    }

    function procesarFinal(estado) {
        const msg = (estado === 'B') ? "Confirmar aprobación?" : "Rechazar solicitud?";
        if(confirm(msg)) {
            document.getElementById('nuevoEstadoInput').value = estado;
            document.getElementById('formFinal').submit();
        }
    }
</script>

<style>
    .locker-container { overflow-x: auto; padding: 10px; }
    .locker-container::-webkit-scrollbar { height: 8px; }
    .locker-container::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 10px; }
    .locker-column { background: #f8f9fa; padding: 8px; border-radius: 8px; border: 1px solid #e2e8f0; min-width: 110px; }
    .locker-card { width: 100%; border-width: 2px; }
    .small-text { font-size: 0.6rem; font-weight: bold; }
    .btn-xs { padding: 0.1rem 0.3rem; font-size: 0.7rem; }
</style>

<?php require APPROOT . '/Views/layout/footer.php'; ?>