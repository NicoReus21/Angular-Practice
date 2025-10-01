<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AvisoCitacionController extends BaseDocumentController
{
    protected function getSectionTitle(): string
    {
        return 'DOCUMENTOS DEL CUERPO DE BOMBEROS';
    }

    protected function getStepTitle(): string
    {
        return 'Copia Aviso de citación al acto de servicio (ACADEMIA).';
    }
}
