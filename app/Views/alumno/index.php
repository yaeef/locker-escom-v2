<?php require APPROOT . '/Views/layout/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <main class="col-lg-8 col-md-10">
            
            <div class="text-center mb-5">
                <h2 class="fw-bold text-primary">Portal del Alumno</h2>
                <p class="text-muted">Gestión de Casilleros Escolares</p>
            </div>

            <?php if(isset($_GET['msg'])): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
                    <?php 
                        if($_GET['msg'] == 'docs_ok') echo "<i class='fas fa-check-circle me-2'></i> Documentos subidos. Espera validación.";
                        if($_GET['msg'] == 'pago_ok') echo "<i class='fas fa-check-circle me-2'></i> Comprobante subido. Espera confirmación.";
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php $estado = $data['usuario']->estado; ?>

            <?php if($estado == 'I'): ?>
                <div class="card shadow border-primary mt-2">
                    <div class="card-header bg-primary text-white text-center py-4">
                        <h2 class="fw-bold mb-0"><i class="fas fa-sync-alt me-2"></i>Renovación de Semestre</h2>
                        <p class="mb-0 text-white-50">Tienes preferencia para conservar tu lugar anterior</p>
                    </div>
                    <div class="card-body p-5 text-center">
                        
                        <div class="mb-4">
                            <h4 class="text-muted">Tu Casillero Reservado:</h4>
                            <div class="d-inline-block bg-warning text-dark border border-2 rounded px-5 py-3 mt-2 shadow-sm position-relative">
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    ¡Acción Requerida!
                                </span>
                                <h1 class="display-3 fw-bold m-0">
                                    <?= $data['usuario']->numero_locker ?? 'N/A'; ?>
                                </h1>
                                <small>Edificio <?= $data['usuario']->edificio; ?> - Nivel <?= $data['usuario']->nivel; ?></small>
                            </div>
                        </div>

                        <p class="lead mb-4">¿Deseas conservar este casillero para el nuevo ciclo escolar?</p>

                        <div class="row justify-content-center gap-3">
                            <div class="col-md-5">
                                <button class="btn btn-outline-danger btn-lg w-100 py-3" onclick="confirmarRenovacion('liberar')">
                                    <i class="fas fa-times me-2"></i> No, Liberar
                                </button>
                                <small class="text-muted d-block mt-1">El casillero quedará disponible para otros.</small>
                            </div>
                            <div class="col-md-5">
                                <button class="btn btn-success btn-lg w-100 py-3 shadow" onclick="confirmarRenovacion('renovar')">
                                    <i class="fas fa-check me-2"></i> Sí, Renovar
                                </button>
                                <small class="text-muted d-block mt-1">Pasarás a realizar el pago.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <form id="formRenovacion" action="<?= URLROOT; ?>/alumno/procesar_decision_renovacion" method="POST">
                    <input type="hidden" name="decision" id="inputDecision">
                </form>

                <script>
                    function confirmarRenovacion(tipo) {
                        let msg = (tipo === 'renovar') 
                            ? "Al aceptar, conservarás tu casillero y deberás subir tu nuevo comprobante de pago." 
                            : "ADVERTENCIA: Tu casillero se liberará inmediatamente y tendrás que concursar si quieres uno después.";
                        
                        if(confirm(msg)) {
                            document.getElementById('inputDecision').value = tipo;
                            document.getElementById('formRenovacion').submit();
                        }
                    }
                </script>

            <?php elseif($estado == 'G'): ?>
                <div class="card shadow border-0 overflow-hidden">
                    <div style="height: 6px; background: var(--ipn-maroon);"></div>
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <div class="icon-circle bg-light text-primary mx-auto mb-3" style="width: 80px; height: 80px; display:flex; align-items:center; justify-content:center; border-radius:50%;">
                                <i class="fas fa-file-upload fa-2x"></i>
                            </div>
                            <h2 class="fw-bold">Nueva Solicitud de Casillero</h2>
                            <p class="text-muted">Actualiza tu documentación para iniciar el proceso de asignación.</p>
                        </div>

                        <form action="<?= URLROOT; ?>/alumno/solicitar_nueva_asignacion" method="POST" enctype="multipart/form-data">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted">CREDENCIAL VIGENTE (PDF)</label>
                                    <input type="file" name="credencial" class="form-control bg-light" accept=".pdf" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted">HORARIO ACTUAL (PDF)</label>
                                    <input type="file" name="horario" class="form-control bg-light" accept=".pdf" required>
                                </div>
                            </div>

                            <div class="alert alert-info border-0 shadow-sm small mt-4">
                                <i class="fas fa-info-circle me-2"></i> El sistema buscará un locker disponible basándose en tu estatura registrada.
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary btn-lg py-3 fw-bold shadow rounded-pill">
                                    <i class="fas fa-search me-2"></i> Iniciar Solicitud
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            <?php elseif($estado == 'A'): ?>
                <div class="card shadow-sm border-0 bg-light text-center p-5">
                    <div class="mb-3 text-primary">
                        <i class="fas fa-file-contract fa-4x"></i>
                    </div>
                    <h3 class="text-dark">Validando Documentación</h3>
                    <p class="lead text-muted">Tus documentos están en revisión.</p>
                    <hr>
                    <p class="small text-muted">
                        Estamos verificando tu credencial y horario.<br>
                        Si todo es correcto, pasarás a firmar el reglamento.
                    </p>
                    <button class="btn btn-outline-secondary mt-2" disabled>
                        <i class="fas fa-clock me-2"></i> Pendiente de revisión
                    </button>
                </div>

            <?php elseif($estado == 'C'): ?>
                <div class="card shadow border-danger">
                    <div class="card-header bg-danger text-white text-center py-3">
                        <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Corrección Requerida</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-warning mb-4">
                            <strong>Atención:</strong> Tus documentos anteriores fueron rechazados. Súbelos nuevamente.
                        </div>

                        <form action="<?= URLROOT; ?>/alumno/subir_documentos" method="POST" enctype="multipart/form-data">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Boleta</label>
                                    <input type="text" name="boleta" class="form-control bg-light" value="<?= $data['usuario']->boleta ?? ''; ?>" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Carrera</label>
                                    <select name="carrera" class="form-select" required>
                                        <option value="Sistemas" <?= ($data['usuario']->carrera ?? '') == 'Sistemas' ? 'selected' : ''; ?>>Sistemas Computacionales</option>
                                        <option value="Inteligencia" <?= ($data['usuario']->carrera ?? '') == 'Inteligencia' ? 'selected' : ''; ?>>Inteligencia Artificial</option>
                                        <option value="Datos" <?= ($data['usuario']->carrera ?? '') == 'Datos' ? 'selected' : ''; ?>>Ciencia de Datos</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Estatura (mts)</label>
                                <input type="number" step="0.01" name="estatura" class="form-control" value="<?= $data['usuario']->estatura ?? ''; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Credencial (PDF)</label>
                                <input type="file" name="credencial" class="form-control" accept=".pdf" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Horario (PDF)</label>
                                <input type="file" name="horario" class="form-control" accept=".pdf" required>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-danger py-2 fw-bold shadow-sm">
                                    <i class="fas fa-sync me-2"></i> Reenviar Documentos
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            <?php elseif($estado == 'B'): ?>
                <div class="card shadow border-primary">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <h4 class="mb-0"><i class="fas fa-balance-scale me-2"></i>Reglamento de Uso</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-info">
                            <i class="fas fa-check-circle me-2"></i> Documentos aprobados. Acepta el reglamento para continuar.
                        </div>

                        <div class="bg-light p-3 border rounded mb-4" style="max-height: 200px; overflow-y: auto;">
                            <h6 class="fw-bold">TÉRMINOS Y CONDICIONES:</h6>
                            <ol class="small text-muted">
                                <li>El casillero es propiedad de la ESCOM.</li>
                                <li>El alumno es responsable de colocar su candado.</li>
                                <li>Prohibido almacenar material ilícito o peligroso.</li>
                                <li>La escuela puede inspeccionar el casillero por seguridad.</li>
                                <li>Debe desalojarse al finalizar el periodo.</li>
                            </ol>
                        </div>

                        <div class="d-flex justify-content-center gap-3">
                            <form action="<?= URLROOT; ?>/alumno/procesar_reglamento" method="POST">
                                <input type="hidden" name="decision" value="G">
                                <button type="submit" class="btn btn-outline-danger px-4" onclick="return confirm('¿Seguro? Perderás el lugar.')">
                                    Rechazar
                                </button>
                            </form>

                            <form action="<?= URLROOT; ?>/alumno/procesar_reglamento" method="POST">
                                <input type="hidden" name="decision" value="E">
                                <button type="submit" class="btn btn-success px-5 fw-bold shadow">
                                    Aceptar <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            <?php elseif($estado == 'E'): ?>
                <div class="card shadow border-success">
                    <div class="card-header bg-success text-white text-center">
                        <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Asignación y Pago</h5>
                    </div>
                    <div class="card-body text-center p-4">
                        <div class="alert alert-success mb-4">
                            ¡Casillero Asignado! Tu número es: 
                            <h2 class="mt-2 fw-bold"><?= $data['usuario']->numero_locker ?? '---'; ?></h2>
                        </div>

                        <div class="text-start bg-light p-4 rounded mb-4 border">
                            <h6 class="fw-bold">Instrucciones:</h6>
                            <ol class="small text-muted mb-0 ps-3">
                                <li>Realiza el pago correspondiente.</li>
                                <li>Sube el comprobante (PDF/Imagen).</li>
                            </ol>
                        </div>

                        <form action="<?= URLROOT; ?>/alumno/subir_pago" method="POST" enctype="multipart/form-data">
                            <div class="mb-4">
                                <label class="form-label fw-bold">Archivo del Comprobante</label>
                                <input type="file" name="archivo_pago" class="form-control" accept=".pdf, .jpg, .png" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100 py-2 fw-bold shadow-sm">
                                <i class="fas fa-upload me-2"></i> Subir Comprobante
                            </button>
                        </form>
                    </div>
                </div>

            <?php elseif($estado == 'F'): ?>
                <div class="card shadow-sm border-0 bg-light text-center p-5">
                    <div class="mb-3 text-secondary">
                        <i class="fas fa-search-dollar fa-4x"></i>
                    </div>
                    <h3 class="text-secondary">Verificando Pago</h3>
                    <p class="lead text-muted">Hemos recibido tu comprobante. Espera la confirmación.</p>
                </div>
                
            <?php elseif($estado == 'H'): ?>
                <div class="card shadow-lg border-0 bg-success text-white text-center">
                    <div class="card-body p-5">
                        <i class="fas fa-check-circle fa-5x mb-4 text-white-50"></i>
                        <h2 class="fw-bold">¡Trámite Completado!</h2>
                        <div class="bg-white text-success rounded p-4 my-4 d-inline-block shadow">
                            <h1 class="display-1 fw-bold m-0"><?= $data['usuario']->numero_locker; ?></h1>
                            <span class="d-block mt-2 h5">Edificio <?= $data['usuario']->edificio; ?> - Nivel <?= $data['usuario']->nivel; ?></span>
                        </div>
                        <div class="mt-4">
                            <a href="<?= URLROOT; ?>/alumno/descargar_acuse" target="_blank" class="btn btn-light btn-lg text-success fw-bold shadow">
                                <i class="fas fa-file-pdf me-2"></i> Descargar Acuse
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="text-end mt-5 text-muted small opacity-50">
                Estado del sistema: [<?= $estado; ?>]
            </div>

        </main>
    </div>
</div>

<?php require APPROOT . '/Views/layout/footer.php'; ?>