# Tarjeta de Requerimiento

**ID:** AUT-05  
**Usuario:** Administrativo  
**Nombre del Requerimiento:** CRUD de permisos base  
**Programador Responsable:** Por asignar  
**Iteración Asignada:** Por definir  

---

## ÐY"? Descripción

Gestionar permisos (tabla `permissions`) definiendo módulo, sección y acción (create/read/update/delete) con descripción opcional.

---

## ƒo. Validaciones

- Combinación módulo + sección + acción debe ser única.
- La acción solo puede ser create, read, update o delete.
- No eliminar permisos asociados a roles o usuarios activos sin retirar las relaciones.

---

## ÐY"- Estado
**Estado inicial:** Pendiente  
**Última actualización:** _(YYYY-MM-DD)_
