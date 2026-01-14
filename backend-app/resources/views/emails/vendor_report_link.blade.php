<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <title>Reporte Material Mayor</title>
  </head>
  <body style="font-family: Arial, sans-serif; color: #222;">
    <h2 style="margin-bottom: 8px;">Reporte de Material Mayor</h2>
    <p>Estimado proveedor,</p>
    <p>
      Se le ha solicitado completar el reporte asociado a la unidad
      <strong>{{ $car?->name ?? 'Unidad' }}</strong>
      ({{ $car?->plate ?? 'sin patente' }}).
    </p>
    <p>Complete el formulario en el siguiente enlace:</p>
    <p>
      <a href="{{ $url }}" style="color: #b71c1c;">{{ $url }}</a>
    </p>
    <p>Fecha limite: <strong>{{ $expiresAt->format('d-m-Y') }}</strong></p>
    <p>
      Por favor, adjunte la informacion solicitada y firme digitalmente donde se indique.
    </p>
    <p style="margin-top: 24px;">Gracias.</p>
  </body>
</html>
