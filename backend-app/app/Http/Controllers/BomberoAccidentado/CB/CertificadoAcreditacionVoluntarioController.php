<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CertificadoAcreditacionVoluntarioController extends BaseDocumentController
{
    protected function getSectionTitle(): string
    {
        return 'DOCUMENTOS DEL CUERPO DE BOMBEROS';
    }

    protected function getStepTitle(): string
    {
        return 'Certificado Superintendente que acredite calidad voluntario';
    }
}
