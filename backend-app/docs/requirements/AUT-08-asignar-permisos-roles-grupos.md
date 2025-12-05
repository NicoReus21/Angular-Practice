# Tarjeta de Requerimiento

**ID:** AUT-08  
**Usuario:** Administrativo  
**Nombre del Requerimiento:** Asignar permisos a roles y grupos  
**Programador Responsable:** Miguel Araya, Nicolas Carmona
**Iteración Asignada:** 3  

---

## ÐY"? Descripción

Administrar la relación de permisos con roles y grupos usando las tablas `rol_permission` y `group_permissions`, permitiendo asignar y revocar permisos.

---

## ƒo. Validaciones

- Permiso y rol/grupo deben existir.
- Evitar duplicados en la combinación rol-permiso o grupo-permiso.
- Registrar fecha de asignación y usuario responsable; registrar fecha de revocación al remover.

---

## ÐY"- Estado
**Estado inicial:** Validado con el cliente  
**Ultima actualizacion:** 05-12-2025
