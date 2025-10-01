<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CertificadoMedicoAutorizacionExamenController extends BaseDocumentController
{
    protected function getSectionTitle(): string
    {
        return 'CERTIFICADO MÉDICO Y AUTORIZACIÓN DE EXÁMENES';
    }

    protected function getStepTitle(): string
    {
        return 'Certificado del director del servicio que autoriza exámenes, recetas, medicamentos, controles, traslados, acciones médicas y procedimientos generales...';
    }
}
