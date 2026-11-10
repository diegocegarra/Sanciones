<?php
// 1. Limpiar cualquier salida previa (errores, espacios, avisos)
ob_start();

require_once('../../../externo/fpdf/fpdf.php');
require_once(__DIR__ . '/../../../back_end/config.php');

// Función auxiliar para evitar el error de "Deprecated" en PHP 8.2+
if (!function_exists('txt')) {
    function txt($texto) {
        return mb_convert_encoding($texto, 'ISO-8859-1', 'UTF-8');
    }
}

// Evitar el error de "Cannot declare class PDF" si se incluye este archivo dos veces
if (!class_exists('PDF')) {
    class PDF extends FPDF {
        // Función para crear el diseño redondeado del contenedor
        function RoundedRect($x, $y, $w, $h, $r, $style = '') {
            $k = $this->k;
            $hp = $this->h;
            
            if($style=='F') $op='f';
            
            elseif($style=='FD' || $style=='DF') $op='B';
            
            else $op='S';
            $MyArc = 4/3 * (sqrt(2) - 1);
           
            $this->_out(sprintf('%.2F %.2F m',($x+$r)*$k,($hp-$y)*$k));
           
            $xc = $x+$w-$r ;
            
            $yc = $y+$r;
           
            $this->_out(sprintf('%.2F %.2F l', $xc*$k,($hp-$y)*$k));

            $this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);
            
            $xc = $x+$w-$r ;
            $yc = $y+$h-$r;
           
            $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-$yc)*$k));
            $this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);
            $xc = $x+$r ;
            $yc = $y+$h-$r;
            $this->_out(sprintf('%.2F %.2F l',$xc*$k,($hp-($y+$h))*$k));
            $this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);
            $xc = $x+$r ;
            $yc = $y+$r;
            $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$yc)*$k));
            $this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
            $this->_out($op);
        }

        function _Arc($x1, $y1, $x2, $y2, $x3, $y3) {
            $h = $this->h;
            $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1*$this->k, ($h-$y1)*$this->k, 
                $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
        }
    }
}

// 1. Datos
$monto = "2223 Bs";
$nombre_sancion = "Pepsi 4";
$banco = "BANESCO";
$referencia = "444";
$fecha = "2026-02-08";

// 2. Configuración del PDF
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// Título superior
$pdf->SetTextColor(100, 100, 100);
$pdf->Cell(0, 10, txt('Registro total a pagar'), 0, 1, 'C');

// Monto Grande
$pdf->SetFont('Arial', 'B', 40);
$pdf->SetTextColor(33, 37, 41);
$pdf->Cell(0, 25, txt($monto), 0, 1, 'C');
$pdf->Ln(10);

// Contenedor Gris
$pdf->SetFillColor(248, 249, 250);
$pdf->RoundedRect(40, 65, 130, 70, 8, 'F');

// Detalles dentro del contenedor
$pdf->SetLeftMargin(50);
$pdf->SetY(75);

// Función auxiliar para filas de datos mejorada
if (!function_exists('agregarFila')) {
    function agregarFila($pdf, $label, $value) {
        $pdf->SetFont('Arial', '', 11);
        $pdf->SetTextColor(150, 150, 150);
        $pdf->Cell(50, 10, txt($label), 0, 0, 'L');
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetTextColor(33, 37, 41);
        $pdf->Cell(60, 10, txt($value), 0, 1, 'R');
        $pdf->Ln(2);
    }
}

agregarFila($pdf, 'Nombre sancion', $nombre_sancion);
agregarFila($pdf, 'Banco', $banco);
agregarFila($pdf, '# Referencia', $referencia);
agregarFila($pdf, 'Fecha de pago', $fecha);

// Botón inferior decorativo
$pdf->SetY(150);
$pdf->SetX(55);
$pdf->SetFillColor(13, 11, 23);
$pdf->RoundedRect(55, 150, 100, 15, 5, 'F');
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(100, 15, txt('Pago Registrado'), 0, 0, 'C');

// 3. Salida final limpia
ob_end_clean(); 
$pdf->Output('I', 'Factura_' . $nombre_sancion . '.pdf');
?>