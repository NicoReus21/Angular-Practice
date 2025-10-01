<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BoletaGastoAcompananteController extends BaseDocumentController
{
    protected function getSectionTitle(): string
    {
        return 'GASTOS DE TRASLADOS Y ALIMENTACIÓN';
    }

    protected function getStepTitle(): string
    {
        return 'Boleta de gastos de hospedaje y alimentación del acompañante del voluntario.';
    }
}
