# Tarjeta de Requerimiento

**ID:** PBA-01  
**Usuario:** Administrativo, Capitán  
**Nombre del Requerimiento:** Crear Proceso Bombero Accidentado  
**Programador Responsable:** Miguel Araya, Nicolas Carmona  
**Iteración Asignada:** 1  

---

## 📝 Descripción

El sistema debe permitir iniciar el proceso de Bombero Accidentado en el momento en que un voluntario sufre un accidente durante el cumplimiento de sus funciones. Al registrar el incidente, el usuario administrativo podrá ingresar la fecha del suceso y los datos personales del bombero accidentado, tales como nombre, RUT, compañía, cargo y tipo de accidente. Esta información servirá como base para el seguimiento posterior del caso, la gestión de documentación y la tramitación de beneficios o reembolsos asociados al evento.

---

## ✅ Validaciones

- La fecha de creación del proceso debe ser inferior o igual a la fecha actual del sistema; en caso contrario, se enviará una alerta al administrativo.
- El bombero debe existir dentro del sistema.
- La ID del proceso debe ser generada después del inicio del proceso.

---

## 🔖 Estado
**Estado inicial:** Pendiente  
**Última actualización:** _(YYYY-MM-DD)_

