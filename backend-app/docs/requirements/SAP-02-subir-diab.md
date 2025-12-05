# Tarjeta de Requerimiento

**ID:** SAP-02  
**Usuario:** Capitán, Bombero  
**Nombre del Requerimiento:** Subir DIAB  
**Programador Responsable:** Miguel Araya, Nicolas Carmona  
**Iteración Asignada:** 1  

---

## 📝 Descripción

El sistema debe permitir al Capitán o al Bombero responsable del caso subir el documento DIAB (Declaración Individual de Accidente Bomberil) correspondiente al siniestro, registrando además los datos oficiales asociados al evento, tales como número de declaración, fecha, hora, nivel de lesión, dirección de la emergencia y datos personales del bombero accidentado (nombre completo, RUN, edad, fecha de nacimiento, teléfono, cargo, años de servicio y número de registro en el Cuerpo de Bomberos). El documento debe quedar vinculado al proceso activo del bombero accidentado, asegurando su trazabilidad y disponibilidad para revisión por parte del área administrativa.

---

## ✅ Validaciones

- El plazo “dentro del plazo” es de un máximo de 3 días desde la creación del proceso.
- Es necesario subir este documento para pasar al paso siguiente.
- Si se sube fuera de plazo, se envía alerta al administrativo vía correo.
- Es obligatorio.
- El formato del documento debe ser .png o .pdf; cualquier otro formato será rechazado con: “El formato del documento no es soportado por el sistema, por favor suba dentro de los formatos admitidos .png o .pdf”.

---

## 🔖 Estado
**Estado inicial:** Validado con el cliente  
**Ultima actualizacion:** 05-12-2025
