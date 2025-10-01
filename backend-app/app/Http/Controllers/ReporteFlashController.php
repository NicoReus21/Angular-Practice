<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReporteFlashController extends BaseDocumentController
{
    protected function getSectionTitle(): string
    {
        return 'REQUERIMIENTO OPERATIVO';
    }

    protected function getStepTitle(): string
    {
        return 'Reporte Flash';
    }
}
