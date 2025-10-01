<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OtroDocumentoMedicoController extends BaseDocumentControllerBaseDocumentController
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
