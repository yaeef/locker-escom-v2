<?php require APPROOT . '/Views/layout/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <div class="text-center mb-5 animate__animated animate__fadeIn">
                <h2 class="fw-bold" style="color: var(--escom-blue);">Solicitud de Registro</h2>
                <p class="text-muted">Completa tus datos para iniciar el proceso de asignación de casillero (Estado A)</p>
            </div>

            <div class="card shadow-lg border-0 rounded-4 overflow-hidden animate__animated animate__fadeInUp">
                <div style="height: 6px; background: linear-gradient(90deg, var(--ipn-maroon), var(--escom-blue));"></div>
                
                <div class="card-body p-4 p-md-5">
                    <form action="<?= URLROOT; ?>/auth/registrar" method="POST" enctype="multipart/form-data">
                        
                        <h5 class="fw-bold mb-4 border-bottom pb-2"><i class="fas fa-user-edit me-2 text-primary"></i>Datos Personales</h5>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Nombre(s)</label>
                                <input type="text" name="nombre" class="form-control bg-light border-0" placeholder="Ej. Juan" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Apellido Paterno</label>
                                <input type="text" name="paterno" class="form-control bg-light border-0" placeholder="Ej. Pérez" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Apellido Materno</label>
                                <input type="text" name="materno" class="form-control bg-light border-0" placeholder="Ej. García">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Username / Apodo</label>
                                <input type="text" name="username" class="form-control bg-light border-0" placeholder="Nombre de usuario" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Correo Institucional</label>
                                <input type="email" name="correo" class="form-control bg-light border-0" placeholder="alumno@ipn.mx" required>
                            </div>
                        </div>

                        <h5 class="fw-bold mb-4 border-bottom pb-2"><i class="fas fa-graduation-cap me-2 text-primary"></i>Información Escolar</h5>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Boleta</label>
                                <input type="text" name="boleta" class="form-control bg-light border-0" placeholder="202XXXXXXX" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label small fw-bold">Carrera</label>
                                <select name="carrera" class="form-select bg-light border-0" required>
                                    <option value="" selected disabled>Selecciona tu carrera...</option>
                                    <option value="ISC">Ingeniería en Sistemas Computacionales (ISC)</option>
                                    <option value="LCD">Licenciatura en Ciencia de Datos (LCD)</option>
                                    <option value="LIA">Licenciatura en Inteligencia Artificial (LIA)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Estatura (metros)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="estatura" class="form-control bg-light border-0" placeholder="Ej. 1.75" required>
                                    <span class="input-group-text bg-light border-0">m</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Teléfono</label>
                                <input type="text" name="telefono" class="form-control bg-light border-0" placeholder="55XXXXXXXX" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Contraseña</label>
                                <input type="password" name="password" class="form-control bg-light border-0" placeholder="********" required>
                            </div>
                        </div>

                        <h5 class="fw-bold mb-4 border-bottom pb-2"><i class="fas fa-file-pdf me-2 text-primary"></i>Documentación digital</h5>
                        
                        <div class="row g-3 mb-5">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Credencial Vigente (PDF)</label>
                                <input type="file" name="pdf_credencial" class="form-control bg-light border-0" accept=".pdf" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Horario Actual (PDF)</label>
                                <input type="file" name="pdf_horario" class="form-control bg-light border-0" accept=".pdf" required>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-ipn btn-lg px-5 shadow">
                                <i class="fas fa-paper-plane me-2"></i> Finalizar Solicitud de Registro
                            </button>
                            <p class="mt-3 small text-muted">¿Ya tienes cuenta? <a href="<?= URLROOT; ?>/auth/login" class="text-decoration-none fw-bold">Inicia sesión</a></p>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/Views/layout/footer.php'; ?>