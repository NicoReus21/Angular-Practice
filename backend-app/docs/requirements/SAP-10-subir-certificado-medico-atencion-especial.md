# Tarjeta de Requerimiento

**ID:** SAP-10  
**Usuario:** Capitán  
**Nombre del Requerimiento:** Subir certificado médico atención especial  
**Programador Responsable:** Miguel Araya, Nicolas Carmona  
**Iteración Asignada:** 1  

---

## 📝 Descripción

Permite adjuntar un certificado médico que acredite la necesidad de atención especial o tratamiento prolongado.

---

## ✅ Validaciones

- Es opcional; solo se considera fuera de plazo si se sube después del plazo.
- Si se sube fuera de plazo, se envía alerta al administrativo vía correo.
- El plazo “dentro del plazo” es de un máximo de 7 días desde la creación del proceso.
- El formato del documento debe ser .png o .pdf; cualquier otro formato será rechazado con: “El formato del documento no es soportado por el sistema, por favor suba dentro de los formatos admitidos .png o .pdf”.

---

## 🔖 Estado
**Estado inicial:** Validado con el cliente  
**Ultima actualizacion:** 05-12-2025
