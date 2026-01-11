<?php
class AlumnoModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function registrar($datosUsuario, $datosAlumno) {
        try {
            $this->db->beginTransaction();

            //Insertar en la tabla general de usuarios
            $this->db->query("INSERT INTO usuarios (nombre, paterno, materno, telefono, correo, username, password, rol, estado) 
                              VALUES (:nombre, :paterno, :materno, :telefono, :correo, :username, :password, 'alumno', 'A')");
            
            $this->db->bind(':nombre', $datosUsuario['nombre']);
            $this->db->bind(':paterno', $datosUsuario['paterno']);
            $this->db->bind(':materno', $datosUsuario['materno']);
            $this->db->bind(':telefono', $datosUsuario['telefono']);
            $this->db->bind(':correo', $datosUsuario['correo']);
            $this->db->bind(':username', $datosUsuario['username']);
            $this->db->bind(':password', $datosUsuario['password']);
            
            $this->db->execute();
            
            //Obtenemos el ID del usuario recién creado
            $idUsuario = $this->db->getLastInsertId(); 

            //Insertar en la tabla de detalles del alumno
            $this->db->query("INSERT INTO alumnos_detalles (id_usuario, boleta, estatura, url_credencial, url_horario) 
                              VALUES (:id, :boleta, :estatura, :credencial, :horario)");
            
            $this->db->bind(':id', $idUsuario);
            $this->db->bind(':boleta', $datosAlumno['boleta']);
            $this->db->bind(':estatura', $datosAlumno['estatura']);
            $this->db->bind(':credencial', $datosAlumno['url_credencial']);
            $this->db->bind(':horario', $datosAlumno['url_horario']);
            
            $this->db->execute();

            $this->db->commit();
            //Retornamos el ID para que el controlador se lo pase al StateMachine
            return $idUsuario; 
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function existeDato($columna, $valor) {
        $tabla = ($columna == 'boleta') ? 'alumnos_detalles' : 'usuarios';
        $this->db->query("SELECT * FROM $tabla WHERE $columna = :valor");
        $this->db->bind(':valor', $valor);
        return $this->db->single() ? true : false;
    }
}