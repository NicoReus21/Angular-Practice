<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OtroGastoController extends BaseDocumentController
{
    protected function getSectionTitle(): string
    {
        return 'GASTOS DE TRASLADOS Y ALIMENTACIÓN';
    }

    protected function getStepTitle(): string
    {
        return 'Otros (Especificar)';
    }
}
