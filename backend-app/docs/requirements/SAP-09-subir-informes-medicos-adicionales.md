# Tarjeta de Requerimiento

**ID:** SAP-09  
**Usuario:** Capitán  
**Nombre del Requerimiento:** Subir informes médicos adicionales  
**Programador Responsable:** Miguel Araya, Nicolas Carmona  
**Iteración Asignada:** 1  

---

## 📝 Descripción

Permite subir archivos de informes médicos complementarios, recetas médicas o controles posteriores.

---

## ✅ Validaciones

- Si se sube fuera de plazo, se envía alerta al administrativo vía correo.
- El plazo “dentro del plazo” es de un máximo de 7 días desde la creación del proceso.
- Es opcional; solo se considera fuera de plazo si se sube después del plazo.
- Solo se pueden subir informes médicos adicionales después de que exista un informe médico general.
- El formato del documento debe ser .png o .pdf; cualquier otro formato será rechazado con: “El formato del documento no es soportado por el sistema, por favor suba dentro de los formatos admitidos .png o .pdf”.

---

## 🔖 Estado
**Estado inicial:** Validado con el cliente  
**Ultima actualizacion:** 05-12-2025
