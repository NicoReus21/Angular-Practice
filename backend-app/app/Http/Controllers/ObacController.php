<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ObacController extends BaseDocumentController
{
    protected function getSectionTitle(): string
    {
        return 'REQUERIMIENTO OPERATIVO';
    }

    protected function getStepTitle(): string
    {
        return 'Informe del OBAC';
    }
}
