<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CopiaLibroLlamadaController extends BaseDocumentController
{
    protected function getSectionTitle(): string
    {
        return 'DOCUMENTOS DEL CUERPO DE BOMBEROS';
    }

    protected function getStepTitle(): string
    {
        return 'Copia libro de guardia 3 días previos y 3 días posteriores al accidente Legalizado ante notario.';
    }
}
