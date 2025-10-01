<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InformeEjecutivoController extends BaseDocumentController
{
    protected function getSectionTitle(): string
    {
        return 'ANTECEDENTES GENERALES';
    }

    protected function getStepTitle(): string
    {
        return 'Informe Ejecutivo sobre el acto de servicio en que se producen lesiones, suscrito por Superintendente y Comandante.';
    }
}
