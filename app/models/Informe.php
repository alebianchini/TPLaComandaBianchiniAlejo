<?php
require('fpdf/fpdf.php');

class Informe extends FPDF
{
    // Cabecera de página
    function Header()
    {
        $this->SetFont('Times', 'B', 30);
        //$this->Image('img/triangulosrecortados.png',0,0,70); //imagen(archivo, png/jpg || x,y,tamaño)
        $this->setXY(60, 25);
        $this->Cell(90, 8, 'Grizzly Corp', 0, 1, 'C', 0);
        $this->Image('images/logo.jpg', 150, 10, 50); //imagen(archivo, png/jpg || x,y,tamaño)
        $this->Ln(30);
    }

    // Pie de página
    function Footer()
    {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'B', 10);
        // Número de página
        $this->Cell(170, 10, 'Todos los derechos reservados', 0, 0, 'C', 0);
        $this->Cell(25, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    public static function crearInformePDF()
    {
        $pdf = new Informe();
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 20);
        $pdf->SetX(20);
        $pdf->SetFont('Helvetica', 'B', 15);

        $pdf->Output();
    }

}
