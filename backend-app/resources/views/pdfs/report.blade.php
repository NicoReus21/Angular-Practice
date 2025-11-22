<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Mantenimiento</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; line-height: 1.6; }
        h1 { color: #b71c1c; } /* Color rojo de bomberos */
        .content { margin: 20px; }
        .section { margin-bottom: 20px; }
        .section-title { font-weight: bold; font-size: 1.2em; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
        .field { margin-bottom: 10px; }
        .field label { font-weight: bold; color: #333; }
        .field span { color: #555; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .signatures { margin-top: 50px; }
        .signature-box { 
            display: inline-block; 
            width: 45%; 
            margin-top: 40px; 
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 5px;
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
            <div class="field"><label>N° Chasis:</label> <span>{{ $maintenance->chassis_number ?? 'N/A' }}</span></div>
        </div>

        <div class="section">
            <div class="section-title">Descripción del Trabajo</div>
            <div class="field">
                <label>Problema Reportado:</label>
                <p><span>{{ $maintenance->reported_problem }}</span></p>
            </div>
            <div class="field">
                <label>Actividades Desarrolladas:</label>
                <p><span>{{ $maintenance->activities_detail }}</span></p>
            </div>
            <div class="field">
                <label>Trabajo Pendiente:</label>
                <p><span>{{ $maintenance->pending_work ?? 'Ninguno' }} ({{ $maintenance->pending_type ?? 'N/A' }})</span></p>
            </div>
            <div class="field">
                <label>Observaciones:</label>
                <p><span>{{ $maintenance->observations ?? 'Ninguna' }}</span></p>
            </div>
        </div>

        <div class="signatures">
            <div class="signature-box" style="float: left;">
                {{ $maintenance->inspector_signature }}<br>
                Firma Inspector Responsable
            </div>
            <div class="signature-box" style="float: right;">
                {{ $maintenance->officer_signature }}<br>
                Firma Oficial a Cargo
            </div>
        </div>
    </div>
</body>
</html>