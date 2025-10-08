<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CertificadoCarabineroController extends BaseDocumentController
{
    protected function getSectionTitle(): string
    {
        return 'ANTECEDENTES GENERALES';
    }

    protected function getStepTitle(): string
    {
        return 'Certificado de Carabineros.';
    }
}
