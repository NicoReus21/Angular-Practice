# Tarjeta de Requerimiento

**ID:** AUT-02  
**Usuario:** Administrativo  
**Nombre del Requerimiento:** Listar y filtrar usuarios  
**Programador Responsable:** Miguel Araya, Nicolas Carmona  
**Iteración Asignada:** 3 

---

## ÐY"? Descripción

Proveer endpoints para listar usuarios con paginación y filtros por nombre, email, estado, rol y grupo. Incluir detalle de roles, grupos y permisos asignados.

---

## ƒo. Validaciones

- Solo perfiles autorizados pueden consultar usuarios.
- Filtros combinables; limitar resultados por página para evitar respuestas masivas.
- Exponer campos sensibles (password hash) nunca en respuestas.

---

## ÐY"- Estado
**Estado inicial:** Validado con el cliente  
**Ultima actualizacion:** 05-12-2025
