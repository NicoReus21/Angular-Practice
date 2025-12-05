# Tarjeta de Requerimiento

**ID:** AUT-01  
**Usuario:** Administrativo  
**Nombre del Requerimiento:** Registrar usuario  
**Programador Responsable:** Miguel Araya, Nicolas Carmona  
**Iteración Asignada:** 3  

---

## ÐY"? Descripción

Crear usuarios del sistema vía API REST con los campos nombre, email y contraseña. Permitir marcar el email como verificado, forzar cambio de contraseña en el primer inicio de sesión y activar/desactivar usuarios.

---

## ƒo. Validaciones

- Email único y con formato válido.
- Contraseña mínima de 8 caracteres; almacenar encriptada.
- No permitir crear usuario desactivado sin registrar motivo.
- Registrar fecha de creación y usuario que realizó el alta.

---

## ÐY"- Estado
**Estado inicial:** Validado  
**Última actualización:** _(2025-12-05)_
