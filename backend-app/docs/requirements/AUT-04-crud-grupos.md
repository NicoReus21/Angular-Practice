# Tarjeta de Requerimiento

**ID:** AUT-04  
**Usuario:** Administrativo  
**Nombre del Requerimiento:** CRUD de grupos  
**Programador Responsable:** Miguel Araya, Nicolas Carmona 
**Iteración Asignada:** 3 

---

## ÐY"? Descripción

Crear, editar, listar y eliminar grupos (tabla `groups`) con jerarquía opcional (grupo padre), descripción y usuario creador.

---

## ƒo. Validaciones

- Nombre obligatorio y único.
- No permitir jerarquías cíclicas; el grupo padre debe existir.
- No eliminar grupos con usuarios o subgrupos activos sin reasignarlos.

---

## ÐY"- Estado
**Estado inicial:** Validado con el cliente  
**Ultima actualizacion:** 05-12-2025
