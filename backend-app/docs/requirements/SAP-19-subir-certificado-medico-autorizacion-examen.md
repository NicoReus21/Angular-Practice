# Tarjeta de Requerimiento

**ID:** SAP-19  
**Usuario:** Capitán  
**Nombre del Requerimiento:** Subir certificado médico autorización examen  
**Programador Responsable:** Miguel Araya, Nicolas Carmona  
**Iteración Asignada:** 1  

---

## 📝 Descripción

Permite subir un certificado que autoriza la realización de exámenes médicos específicos.

---

## ✅ Validaciones

- Es opcional; se considera fuera de plazo solo si se sube después del plazo.
- Si se sube fuera de plazo, se envía alerta al administrativo vía correo.
- El plazo “dentro del plazo” es de un máximo de 7 días desde la creación del proceso.
- El formato del documento debe ser .png o .pdf; cualquier otro formato será rechazado con: “El formato del documento no es soportado por el sistema, por favor suba dentro de los formatos admitidos .png o .pdf”.

---

## 🔖 Estado
**Estado inicial:** Validado con el cliente  
**Ultima actualizacion:** 05-12-2025
