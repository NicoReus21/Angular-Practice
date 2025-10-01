<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DiabController extends BaseDocumentController
{
    protected function getSectionTitle(): string
    {
        return 'REQUERIMIENTO OPERATIVO';
    }

    protected function getStepTitle(): string
    {
        return 'DIAB (declaracion Individual de accidente bomberil)';
    }
}
