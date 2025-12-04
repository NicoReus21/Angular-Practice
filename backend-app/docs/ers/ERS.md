# Especificación de Requerimientos de Software
_Proyecto: Sistema general de Bomberos Antofagasta_
Generado automáticamente: 2025-12-03T02:46:17.428Z

<div style="page-break-after: always;"></div>

## Tabla de contenido
- [I. Introducción](#i-introduccion)
  - [1. Usuarios](#1-usuarios)
- [II. Requerimientos](#ii-requerimientos)
  - [Módulo: Bombero Accidentado](#modulo-bombero-accidentado)
  - [Módulo: Material Mayor](#modulo-material-mayor)
  - [Módulo: Autentificación](#modulo-autentificacion)
- [III. Requerimientos no funcionales](#iii-requerimientos-no-funcionales)
- [IV. Flujo del sistema](#iv-flujo-del-sistema)

<div style="page-break-after: always;"></div>
## I. Introducción

Este documento consolida los requerimientos funcionales a partir de las Tarjetas de Requerimiento vigentes. Incluye un resumen por módulo, el detalle de cada tarjeta y un diagrama tipo mindmap en PlantUML.

### 1. Usuarios

| Usuario | Descripción |
| --- | --- |
| Administrativo | Gestiona la operación administrativa diaria, ingreso y actualización de documentos y seguimiento de plazos. |
| Usuario | Perfil genérico para consumo del sistema según permisos asignados (rol/grupo). |
| Capitán | Lidera la compañía, valida información operativa y aprueba documentación clave. |
| Encargado de Material Mayor | Responsable de la gestión de flota y equipamiento mayor, incluyendo altas, mantenimientos y documentación. |
| Contabilidad | Administra pagos, facturas y conciliación financiera asociada a Material Mayor y otros procesos. |
| Inspector | Verifica cumplimiento operativo y documental, emitiendo observaciones y requerimientos de corrección. |
| Comandancia | Supervisa la operación general, prioriza recursos y aprueba decisiones estratégicas. |
| Auditoría | Revisa trazabilidad y cumplimiento normativo de procesos y documentos. |
| Todos | Rol genérico que agrupa permisos comunes mínimos para cualquier usuario autenticado. |
| Inspector de Material Mayor | Especialista que inspecciona flota y equipos mayores para certificar su estado y uso. |
| Encargados de Material Mayor | Equipo de apoyo al encargado principal para la gestión operativa y documental de Material Mayor. |
| Dirección | Nivel directivo que define lineamientos y evalúa indicadores estratégicos. |
| Encargado | Responsable operativo designado para tareas específicas dentro de un módulo. |
| Encargado de Compañía | Responsable de coordinar recursos y documentación a nivel de compañía. |
| Bombero | Voluntario operativo que participa en emergencias y puede reportar o completar información de incidentes. |

<div style="page-break-after: always;"></div>

## II. Requerimientos

### Módulo: Bombero Accidentado

#### Tarjeta de Requerimiento — ID: COR-01

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> COR-01</td>
    <td><strong>Usuario(s):</strong> Administrativo, Capitán</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Miguel Araya, Nicolas Carmona</td>
    <td><strong>Iteración Asignada:</strong> 1</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Enviar correo</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>En caso de que algún documento sea subido fuera del plazo establecido, el sistema deberá enviar automáticamente un correo electrónico al grupo administrativo, informando la situación. El mensaje deberá incluir los siguientes datos: Identificador del proceso. Nombre del bombero accidentado. Título del documento atrasado. Fecha de subida del documento atrasado. Tiempo de atraso (en días u horas si es menos de un día). Usuario responsable de la subida del documento.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Si por algún motivo el correo no puede ser enviado (por error del sistema o falta de conexión), el sistema deberá registrar el fallo en un log o registro de incidencias para su posterior revisión y gestión por el área administrativa o técnica.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: PBA-01

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> PBA-01</td>
    <td><strong>Usuario(s):</strong> Administrativo, Capitán</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Miguel Araya, Nicolas Carmona</td>
    <td><strong>Iteración Asignada:</strong> 1</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Crear Proceso Bombero Accidentado</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>El sistema debe permitir iniciar el proceso de Bombero Accidentado en el momento en que un voluntario sufre un accidente durante el cumplimiento de sus funciones. Al registrar el incidente, el usuario administrativo podrá ingresar la fecha del suceso y los datos personales del bombero accidentado, tales como nombre, RUT, compañía, cargo y tipo de accidente. Esta información servirá como base para el seguimiento posterior del caso, la gestión de documentación y la tramitación de beneficios o reembolsos asociados al evento.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>La fecha de creación del proceso debe ser inferior o igual a la fecha actual del sistema; en caso contrario, se enviará una alerta al administrativo.</li><li>El bombero debe existir dentro del sistema.</li><li>La ID del proceso debe ser generada después del inicio del proceso.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: PBA-02

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> PBA-02</td>
    <td><strong>Usuario(s):</strong> Administrativo, Capitán</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Miguel Araya, Nicolas Carmona</td>
    <td><strong>Iteración Asignada:</strong> 1</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Listar procesos de bombero accidentado</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Si no hay procesos debe aparecer un mensaje indicando “no hay procesos de bombero accidentado para ver”.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: SAP-01

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> SAP-01</td>
    <td><strong>Usuario(s):</strong> Capitán</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Miguel Araya, Nicolas Carmona</td>
    <td><strong>Iteración Asignada:</strong> 1</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Subir reporte flash</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>El sistema debe permitir al usuario asignado subir un Reporte Flash, el cual es un resumen preliminar del accidente. Este reporte debe incluir al menos la situación inicial, condiciones del evento, ubicación, personas involucradas y gravedad estimada, y podrá ser enviado inmediatamente después del accidente para su visualización por el personal administrativo.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>El plazo “dentro del plazo” es de un máximo de 3 días desde la creación del proceso.</li><li>Es necesario subir este documento para pasar al paso siguiente.</li><li>Si se sube fuera de plazo, se envía alerta al administrativo vía correo.</li><li>El formato del documento debe ser .png o .pdf; cualquier otro formato será rechazado con: “El formato del documento no es soportado por el sistema, por favor suba dentro de los formatos admitidos .png o .pdf”.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: SAP-02

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> SAP-02</td>
    <td><strong>Usuario(s):</strong> Capitán, Bombero</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Miguel Araya, Nicolas Carmona</td>
    <td><strong>Iteración Asignada:</strong> 1</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Subir DIAB</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>El sistema debe permitir al Capitán o al Bombero responsable del caso subir el documento DIAB (Declaración Individual de Accidente Bomberil) correspondiente al siniestro, registrando además los datos oficiales asociados al evento, tales como número de declaración, fecha, hora, nivel de lesión, dirección de la emergencia y datos personales del bombero accidentado (nombre completo, RUN, edad, fecha de nacimiento, teléfono, cargo, años de servicio y número de registro en el Cuerpo de Bomberos). El documento debe quedar vinculado al proceso activo del bombero accidentado, asegurando su trazabilidad y disponibilidad para revisión por parte del área administrativa.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>El plazo “dentro del plazo” es de un máximo de 3 días desde la creación del proceso.</li><li>Es necesario subir este documento para pasar al paso siguiente.</li><li>Si se sube fuera de plazo, se envía alerta al administrativo vía correo.</li><li>Es obligatorio.</li><li>El formato del documento debe ser .png o .pdf; cualquier otro formato será rechazado con: “El formato del documento no es soportado por el sistema, por favor suba dentro de los formatos admitidos .png o .pdf”.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: SAP-03

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> SAP-03</td>
    <td><strong>Usuario(s):</strong> Capitán</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Miguel Araya, Nicolas Carmona</td>
    <td><strong>Iteración Asignada:</strong> 1</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Subir Informe OBAC</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>El sistema debe permitir que el OBAC (Oficial Bombero Accidentado) adjunte su informe técnico sobre el accidente, incluyendo causas, medidas adoptadas y observaciones.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>El plazo “dentro del plazo” es de un máximo de 7 días desde la creación del proceso.</li><li>Es obligatorio.</li><li>Si se sube fuera de plazo, se envía alerta al administrativo vía correo.</li><li>El formato del documento debe ser .png o .pdf; cualquier otro formato será rechazado con: “El formato del documento no es soportado por el sistema, por favor suba dentro de los formatos admitidos .png o .pdf”.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: SAP-04

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> SAP-04</td>
    <td><strong>Usuario(s):</strong> Capitán</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Miguel Araya, Nicolas Carmona</td>
    <td><strong>Iteración Asignada:</strong> 1</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Subir declaración de testigos</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>El sistema debe permitir subir una o más declaraciones de testigos presenciales del accidente.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Es opcional; se considera fuera de plazo solo si se sube después del plazo.</li><li>El plazo “dentro del plazo” es de un máximo de 7 días desde la creación del proceso.</li><li>El formato del documento debe ser .png o .pdf; cualquier otro formato será rechazado con: “El formato del documento no es soportado por el sistema, por favor suba dentro de los formatos admitidos .png o .pdf”.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: SAP-05

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> SAP-05</td>
    <td><strong>Usuario(s):</strong> Capitán</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Miguel Araya, Nicolas Carmona</td>
    <td><strong>Iteración Asignada:</strong> 1</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Incidente sin lesiones</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Permite registrar un incidente que no generó lesiones, pero que se desea documentar por prevención o análisis posterior.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Es opcional.</li><li>El plazo “dentro del plazo” es de un máximo de 7 días desde la creación del proceso.</li><li>El formato del documento debe ser .png o .pdf; cualquier otro formato será rechazado con: “El formato del documento no es soportado por el sistema, por favor suba dentro de los formatos admitidos .png o .pdf”.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: SAP-06

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> SAP-06</td>
    <td><strong>Usuario(s):</strong> Capitán</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Miguel Araya, Nicolas Carmona</td>
    <td><strong>Iteración Asignada:</strong> 1</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Subir certificado carabineros</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>El sistema debe permitir adjuntar el parte o certificado emitido por Carabineros relacionado con el accidente.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Es obligatorio.</li><li>Si se sube fuera de plazo, se envía alerta al administrativo vía correo.</li><li>El plazo “dentro del plazo” es de un máximo de 7 días desde la creación del proceso.</li><li>El formato del documento debe ser .png o .pdf; cualquier otro formato será rechazado con: “El formato del documento no es soportado por el sistema, por favor suba dentro de los formatos admitidos .png o .pdf”.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: SAP-07

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> SAP-07</td>
    <td><strong>Usuario(s):</strong> Capitán</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Miguel Araya, Nicolas Carmona</td>
    <td><strong>Iteración Asignada:</strong> 1</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Subir informe DAU (Dato de Atención de Urgencia)</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Si se sube fuera de plazo, se envía alerta al administrativo vía correo.</li><li>El plazo “dentro del plazo” es de un máximo de 7 días desde la creación del proceso.</li><li>El formato del documento debe ser .png o .pdf; cualquier otro formato será rechazado con: “El formato del documento no es soportado por el sistema, por favor suba dentro de los formatos admitidos .png o .pdf”.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: SAP-08

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> SAP-08</td>
    <td><strong>Usuario(s):</strong> Capitán</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Miguel Araya, Nicolas Carmona</td>
    <td><strong>Iteración Asignada:</strong> 1</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Subir informe médico</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Permite subir el informe médico inicial emitido por el centro asistencial que atendió al bombero.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Si se sube fuera de plazo, se envía alerta al administrativo vía correo.</li><li>El plazo “dentro del plazo” es de un máximo de 7 días desde la creación del proceso.</li><li>Es obligatorio.</li><li>El formato del documento debe ser .png o .pdf; cualquier otro formato será rechazado con: “El formato del documento no es soportado por el sistema, por favor suba dentro de los formatos admitidos .png o .pdf”.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: SAP-09

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> SAP-09</td>
    <td><strong>Usuario(s):</strong> Capitán</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Miguel Araya, Nicolas Carmona</td>
    <td><strong>Iteración Asignada:</strong> 1</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Subir informes médicos adicionales</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Permite subir archivos de informes médicos complementarios, recetas médicas o controles posteriores.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Si se sube fuera de plazo, se envía alerta al administrativo vía correo.</li><li>El plazo “dentro del plazo” es de un máximo de 7 días desde la creación del proceso.</li><li>Es opcional; solo se considera fuera de plazo si se sube después del plazo.</li><li>Solo se pueden subir informes médicos adicionales después de que exista un informe médico general.</li><li>El formato del documento debe ser .png o .pdf; cualquier otro formato será rechazado con: “El formato del documento no es soportado por el sistema, por favor suba dentro de los formatos admitidos .png o .pdf”.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: SAP-10

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> SAP-10</td>
    <td><strong>Usuario(s):</strong> Capitán</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Miguel Araya, Nicolas Carmona</td>
    <td><strong>Iteración Asignada:</strong> 1</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Subir certificado médico atención especial</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Permite adjuntar un certificado médico que acredite la necesidad de atención especial o tratamiento prolongado.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Es opcional; solo se considera fuera de plazo si se sube después del plazo.</li><li>Si se sube fuera de plazo, se envía alerta al administrativo vía correo.</li><li>El plazo “dentro del plazo” es de un máximo de 7 días desde la creación del proceso.</li><li>El formato del documento debe ser .png o .pdf; cualquier otro formato será rechazado con: “El formato del documento no es soportado por el sistema, por favor suba dentro de los formatos admitidos .png o .pdf”.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: SAP-11

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> SAP-11</td>
    <td><strong>Usuario(s):</strong> Capitán</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Miguel Araya, Nicolas Carmona</td>
    <td><strong>Iteración Asignada:</strong> 1</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Subir certificado acreditación voluntario</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Permite subir documento que valida la condición de voluntario del bombero accidentado.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Es obligatorio para continuar con el proceso.</li><li>Si se sube fuera de plazo, se envía alerta al administrativo vía correo.</li><li>El plazo “dentro del plazo” es de un máximo de 7 días desde la creación del proceso.</li><li>El formato del documento debe ser .png o .pdf; cualquier otro formato será rechazado con: “El formato del documento no es soportado por el sistema, por favor suba dentro de los formatos admitidos .png o .pdf”.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: SAP-12

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> SAP-12</td>
    <td><strong>Usuario(s):</strong> Capitán</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Miguel Araya, Nicolas Carmona</td>
    <td><strong>Iteración Asignada:</strong> 1</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Subir copia libro llamada</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Permite subir archivo del registro de llamadas del cuartel donde se activó el servicio.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Es obligatorio para continuar con el proceso.</li><li>Si se sube fuera de plazo, se envía alerta al administrativo vía correo.</li><li>El plazo “dentro del plazo” es de un máximo de 7 días desde la creación del proceso.</li><li>El formato del documento debe ser .png o .pdf; cualquier otro formato será rechazado con: “El formato del documento no es soportado por el sistema, por favor suba dentro de los formatos admitidos .png o .pdf”.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: SAP-13

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> SAP-13</td>
    <td><strong>Usuario(s):</strong> Capitán</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Miguel Araya, Nicolas Carmona</td>
    <td><strong>Iteración Asignada:</strong> 1</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Subir copia aviso citación</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Permite subir archivo de la copia de la citación al servicio que originó el accidente.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Es obligatorio para continuar con el proceso.</li><li>Si se sube fuera de plazo, se envía alerta al administrativo vía correo.</li><li>El plazo “dentro del plazo” es de un máximo de 7 días desde la creación del proceso.</li><li>El formato del documento debe ser .png o .pdf; cualquier otro formato será rechazado con: “El formato del documento no es soportado por el sistema, por favor suba dentro de los formatos admitidos .png o .pdf”.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: SAP-14

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> SAP-14</td>
    <td><strong>Usuario(s):</strong> Capitán</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Miguel Araya, Nicolas Carmona</td>
    <td><strong>Iteración Asignada:</strong> 1</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Subir copia libro asistencia</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Permite subir archivo que contiene el registro de asistencia al servicio, firmado por los participantes.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Es obligatorio.</li><li>Si se sube fuera de plazo, se envía alerta al administrativo vía correo.</li><li>El plazo “dentro del plazo” es de un máximo de 7 días desde la creación del proceso.</li><li>El formato del documento debe ser .png o .pdf; cualquier otro formato será rechazado con: “El formato del documento no es soportado por el sistema, por favor suba dentro de los formatos admitidos .png o .pdf”.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: SAP-15

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> SAP-15</td>
    <td><strong>Usuario(s):</strong> Capitán</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Miguel Araya, Nicolas Carmona</td>
    <td><strong>Iteración Asignada:</strong> 1</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Subir informe ejecutivo</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Permite a la Comandancia adjuntar el informe ejecutivo con el resumen del accidente y la resolución administrativa.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Es obligatorio.</li><li>Si se sube fuera de plazo, se envía alerta al administrativo vía correo.</li><li>El plazo “dentro del plazo” es de un máximo de 7 días desde la creación del proceso.</li><li>El formato del documento debe ser .png o .pdf; cualquier otro formato será rechazado con: “El formato del documento no es soportado por el sistema, por favor suba dentro de los formatos admitidos .png o .pdf”.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: SAP-16

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> SAP-16</td>
    <td><strong>Usuario(s):</strong> Capitán, Contabilidad</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Miguel Araya, Nicolas Carmona</td>
    <td><strong>Iteración Asignada:</strong> 1</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Subir factura prestaciones</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>El sistema debe permitir subir la factura correspondiente a atenciones médicas derivadas del accidente, incluyendo los valores neto, impuesto y total como parámetros separados. Estos datos se asociarán al registro del proceso de Bombero Accidentado y serán respaldados por el archivo de la factura, garantizando la trazabilidad y correcta contabilización de cada gasto relacionado con la atención del bombero.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>El monto neto debe ser inferior al monto total.</li><li>El monto total debe ser igual al monto neto más impuesto.</li><li>Si se sube fuera de plazo, se envía alerta al administrativo vía correo.</li><li>El plazo “dentro del plazo” es de un máximo de 7 días desde la creación del proceso.</li><li>El formato del documento debe ser .png o .pdf; cualquier otro formato será rechazado con: “El formato del documento no es soportado por el sistema, por favor suba dentro de los formatos admitidos .png o .pdf”.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: SAP-17

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> SAP-17</td>
    <td><strong>Usuario(s):</strong> Capitán</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Miguel Araya, Nicolas Carmona</td>
    <td><strong>Iteración Asignada:</strong> 1</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Subir boleta honorarios visada</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Permite subir la boleta de honorarios visada por el área correspondiente a la prestación de servicios.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Es opcional; se considera fuera de plazo solo si se sube después del plazo.</li><li>Si se sube fuera de plazo, se envía alerta al administrativo vía correo.</li><li>El plazo “dentro del plazo” es de un máximo de 7 días desde la creación del proceso.</li><li>El formato del documento debe ser .png o .pdf; cualquier otro formato será rechazado con: “El formato del documento no es soportado por el sistema, por favor suba dentro de los formatos admitidos .png o .pdf”.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: SAP-18

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> SAP-18</td>
    <td><strong>Usuario(s):</strong> Capitán</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Miguel Araya, Nicolas Carmona</td>
    <td><strong>Iteración Asignada:</strong> 1</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Subir boleta medicamentos</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Permite subir boletas de compra de medicamentos utilizados en el tratamiento.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Es opcional; se considera fuera de plazo solo si se sube después del plazo.</li><li>Si se sube fuera de plazo, se envía alerta al administrativo vía correo.</li><li>El plazo “dentro del plazo” es de un máximo de 7 días desde la creación del proceso.</li><li>El formato del documento debe ser .png o .pdf; cualquier otro formato será rechazado con: “El formato del documento no es soportado por el sistema, por favor suba dentro de los formatos admitidos .png o .pdf”.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: SAP-19

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> SAP-19</td>
    <td><strong>Usuario(s):</strong> Capitán</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Miguel Araya, Nicolas Carmona</td>
    <td><strong>Iteración Asignada:</strong> 1</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Subir certificado médico autorización examen</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Permite subir un certificado que autoriza la realización de exámenes médicos específicos.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Es opcional; se considera fuera de plazo solo si se sube después del plazo.</li><li>Si se sube fuera de plazo, se envía alerta al administrativo vía correo.</li><li>El plazo “dentro del plazo” es de un máximo de 7 días desde la creación del proceso.</li><li>El formato del documento debe ser .png o .pdf; cualquier otro formato será rechazado con: “El formato del documento no es soportado por el sistema, por favor suba dentro de los formatos admitidos .png o .pdf”.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: SAP-20

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> SAP-20</td>
    <td><strong>Usuario(s):</strong> Capitán</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Miguel Araya, Nicolas Carmona</td>
    <td><strong>Iteración Asignada:</strong> 1</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Subir boleta factura traslado</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Permite subir documentos de respaldo por traslados médicos o transporte a centros asistenciales.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Es opcional; se considera fuera de plazo solo si se sube después del plazo.</li><li>Si se sube fuera de plazo, se envía alerta al administrativo vía correo.</li><li>El plazo “dentro del plazo” es de un máximo de 7 días desde la creación del proceso.</li><li>El formato del documento debe ser .png o .pdf; cualquier otro formato será rechazado con: “El formato del documento no es soportado por el sistema, por favor suba dentro de los formatos admitidos .png o .pdf”.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: SAP-21

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> SAP-21</td>
    <td><strong>Usuario(s):</strong> Capitán</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Miguel Araya, Nicolas Carmona</td>
    <td><strong>Iteración Asignada:</strong> 1</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Subir certificado médico traslado</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Permite subir un certificado que justifique la necesidad del traslado del bombero a otro centro asistencial.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Es opcional; se considera fuera de plazo solo si se sube después del plazo.</li><li>Si se sube fuera de plazo, se envía alerta al administrativo vía correo.</li><li>El plazo “dentro del plazo” es de un máximo de 7 días desde la creación del proceso.</li><li>El formato del documento debe ser .png o .pdf; cualquier otro formato será rechazado con: “El formato del documento no es soportado por el sistema, por favor suba dentro de los formatos admitidos .png o .pdf”.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: SAP-22

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> SAP-22</td>
    <td><strong>Usuario(s):</strong> Capitán</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Miguel Araya, Nicolas Carmona</td>
    <td><strong>Iteración Asignada:</strong> 1</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Subir boleta gastos acompañante</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Permite registrar boletas de gastos incurridos por acompañante del bombero hospitalizado.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Es opcional; se considera fuera de plazo solo si se sube después del plazo.</li><li>Si se sube fuera de plazo, se envía alerta al administrativo vía correo.</li><li>El plazo “dentro del plazo” es de un máximo de 7 días desde la creación del proceso.</li><li>El formato del documento debe ser .png o .pdf; cualquier otro formato será rechazado con: “El formato del documento no es soportado por el sistema, por favor suba dentro de los formatos admitidos .png o .pdf”.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: SAP-23

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> SAP-23</td>
    <td><strong>Usuario(s):</strong> Capitán</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Miguel Araya, Nicolas Carmona</td>
    <td><strong>Iteración Asignada:</strong> 1</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Subir certificado médico incapacidad</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Permite subir el certificado que acredite la incapacidad temporal o permanente del bombero.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Es opcional; se considera fuera de plazo solo si se sube después del plazo.</li><li>Si se sube fuera de plazo, se envía alerta al administrativo vía correo.</li><li>El plazo “dentro del plazo” es de un máximo de 7 días desde la creación del proceso.</li><li>El formato del documento debe ser .png o .pdf; cualquier otro formato será rechazado con: “El formato del documento no es soportado por el sistema, por favor suba dentro de los formatos admitidos .png o .pdf”.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: SAP-24

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> SAP-24</td>
    <td><strong>Usuario(s):</strong> Capitán</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Miguel Araya, Nicolas Carmona</td>
    <td><strong>Iteración Asignada:</strong> 1</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Subir boleta alimentación acompañante</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Permite registrar gastos de alimentación del acompañante durante la hospitalización del bombero.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Es opcional; se considera fuera de plazo solo si se sube después del plazo.</li><li>Si se sube fuera de plazo, se envía alerta al administrativo vía correo.</li><li>El plazo “dentro del plazo” es de un máximo de 7 días desde la creación del proceso.</li><li>El formato del documento debe ser .png o .pdf; cualquier otro formato será rechazado con: “El formato del documento no es soportado por el sistema, por favor suba dentro de los formatos admitidos .png o .pdf”.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: SAP-25

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> SAP-25</td>
    <td><strong>Usuario(s):</strong> Capitán</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Miguel Araya, Nicolas Carmona</td>
    <td><strong>Iteración Asignada:</strong> 1</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Subir otros gastos</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Permite subir documentos de respaldo de otros gastos médicos o logísticos no contemplados en las categorías anteriores.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Es opcional; se considera fuera de plazo solo si se sube después del plazo.</li><li>El plazo “dentro del plazo” es de un máximo de 7 días desde la creación del proceso.</li><li>Si se sube fuera de plazo, se envía alerta al administrativo vía correo.</li><li>El formato del documento debe ser .png o .pdf; cualquier otro formato será rechazado con: “El formato del documento no es soportado por el sistema, por favor suba dentro de los formatos admitidos .png o .pdf”.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>


### Módulo: Material Mayor

#### Tarjeta de Requerimiento — ID: MM-01

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> MM-01</td>
    <td><strong>Usuario(s):</strong> Administrativo, Encargado de Material Mayor</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Por asignar</td>
    <td><strong>Iteración Asignada:</strong> Por definir</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Adjuntar documento de orden de pago con expiración</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Adjuntar documento de Orden de Pago al registro de Material Mayor con una fecha de expiración de 1 mes desde su emisión. Incluir notificaciones de aviso previas a la expiración para gestionar renovaciones o acciones pendientes.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>La fecha de expiración se calcula a 1 mes desde la fecha del documento.</li><li>No se permiten fechas de expiración en el pasado.</li><li>Enviar notificación de aviso antes de expirar (p. ej. 7 días y 1 día).</li><li>Formatos permitidos del documento: .pdf, .png.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: MM-02

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> MM-02</td>
    <td><strong>Usuario(s):</strong> Contabilidad, Administrativo</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Por asignar</td>
    <td><strong>Iteración Asignada:</strong> Por definir</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Pago a proveedores</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Registrar y gestionar pagos a proveedores vinculados a Material Mayor, asociando facturas, órdenes de compra y órdenes de pago al proveedor correspondiente.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Campos obligatorios: proveedor, documento asociado, fecha, monto total.</li><li>Validar que el monto total sea mayor a 0.</li><li>Formatos permitidos de respaldo: .pdf, .png.</li><li>Registrar intento fallido de notificación por correo en caso de error de envío.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: MM-03

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> MM-03</td>
    <td><strong>Usuario(s):</strong> Encargado de Material Mayor, Inspector</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Por asignar</td>
    <td><strong>Iteración Asignada:</strong> Por definir</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Cambiar tipo de estado (botón)</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Agregar un botón para cambiar el estado de un registro de Material Mayor (p. ej., Activo, En mantención, Urgente, Dado de baja, Pendiente).</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Solo perfiles autorizados pueden cambiar el estado.</li><li>Registrar histórico de cambios (usuario, fecha, estado anterior → nuevo).</li><li>No permitir estado nulo.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: MM-04

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> MM-04</td>
    <td><strong>Usuario(s):</strong> Comandancia, Encargado de Material Mayor</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Por asignar</td>
    <td><strong>Iteración Asignada:</strong> Por definir</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Carros de Comandancia</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Incluir en Material Mayor los vehículos de Comandancia (camiones, camionetas, carros de arrastre, moto de agua, ATB, BT) con su ficha técnica y trazabilidad.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Campos mínimos: marca, patente, compañía, número de chasis.</li><li>No permitir eliminación física; usar baja lógica manteniendo histórico.</li><li>Filtros por tipo de vehículo y compañía.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: MM-05

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> MM-05</td>
    <td><strong>Usuario(s):</strong> Encargado de Material Mayor, Auditoría</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Por asignar</td>
    <td><strong>Iteración Asignada:</strong> Por definir</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Registro histórico de Material Mayor (no eliminar)</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Mantener un registro histórico inalterable de Material Mayor: altas, bajas, cambios de estado y documentos asociados.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>No eliminación física de registros; aplicar baja lógica con motivo y fecha.</li><li>Auditoría de cambios (quién, cuándo, qué).</li><li>Exportable a Excel/CSV.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: MM-06

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> MM-06</td>
    <td><strong>Usuario(s):</strong> Administrativo, Contabilidad</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Por asignar</td>
    <td><strong>Iteración Asignada:</strong> Por definir</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Alertas para facturas, órdenes de pago y compra</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Generar alertas con tiempo para vencimientos de facturas, órdenes de pago y órdenes de compra.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Configurar días de antelación (p. ej., 7/3/1 días).</li><li>Enviar correo al grupo administrativo; registrar fallos de envío.</li><li>Mostrar alertas en la interfaz (banner o popup).</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: MM-07

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> MM-07</td>
    <td><strong>Usuario(s):</strong> Contabilidad, Encargado de Material Mayor</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Por asignar</td>
    <td><strong>Iteración Asignada:</strong> Por definir</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Subir orden de compra, registrar gasto y presupuesto</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Agregar apartado para subir órdenes de compra y registrar el gasto asociado. Permitir consolidar por compañía y global, mostrando porcentaje de gasto respecto del presupuesto mensual y anual.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Campos obligatorios: compañía, fecha, monto neto, impuesto, total.</li><li>total = neto + impuesto; neto < total.</li><li>Reportes por mes y año; filtros por compañía.</li><li>Formatos permitidos: .pdf, .png.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: MM-08

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> MM-08</td>
    <td><strong>Usuario(s):</strong> Todos</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Por asignar</td>
    <td><strong>Iteración Asignada:</strong> Por definir</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Filtros de búsqueda por compañía y fecha</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Incluir filtros de búsqueda por compañía, por fecha y otros criterios relevantes en Material Mayor.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Permitir combinar filtros (compañía + rango de fechas + estado).</li><li>Paginación y ordenamiento por fecha y compañía.</li><li>Exportar resultados filtrados.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: MM-09

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> MM-09</td>
    <td><strong>Usuario(s):</strong> Encargado de Material Mayor</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Por asignar</td>
    <td><strong>Iteración Asignada:</strong> Por definir</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Integración con MoreApp para formularios</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Registrar que se utiliza MoreApp para la creación de formularios y evaluar integración (importación de datos o enlaces a formularios) dentro del flujo de Material Mayor.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Configuración de credenciales o enlaces de acceso.</li><li>Verificar trazabilidad entre registro interno y formulario MoreApp.</li><li>Manejo de errores y registro de incidencias de integración.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: MM-10

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> MM-10</td>
    <td><strong>Usuario(s):</strong> Administrativo, Contabilidad</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Por asignar</td>
    <td><strong>Iteración Asignada:</strong> Por definir</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Generar reporte por correo y por proveedor</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Generar reportes automáticos por correo e informes por proveedor cada vez que exista trabajo asociado (servicio o mantenimiento).</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Incluir: identificador, proveedor, vehículo, montos, fecha, estado.</li><li>Registrar fallo de envío en log ante error de correo.</li><li>Permitir exportar el mismo reporte a PDF/Excel.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: MM-11

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> MM-11</td>
    <td><strong>Usuario(s):</strong> Inspector, Encargado de Material Mayor</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Por asignar</td>
    <td><strong>Iteración Asignada:</strong> Por definir</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Checklist con tiempos y alertas</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Incorporar checklist con fechas objetivo y alertas tipo popup para actividades de Material Mayor.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Definir vencimientos por ítem; generar alertas al acercarse el plazo.</li><li>No permitir cierre del proceso si checklist crítico queda incompleto.</li><li>Registrar fecha de cumplimiento por ítem.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: MM-12

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> MM-12</td>
    <td><strong>Usuario(s):</strong> Inspector, Encargado de Material Mayor</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Por asignar</td>
    <td><strong>Iteración Asignada:</strong> Por definir</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Reporte de servicio/mantención (borrador y PDF final)</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Crear formulario de reporte de servicio/mantención editable con registro de última modificación y dos acciones: guardar como borrador y generar documento final en PDF que se envía por correo.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>No permitir generar documento final si faltan campos obligatorios.</li><li>Registrar usuario y fecha de última modificación.</li><li>Enviar PDF al correo configurado; registrar fallos de envío.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: MM-13

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> MM-13</td>
    <td><strong>Usuario(s):</strong> Inspector, Encargado de Material Mayor</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Por asignar</td>
    <td><strong>Iteración Asignada:</strong> Por definir</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Campos de reporte de servicio/mantención</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Incorporar los campos del reporte: Marca, Patente, Compañía, Cabina, Filtros (código), Horómetro, Kilometraje, Tipo de servicio, Inspector a cargo, Ubicación del equipo, Fecha de realización, Problema reportado, Detalle de actividades, Trabajo pendiente, Observaciones complementarias, Firma inspector responsable, Firma oficial a cargo, Número de chasis, Anexo información del carro.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Campos obligatorios mínimos: Marca, Patente, Compañía, Número de chasis, Tipo de servicio, Fecha de realización.</li><li>Validar Horómetro y Kilometraje como numéricos no negativos.</li><li>La fecha de realización no puede ser futura.</li><li>Requerir firmas para documento final.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: MM-14

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> MM-14</td>
    <td><strong>Usuario(s):</strong> Inspector, Encargado de Material Mayor</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Por asignar</td>
    <td><strong>Iteración Asignada:</strong> Por definir</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Subir documentos finales (imágenes, PDF)</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Permitir subir documentos finales vinculados al servicio/mantención en formatos de imagen o PDF.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Formatos permitidos: .pdf, .png, .jpg, .jpeg.</li><li>Asociar cada documento al registro correspondiente.</li><li>Validar tamaño máximo por archivo (p. ej. 10 MB).</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: MM-15

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> MM-15</td>
    <td><strong>Usuario(s):</strong> Encargado de Material Mayor</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Por asignar</td>
    <td><strong>Iteración Asignada:</strong> Por definir</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Definir tipo de pendiente</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Agregar campo "Tipo de pendiente" para clasificar acciones pendientes dentro del flujo de Material Mayor.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Catálogo de tipos configurable (p. ej. repuesto, presupuesto, agenda, aprobación).</li><li>No permitir cerrar como completado si existe pendiente sin resolver.</li><li>Registrar cambios de tipo y responsable asignado.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: MM-16

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> MM-16</td>
    <td><strong>Usuario(s):</strong> Inspector de Material Mayor, Encargados de Material Mayor</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Por asignar</td>
    <td><strong>Iteración Asignada:</strong> Por definir</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Roles y permisos de Material Mayor</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Definir roles: Inspector (edición de reportes y estados) y Encargado de Material Mayor (visualización sin edición, con acceso a información de todas las compañías si aplica).</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Aplicar control de acceso por rol en todas las acciones.</li><li>Registrar auditoría de visualizaciones sensibles si corresponde.</li><li>Los Encargados no pueden editar, solo visualizar.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: MM-17

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> MM-17</td>
    <td><strong>Usuario(s):</strong> Dirección, Contabilidad, Encargado</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Por asignar</td>
    <td><strong>Iteración Asignada:</strong> Por definir</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Estadísticas de gastos y dashboard</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Generar estadísticas sobre gastos de reparación y mantenimiento: gasto mensual, presupuesto vs gasto, comparativos por compañía; exportar a Excel y visualizar en dashboard (torta, barras).</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Definir rango temporal (mes, año, personalizado).</li><li>Validar consistencia de moneda y totales.</li><li>Exportación a Excel debe reflejar filtros aplicados.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: MM-18

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> MM-18</td>
    <td><strong>Usuario(s):</strong> Dirección, Contabilidad</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Por asignar</td>
    <td><strong>Iteración Asignada:</strong> Por definir</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Historial de gastos por compañía</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Generar historial de gastos para comparar qué compañía gasta más, con detalle por periodo y categoría.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Filtros por compañía y periodo.</li><li>Totales y promedios por compañía.</li><li>Indicadores top/bottom de gasto.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: MM-19

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> MM-19</td>
    <td><strong>Usuario(s):</strong> Comandancia, Encargado</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Por asignar</td>
    <td><strong>Iteración Asignada:</strong> Por definir</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Estado urgente para mantención</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Agregar opción de marcar un registro como "Urgente" para priorizar su mantención; incluir vista de conteo total de vehículos y urgentemente marcados.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Solo roles autorizados pueden marcar/desmarcar urgencias.</li><li>Generar alerta inmediata a responsables.</li><li>Registrar histórico de urgencias.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: MM-20

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> MM-20</td>
    <td><strong>Usuario(s):</strong> Encargado de Compañía</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Por asignar</td>
    <td><strong>Iteración Asignada:</strong> Por definir</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Reporte semanal de horómetro</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>El encargado de compañía debe informar semanalmente el registro de horómetro de los equipos, con recordatorios automáticos.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Recordatorio semanal automático a encargados.</li><li>Validar horómetro como numérico no negativo.</li><li>Alertar por correo y en interfaz cuando no se reporte dentro del plazo.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>


### Módulo: Autentificación

#### Tarjeta de Requerimiento — ID: AUT-01

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> AUT-01</td>
    <td><strong>Usuario(s):</strong> Administrativo</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Por asignar</td>
    <td><strong>Iteración Asignada:</strong> Por definir</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Registrar usuario</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Crear usuarios del sistema vía API REST con los campos nombre, email y contraseña. Permitir marcar el email como verificado, forzar cambio de contraseña en el primer inicio de sesión y activar/desactivar usuarios.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Email único y con formato válido.</li><li>Contraseña mínima de 8 caracteres; almacenar encriptada.</li><li>No permitir crear usuario desactivado sin registrar motivo.</li><li>Registrar fecha de creación y usuario que realizó el alta.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: AUT-02

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> AUT-02</td>
    <td><strong>Usuario(s):</strong> Administrativo</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Por asignar</td>
    <td><strong>Iteración Asignada:</strong> Por definir</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Listar y filtrar usuarios</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Proveer endpoints para listar usuarios con paginación y filtros por nombre, email, estado, rol y grupo. Incluir detalle de roles, grupos y permisos asignados.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Solo perfiles autorizados pueden consultar usuarios.</li><li>Filtros combinables; limitar resultados por página para evitar respuestas masivas.</li><li>Exponer campos sensibles (password hash) nunca en respuestas.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: AUT-03

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> AUT-03</td>
    <td><strong>Usuario(s):</strong> Administrativo</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Por asignar</td>
    <td><strong>Iteración Asignada:</strong> Por definir</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> CRUD de roles</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Crear, editar, listar y eliminar roles (tabla `rols`) con campos nombre y descripción. Registrar el usuario que creó o modificó cada rol.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Nombre de rol obligatorio y único.</li><li>No permitir eliminar roles que tengan usuarios activos asignados.</li><li>Mantener histórico de creación/actualización.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: AUT-04

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> AUT-04</td>
    <td><strong>Usuario(s):</strong> Administrativo</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Por asignar</td>
    <td><strong>Iteración Asignada:</strong> Por definir</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> CRUD de grupos</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Crear, editar, listar y eliminar grupos (tabla `groups`) con jerarquía opcional (grupo padre), descripción y usuario creador.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Nombre obligatorio y único.</li><li>No permitir jerarquías cíclicas; el grupo padre debe existir.</li><li>No eliminar grupos con usuarios o subgrupos activos sin reasignarlos.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: AUT-05

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> AUT-05</td>
    <td><strong>Usuario(s):</strong> Administrativo</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Por asignar</td>
    <td><strong>Iteración Asignada:</strong> Por definir</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> CRUD de permisos base</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Gestionar permisos (tabla `permissions`) definiendo módulo, sección y acción (create/read/update/delete) con descripción opcional.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Combinación módulo + sección + acción debe ser única.</li><li>La acción solo puede ser create, read, update o delete.</li><li>No eliminar permisos asociados a roles o usuarios activos sin retirar las relaciones.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: AUT-06

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> AUT-06</td>
    <td><strong>Usuario(s):</strong> Administrativo</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Por asignar</td>
    <td><strong>Iteración Asignada:</strong> Por definir</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Asignar roles a usuarios</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Asignar y revocar roles a usuarios usando la tabla `user_rols`, registrando fechas de asignación y remoción y el usuario que ejecuta el cambio.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Usuario y rol deben existir y estar activos.</li><li>No permitir duplicar un rol activo en el mismo usuario.</li><li>Registrar historial de altas y bajas de roles por usuario.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: AUT-07

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> AUT-07</td>
    <td><strong>Usuario(s):</strong> Administrativo</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Por asignar</td>
    <td><strong>Iteración Asignada:</strong> Por definir</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Asignar usuarios a grupos</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Gestionar la membresía de usuarios en grupos mediante la tabla `user_groups`, permitiendo asignar y remover usuarios con fechas de vigencia.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Usuario y grupo deben existir.</li><li>No permitir asignar un mismo usuario dos veces al mismo grupo sin cerrar la asignación vigente.</li><li>Registrar asignación, remoción y usuario que efectuó la acción.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: AUT-08

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> AUT-08</td>
    <td><strong>Usuario(s):</strong> Administrativo</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Por asignar</td>
    <td><strong>Iteración Asignada:</strong> Por definir</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Asignar permisos a roles y grupos</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Administrar la relación de permisos con roles y grupos usando las tablas `rol_permission` y `group_permissions`, permitiendo asignar y revocar permisos.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Permiso y rol/grupo deben existir.</li><li>Evitar duplicados en la combinación rol-permiso o grupo-permiso.</li><li>Registrar fecha de asignación y usuario responsable; registrar fecha de revocación al remover.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: AUT-09

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> AUT-09</td>
    <td><strong>Usuario(s):</strong> Administrativo, Usuario</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Por asignar</td>
    <td><strong>Iteración Asignada:</strong> Por definir</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Autenticación y cierre de sesión</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Permitir inicio de sesión con email y contraseña, generando token de acceso (personal access token) para consumir el API. Incluir endpoint de cierre de sesión para revocar el token y limpiar sesión.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>Solo usuarios activos pueden autenticarse.</li><li>Bloquear después de múltiples intentos fallidos.</li><li>Revocar token al cerrar sesión o al desactivar al usuario.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

#### Tarjeta de Requerimiento — ID: AUT-10

<table>
  <tr>
    <th colspan="2">Tarjeta de Requerimiento</th>
  </tr>
  <tr>
    <td><strong>ID:</strong> AUT-10</td>
    <td><strong>Usuario(s):</strong> Usuario</td>
  </tr>
  <tr>
    <td><strong>Programador Responsable:</strong> Por asignar</td>
    <td><strong>Iteración Asignada:</strong> Por definir</td>
  </tr>
  <tr>
    <td><strong>Estado:</strong> Pendiente</td>
    <td><strong>Última actualización:</strong> N/D</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Nombre del Requerimiento:</strong> Recuperar contraseña</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Descripción:</strong><br>Implementar recuperación de contraseña enviando token de restablecimiento al email registrado y permitiendo definir una nueva contraseña mediante el token.</td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validaciones:</strong><br><ul><li>El email debe existir en el sistema.</li><li>El token tiene vigencia máxima (p. ej. 60 minutos) y se invalida al usarlo.</li><li>La nueva contraseña debe cumplir las políticas vigentes.</li></ul></td>
  </tr>
</table>

<div style="page-break-after: always;"></div>

## III. Requerimientos no funcionales

- **RNF-01 – Seguridad:** Pendiente de documentar.
- **RNF-02 – Velocidad:** Pendiente de documentar.
- **RNF-03 – Disponibilidad:** Pendiente de documentar.
- **RNF-04 – Usabilidad:** Pendiente de documentar.
- **RNF-05 – Mantenibilidad:** Pendiente de documentar.
- **RNF-06 – Compatibilidad:** Pendiente de documentar.
- **RNF-07 – Experiencia de Usuario:** Pendiente de documentar.

## IV. Flujo del sistema

El diagrama de relación de requerimientos se encuentra en `docs/ers/ERS.puml` y se renderiza a PNG como `docs/ers/ERS.png`.
