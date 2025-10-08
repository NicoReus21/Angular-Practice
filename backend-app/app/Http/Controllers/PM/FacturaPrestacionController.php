<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FacturaPrestacionController extends BaseDocumentController
{
    protected function getSectionTitle(): string
    {
        return 'PRESTACIONES MEDICA';
    }

    protected function getStepTitle(): string
    {
        return 'Informe Ejecutivo sobre el acto de servicio en que se producen lesiones, suscrito por Superintendente y Comandante.';
    }
}
