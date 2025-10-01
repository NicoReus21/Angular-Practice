<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CopiaListaAsistenciaController extends BaseDocumentController
{
    protected function getSectionTitle(): string
    {
        return 'DOCUMENTOS DEL CUERPO DE BOMBEROS';
    }

    protected function getStepTitle(): string
    {
        return 'Copia Lista de Asistencia al acto de servicio específico (ACADEMIA), autorizado ante notario.';
    }
}
