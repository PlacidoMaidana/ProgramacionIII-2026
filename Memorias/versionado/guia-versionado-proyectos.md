# Guía de Versionado de Proyectos — Programación III 2026

> **Materia:** Programación III  
> **Docente:** Placido Maidana  
> **Repositorio base:** https://github.com/PlacidoMaidana/ProgramacionIII-2026.git

---

## Índice

1. [Conceptos clave](#1-conceptos-clave)
2. [Estructura de ramas](#2-estructura-de-ramas)
3. [Flujo de trabajo](#3-flujo-de-trabajo)
4. [Etiquetas (Tags) de revisión](#4-etiquetas-tags-de-revisión)
5. [Comandos de referencia rápida](#5-comandos-de-referencia-rápida)
6. [Convenciones de mensajes de commit](#6-convenciones-de-mensajes-de-commit)
7. [Historial de etiquetas del curso](#7-historial-de-etiquetas-del-curso)

---

## 1. Conceptos clave

| Término | Descripción |
|--------|-------------|
| **Repositorio** | Carpeta del proyecto con historial de cambios gestionado por Git |
| **Commit** | Instantánea guardada de los cambios realizados |
| **Rama (Branch)** | Línea de desarrollo paralela e independiente |
| **Tag (Etiqueta)** | Marca fija sobre un commit, usada para revisiones o versiones |
| **Remote (Origen)** | Copia del repositorio en GitHub |
| **Push** | Enviar cambios locales al repositorio remoto |
| **Pull** | Traer cambios del repositorio remoto al local |

---

## 2. Estructura de ramas

El proyecto maneja tres ramas principales con propósitos bien definidos:

```
main
├── clases/material-base
└── docente/avances-pruebas
```

### `main`
- Rama principal y espejo del repositorio en GitHub.
- Contiene el estado estable y productivo del proyecto.
- **Solo recibe cambios** mediante merge desde las otras ramas.

### `clases/material-base`
- Material oficial entregado a los estudiantes.
- Contiene el código base de cada clase, limpio y documentado.
- Se actualiza al inicio o cierre de cada unidad temática.

### `docente/avances-pruebas`
- Rama de uso exclusivo del docente.
- Experimentos, validaciones, pruebas de concepto y borradores.
- Puede tener código incompleto; **no se comparte directamente con alumnos**.

---

## 3. Flujo de trabajo

### Inicializar un proyecto nuevo

```bash
# 1. Inicializar el repositorio local
git init

# 2. Configurar identidad
git config user.name "Tu Nombre"
git config user.email "tu-email@example.com"

# 3. Agregar el repositorio remoto
git remote add origin https://github.com/PlacidoMaidana/ProgramacionIII-2026.git

# 4. Primer commit
git add .
git commit -m "Initial commit: descripción del proyecto"

# 5. Renombrar rama principal
git branch -M main

# 6. Crear ramas del curso
git branch clases/material-base
git branch docente/avances-pruebas

# 7. Publicar todo en GitHub
git push -u origin main clases/material-base docente/avances-pruebas
```

### Trabajar en la rama de clases

```bash
git checkout clases/material-base

# ... editar y crear archivos ...

git add .
git commit -m "Clase N: descripción del contenido"
git push origin clases/material-base
```

### Trabajar en la rama del docente

```bash
git checkout docente/avances-pruebas

# ... experimentos y pruebas ...

git add .
git commit -m "Prueba: descripción de la prueba"
git push origin docente/avances-pruebas
```

### Pasar cambios a main (cuando están listos)

```bash
git checkout main
git merge clases/material-base
git push origin main
```

---

## 4. Etiquetas (Tags) de revisión

Las etiquetas sirven para **marcar puntos concretos en la historia** del proyecto, como el cierre de una clase o unidad temática.

### Convención de nombre para etiquetas

```
clase-{tema}-v{número}
```

**Ejemplos:**
- `clase-persistencia-laravel-v1`
- `clase-controladores-v1`
- `clase-autenticacion-v1`
- `clase-api-rest-v2`

### Crear y publicar una etiqueta

```bash
# Crear etiqueta anotada (con mensaje descriptivo)
git tag -a clase-persistencia-laravel-v1 -m "Clase persistencia Laravel: migraciones, modelos y Eloquent"

# Publicar la etiqueta en GitHub
git push origin clase-persistencia-laravel-v1
```

### Ver todas las etiquetas

```bash
git tag --list
```

### Ver el detalle de una etiqueta

```bash
git show clase-persistencia-laravel-v1
```

### Volver al estado de una etiqueta (solo lectura)

```bash
git checkout clase-persistencia-laravel-v1
```

> ⚠️ Al hacer checkout de una etiqueta se entra en estado "detached HEAD". No hacer commits en ese estado; es solo para revisar.

---

## 5. Comandos de referencia rápida

### Estado y navegación

```bash
git status                    # Estado actual del repo
git branch                    # Ver ramas locales
git branch -a                 # Ver todas las ramas (locales y remotas)
git branch --show-current     # Ver en qué rama estás
git log --oneline -10         # Ver los últimos 10 commits
git log --oneline --graph     # Ver historial visual de ramas
```

### Sincronización con GitHub

```bash
git pull origin main          # Actualizar main desde GitHub
git push origin main          # Subir main a GitHub
git fetch --all               # Descargar todos los cambios sin aplicar
```

### Deshacer cambios (con cuidado)

```bash
git restore nombre-archivo    # Descartar cambios en un archivo
git reset --soft HEAD~1       # Deshacer el último commit (conserva cambios)
```

---

## 6. Convenciones de mensajes de commit

Un buen mensaje de commit describe **qué** se hizo y **por qué**.

### Formato sugerido

```
Tipo: descripción breve en presente

(opcional) Detalle adicional si es necesario
```

### Tipos recomendados para la materia

| Tipo | Uso |
|------|-----|
| `feat` | Nueva funcionalidad |
| `fix` | Corrección de error |
| `docs` | Cambios en documentación |
| `clase` | Material de clase |
| `prueba` | Código de prueba o experimento |
| `refactor` | Reorganización sin cambiar funcionalidad |
| `mig` | Migraciones de base de datos |

**Ejemplos:**

```bash
git commit -m "clase: agregar migración y modelo Persona"
git commit -m "feat: CRUD completo para Persona con Eloquent"
git commit -m "prueba: validar relaciones entre modelos"
git commit -m "mig: agregar campo telefono a tabla personas"
```

---

## 7. Historial de etiquetas del curso

| Etiqueta | Fecha | Descripción |
|----------|-------|-------------|
| `clase-persistencia-laravel-v1` | 2026-04-27 | Migraciones, Modelos y Eloquent |

> Actualizar esta tabla al crear cada nueva etiqueta de clase.

---

*Documento generado el 28 de abril de 2026*
