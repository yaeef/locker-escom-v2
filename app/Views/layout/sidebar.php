<nav class="col-md-2 d-none d-md-block bg-white sidebar shadow-sm vh-100 p-3 me-3 rounded">
    <h6 class="text-muted small text-uppercase fw-bold mb-3">Menú Principal</h6>
    
    <?php 
        try {
            $db_menu = new Database();
            $db_menu->query("SELECT COUNT(*) as total FROM usuarios WHERE estado = 'A' AND rol = 'alumno'");
            $docsPendientes = ($res = $db_menu->single()) ? (int)$res->total : 0;

            $db_menu->query("SELECT COUNT(*) as total FROM usuarios WHERE estado = 'F' AND rol = 'alumno'");
            $pagosPendientes = ($res = $db_menu->single()) ? (int)$res->total : 0;
        } catch (Exception $e) { $docsPendientes = $pagosPendientes = 0; }
    ?>

    <ul class="nav flex-column gap-2">
        <?php
            // Función auxiliar para determinar si la opción está activa
            function isActive($current, $target) {
                return ($current == $target) ? 'bg-ipn-guinda text-white fw-bold active' : 'text-dark';
            }
        ?>

        <li class="nav-item">
            <a class="nav-link rounded <?= isActive($data['titulo'], 'Panel de Control'); ?> <?= isActive($data['titulo'], 'Dashboard'); ?>" 
               href="<?= URLROOT; ?>/admin/index">
                <i class="fas fa-chart-line me-2"></i> Dashboard
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link rounded d-flex justify-content-between align-items-center <?= (strpos($data['titulo'], 'Validar Documentos') !== false) ? 'bg-ipn-guinda text-white fw-bold active' : 'text-dark'; ?>" 
               href="<?= URLROOT; ?>/admin/solicitudes">
                <span><i class="fas fa-file-signature me-2"></i> Validar Docs</span>
                <?php if($docsPendientes > 0): ?>
                    <span class="badge bg-danger rounded-pill"><?= $docsPendientes; ?></span>
                <?php endif; ?>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link rounded d-flex justify-content-between align-items-center <?= isActive($data['titulo'], 'Validación de Pagos'); ?>" 
               href="<?= URLROOT; ?>/admin/validacion_pagos">
                <span><i class="fas fa-file-invoice-dollar me-2"></i> Validar Pagos</span>
                <?php if($pagosPendientes > 0): ?>
                    <span class="badge bg-danger rounded-pill"><?= $pagosPendientes; ?></span>
                <?php endif; ?>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link rounded <?= isActive($data['titulo'], 'Mapa de Casilleros'); ?>" href="<?= URLROOT; ?>/admin/casilleros">
                <i class="fas fa-border-all me-2"></i> Mapa de Casilleros
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link rounded <?= isActive($data['titulo'], 'Reportes Estadísticos'); ?>" href="<?= URLROOT; ?>/admin/reportes">
                <i class="fas fa-chart-bar me-2"></i> Reportes
            </a>
        </li>

        <hr class="sidebar-divider my-2">

        <li class="nav-item">
            <a class="nav-link rounded <?= isActive($data['titulo'], 'Gestión de Alumnos'); ?>" href="<?= URLROOT; ?>/admin/gestion_alumnos">
                <i class="fas fa-users-cog me-2"></i> Alumnos
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link rounded <?= isActive($data['titulo'], 'Inventario Lockers'); ?>" href="<?= URLROOT; ?>/admin/gestion_casilleros">
                <i class="fas fa-th-list me-2"></i> Inventario Lockers
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link rounded <?= isActive($data['titulo'], 'Registrar Nuevo Administrador'); ?>" href="<?= URLROOT; ?>/admin/registrar_admin">
                <i class="fas fa-user-plus me-2"></i> Nuevo Admin
            </a>
        </li>
        
        <li class="nav-item mt-4 border-top pt-3">
            <a class="nav-link text-danger fw-bold" href="<?= URLROOT; ?>/auth/logout">
                <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
            </a>
        </li>
    </ul>
</nav>

<style>
    /* Color Guinda Institucional IPN */
.bg-ipn-guinda {
    background-color: #621132 !important; /* El guinda exacto */
}

/* Forzar que el texto y los iconos sean blancos cuando la opción está activa */
.sidebar .nav-link.active,
.sidebar .nav-link.active i {
    color: #ffffff !important;
}

/* Efecto hover sutil */
.sidebar .nav-link:hover:not(.active) {
    background-color: #f8f9fa;
    color: #621132;
}
</style>