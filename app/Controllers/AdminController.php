<?php
class AdminController {
    private $db;

    public function __construct() {
        //Seguridad: Solo admin
        if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
            header('Location: ' . URLROOT . '/auth/login');
            exit();
        }
        $this->db = new Database();
    }

    public function index() {
        //Estadísticas para los gráficos/tarjetas
        $this->db->query("SELECT estado, COUNT(*) as total FROM usuarios WHERE rol = 'alumno' GROUP BY estado");
        $statsResult = $this->db->resultSet();
        
        $stats = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0, 'F' => 0, 'G' => 0, 'H' => 0];
        foreach($statsResult as $row) {
            $stats[$row->estado] = $row->total;
        }

        //Alumnos en espera de asignación (Estado D)
        $this->db->query("SELECT u.nombre, u.paterno, d.boleta, d.estatura 
                        FROM usuarios u 
                        JOIN alumnos_detalles d ON u.id_usuario = d.id_usuario 
                        WHERE u.estado = 'D' ORDER BY u.id_usuario ASC LIMIT 5");
        $esperaAsignacion = $this->db->resultSet();

        $data = [
            'titulo' => 'Panel de Control',
            'stats' => $stats,
            'cola_espera' => $esperaAsignacion
        ];

        require_once APPROOT . '/Views/admin/dashboard.php';
    }

    public function casilleros() {
        $edificioActual = isset($_GET['edificio']) ? $_GET['edificio'] : 1;

        //Obtener casilleros del edificio
        $this->db->query("SELECT * FROM casilleros WHERE edificio = :ed ORDER BY numero_locker ASC");
        $this->db->bind(':ed', $edificioActual);
        $casilleros = $this->db->resultSet();

        //Contar pagos pendientes (Estado F) para el Sidebar
        $this->db->query("SELECT COUNT(*) as total FROM usuarios WHERE estado = 'F'");
        $res = $this->db->single();
        $pendientes = ($res) ? $res->total : 0;

        $data = [
            'titulo' => 'Mapa de Casilleros',
            'edificio' => $edificioActual,
            'casilleros' => $casilleros,
            'stats' => ['F' => $pendientes] //Pasamos el dato al sidebar
        ];

        require_once APPROOT . '/Views/admin/casilleros.php';
    }

    //AJAX para obtener info de alumno + locker + pago
    public function info_casillero($id_casillero) {
        $this->db->query("SELECT u.nombre, u.paterno, u.materno, u.correo, 
                                 d.boleta, d.carrera, d.estatura, 
                                 d.url_credencial, d.url_horario, d.url_pago,
                                 c.numero_locker
                          FROM casilleros c
                          JOIN alumnos_detalles d ON c.id_casillero = d.id_casillero
                          JOIN usuarios u ON d.id_usuario = u.id_usuario
                          WHERE c.id_casillero = :id");
        
        $this->db->bind(':id', $id_casillero);
        $row = $this->db->single();

        echo json_encode($row);
    }

    //AJAX para llenar select de lockers libres
    public function get_disponibles($edificio) {
        $this->db->query("SELECT id_casillero, numero_locker, nivel FROM casilleros 
                        WHERE edificio = :ed AND estatus = 'disponible' ORDER BY nivel, numero_locker");
        $this->db->bind(':ed', $edificio);
        echo json_encode($this->db->resultSet());
    }

    //Visualizador de PDF (Credenciales y Horarios)
    public function ver_pdf($nombre_archivo_sucio) {
        //Limpiamos el nombre (quitamos rutas si el JS mandó "credenciales/archivo.pdf")
        $archivo = basename($nombre_archivo_sucio);
        
        //Definimos la carpeta basándonos SOLO en el inicio del nombre
        $carpeta = 'comprobantes'; //Carpeta por defecto (para los pagos)

        if (strpos($archivo, 'cred') === 0) {
            $carpeta = 'credenciales';
        } elseif (strpos($archivo, 'hor') === 0) {
            $carpeta = 'horarios';
        }

        //Ruta exacta según la estructura
        $ruta = APPROOT . '/../storage/' . $carpeta . '/' . $archivo;

        //Servir el archivo
        if (file_exists($ruta)) {
            $ext = pathinfo($ruta, PATHINFO_EXTENSION);
            $mime = ($ext === 'pdf') ? 'application/pdf' : 'image/jpeg';
            
            header('Content-Type: ' . $mime);
            header('Content-Disposition: inline; filename="' . $archivo . '"');
            readfile($ruta);
            exit;
        } else {
            // DEBUG SIMPLE :v
            die("Error 404: No encontré el archivo en: " . $ruta);
        }
    }


    //Método específico para recuperar los pagos (comprobantes)
    public function ver_archivo($nombre_archivo) {
        //Limpieza de seguridad
        $archivo = basename($nombre_archivo);
        
        //Ruta exacta
        $ruta = APPROOT . '/../storage/comprobantes/' . $archivo;

        if (file_exists($ruta)) {
            
            //LIMPIEZA DE BUFFER
            // Esto borra cualquier espacio en blanco, echo o html que se haya colado antes
            while (ob_get_level()) {
                ob_end_clean();
            }

            //Detectar tipo de archivo real
            $ext = strtolower(pathinfo($ruta, PATHINFO_EXTENSION));
            switch ($ext) {
                case 'pdf': $mime = 'application/pdf'; break;
                case 'jpg': 
                case 'jpeg': $mime = 'image/jpeg'; break;
                case 'png': $mime = 'image/png'; break;
                default: $mime = 'application/octet-stream';
            }

            //Cabeceras HTTP estrictas
            header('Content-Description: File Transfer');
            header('Content-Type: ' . $mime);
            header('Content-Disposition: inline; filename="' . $archivo . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($ruta));
            
            //Enviar el archivo limpio
            readfile($ruta);
            exit;

        } else {
            //Si no existe, mostrar error visual
            die("Error 404: El archivo no existe en la ruta: " . $ruta);
        }
    }
    //Lógica para CAMBIAR locker (Usada en Mapa y en Solicitudes)
    public function cambiar_asignacion() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_locker_viejo = $_POST['id_locker_viejo'];
            $id_locker_nuevo = $_POST['id_locker_nuevo'];
            
            //Detectar si venimos de 'solicitudes' o 'casilleros' para regresar ahí
            $redireccion = isset($_POST['redirect_to']) && $_POST['redirect_to'] == 'solicitudes' 
                           ? '/admin/solicitudes' 
                           : '/admin/casilleros';
            
            try {
                $this->db->beginTransaction();

                //Identificar alumno
                $this->db->query("SELECT id_usuario FROM alumnos_detalles WHERE id_casillero = :idV");
                $this->db->bind(':idV', $id_locker_viejo);
                $alumno = $this->db->single();

                if (!$alumno) throw new Exception("No se encontró alumno.");

                //Liberar viejo
                $this->db->query("UPDATE casilleros SET estatus = 'disponible' WHERE id_casillero = :idV");
                $this->db->bind(':idV', $id_locker_viejo);
                $this->db->execute();

                //Reservar nuevo
                $this->db->query("UPDATE casilleros SET estatus = 'reservado' WHERE id_casillero = :idN");
                $this->db->bind(':idN', $id_locker_nuevo);
                $this->db->execute();

                //Mover alumno
                $this->db->query("UPDATE alumnos_detalles SET id_casillero = :idN WHERE id_usuario = :idU");
                $this->db->bind(':idN', $id_locker_nuevo);
                $this->db->bind(':idU', $alumno->id_usuario);
                $this->db->execute();

                $this->db->commit();
                header('Location: ' . URLROOT . $redireccion . '?msg=cambio_exitoso');
            } catch (Exception $e) {
                $this->db->rollBack();
                die("Error: " . $e->getMessage());
            }
        }
    }

    //Lógica para APROBAR (B) o RECHAZAR (C)
    public function procesar_estado_final() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_locker = $_POST['id_casillero'];
            $nuevo_estado_usuario = $_POST['nuevo_estado']; // 'B' o 'C'
            
            //Detectar redirección
            $redireccion = isset($_POST['redirect_to']) && $_POST['redirect_to'] == 'solicitudes' 
                           ? '/admin/solicitudes' 
                           : '/admin/casilleros';

            // B (Aprobar) -> Ocupado
            // C (Rechazar) -> Disponible
            $estatus_locker = ($nuevo_estado_usuario == 'B') ? 'ocupado' : 'disponible';

            try {
                $this->db->beginTransaction();

                $this->db->query("SELECT id_usuario FROM alumnos_detalles WHERE id_casillero = :idL");
                $this->db->bind(':idL', $id_locker);
                $alumno = $this->db->single();

                //Actualizar Usuario
                $this->db->query("UPDATE usuarios SET estado = :est WHERE id_usuario = :idU");
                $this->db->bind(':est', $nuevo_estado_usuario);
                $this->db->bind(':idU', $alumno->id_usuario);
                $this->db->execute();

                //Actualizar Locker
                $this->db->query("UPDATE casilleros SET estatus = :stL WHERE id_casillero = :idL");
                $this->db->bind(':stL', $estatus_locker);
                $this->db->bind(':idL', $id_locker);
                $this->db->execute();

                //Si rechaza, limpiar vínculo
                if($nuevo_estado_usuario == 'C') {
                    $this->db->query("UPDATE alumnos_detalles SET id_casillero = NULL WHERE id_usuario = :idU");
                    $this->db->bind(':idU', $alumno->id_usuario);
                    $this->db->execute();
                }

                $this->db->commit();
                header('Location: ' . URLROOT . $redireccion . '?msg=success');
            } catch (Exception $e) {
                $this->db->rollBack();
                die($e->getMessage());
            }
        }
    }

    // ==========================================
    //  VALIDACIÓN DE PAGOS
    // ==========================================
    public function validacion_pagos() {
        $this->db->query("SELECT u.id_usuario, u.nombre, u.paterno, d.boleta, d.carrera, d.url_pago, c.numero_locker
                          FROM usuarios u 
                          JOIN alumnos_detalles d ON u.id_usuario = d.id_usuario 
                          JOIN casilleros c ON d.id_casillero = c.id_casillero 
                          WHERE u.estado = 'F'");
        $pendientes = $this->db->resultSet();
        $data = ['titulo' => 'Validación de Pagos', 'pendientes' => $pendientes];
        require_once APPROOT . '/Views/admin/validacion_pagos.php';
    }
    
    public function validar_pago($id_usuario) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $periodoActual = '2025-2'; 
            try {
                $this->db->beginTransaction();

                //Obtener datos actuales
                $this->db->query("SELECT id_casillero, url_pago FROM alumnos_detalles WHERE id_usuario = :id");
                $this->db->bind(':id', $id_usuario);
                $detalles = $this->db->single();

                if (!$detalles || !$detalles->id_casillero) throw new Exception("Error de integridad.");

                //Histórico
                $this->db->query("INSERT INTO asignaciones (id_alumno, id_casillero, periodo, url_comprobante) 
                                  VALUES (:idA, :idC, :per, :url)");
                $this->db->bind(':idA', $id_usuario);
                $this->db->bind(':idC', $detalles->id_casillero);
                $this->db->bind(':per', $periodoActual);
                $this->db->bind(':url', $detalles->url_pago);
                $this->db->execute();

                //Locker Ocupado
                $this->db->query("UPDATE casilleros SET estatus = 'ocupado' WHERE id_casillero = :idC");
                $this->db->bind(':idC', $detalles->id_casillero);
                $this->db->execute();

                //Usuario Finalizado (H)
                $this->db->query("UPDATE usuarios SET estado = 'H' WHERE id_usuario = :id");
                $this->db->bind(':id', $id_usuario);
                $this->db->execute();

                $this->db->commit();
                header('Location: ' . URLROOT . '/admin/validacion_pagos?msg=validado');

            } catch (Exception $e) {
                $this->db->rollBack();
                die("Error BD: " . $e->getMessage());
            }
        }
    }

    public function rechazar_pago($id_usuario) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            //Regresar a E (Subir pago) y borrar archivo malo
            $this->db->query("UPDATE usuarios SET estado = 'E' WHERE id_usuario = :id");
            $this->db->bind(':id', $id_usuario);
            $this->db->execute();
            
            $this->db->query("UPDATE alumnos_detalles SET url_pago = NULL WHERE id_usuario = :id");
            $this->db->bind(':id', $id_usuario);
            $this->db->execute();

            header('Location: ' . URLROOT . '/admin/validacion_pagos?msg=rechazado');
        }
    }

    // ==========================================
    //  CRUD ALUMNOS
    // ==========================================
    public function gestion_alumnos() {
        $this->db->query("SELECT u.*, d.boleta, d.carrera, d.estatura, d.telefono 
                          FROM usuarios u 
                          LEFT JOIN alumnos_detalles d ON u.id_usuario = d.id_usuario 
                          WHERE u.rol = 'alumno' ORDER BY u.paterno ASC");
        $alumnos = $this->db->resultSet();
        $data = ['titulo' => 'Gestión de Alumnos', 'alumnos' => $alumnos];
        require_once APPROOT . '/Views/admin/gestion_alumnos.php';
    }

    public function guardar_alumno() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_usuario = $_POST['id_usuario'];
            $datos = [
                'nombre'   => trim($_POST['nombre']),
                'paterno'  => trim($_POST['paterno']),
                'materno'  => trim($_POST['materno']),
                'correo'   => trim($_POST['correo']),
                'boleta'   => trim($_POST['boleta']),
                'carrera'  => $_POST['carrera'],
                'estatura' => $_POST['estatura'],
                'telefono' => trim($_POST['telefono'])
            ];

            try {
                $this->db->beginTransaction();

                //Actualizar tabla base: usuarios
                $this->db->query("UPDATE usuarios SET nombre = :n, paterno = :p, materno = :m, correo = :c WHERE id_usuario = :id");
                $this->db->bind(':n', $datos['nombre']);
                $this->db->bind(':p', $datos['paterno']);
                $this->db->bind(':m', $datos['materno']);
                $this->db->bind(':c', $datos['correo']);
                $this->db->bind(':id', $id_usuario);
                $this->db->execute();

                //Actualizar tabla extendida: alumnos_detalles
                $this->db->query("UPDATE alumnos_detalles SET boleta = :bol, carrera = :car, estatura = :est, telefono = :tel WHERE id_usuario = :id");
                $this->db->bind(':bol', $datos['boleta']);
                $this->db->bind(':car', $datos['carrera']);
                $this->db->bind(':est', $datos['estatura']);
                $this->db->bind(':tel', $datos['telefono']);
                $this->db->bind(':id', $id_usuario);
                $this->db->execute();

                $this->db->commit();
                header('Location: ' . URLROOT . '/admin/gestion_alumnos?msg=actualizado');
            } catch (Exception $e) {
                $this->db->rollBack();
                die("Error en la actualización: " . $e->getMessage());
            }
        }
    }

    public function eliminar_alumno($id) {
        try {
            //Iniciamos una transacción atómica para asegurar la consistencia total
            $this->db->beginTransaction();

            //Localizar el casillero vinculado antes de borrar los detalles
            //Buscamos en alumnos_detalles si el usuario tiene un id_casillero asignado
            $this->db->query("SELECT id_casillero FROM alumnos_detalles WHERE id_usuario = :id");
            $this->db->bind(':id', $id);
            $alumnoDetalle = $this->db->single();

            if ($alumnoDetalle && !empty($alumnoDetalle->id_casillero)) {
                //Liberación del Recurso (Estado 'disponible')
                //Si el alumno tenía un casillero, lo devolvemos al pool de libres
                $this->db->query("UPDATE casilleros SET estatus = 'disponible' WHERE id_casillero = :idc");
                $this->db->bind(':idc', $alumnoDetalle->id_casillero);
                $this->db->execute();
            }

            //Limpieza de Dependencias: Tabla Asignaciones
            //Corregido: Usamos id_alumno como indicaste
            $this->db->query("DELETE FROM asignaciones WHERE id_alumno = :id");
            $this->db->bind(':id', $id);
            $this->db->execute();

            //Limpieza de Dependencias: Tabla Alumnos_Detalles
            $this->db->query("DELETE FROM alumnos_detalles WHERE id_usuario = :id");
            $this->db->bind(':id', $id);
            $this->db->execute();

            //Eliminación del Nodo Principal: Tabla Usuarios
            //Solo eliminamos si es rol alumno por seguridad
            $this->db->query("DELETE FROM usuarios WHERE id_usuario = :id AND rol = 'alumno'");
            $this->db->bind(':id', $id);
            
            if ($this->db->execute()) {
                //Si todo el grafo de dependencias se limpió correctamente, confirmamos
                $this->db->commit();
                header('Location: ' . URLROOT . '/admin/gestion_alumnos?msg=eliminado');
                exit();
            }

        } catch (Exception $e) {
            //Ante cualquier fallo (ej. bloqueo de tabla o error de red), hacemos rollback
            $this->db->rollBack();
            die("Fallo en la integridad de la transacción: " . $e->getMessage());
        }
    }

    // ==========================================
    //  CRUD CASILLEROS
    // ==========================================
    public function gestion_casilleros() {
        $this->db->query("SELECT * FROM casilleros ORDER BY edificio, numero_locker");
        $casilleros = $this->db->resultSet();
        $data = ['titulo' => 'Inventario Lockers', 'casilleros' => $casilleros];
        require_once APPROOT . '/Views/admin/gestion_casilleros.php';
    }

    public function guardar_casillero() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_casillero = trim($_POST['id_casillero']);
            $numero = trim($_POST['numero_locker']);
            $edificio = $_POST['edificio'];
            $nivel = $_POST['nivel'];
            $estatus = $_POST['estatus'];

            if (empty($id_casillero)) {
                //INSERT: Nuevo casillero
                $this->db->query("INSERT INTO casilleros (numero_locker, edificio, nivel, estatus) 
                                VALUES (:num, :edi, :niv, :est)");
            } else {
                //UPDATE: Editar existente
                $this->db->query("UPDATE casilleros SET numero_locker = :num, edificio = :edi, 
                                nivel = :niv, estatus = :est WHERE id_casillero = :id");
                $this->db->bind(':id', $id_casillero);
            }

            $this->db->bind(':num', $numero);
            $this->db->bind(':edi', $edificio);
            $this->db->bind(':niv', $nivel);
            $this->db->bind(':est', $estatus);

            if ($this->db->execute()) {
                header('Location: ' . URLROOT . '/admin/gestion_casilleros?msg=success');
            }
        }
    }

    public function eliminar_casillero($id) {
        try {
            //Iniciamos transacción para asegurar que se borre todo o nada
            $this->db->beginTransaction();

            //Eliminamos primero las asignaciones vinculadas a este locker
            //Esto resuelve el error 1451 de Integrity Constraint
            $this->db->query("DELETE FROM asignaciones WHERE id_casillero = :id");
            $this->db->bind(':id', $id);
            $this->db->execute();

            //Opcional: Si  alumnos_detalles vinculados al locker, 
            //hay que poner el id_casillero en NULL para no borrarlos a ellos
            $this->db->query("UPDATE alumnos_detalles SET id_casillero = NULL WHERE id_casillero = :id");
            $this->db->bind(':id', $id);
            $this->db->execute();

            //Finalmente, borramos el casillero
            $this->db->query("DELETE FROM casilleros WHERE id_casillero = :id");
            $this->db->bind(':id', $id);
            
            if ($this->db->execute()) {
                $this->db->commit();
                header('Location: ' . URLROOT . '/admin/gestion_casilleros?msg=eliminado');
            }
        } catch (Exception $e) {
            //Si algo falla, revertimos los cambios para no corromper la DB
            $this->db->rollBack();
            die("Error al eliminar: " . $e->getMessage());
        }
    }

    // ==========================================
    //  VISTA LISTA DE SOLICITUDES
    // ==========================================
    public function solicitudes() {
        //Traer solicitudes pendientes (Amarillos/Reservados) para gestión en lista
        $this->db->query("SELECT u.id_usuario, u.nombre, u.paterno, u.materno, 
                                 d.boleta, d.carrera, d.estatura,
                                 c.id_casillero, c.numero_locker, c.edificio, c.nivel, c.estatus
                          FROM casilleros c
                          JOIN alumnos_detalles d ON c.id_casillero = d.id_casillero
                          JOIN usuarios u ON d.id_usuario = u.id_usuario
                          WHERE u.estado = 'A'
                          ORDER BY c.edificio, c.numero_locker ASC");
        
        $solicitudes = $this->db->resultSet();

        $data = [
            'titulo' => 'Validar Documentos (A)',
            'solicitudes' => $solicitudes
        ];

        require_once APPROOT . '/Views/admin/solicitudes.php';
    }

    public function reset_semestre() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            $accion = $_POST['tipo_reset']; 

            try {
                $this->db->beginTransaction();

                //RESET TOTAL (G) - SE QUEDA IGUAL
                if ($accion === 'G') {
                    $this->db->query("UPDATE casilleros SET estatus = 'disponible'");
                    $this->db->execute();

                    $this->db->query("UPDATE alumnos_detalles SET id_casillero = NULL, url_pago = NULL");
                    $this->db->execute();

                    $this->db->query("UPDATE usuarios SET estado = 'G' WHERE rol = 'alumno'");
                    $this->db->execute();
                } 
                
                //INVITACIÓN A RENOVAR (I) - LÓGICA CORREGIDA
                elseif ($accion === 'I') {
                    //Lockers OCUPADOS pasan a RESERVADOS (Amarillo)
                    //Esto evita que otros alumnos los ganen mientras el dueño decide
                    $this->db->query("UPDATE casilleros SET estatus = 'reservado' WHERE estatus = 'ocupado'");
                    $this->db->execute();

                    //Borrar URL de pago anterior (pero NO el id_casillero)
                    $this->db->query("UPDATE alumnos_detalles SET url_pago = NULL WHERE id_casillero IS NOT NULL");
                    $this->db->execute();

                    //Mover alumnos con locker asignado al estado 'I' (Invitación)
                    $this->db->query("UPDATE usuarios u
                                      JOIN alumnos_detalles d ON u.id_usuario = d.id_usuario
                                      SET u.estado = 'I'
                                      WHERE u.rol = 'alumno' AND d.id_casillero IS NOT NULL");
                    $this->db->execute();
                    
                    //Los que NO tenían locker, se van a 'G' para concursar desde cero
                    $this->db->query("UPDATE usuarios u
                                      LEFT JOIN alumnos_detalles d ON u.id_usuario = d.id_usuario
                                      SET u.estado = 'G'
                                      WHERE u.rol = 'alumno' AND d.id_casillero IS NULL");
                    $this->db->execute();
                }

                $this->db->commit();
                
                $msg = ($accion === 'G') ? 'reset_g_ok' : 'reset_i_ok';
                header('Location: ' . URLROOT . '/admin/index?msg=' . $msg);

            } catch (Exception $e) {
                $this->db->rollBack();
                die("Error crítico: " . $e->getMessage());
            }
        }
    }

    //Cargar el formulario
    //Cargar el formulario (Siguiendo tu estilo de require_once)
    public function registrar_admin() {
        $data = [
            'titulo' => 'Registrar Nuevo Administrador'
        ];
        require_once APPROOT . '/Views/admin/registrar_admin.php';
    }

    //Procesar el registro (Siguiendo tu estilo de $this->db)
    public function guardar_admin() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            //Limpieza de datos (Sanitización básica)
            $username = trim($_POST['username']);
            $nombre   = trim($_POST['nombre']);
            $paterno  = trim($_POST['paterno']);
            $materno  = trim($_POST['materno']);
            $correo   = trim($_POST['correo']);
            //Hasheo de seguridad para la contraseña
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

            try {
                //Preparamos la consulta con la estructura completa de tu tabla
                $this->db->query("INSERT INTO usuarios (username, nombre, paterno, materno, correo, password, rol, estado) 
                                VALUES (:u, :n, :p, :m, :c, :pass, 'admin', 'A')");
                
                //Vinculamos cada valor con su marcador de posición
                $this->db->bind(':u', $username);
                $this->db->bind(':n', $nombre);
                $this->db->bind(':p', $paterno);
                $this->db->bind(':m', $materno);
                $this->db->bind(':c', $correo);
                $this->db->bind(':pass', $password);
                
                //Ejecutamos la inserción
                if($this->db->execute()) {
                    //Redirigimos a la gestión de alumnos con un mensaje de éxito
                    header('Location: ' . URLROOT . '/admin/registrar_admin?msg=admin_creado');
                    exit();
                }
            } catch (Exception $e) {
                //En caso de error (ej. username o correo duplicado)
                die("Error crítico al insertar administrador: " . $e->getMessage());
            }
        }
    }

    public function reportes() {
        //Distribución de casilleros por estado (Global)
        $this->db->query("SELECT estatus, COUNT(*) as total FROM casilleros GROUP BY estatus");
        $statusStats = $this->db->resultSet();

        //Alumnos con casillero por carrera
        $this->db->query("SELECT d.carrera, COUNT(*) as total 
                        FROM alumnos_detalles d 
                        WHERE d.id_casillero IS NOT NULL 
                        GROUP BY d.carrera");
        $careerStats = $this->db->resultSet();

        //ESTADÍSTICA POR EDIFICIO (Cálculo de Densidad de Ocupación)
        //Usamos una consulta agregada para calcular la capacidad y el uso por edificio
        $this->db->query("SELECT edificio, 
                                COUNT(*) as capacidad_total,
                                SUM(CASE WHEN estatus IN ('ocupado', 'reservado') THEN 1 ELSE 0 END) as espacios_uso
                        FROM casilleros 
                        GROUP BY edificio 
                        ORDER BY edificio ASC");
        $buildingStats = $this->db->resultSet();

        //Reporte detallado (Tabla inferior)
        $this->db->query("SELECT c.numero_locker, c.edificio, c.estatus, u.nombre, u.paterno, d.carrera
                        FROM casilleros c
                        LEFT JOIN alumnos_detalles d ON c.id_casillero = d.id_casillero
                        LEFT JOIN usuarios u ON d.id_usuario = u.id_usuario
                        ORDER BY c.edificio ASC, c.numero_locker ASC");
        $reporte = $this->db->resultSet();

        $data = [
            'titulo' => 'Reportes Estadísticos',
            'statusStats' => $statusStats,
            'careerStats' => $careerStats,
            'buildingStats' => $buildingStats,
            'reporte' => $reporte
        ];

        require_once APPROOT . '/Views/admin/reportes.php';
    }
}