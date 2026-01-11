<?php

require_once __DIR__ . '/fpdf.php';

class AcuseService {

    public function generarPDF($datos) {
        // Definimos constantes de rutas para imágenes 
        define('PATH_LOGO_IPN', dirname(APPROOT) . '/public/img/logo_ipn.png'); 
        define('PATH_LOGO_ESCOM', dirname(APPROOT) . '/public/img/logo_escom.png');

        $pdf = new class extends FPDF {
            function Header() {
                // --- LOGOS (Con validación para evitar errores) ---
                if (file_exists(PATH_LOGO_IPN)) {
                    $this->Image(PATH_LOGO_IPN, 15, 10, 25); // Logo Izq
                }
                if (file_exists(PATH_LOGO_ESCOM)) {
                    $this->Image(PATH_LOGO_ESCOM, 170, 10, 25); // Logo Der
                }

                // --- ENCABEZADO DE TEXTO ---
                $this->SetFont('Arial', 'B', 14);
                //Color Guinda IPN
                $this->SetTextColor(108, 19, 43); 
                $this->Cell(0, 10, utf8_decode('INSTITUTO POLITÉCNICO NACIONAL'), 0, 1, 'C');
                
                $this->SetTextColor(0, 0, 0); // Reset a negro
                $this->SetFont('Arial', 'B', 12);
                $this->Cell(0, 5, utf8_decode('ESCUELA SUPERIOR DE CÓMPUTO'), 0, 1, 'C');
                $this->Ln(2);
                
                $this->SetFont('Arial', '', 10);
                $this->Cell(0, 5, utf8_decode('SISTEMA DE ASIGNACIÓN DE LOCKERS'), 0, 1, 'C');
                $this->Ln(10);
                
                //Título del Documento con fondo
                $this->SetFillColor(240, 240, 240);
                $this->SetFont('Arial', 'B', 16);
                $this->Cell(0, 12, utf8_decode('COMPROBANTE DE ASIGNACIÓN'), 1, 1, 'C', true);
                $this->Ln(5);
            }

            function Footer() {
                $this->SetY(-15);
                $this->SetFont('Arial', 'I', 8);
                $this->SetTextColor(128);
                $this->Cell(0, 10, utf8_decode('Este documento es un comprobante oficial. Página ') . $this->PageNo(), 0, 0, 'C');
            }
        };

        $pdf->AddPage();
        $pdf->SetMargins(20, 20, 20);

        // --- FECHA Y FOLIO ---
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 5, utf8_decode('Fecha de emisión: ' . date('d/m/Y H:i:s')), 0, 1, 'R');
        $pdf->Ln(5);

        // --- SECCIÓN 1: DATOS DEL ALUMNO ---
        $this->seccionTitulo($pdf, '1. DATOS DEL ALUMNO');
        
        $pdf->SetFont('Arial', '', 11);
        // Usamos anchos fijos para que se vea como tabla invisible
        $this->filaDoble($pdf, 'Nombre Completo:', $datos->nombre . ' ' . $datos->paterno . ' ' . $datos->materno);
        $this->filaDoble($pdf, 'Boleta:', $datos->boleta);
        $this->filaDoble($pdf, 'Carrera:', $datos->carrera);
        $this->filaDoble($pdf, 'Correo Institucional:', $datos->correo);

        $pdf->Ln(5);

        // --- SECCIÓN 2: DATOS DEL CASILLERO ---
        $this->seccionTitulo($pdf, '2. LOCKER ASIGNADO');

        //Cuadro visual del Locker
        $pdf->Ln(2);
        $pdf->SetFont('Arial', 'B', 40);
        $pdf->SetTextColor(108, 19, 43); // Guinda
        $pdf->Cell(0, 25, utf8_decode($datos->numero_locker), 1, 1, 'C'); // Cuadro con borde
        $pdf->SetTextColor(0); // Reset
        
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 8, utf8_decode('UBICACIÓN FÍSICA'), 0, 1, 'C');
        
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 6, utf8_decode('Edificio: ' . $datos->edificio . '  |  Nivel: ' . $datos->nivel), 0, 1, 'C');
        $pdf->Ln(10);

        // --- SECCIÓN 3: TÉRMINOS Y FIRMAS ---
        $this->seccionTitulo($pdf, '3. VALIDACIÓN');
        
        $pdf->SetFont('Arial', '', 9);
        $texto = "Al firmar este documento, el alumno acepta la responsabilidad sobre el uso del casillero asignado, " .
                 "comprometiéndose a utilizar su propio candado de seguridad y desalojarlo al término del semestre vigente. " .
                 "La escuela no se hace responsable por objetos de valor dejados en el interior.";
        $pdf->MultiCell(0, 5, utf8_decode($texto));

        $pdf->Ln(25); // Espacio para firmar

        //Firmas centradas y alineadas
        $y = $pdf->GetY();
        
        // Firma Alumno (Izquierda)
        $pdf->Line(30, $y, 90, $y); 
        $pdf->SetXY(30, $y + 2);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(60, 5, utf8_decode('Firma del Alumno'), 0, 0, 'C');
        
        // Sello (Derecha)
        $pdf->Line(120, $y, 180, $y);
        $pdf->SetXY(120, $y + 2);
        $pdf->Cell(60, 5, utf8_decode('Sello de Coordinación'), 0, 1, 'C');

        // Generar salida
        $pdf->Output('I', 'Acuse_Locker_' . $datos->boleta . '.pdf');
    }

    // Helper para títulos de sección bonitos
    private function seccionTitulo($pdf, $texto) {
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetFillColor(220, 220, 220); // Gris claro
        $pdf->Cell(0, 8, utf8_decode('  ' . $texto), 0, 1, 'L', true);
        $pdf->Ln(2);
    }

    // Helper para filas de datos con subrayado sutil
    private function filaDoble($pdf, $label, $valor) {
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(45, 8, utf8_decode($label), 'B', 0, 'L'); // Borde inferior ('B')
        
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 8, utf8_decode($valor), 'B', 1, 'L'); // Borde inferior ('B')
    }
}