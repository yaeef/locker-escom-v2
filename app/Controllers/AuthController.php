<?php
    class AuthController {
        private $db;

        public function __construct() {
            $this->db = new Database();
        }

        public function login() {
            //solo cargamos la vista
            require_once APPROOT . '/Views/auth/login.php';
        }
        public function registro() {
            //Cargar la vista de registro
            require_once APPROOT . '/Views/auth/registro.php';
        }

        public function registrar() {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                //Recopilación de datos básicos
                $data = [
                    'username' => trim($_POST['username']),
                    'nombre'   => trim($_POST['nombre']),
                    'paterno'  => trim($_POST['paterno']),
                    'materno'  => trim($_POST['materno']),
                    'correo'   => trim($_POST['correo']),
                    'password' => password_hash($_POST['password'], PASSWORD_BCRYPT),
                    'boleta'   => trim($_POST['boleta']),
                    'carrera'  => $_POST['carrera'],
                    'estatura' => (float)$_POST['estatura'],
                    'telefono' => trim($_POST['telefono']),
                    'estado'   => 'A' 
                ];

                try {
                    $this->db->beginTransaction();

                    //ALGORITMO DE ASIGNACIÓN POR ESTATURA   REVISARRRRR
                    $nivelesBusqueda = [];
                    if ($data['estatura'] < 1.60) {
                        $nivelesBusqueda = [1, 2, 3, 4];
                    } elseif ($data['estatura'] <= 1.75) {
                        $nivelesBusqueda = [2, 3, 1, 4];
                    } else {
                        $nivelesBusqueda = [4, 3, 2, 1];
                    }

                    $idLockerEncontrado = null;
                    foreach ($nivelesBusqueda as $nivel) {
                        //Bloqueo de fila (FOR UPDATE) para evitar race conditions
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

                    if (!$idLockerEncontrado) {
                        throw new Exception("No hay casilleros disponibles en el sistema.");
                    }

                    //Marcar casillero como RESERVADO
                    $this->db->query("UPDATE casilleros SET estatus = 'reservado' WHERE id_casillero = :idL");
                    $this->db->bind(':idL', $idLockerEncontrado);
                    $this->db->execute();

                    //Insertar en tabla USUARIOS
                    $this->db->query("INSERT INTO usuarios (username, nombre, paterno, materno, correo, password, rol, estado) 
                                    VALUES (:u, :n, :p, :m, :c, :pass, 'alumno', :e)");
                    $this->db->bind(':u', $data['username']);
                    $this->db->bind(':n', $data['nombre']);
                    $this->db->bind(':p', $data['paterno']);
                    $this->db->bind(':m', $data['materno']);
                    $this->db->bind(':c', $data['correo']);
                    $this->db->bind(':pass', $data['password']);
                    $this->db->bind(':e', $data['estado']);
                    $this->db->execute();
                    $idUsuario = $this->db->lastInsertId();

                    //Manejo de archivos (Storage)
                    $rutaStorage = APPROOT . '/../storage/';
                    $nombreCred = 'cred_' . $data['boleta'] . '_' . time() . '.pdf';
                    $nombreHor  = 'hor_' . $data['boleta'] . '_' . time() . '.pdf';

                    move_uploaded_file($_FILES['pdf_credencial']['tmp_name'], $rutaStorage . 'credenciales/' . $nombreCred);
                    move_uploaded_file($_FILES['pdf_horario']['tmp_name'], $rutaStorage . 'horarios/' . $nombreHor);

                    //Insertar en ALUMNOS_DETALLES vinculando el casillero
                    $this->db->query("INSERT INTO alumnos_detalles (id_usuario, boleta, carrera, estatura, telefono, url_credencial, url_horario, id_casillero) 
                                    VALUES (:id, :bol, :car, :est, :tel, :u_cred, :u_hor, :idL)");
                    $this->db->bind(':id', $idUsuario);
                    $this->db->bind(':bol', $data['boleta']);
                    $this->db->bind(':car', $data['carrera']);
                    $this->db->bind(':est', $data['estatura']);
                    $this->db->bind(':tel', $data['telefono']);
                    $this->db->bind(':u_cred', 'credenciales/' . $nombreCred);
                    $this->db->bind(':u_hor', 'horarios/' . $nombreHor);
                    $this->db->bind(':idL', $idLockerEncontrado);
                    $this->db->execute();

                    $this->db->commit();
                    header('Location: ' . URLROOT . '/auth/login?status=success');

                } catch (Exception $e) {
                    $this->db->rollBack();
                    die("Error en el registro: " . $e->getMessage());
                }
            }
        }

        //Método para validar un inicio de sesión
        public function validar() {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $identificador = trim($_POST['usuario']); // Puede ser correo o username (IMPORTANTE)
                $password = $_POST['password'];

                //Buscar al usuario por correo o username
                $this->db->query("SELECT * FROM usuarios WHERE correo = :id OR username = :id");
                $this->db->bind(':id', $identificador);
                $row = $this->db->single();

                if ($row) {
                    if (password_verify($password, $row->password)) {
                        //Crear Sesión
                        $_SESSION['user_id'] = $row->id_usuario;
                        $_SESSION['rol'] = $row->rol;
                        $_SESSION['nombre'] = $row->nombre;
                        $_SESSION['estado'] = $row->estado;

                        //Redirección por Rol
                        if ($row->rol === 'admin') {
                            header('Location: ' . URLROOT . '/admin/index');
                        } else {
                            header('Location: ' . URLROOT . '/alumno/index');
                        }
                        exit();
                    } else {
                        header('Location: ' . URLROOT . '/auth/login?error=pass_incorrecto');
                    }
                } else {
                    header('Location: ' . URLROOT . '/auth/login?error=no_existe');
                }
            }
        }

        //logout
        public function logout() {
            //Limpiar variables de sesión
            unset($_SESSION['user_id']);
            unset($_SESSION['rol']);
            unset($_SESSION['nombre']);
            unset($_SESSION['estado']);
            
            //Destruir la sesión
            session_destroy();

            header('Location: ' . URLROOT . '/auth/login?msg=sesion_cerrada');
            exit();
        }

        public function recuperar() {
            $data = [
                'titulo' => 'Recuperar Contraseña'
            ];
            
            if (file_exists(APPROOT . '/Views/auth/recuperar.php')) {
                
                require_once APPROOT . '/Views/auth/recuperar.php';
                
            } else {
                die("Error: La vista de recuperación no existe.");
            }
        }

        public function enviar_token() {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $correo = trim($_POST['correo']);
                
                $this->db->query("SELECT id_usuario, nombre FROM usuarios WHERE correo = :correo");
                $this->db->bind(':correo', $correo);
                $user = $this->db->single();

                if ($user) {
                    $token = bin2hex(random_bytes(32));
                    $expiracion = date("Y-m-d H:i:s", strtotime('+1 hour'));

                    $this->db->query("UPDATE usuarios SET reset_token = :t, token_expira = :e WHERE correo = :c");
                    $this->db->bind(':t', $token);
                    $this->db->bind(':e', $expiracion);
                    $this->db->bind(':c', $correo);
                    $this->db->execute();

                    //Simulación: Redirigimos con el token en la URL para mostrarlo en la vista
                    header('Location: ' . URLROOT . '/auth/recuperar?success=true&token=' . $token);
                } else {
                    header('Location: ' . URLROOT . '/auth/recuperar?err=no_existe');
                }
            }
        }
        public function resetear($token) {
            //Buscar el usuario con ese token y verificar tiempo
            $this->db->query("SELECT id_usuario, correo FROM usuarios 
                            WHERE reset_token = :t 
                            AND token_expira > NOW()");
            $this->db->bind(':t', $token);
            $user = $this->db->single();

            if ($user) {
                $data = [
                    'titulo' => 'Cambiar Contraseña',
                    'token'  => $token,
                    'correo' => $user->correo
                ];
                
                //Cargar la vista de cambio de contraseña
                
                require_once APPROOT . '/Views/auth/cambiar_password.php';
                
            } else {
                //Token inválido o expirado
                header('Location: ' . URLROOT . '/auth/login?error=token_invalido');
            }
        }

        public function actualizar_password() {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $token = $_POST['token'];
                $password = $_POST['password'];
                $confirmar = $_POST['confirm_password'];

                if ($password === $confirmar) {
                    //Hash de la nueva contraseña
                    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

                    $this->db->query("UPDATE usuarios 
                                    SET password = :p, reset_token = NULL, token_expira = NULL 
                                    WHERE reset_token = :t");
                    $this->db->bind(':p', $passwordHash);
                    $this->db->bind(':t', $token);

                    if ($this->db->execute()) {
                        header('Location: ' . URLROOT . '/auth/login?msg=password_actualizado');
                    }
                } else {
                    die("Las contraseñas no coinciden.");
                }
            }
        }

        
    }



?>