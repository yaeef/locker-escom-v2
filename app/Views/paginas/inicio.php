<div class="parallax-section shadow-sm mb-5" style="background-image: url('<?= URLROOT; ?>/img/hero.jpeg'); height: 600px;">
    <div class="parallax-overlay">
        <div class="container h-100 d-flex align-items-center justify-content-center text-center">
            <div class="text-white px-5 py-5 rounded-4 animate__animated animate__fadeInDown" style="background: rgba(0,0,0,0.3); backdrop-filter: blur(4px);">
                <h1 class="display-1 fw-bold mb-0">ESCOM</h1>
                <div class="mx-auto my-3" style="width: 150px; height: 6px; background: #621132;"></div>
                <p class="display-6 fw-light">Gestión de Casilleros | Locker Escom</p>
                <?php if(!isset($_SESSION['user_id'])): ?>
                    <a href="<?= URLROOT; ?>/auth/login" class="btn btn-light btn-lg rounded-pill fw-bold text-dark mt-4 px-5 shadow-lg border-0">
                        <i class="fas fa-sign-in-alt me-2"></i>Ingresar al Sistema
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="container pb-5">
    
    <div id="mainCarousel" class="carousel slide carousel-fade shadow-sm mb-5" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="1"></button>
        </div>
        <div class="carousel-inner rounded-4">
            <div class="carousel-item active" style="height: 300px;">
                <div class="w-100 h-100 d-flex align-items-center justify-content-center text-center px-4" 
                     style="background: linear-gradient(135deg, #621132 0%, #003366 100%);">
                    <div class="text-white">
                        <h2 class="display-5 fw-bold">Comunicados Oficiales</h2>
                        <p class="lead">Mantente informado sobre los procesos institucionales.</p>
                    </div>
                </div>
            </div>
            <div class="carousel-item" style="height: 300px;">
                <div class="w-100 h-100 d-flex align-items-center justify-content-center text-center px-4" 
                     style="background: linear-gradient(135deg, #003366 0%, #621132 100%);">
                    <div class="text-white">
                        <h2 class="display-5 fw-bold">"La Técnica al Servicio de la Patria"</h2>
                        <p class="lead">Formación de excelencia en Ciencias de la Computación.</p>
                    </div>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>

    <div class="row mb-5">
        <div class="col-12 mb-4 d-flex align-items-center">
            <h3 class="fw-bold m-0"><i class="fas fa-bullhorn me-3 text-danger"></i>Avisos y Becas</h3>
            <div class="flex-grow-1 ms-4 border-bottom opacity-25"></div>
        </div>
        
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4 border-start border-4 border-success">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-9">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="fw-bold m-0">Convocatoria de Becas IPN</h5>
                                <span class="badge bg-success">Oficial</span>
                            </div>
                            <p class="text-muted small">Consulta los requisitos y las fechas de registro para el ciclo escolar actual en el portal de la DAES.</p>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="https://www.ipn.mx/daes/servicios/becas/" target="_blank" class="btn btn-success btn-sm rounded-pill px-3">
                                    <i class="fas fa-external-link-alt me-1"></i> Portal de Becas DAES
                                </a>
                                <a href="https://www.sibec.ipn.mx/" target="_blank" class="btn btn-outline-success btn-sm rounded-pill px-3">
                                    Sistema SIBEC
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 text-center d-none d-md-block">
                            <img src="<?= URLROOT; ?>/img/tbs.png" class="img-fluid opacity-75" style="max-height: 80px;" alt="Servicios IPN">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4 border-start border-4 border-primary">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-2">Servicios Estudiantiles</h5>
                    <p class="text-muted small">Consulta procesos de reinscripción y trámites de gestión escolar en ESCOM.</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="bg-white p-4 rounded-4 shadow-sm border-top border-4" style="border-top-color: #621132 !important;">
                <h6 class="fw-bold text-uppercase small text-muted mb-3 text-center">Calendario Académico</h6>
                <div class="text-center mb-4">
                    <i class="fas fa-calendar-alt fa-3x mb-3 text-maroon" style="color:#621132;"></i>
                    <p class="small text-muted">Fechas de inicio de semestre y días inhábiles.</p>
                    <a href="https://www.ipn.mx/calendario-academico.html" target="_blank" class="btn btn-dark btn-sm w-100 rounded-pill shadow-sm">
                        <i class="fas fa-file-pdf me-2"></i>Ver Calendario Oficial
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-4 d-flex align-items-center">
            <h3 class="fw-bold m-0"><i class="fas fa-palette me-3 text-primary"></i>Actividades Culturales</h3>
            <div class="flex-grow-1 ms-4 border-bottom opacity-25"></div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm card-hover">
                <div class="p-4 text-center">
                    <div class="icon-shape bg-light-primary text-primary mb-3 mx-auto" style="width: 60px; height: 60px; line-height: 60px; border-radius: 50%;">
                        <i class="fas fa-robot fa-2x"></i>
                    </div>
                    <h5 class="fw-bold">Club de Robótica</h5>
                    <p class="small text-muted">Desarrollo de proyectos para competencias internacionales.</p>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm card-hover">
                <div class="p-4 text-center">
                    <div class="icon-shape bg-light-success text-success mb-3 mx-auto" style="width: 60px; height: 60px; line-height: 60px; border-radius: 50%;">
                        <i class="fas fa-chess fa-2x"></i>
                    </div>
                    <h5 class="fw-bold">Taller de Ajedrez</h5>
                    <p class="small text-muted">Práctica y torneos abiertos a la comunidad.</p>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm card-hover">
                <div class="p-4 text-center">
                    <div class="icon-shape bg-light-danger text-danger mb-3 mx-auto" style="width: 60px; height: 60px; line-height: 60px; border-radius: 50%;">
                        <i class="fas fa-music fa-2x"></i>
                    </div>
                    <h5 class="fw-bold">Talleres Culturales</h5>
                    <p class="small text-muted">Música, danza y teatro en la ESCOM.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Estilos del Parallax Hero */
    .parallax-section {
        background-attachment: fixed;
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
        position: relative;
        overflow: hidden;
        margin-top: -24px; /* Ajuste para subir el hero pegado al navbar */
    }

    .parallax-overlay {
        background: rgba(0, 0, 0, 0.4);
        width: 100%;
        height: 100%;
    }

    /* Clases de utilidad */
    .bg-light-primary { background-color: rgba(0, 51, 102, 0.1); }
    .bg-light-success { background-color: rgba(25, 135, 84, 0.1); }
    .bg-light-danger { background-color: rgba(220, 53, 69, 0.1); }
    .card-hover { transition: all 0.3s ease; }
    .card-hover:hover { transform: translateY(-10px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
    .text-maroon { color: #621132; }

    @media (max-width: 992px) {
        .parallax-section {
            background-attachment: scroll;
            height: 450px !important;
        }
    }
</style>