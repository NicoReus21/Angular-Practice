<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Mantenimiento</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; line-height: 1.6; font-size: 14px; }
        h1 { color: #b71c1c; text-align: center; margin-bottom: 30px; }
        .content { margin: 20px; }
        
        .section { margin-bottom: 25px; }
        .section-title { 
            font-weight: bold; 
            font-size: 1.1em; 
            color: #333;
            border-bottom: 2px solid #b71c1c; 
            margin-bottom: 10px; 
            padding-bottom: 5px; 
        }
        
        .field { margin-bottom: 8px; }
        .field label { font-weight: bold; color: #444; margin-right: 5px; }
        .field span, .field p { display: inline; color: #000; margin: 0; }
        
        .signatures { 
            margin-top: 60px; 
            width: 100%;
            height: 150px;
        }
        
        .signature-wrapper {
            width: 45%;
            text-align: center;
            display: inline-block;
            vertical-align: top;
        }

        .signature-image {
            height: 80px;
            margin-bottom: 5px;
            display: flex;
            align-items: end;
            justify-content: center;
        }

        .signature-image img {
            max-height: 80px;
            max-width: 100%;
        }

        .signature-line {
            border-top: 1px solid #000;
            padding-top: 5px;
            margin: 0 10px;
            font-weight: bold;
        }
        
        .float-right { float: right; }
        .float-left { float: left; }
        
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>
    <div class="content">
        <h1>Reporte de Servicio/Mantención</h1>

        <div class="section">
            <div class="section-title">Detalles del Servicio</div>
            <div class="field"><label>Fecha:</label> <span>{{ \Carbon\Carbon::parse($maintenance->service_date)->format('d/m/Y') }}</span></div>
            <div class="field"><label>Tipo de Servicio:</label> <span>{{ $maintenance->service_type }}</span></div>
            <div class="field"><label>Inspector a Cargo:</label> <span>{{ $maintenance->inspector_name }}</span></div>
            <div class="field"><label>Ubicación:</label> <span>{{ $maintenance->location ?? 'N/A' }}</span></div>
        </div>

        <div class="section">
            <div class="section-title">Detalles de la Unidad</div>
            <div class="field"><label>Kilometraje:</label> <span>{{ $maintenance->mileage }} km</span></div>
            <div class="field"><label>Horómetro:</label> <span>{{ $maintenance->hourmeter ?? 'N/A' }}</span></div>
            
            <!-- CORRECCIÓN: Accedemos a los datos a través de la relación 'car' -->
            <div class="field"><label>N° Chasis:</label> <span>{{ $maintenance->car->chassis_number ?? 'N/A' }}</span></div>
            <div class="field"><label>Año de Fabricación:</label> <span>{{ $maintenance->car->manufacturing_year ?? 'N/A' }}</span></div>
        </div>

        <div class="section">
            <div class="section-title">Descripción del Trabajo</div>
            
            <div class="field">
                <label>Problema Reportado:</label>
                <p>{{ $maintenance->reported_problem }}</p>
            </div>
            <br>
            <div class="field">
                <label>Actividades Desarrolladas:</label>
                <p>{{ $maintenance->activities_detail }}</p>
            </div>
            <br>
            <div class="field">
                <label>Trabajo Pendiente:</label>
                <p>{{ $maintenance->pending_work ?? 'Ninguno' }} (Tipo: {{ $maintenance->pending_type ?? 'N/A' }})</p>
            </div>
            <br>
            <div class="field">
                <label>Observaciones:</label>
                <p>{{ $maintenance->observations ?? 'Ninguna' }}</p>
            </div>
        </div>

        <div class="signatures clearfix">
            <div class="signature-wrapper float-left">
                <div class="signature-image">
                    @if($maintenance->inspector_signature)
                        <img src="{{ $maintenance->inspector_signature }}" alt="Firma Inspector">
                    @else
                        <br><br><br>
                    @endif
                </div>
                <div class="signature-line">
                    Firma Inspector Responsable
                </div>
            </div>

            <div class="signature-wrapper float-right">
                <div class="signature-image">
                    @if($maintenance->officer_signature)
                        <img src="{{ $maintenance->officer_signature }}" alt="Firma Oficial">
                    @else
                        <br><br><br>
                    @endif
                </div>
                <div class="signature-line">
                    Firma Oficial a Cargo
                </div>
            </div>
        </div>

        <!-- ANEXOS FOTOGRÁFICOS -->
        @if(isset($attachedImages) && count($attachedImages) > 0)
            <div style="page-break-before: always;"></div>
            
            <div class="section">
                <div class="section-title">Anexos Fotográficos</div>
                <br>
                <div style="text-align: center;">
                    @foreach($attachedImages as $imgData)
                        <div style="margin-bottom: 20px; border: 1px solid #ddd; padding: 5px; display: inline-block;">
                            <!-- Aquí se renderiza el base64 directamente -->
                            <img src="{{ $imgData }}" style="max-width: 100%; max-height: 400px;">
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</body>
</html>