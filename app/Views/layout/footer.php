<footer class="mt-auto py-5 bg-dark text-white">
    <div class="container">
        <div class="row gy-4 align-items-center mb-4">
            <div class="col-lg-4 col-md-12 text-center text-lg-start">
                <div class="d-flex justify-content-center justify-content-lg-start align-items-center gap-4">
                    <a href="http://www.ipn.mx/" target="_blank">
                        <img src="<?= URLROOT; ?>/img/IPN-Logo.png" alt="Logotipo del IPN" style="height: 60px; filter: brightness(0) invert(1);">
                    </a>
                    <a href="http://www.escom.ipn.mx/" target="_blank">
                        <img src="<?= URLROOT; ?>/img/logoESCOMBlanco.png" alt="Logotipo de ESCOM" style="height: 50px;">
                    </a>
                </div>
            </div>
            
            <div class="col-lg-8 col-md-12 text-center text-lg-end">
                <h5 class="fw-bold mb-1">Gestión de Casilleros Escolares</h5>
                <p class="text-white-50 mb-0 small">Escuela Superior de Cómputo - Instituto Politécnico Nacional</p>
            </div>
        </div>

        <hr class="my-4 opacity-25">

        <div class="row gy-4">
            <div class="col-lg-4 col-md-6">
                <h6 class="text-uppercase fw-bold mb-3 small text-white-50">Sobre la Plataforma</h6>
                <p class="text-white-50 small">
                    Sistema centralizado optimizado para la comunidad de ESCOM. Facilita la asignación basada en algoritmos de estatura y validación documental.
                </p>
                <div class="d-flex gap-3">
                    <a href="#" class="text-white-50"><i class="fab fa-facebook fa-lg"></i></a>
                    <a href="#" class="text-white-50"><i class="fab fa-github fa-lg"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 ms-auto">
                <h6 class="text-uppercase fw-bold mb-3 small text-white-50">Enlaces Útiles</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="<?= URLROOT . '/paginas/inicio'; ?>" class="text-white-50 text-decoration-none hover-white">Inicio</a></li>
                    <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none hover-white">Reglamento de Uso</a></li>
                    <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none hover-white">Soporte Técnico</a></li>
                </ul>
            </div>

            <div class="col-lg-4 col-md-6">
                <h6 class="text-uppercase fw-bold mb-3 small text-white-50">Ubicación y Contacto</h6>
                <p class="text-white-50 small mb-1">
                    <i class="fas fa-map-marker-alt me-2"></i> Av. Juan de Dios Bátiz esq. Miguel Othón de Mendizábal.
                </p>
                <p class="text-white-50 small">
                    <i class="fas fa-envelope me-2"></i> soporte_lockers@ipn.mx
                </p>
            </div>
        </div>
        
        <hr class="my-4 opacity-25">
        
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <small class="text-white-50">&copy; <?= date('Y'); ?> Gestión de Casilleros | ESCOM IPN. Todos los derechos reservados.</small>
            </div>
            <div class="col-md-6 text-center text-md-end mt-3 mt-md-0">
                <span class="badge rounded-pill border border-secondary text-white-50 fw-light" style="font-size: 0.7rem;">v2.1 | Dev by EQUIPO E | FEPI</span>
            </div>
        </div>
    </div>
</footer>

<style>
    .hover-white:hover { color: white !important; transition: 0.3s; }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Ingeniería de UX: Auto-ocultar alertas para mantener la interfaz limpia
    document.addEventListener("DOMContentLoaded", function() {
        const alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(alert => {
            setTimeout(() => {
                const bAlert = bootstrap.Alert.getOrCreateInstance(alert);
                if(bAlert) bAlert.close();
            }, 5000);
        });
    });
</script>
</body>
</html>