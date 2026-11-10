<?php
// 1. Iniciar el búfer para atrapar cualquier error o espacio accidental
ob_start();

require_once('../../../externo/fpdf/fpdf.php');
require_once(__DIR__ . '/../../../back_end/config.php');

// Función auxiliar para codificación moderna (reemplaza utf8_decode)
if (!function_exists('txt')) {
    function txt($texto) {
        return mb_convert_encoding($texto, 'ISO-8859-1', 'UTF-8');
    }
}

// --- 1. DATOS ---
$nombre_empresa = $_POST['empresa'] ?? 'EMPRESA DE PRUEBA C.A.';
$fecha_reporte = "ene-25";
$monto_total_bs = "43,00";

$sanciones = [
    ["desc" => "Incumplir con la obligación de informar al Instituto Venezolano de los Seguros Sociales en el tiempo previsto...", "base" => "86 Lit. A #1", "ut" => "25 UT", "multa" => "Bs. F 34,00"],
    ["desc" => "Incumplir con la obligación de llevar y mantener al día el registro del personal a su servicio...", "base" => "SS Lit. B #1", "ut" => "50 UT", "multa" => "Bs. F 234,00"],
    ["desc" => "La omisión de informar sobre la cesación de actividades, cambios de razón social, traspaso del dominio...", "base" => "86 Lit. A #2", "ut" => "50 UT", "multa" => "Bs. F 344,00"],
    ["desc" => "La falta de inscripción en el Instituto Venezolano de los Seguros Sociales antes del inicio de su actividad.", "base" => "86 Lit. B #1", "ut" => "50 UT", "multa" => "Bs. F34,00"],
    ["desc" => "La omisión de informar sobre la cesación de actividades, cambios de razón social, establecimiento o faena.", "base" => "86 Lit. B #2", "ut" => "50 UT", "multa" => "Bs. F 556,00"],
    ["desc" => "La omisión de inscribir a sus trabajadoras y trabajadores dentro de los tres (3) días hábiles siguientes.", "base" => "86 Lit. B #3", "ut" => "50 UT", "multa" => "Bs. F 0,00"],
    ["desc" => "La omisión de suministrar en el tiempo previsto toda variación en el salario de la trabajadora o trabajador.", "base" => "86 Lit. B #4", "ut" => "50 UT", "multa" => "Bs. F 34,00"],
    ["desc" => "Efectuar retenciones por concepto de cotizaciones superiores a los establecidos en esta Ley.", "base" => "86 Lit. C #1", "ut" => "100 UT", "multa" => "Bs. F 0,00"],
    ["desc" => "Impedir las fiscalizaciones que ordene el IVSS, negando el acceso o obstaculizando la labor.", "base" => "86 Lit. C #2", "ut" => "100 UT", "multa" => "Bs. F 34,00"],
    ["desc" => "Presentar documentos con enmendaduras o alteraciones que afecten sustancialmente la legalidad.", "base" => "86 Lit. C #3", "ut" => "100 UT", "multa" => "Bs. F 0,00"],
    ["desc" => "Dejar de enterar en el tiempo previsto cualquier cuantía que adeude al IVSS distinta de cotizaciones.", "base" => "86 Lit. C #4", "ut" => "100 UT", "multa" => "Bs. F 45,00"],
    ["desc" => "Incumplir con la obligación de enterar las cotizaciones recaudadas (5 UT por semana hasta 52 semanas).", "base" => "88", "ut" => "5 UT x 52", "multa" => "Bs. F 0,00"]
];

// --- 2. CLASE PDF ÚNICA ---
if (!class_exists('PDF')) {
    class PDF extends FPDF {
        function Header() {
            // Cabecera Roja
            $this->SetFillColor(180, 0, 0);
            $this->Rect(0, 0, 215, 35, 'F');
            
            $this->SetFont('Arial', 'B', 22);
            $this->SetTextColor(255, 255, 255);
            $this->SetXY(0, 5);
            $this->Cell(210, 15, txt("Multas"), 0, 1, 'C');
            
            $this->SetFont('Arial', '', 16);
            $this->SetX(60);
            $this->Cell(30, 10, txt("Fecha :"), 0, 0, 'L');
            $this->SetFont('Arial', 'B', 18);
            $this->Cell(40, 10, "ene-25", 0, 0, 'L');
            
            $this->SetTextColor(255, 255, 0);
            $this->Cell(50, 10, "Bs. F 43,00", 0, 1, 'R');
            $this->Ln(10);
        }

        function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->SetTextColor(0,0,0);
            $this->Cell(0, 10, txt('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
        }
    }
}

// --- 3. CONFIGURACIÓN INICIAL ---
$pdf = new PDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetMargins(10, 10, 10);

// Nombre de Empresa
$pdf->SetY(45);
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, txt($nombre_empresa), 0, 1);

$pdf->SetFont('Arial', '', 9);
$pdf->MultiCell(0, 6, txt('Sanciones desglosadas'), 0, 'L');
$pdf->Ln(2);

// Anchos de columnas (Ajustados para sumar 190mm aprox)
$w = [85, 25, 25, 25, 30]; 

// --- 4. CUERPO - TABLA DE SANCIONES ---
foreach ($sanciones as $s) {
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    
    // Cálculo de altura dinámica para MultiCell
    $lineas = ceil(strlen(txt($s['desc'])) / 55); 
    $h = max($lineas * 5, 10); // Mínimo 10mm de alto

    // Salto de página automático si la fila no cabe
    if($y + $h > 270) {
        $pdf->AddPage();
        $y = $pdf->GetY();
        $x = $pdf->GetX();
    }

    // Dibujar Bordes y Fondos
    $pdf->Rect($x, $y, $w[0], $h);
    $pdf->Rect($x + $w[0], $y, $w[1], $h);
    $pdf->Rect($x + $w[0] + $w[1], $y, $w[2], $h);
    
    // Celda Amarilla (Multa)
    $pdf->SetFillColor(255, 255, 0);
    $pdf->Rect($x + $w[0] + $w[1] + $w[2], $y, $w[3], $h, 'DF');
    $pdf->Rect($x + $w[0] + $w[1] + $w[2] + $w[3], $y, $w[4], $h);

    // Texto de la Descripción
    $pdf->SetFont('Arial', '', 7);
    $pdf->SetXY($x + 1, $y + 1);
    $pdf->MultiCell($w[0] - 2, 4, txt($s['desc']), 0, 'L');

    // Resto de columnas
    $pdf->SetXY($x + $w[0], $y);
    $pdf->Cell($w[1], $h, txt($s['base']), 0, 0, 'C');
    $pdf->Cell($w[2], $h, txt($s['ut']), 0, 0, 'C');
    $pdf->Cell($w[3], $h, txt($s['multa']), 0, 0, 'C');
    $pdf->Cell($w[4], $h, txt($s['multa']), 0, 1, 'C');

    $pdf->SetY($y + $h);
}

// --- 5. SALIDA ---
ob_end_clean(); // Limpia cualquier warning previo para que el PDF no falle
$pdf->Output('I', 'Multas_IVSS.pdf');