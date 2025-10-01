<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CertificadoMedicoTrasladoController extends BaseDocumentController
{
    protected function getSectionTitle(): string
    {
        return 'GASTOS DE TRASLADOS Y ALIMENTACIÓN';
    }

    protected function getStepTitle(): string
    {
        return 'Certificado médico tratante que justifique la necesidad de traslado del voluntario y del medio empleado.';
    }
}
