<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BoletaMedicamentoController extends BaseDocumentController
{
    protected function getSectionTitle(): string
    {
        return 'MEDICAMENTOS';
    }

    protected function getStepTitle(): string
    {
        return 'Boletas de Medicamentos, no incluidas en factura, visadas por medico jefe del servicio.';
    }
}
