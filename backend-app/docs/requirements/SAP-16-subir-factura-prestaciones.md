# Tarjeta de Requerimiento

**ID:** SAP-16  
**Usuario:** Capitán, Contabilidad  
**Nombre del Requerimiento:** Subir factura prestaciones  
**Programador Responsable:** Miguel Araya, Nicolas Carmona  
**Iteración Asignada:** 1  

---

## 📝 Descripción

El sistema debe permitir subir la factura correspondiente a atenciones médicas derivadas del accidente, incluyendo los valores neto, impuesto y total como parámetros separados. Estos datos se asociarán al registro del proceso de Bombero Accidentado y serán respaldados por el archivo de la factura, garantizando la trazabilidad y correcta contabilización de cada gasto relacionado con la atención del bombero.

---

## ✅ Validaciones

- El monto neto debe ser inferior al monto total.
- El monto total debe ser igual al monto neto más impuesto.
- Si se sube fuera de plazo, se envía alerta al administrativo vía correo.
- El plazo “dentro del plazo” es de un máximo de 7 días desde la creación del proceso.
- El formato del documento debe ser .png o .pdf; cualquier otro formato será rechazado con: “El formato del documento no es soportado por el sistema, por favor suba dentro de los formatos admitidos .png o .pdf”.

---

## 🔖 Estado
**Estado inicial:** Pendiente  
**Última actualización:** _(YYYY-MM-DD)_

