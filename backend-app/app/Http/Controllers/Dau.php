<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CopiaListaAsistenciaController extends BaseDocumentController
{
    protected function getSectionTitle(): string
    {
        return 'ANTECEDENTES GENERALES';
    }

    protected function getStepTitle(): string
    {
        return 'DAU o variantes.';
    }
}
