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
        return 'Certificado del médico tratante que determine la incapacidad de asistir al voluntario...';
    }
}