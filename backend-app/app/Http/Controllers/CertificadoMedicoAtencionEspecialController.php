<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CertificadoMedicoAtencionEspecialController extends BaseDocumentController
{
    protected function getSectionTitle(): string
    {
        return 'ANTECEDENTES GENERALES';
    }

    protected function getStepTitle(): string
    {
        return 'Orden de atención médica';
    }
}
