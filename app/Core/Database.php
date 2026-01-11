<?php
    /*
    * Clase Database: Encapsula la conexión a PDO
    * Crea sentencias preparadas, vincula valores y retorna resultados
    */
    class Database {
        private $host = DB_HOST;
        private $user = DB_USER;
        private $pass = DB_PASS;
        private $dbname = DB_NAME;

        private $dbh; //Database Handler
        private $stmt; //Statement
        private $error;

        public function __construct() {
            //Configurar DSN (Data Source Name)
            $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
            $options = array(
                PDO::ATTR_PERSISTENT => true, // Mantiene la conexión viva para mayor eficiencia
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            );

            //Crear instancia de PDO
            try {
                $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
                //Aseguramos que los nombres de las columnas se mantengan en minúsculas/originales
                $this->dbh->exec("set names utf8");
            } catch (PDOException $e) {
                $this->error = $e->getMessage();
                echo "Error de Conexión: " . $this->error;
            }
        }

        //Preparar la consulta con sentencias preparadas
        public function query($sql) {
            $this->stmt = $this->dbh->prepare($sql);
        }

        //Vincular valores (Protección contra Inyección SQL)
        public function bind($param, $value, $type = null) {
            if (is_null($type)) {
                switch (true) {
                    case is_int($value):
                        $type = PDO::PARAM_INT;
                        break;
                    case is_bool($value):
                        $type = PDO::PARAM_BOOL;
                        break;
                    case is_null($value):
                        $type = PDO::PARAM_NULL;
                        break;
                    default:
                        $type = PDO::PARAM_STR;
                }
            }
            $this->stmt->bindValue($param, $value, $type);
        }

        //Ejecutar la sentencia preparada
        public function execute() {
            return $this->stmt->execute();
        }

        //Obtener el conjunto de resultados como un array de objetos
        public function resultSet() {
            $this->execute();
            return $this->stmt->fetchAll(PDO::FETCH_OBJ);
        }

        //Obtener un solo registro como objeto
        public function single() {
            $this->execute();
            return $this->stmt->fetch(PDO::FETCH_OBJ);
        }

        //Obtener el número de filas
        public function rowCount() {
            return $this->stmt->rowCount();
        }
        
        //Métodos para Transacciones (NECESARIO para el Autómata)
        public function beginTransaction(){
            return $this->dbh->beginTransaction();
        }

        public function commit(){
            return $this->dbh->commit();
        }

        public function rollBack(){
            return $this->dbh->rollBack();
        }

        //Retorna el ID de la última inserción
        public function lastInsertId() {
            return $this->dbh->lastInsertId();
        }
    }












?>