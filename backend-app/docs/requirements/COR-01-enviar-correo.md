# Tarjeta de Requerimiento

**ID:** COR-01  
**Usuario:** Administrativo, Capitán  
**Nombre del Requerimiento:** Enviar correo  
**Programador Responsable:** Miguel Araya, Nicolas Carmona  
**Iteración Asignada:** 1  

---

## 📝 Descripción

En caso de que algún documento sea subido fuera del plazo establecido, el sistema deberá enviar automáticamente un correo electrónico al grupo administrativo, informando la situación. El mensaje deberá incluir los siguientes datos: Identificador del proceso. Nombre del bombero accidentado. Título del documento atrasado. Fecha de subida del documento atrasado. Tiempo de atraso (en días u horas si es menos de un día). Usuario responsable de la subida del documento.

---

## ✅ Validaciones

- Si por algún motivo el correo no puede ser enviado (por error del sistema o falta de conexión), el sistema deberá registrar el fallo en un log o registro de incidencias para su posterior revisión y gestión por el área administrativa o técnica.

---

## 🔖 Estado
**Estado inicial:** Validado con el cliente  
**Ultima actualizacion:** 05-12-2025
