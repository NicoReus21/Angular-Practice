# Tarjeta de Requerimiento

**ID:** AUT-09  
**Usuario:** Administrativo, Usuario  
**Nombre del Requerimiento:** Autenticación y cierre de sesión  
**Programador Responsable:** Miguel Araya, Nicolas Carmona  
**Iteración Asignada:** 3 

---

## ÐY"? Descripción

Permitir inicio de sesión con email y contraseña, generando token de acceso (personal access token) para consumir el API. Incluir endpoint de cierre de sesión para revocar el token y limpiar sesión.

---

## ƒo. Validaciones

- Solo usuarios activos pueden autenticarse.
- Bloquear después de múltiples intentos fallidos.
- Revocar token al cerrar sesión o al desactivar al usuario.

---

## ÐY"- Estado
**Estado inicial:** Validado con el cliente  
**Ultima actualizacion:** 05-12-2025
