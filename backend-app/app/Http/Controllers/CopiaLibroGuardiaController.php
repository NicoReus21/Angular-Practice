<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CopiaLibroGuardiaController extends BaseDocumentController
{
    protected function getSectionTitle(): string
    {
        return 'REQUERIMIENTO OPERATIVO';
    }

    protected function getStepTitle(): string
    {
        return 'Incidente sin lesiones Copia del Libro de Guardia (no legalizado y solo el el registro del accidente)';
    }
}
