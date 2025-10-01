<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CopiaListaAsistenciaController extends BaseDocumentController
{
    protected function getSectionTitle(): string
    {
        return 'REQUERIMIENTO OPERATIVO';
    }

    protected function getStepTitle(): string
    {
        return 'Declaracion de testigos (si es que aplica)';
    }
}
