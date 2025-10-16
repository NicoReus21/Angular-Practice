<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Process;
class UploadAllDocumentTest extends TestUploadBaseDocument
{

    public function setUp(): void{
        parent::setUp();
    }
    public function test_upload_reporte_flash(){

        $this->upload_file($this->process,'reporte_flash','process.upload.reporte_flash','requerimiento_operativo','Reporte Flash subido correctamente');
    }
    public function test_upload_diab(){
            $this->upload_file($this->process,'diab','process.upload.diab','requerimiento_operativo','DIAB subido correctamente');
    }
    public function test_upload_obac(){
        $this->upload_file($this->process,'obac','process.upload.obac','requerimiento_operativo','OBAC subido correctamente');
    }
    public function test_upload_copia_libro_guardia(){
        $this->upload_file($this->process,'copia_libro_guardia','process.upload.copia_libro_guia','requerimiento_operativo','Copia Libro Guardia subido correctamente');
    }
    public function test_upload_declaracion_testigo(){
        $this->upload_file($this->process,'declaracion_testigo','process.upload.declaracion_testigo','requerimiento_operativo','Declaracion Testigo subido correctamente');
    }

    public function test_upload_dau(){
        $this->upload_file($this->process,'dau','process.upload.dau','antecedente_general','DAU subido correctamente');
    }
    
}
