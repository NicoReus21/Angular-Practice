<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BoletaHonorarioVisadaController extends BaseDocumentController
{
    protected function getSectionTitle(): string
    {
        return 'BOLETA DE HONORARIO VISADA';
    }

    protected function getStepTitle(): string
    {
        return 'Boletas de Honorarios Profesionales, no incluidas en factura, visadas por medico jefe del servicio.';
    }
}
