# Tarjeta de Requerimiento

**ID:** AUT-05  
**Usuario:** Administrativo  
**Nombre del Requerimiento:** CRUD de permisos base  
**Programador Responsable:** Miguel Araya, Nicolas Carmona  
**Iteración Asignada:** 3

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
**Estado inicial:** Validado con el cliente  
**Ultima actualizacion:** 05-12-2025
