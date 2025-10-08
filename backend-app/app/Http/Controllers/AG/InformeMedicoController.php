<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InformeMedicoController extends BaseDocumentController
{
    protected function getSectionTitle(): string
    {
        return 'ANTECEDENTES GENERALES';
    }

    protected function getStepTitle(): string
    {
        return 'Otros Documentos de caracter Médico adicional';
    }
}
