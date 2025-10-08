<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BoletaFacturaTrasladoController extends BaseDocumentController
{
    protected function getSectionTitle(): string
    {
        return 'GASTOS DE TRASLADO Y ALIMENTACIÓN';
    }

    protected function getStepTitle(): string
    {
        return 'Boleta o factura de gastos de traslado del voluntario';
    }
}
