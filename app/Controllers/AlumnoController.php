<?php
class AlumnoController {
    private $db;

    public function __construct() {
        //Validación estándar: Si no es alumno, va para afuera el pnjo.
        if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'alumno') {
            header('Location: ' . URLROOT . '/auth/login');
            exit();
        }
        $this->db = new Database();
    }

    public function index() {
        $this->db->query("SELECT u.estado, u.nombre, d.boleta, d.carrera, d.url_pago, c.numero_locker, c.edificio, c.nivel 
                FROM usuarios u 
                LEFT JOIN alumnos_detalles d ON u.id_usuario = d.id_usuario 
                LEFT JOIN casilleros c ON d.id_casillero = c.id_casillero 
                WHERE u.id_usuario = :id");
        $this->db->bind(':id', $_SESSION['user_id']);
        $usuario = $this->db->single();

        $data = [
            'titulo' => 'Mi Panel - Locker ESCOM',
            'usuario' => $usuario
        ];

        require_once APPROOT . '/Views/alumno/index.php';
    }

    public function procesar_reglamento() {
        //Método limpio para aceptar/rechazar términos
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            //Validar sesión
            if (!isset($_SESSION['user_id'])) {
                header('Location: ' . URLROOT . '/auth/login');
                exit;
            }

            $decision = $_POST['decision'];
            $id_usuario = $_SESSION['user_id'];

            try {
                $this->db->beginTransaction();

                if ($decision === 'E') {
                    // --- CASO 1: ACEPTÓ (Pasa a E - Pago) ---
                    $this->db->query("UPDATE usuarios SET estado = 'E' WHERE id_usuario = :id");
                    $this->db->bind(':id', $id_usuario);
                    $this->db->execute();

                } else if ($decision === 'G') {
                    // --- CASO 2: RECHAZÓ (Pasa a G - Cancelado y Libera Locker) ---
                    
                    //Obtener casillero actual para liberarlo
                    $this->db->query("SELECT id_casillero FROM alumnos_detalles WHERE id_usuario = :id");
                    $this->db->bind(':id', $id_usuario);
                    $detalles = $this->db->single();

                    if ($detalles && $detalles->id_casillero) {
                        //Liberar casillero físico
                        $this->db->query("UPDATE casilleros SET estatus = 'disponible' WHERE id_casillero = :idC");
                        $this->db->bind(':idC', $detalles->id_casillero);
                        $this->db->execute();

                        //Desvincular del alumno
                        $this->db->query("UPDATE alumnos_detalles SET id_casillero = NULL WHERE id_usuario = :id");
                        $this->db->bind(':id', $id_usuario);
                        $this->db->execute();
                    }
                    
                    //Cambiar usuario a estado G
                    $this->db->query("UPDATE usuarios SET estado = 'G' WHERE id_usuario = :id");
                    $this->db->bind(':id', $id_usuario);
                    $this->db->execute();
                }

                $this->db->commit();
                
                header('Location: ' . URLROOT . '/alumno/index');
                exit;

            } catch (Exception $e) {
                if(isset($this->db)) $this->db->rollBack();
                die("Error crítico: " . $e->getMessage());
            }
        }
    }

    public function subir_pago() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            //Validar que se haya subido un archivo
            if (isset($_FILES['archivo_pago']) && $_FILES['archivo_pago']['error'] === 0) {
                
                $file = $_FILES['archivo_pago'];
                $fileName = $file['name'];
                $fileTmp = $file['tmp_name'];
                $fileSize = $file['size'];
                
                //Validar extensión (PDF o Imagen)
                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowed = ['pdf', 'jpg', 'jpeg', 'png'];
                
                if (in_array($ext, $allowed)) {
                    
                    //DEFINIR RUTA DE DESTINO CORRECTA (storage/comprobantes)
                    $carpetaDestino = APPROOT . '/../storage/comprobantes/';
                    
                    //Crear carpeta si no existe 
                    if (!is_dir($carpetaDestino)) {
                        mkdir($carpetaDestino, 0777, true);
                    }

                    //Generar Nombre Único: pago_IDUSUARIO_TIMESTAMP.ext
                    $nombreFinal = 'pago_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
                    $rutaCompleta = $carpetaDestino . $nombreFinal;

                    //Mover el archivo del temporal a nuestra carpeta
                    if (move_uploaded_file($fileTmp, $rutaCompleta)) {
                        
                        //ACTUALIZAR BASE DE DATOS
                        // Guardamos el nombre del archivo. El AdminController sabrá buscarlo en 'comprobantes'
                        $this->db->query("UPDATE alumnos_detalles SET url_pago = :url WHERE id_usuario = :id");
                        $this->db->bind(':url', $nombreFinal); 
                        $this->db->bind(':id', $_SESSION['user_id']);
                        $this->db->execute();

                        //Cambiar estado del usuario a 'F' (En Revisión)
                        $this->db->query("UPDATE usuarios SET estado = 'F' WHERE id_usuario = :id");
                        $this->db->bind(':id', $_SESSION['user_id']);
                        $this->db->execute();

                        //Éxito: Redirigir
                        header('Location: ' . URLROOT . '/alumno/index');
                    } else {
                        die("Error crítico: No se pudo guardar el archivo en la carpeta storage/comprobantes. Verifica permisos.");
                    }

                } else {
                    die("Error: Formato no permitido. Solo PDF, JPG o PNG.");
                }
            } else {
                die("Error: No se seleccionó ningún archivo o hubo un error en la subida.");
            }
        }
    }

   public function descargar_acuse() {
        //Seguridad
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }

        $id_usuario = $_SESSION['user_id'];

        //Consulta SQL (Con los nombres de columna CORRECTOS: paterno, materno)
        $this->db->query("SELECT u.nombre, u.paterno, u.materno, u.correo,
                                 d.boleta, d.carrera,
                                 c.numero_locker, c.edificio, c.nivel
                          FROM usuarios u 
                          JOIN alumnos_detalles d ON u.id_usuario = d.id_usuario 
                          JOIN casilleros c ON d.id_casillero = c.id_casillero 
                          WHERE u.id_usuario = :id");
        
        $this->db->bind(':id', $id_usuario);
        $datos = $this->db->single();

        if (!$datos) {
            die("Error: No se encontraron datos de asignación para este usuario.");
        }

        //Cargar el Servicio
        require_once APPROOT . '/Services/AcuseService.php';
        
        $servicio = new AcuseService();
        $servicio->generarPDF($datos);
    }

    public function procesar_decision_renovacion() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            //Validar sesión
            if (!isset($_SESSION['user_id'])) { header('Location: ' . URLROOT . '/auth/login'); exit; }

            $decision = $_POST['decision']; // 'renovar' o 'liberar'
            $id_usuario = $_SESSION['user_id'];

            try {
                $this->db->beginTransaction();

                if ($decision === 'renovar') {
                    // --- CAMINO A: RENOVAR (I -> E) ---
                    //El locker sigue reservado (lo puso así el admin), 
                    //solo cambiamos al alumno a 'E' para que suba el pago.
                    $this->db->query("UPDATE usuarios SET estado = 'E' WHERE id_usuario = :id");
                    $this->db->bind(':id', $id_usuario);
                    $this->db->execute();

                } elseif ($decision === 'liberar') {
                    // --- CAMINO B: RECHAZAR (I -> G) ---
                    
                    //OBTENER EL CASILLERO QUE OCUPA ACTUALMENTE
                    $this->db->query("SELECT id_casillero FROM alumnos_detalles WHERE id_usuario = :id");
                    $this->db->bind(':id', $id_usuario);
                    $res = $this->db->single();
                    
                    if ($res && $res->id_casillero) {
                        //LIBERAR EL CASILLERO FÍSICO (Ponerlo en 'disponible' / Verde)
                        $this->db->query("UPDATE casilleros SET estatus = 'disponible' WHERE id_casillero = :idC");
                        $this->db->bind(':idC', $res->id_casillero);
                        $this->db->execute();
                    }

                    //DESVINCULAR AL ALUMNO (Borrar id_casillero de su registro)
                    $this->db->query("UPDATE alumnos_detalles SET id_casillero = NULL, url_pago = NULL WHERE id_usuario = :id");
                    $this->db->bind(':id', $id_usuario);
                    $this->db->execute();

                    //MOVER ALUMNO A ESTADO 'G' (Sin Asignación)
                    $this->db->query("UPDATE usuarios SET estado = 'G' WHERE id_usuario = :id");
                    $this->db->bind(':id', $id_usuario);
                    $this->db->execute();
                }

                $this->db->commit();
                header('Location: ' . URLROOT . '/alumno/index');

            } catch (Exception $e) {
                $this->db->rollBack();
                die("Error al procesar decisión: " . $e->getMessage());
            }
        }
    }
    
    public function solicitar_nueva_asignacion() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            if (!isset($_SESSION['user_id'])) { header('Location: ' . URLROOT . '/auth/login'); exit; }
            $id_usuario = $_SESSION['user_id'];

            try {
                $this->db->beginTransaction();

                $ruta_destino = "/home/vixen/locker2/public/uploads/";
                $file_credencial = "credencial_" . $id_usuario . "_" . time() . ".pdf";
                $file_horario = "horario_" . $id_usuario . "_" . time() . ".pdf";

                if (move_uploaded_file($_FILES['credencial']['tmp_name'], $ruta_destino . $file_credencial) &&
                    move_uploaded_file($_FILES['horario']['tmp_name'], $ruta_destino . $file_horario)) {
                    
                    //Actualizamos los paths en la tabla de detalles
                    $this->db->query("UPDATE alumnos_detalles SET pdf_credencial = :p1, pdf_horario = :p2 WHERE id_usuario = :id");
                    $this->db->bind(':p1', $file_credencial);
                    $this->db->bind(':p2', $file_horario);
                    $this->db->bind(':id', $id_usuario);
                    $this->db->execute();
                }
                // ----------------------------------

                //Verificar estado G 
                $this->db->query("SELECT u.estado, d.estatura 
                                  FROM usuarios u 
                                  LEFT JOIN alumnos_detalles d ON u.id_usuario = d.id_usuario 
                                  WHERE u.id_usuario = :id");
                $this->db->bind(':id', $id_usuario);
                $datos = $this->db->single();

                if ($datos->estado !== 'G') {
                    throw new Exception("Solo usuarios en Estado G pueden reiniciar.");
                }

                //Algoritmo de Estatura 
                $estatura = (float)$datos->estatura;
                $nivelesBusqueda = [];

                if ($estatura < 1.60) {  //REVISAR PORQUE NO ASIGNA BIEN 
                    $nivelesBusqueda = [1, 2, 3, 4];
                } elseif ($estatura <= 1.75) {
                    $nivelesBusqueda = [2, 3, 1, 4];
                } else {
                    $nivelesBusqueda = [4, 3, 2, 1];
                }

                $idLockerEncontrado = null;

                foreach ($nivelesBusqueda as $nivel) {
                    $this->db->query("SELECT id_casillero FROM casilleros 
                                      WHERE estatus = 'disponible' AND nivel = :niv 
                                      LIMIT 1 FOR UPDATE");
                    $this->db->bind(':niv', $nivel);
                    $res = $this->db->single();
                    
                    if ($res) {
                        $idLockerEncontrado = $res->id_casillero;
                        break;
                    }
                }

                if ($idLockerEncontrado) {
                    //CASO A: HAY LOCKER
                    $this->db->query("UPDATE casilleros SET estatus = 'reservado' WHERE id_casillero = :idL");
                    $this->db->bind(':idL', $idLockerEncontrado);
                    $this->db->execute();

                    $this->db->query("UPDATE alumnos_detalles SET id_casillero = :idL WHERE id_usuario = :idU");
                    $this->db->bind(':idL', $idLockerEncontrado);
                    $this->db->bind(':idU', $id_usuario);
                    $this->db->execute();

                    $this->db->query("UPDATE usuarios SET estado = 'A' WHERE id_usuario = :id");
                    $this->db->bind(':id', $id_usuario);
                    $this->db->execute();
                } else {
                    //CASO B: NO HAY LOCKER
                    $this->db->query("UPDATE usuarios SET estado = 'A' WHERE id_usuario = :id");
                    $this->db->bind(':id', $id_usuario);
                    $this->db->execute();
                }

                $this->db->commit();
                header('Location: ' . URLROOT . '/alumno/index?msg=docs_ok');

            } catch (Exception $e) {
                $this->db->rollBack();
                die("Error: " . $e->getMessage());
            }
        }
    }
}