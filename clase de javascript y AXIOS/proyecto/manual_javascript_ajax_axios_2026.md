# Manual: JavaScript, AJAX y Axios en Laravel
**Módulo:** Programación III — 2026  
**Rama:** `profesor`  
**Archivo de laboratorio:** `resources/views/subjects/edit.blade.php`  
**Fecha:** Junio 2026

---

## Índice

1. [¿Qué es AJAX?](#1-qué-es-ajax)
2. [¿Cómo funciona AJAX?](#2-cómo-funciona-ajax)
3. [AJAX en Laravel: rutas y respuestas JSON](#3-ajax-en-laravel-rutas-y-respuestas-json)
4. [Herramientas del navegador: la consola](#4-herramientas-del-navegador-la-consola)
5. [Eventos en JavaScript](#5-eventos-en-javascript)
6. [Manipulación del DOM](#6-manipulación-del-dom)
7. [Axios: qué es y cómo se usa](#7-axios-qué-es-y-cómo-se-usa)
8. [Promesas y manejo de errores](#8-promesas-y-manejo-de-errores)
9. [Laboratorio incremental — 9 pasos](#9-laboratorio-incremental--9-pasos)
10. [Endpoints Eloquent del backend](#10-endpoints-eloquent-del-backend)
11. [Resumen de archivos del módulo](#11-resumen-de-archivos-del-módulo)

---

## 1. ¿Qué es AJAX?

**AJAX** significa _Asynchronous JavaScript and XML_ (JavaScript Asíncrono y XML), aunque hoy en día casi siempre se trabaja con **JSON** en lugar de XML.

Es una **técnica de comunicación** entre el navegador y el servidor que permite:

- Enviar y recibir datos **sin recargar la página completa**.
- Actualizar **partes específicas** del HTML de forma dinámica.
- Crear interfaces más rápidas y fluidas para el usuario.

### Comparación: sin AJAX vs. con AJAX

| Sin AJAX | Con AJAX |
|---|---|
| El usuario hace clic en un botón | El usuario hace clic en un botón |
| El navegador envía una petición HTTP | El navegador envía una petición HTTP **en segundo plano** |
| El servidor responde con una página HTML completa | El servidor responde con **datos JSON** |
| La página entera se recarga | Solo se actualiza **el elemento HTML necesario** |

### Ejemplo de la vida real

Cuando escribís en el buscador de Google y aparecen sugerencias mientras escribís, eso es AJAX: el navegador consulta al servidor en cada tecla pulsada y actualiza solo la lista de sugerencias, sin recargar la página.

---

## 2. ¿Cómo funciona AJAX?

El flujo tiene **cuatro actores**:

```
[Usuario]  →  [JavaScript en el navegador]  →  [Servidor Laravel]  →  [Base de datos]
    ↑                      |                            |
    └──────────────────────┘                            |
         (actualiza el DOM con los datos JSON) ←────────┘
```

### Paso a paso

1. El usuario interactúa (hace clic en un botón, escribe en un campo, selecciona una opción).
2. JavaScript **captura el evento** y prepara una petición HTTP.
3. La petición viaja al servidor **sin interrumpir la página actual**.
4. Laravel recibe la petición, ejecuta la lógica del controlador y devuelve una **respuesta JSON**.
5. JavaScript recibe la respuesta y **actualiza el DOM** mostrando los datos.

---

## 3. AJAX en Laravel: rutas y respuestas JSON

Para que Axios pueda consumir datos desde Laravel necesitamos:

### 3.1 Una ruta en `routes/web.php`

```php
// Ruta simple que devuelve JSON
Route::get('/enrollments/resumen', [EnrollmentController::class, 'resumen']);
Route::get('/enrollments/materias', [EnrollmentController::class, 'materias']);
Route::get('/enrollments/data',     [EnrollmentController::class, 'data']);
```

> **Importante:** Las rutas que devuelven JSON deben definirse **antes** de las rutas `resource` que también coincidan con ese prefijo, o usar nombres de URL que no colisionen.

### 3.2 Un método en el controlador que devuelva `JsonResponse`

```php
use Illuminate\Http\JsonResponse;

public function resumen(): JsonResponse
{
    $summary = Enrollment::query()
        ->selectRaw('COUNT(*) as total')
        ->selectRaw('AVG(grade) as avg_grade')
        ->selectRaw('MIN(grade) as min_grade')
        ->selectRaw('MAX(grade) as max_grade')
        ->first();

    return response()->json([
        'totals' => [
            'enrollments' => (int) ($summary->total ?? 0),
            'avg_grade'   => $summary->avg_grade !== null
                                ? round((float) $summary->avg_grade, 2)
                                : null,
        ],
    ]);
}
```

### 3.3 Estructura típica de respuesta JSON

```json
{
    "totals": {
        "enrollments": 42,
        "avg_grade": 7.35,
        "min_grade": 2.5,
        "max_grade": 10.0
    },
    "top_subjects": [
        { "id": 1, "name": "Programación III", "code": "PROG3", "enrollments": 15 }
    ]
}
```

---

## 4. Herramientas del navegador: la consola

Antes de escribir una sola línea de AJAX, los alumnos deben conocer la **consola del navegador** (F12 → pestaña Console).

### `console.log()`

```javascript
function mostrarDatos() {
    let nombre = 'Programación III';
    let id = 1;

    // Muestra texto y variables juntos
    console.log('Materia:', nombre, '| ID:', id);

    // También sirve para inspeccionar objetos completos
    let objeto = { id: id, nombre: nombre };
    console.log('Objeto completo:', objeto);
}
```

### ¿Por qué usar `console.log` en lugar de `alert`?

| `alert()`                          | `console.log()`                           |
|------------------------------------|-------------------------------------------|
| Bloquea la página                  | No interrumpe al usuario                  |
| Solo muestra texto plano           | Muestra objetos expandibles               |
| Hay que aceptar para continuar     | El historial queda registrado             |
| Inútil con objetos (`[object Object]`) | Inspecciona propiedades anidadas      |

### Ejemplo con AJAX (lo que veremos en Axios)

```javascript
// Antes de la petición
console.log('1. Enviando petición a /enrollments/resumen...');

axios.get('/enrollments/resumen')
    .then(function (response) {
        // Después de recibir la respuesta
        console.log('2. Respuesta recibida:', response.data);
    });
```

---

## 5. Eventos en JavaScript

Un **evento** es algo que ocurre en el navegador: un clic, una tecla presionada, un cambio de selección, etc.

### 5.1 El evento `click` en un botón

```html
<!-- HTML -->
<button type="button" id="btn-alerta">Mostrar alerta</button>
```

```javascript
// JavaScript
document.getElementById('btn-alerta').addEventListener('click', function () {
    alert('¡Hola! Este botón funciona.');
});
```

**Conceptos clave:**
- `getElementById()` busca y obtiene el elemento del DOM por su `id`.
- `addEventListener('click', función)` registra qué código ejecutar cuando ocurre el evento.
- La función anónima `function () { ... }` se llama **callback** (función de retorno).

### 5.2 El evento `change` en un `<select>`

```html
<select id="sel-tema">
    <option value="">-- Elegir --</option>
    <option value="eventos">Eventos básicos</option>
    <option value="dom">Manipulación DOM</option>
    <option value="axios">Consultas con Axios</option>
</select>

<div id="out-tema">Aún sin selección.</div>
```

```javascript
document.getElementById('sel-tema').addEventListener('change', function () {
    const valor = document.getElementById('sel-tema').value;

    if (!valor) {
        document.getElementById('out-tema').textContent = 'Aún sin selección.';
        return;
    }

    document.getElementById('out-tema').textContent = 'Selección actual: ' + valor;
});
```

### 5.3 El evento `input` en un campo de texto

El evento `input` se dispara **en cada tecla**, ideal para búsquedas en vivo:

```javascript
document.getElementById('txt-alumno').addEventListener('input', function () {
    const texto = document.getElementById('txt-alumno').value.trim();

    document.getElementById('out-texto').textContent = texto
        ? 'Texto en vivo: ' + texto
        : 'Esperando texto...';
});
```

### 5.4 El evento `change` en un `checkbox`

```javascript
document.getElementById('chk-activar-axios').addEventListener('change', function () {
    const habilitado = document.getElementById('chk-activar-axios').checked;
    document.getElementById('btn-check-accion').disabled = !habilitado;
});
```

---

## 6. Manipulación del DOM

**DOM** (_Document Object Model_) es la representación en memoria de la página HTML. JavaScript puede leer y modificar cualquier elemento.

### Leer un `data-attribute` (patrón Blade + JS seguro)

En Blade no es recomendable mezclar `{{ }}` directamente dentro de bloques `<script>`. La forma correcta es usar atributos `data-*` en el HTML:

```html
{{-- En Blade: incrustar datos de PHP en el HTML de forma segura --}}
<div id="subject-meta"
     data-subject-id="{{ $subject->id }}"
     data-subject-name="{{ e($subject->name) }}">
</div>
```

```javascript
// En JavaScript: leer esos datos sin mezclar PHP con JS
const meta    = document.getElementById('subject-meta');
const id      = Number(meta.dataset.subjectId);
const nombre  = meta.dataset.subjectName;

console.log('Materia actual:', id, nombre);
```

### Crear elementos y agregarlos al DOM

```javascript
// Agregar un ítem a una lista
const lista = document.getElementById('lista-local');
const li    = document.createElement('li');

li.className   = 'list-group-item';
li.textContent = 'Nuevo ítem de la lista';

lista.appendChild(li);
```

### Vaciar un contenedor y poblarlo con datos

```javascript
const tbody = document.querySelector('#tabla tbody');
tbody.innerHTML = ''; // Vaciamos la tabla

datos.forEach(function (fila) {
    const tr = document.createElement('tr');
    tr.innerHTML = '<td>' + fila.id + '</td><td>' + fila.nombre + '</td>';
    tbody.appendChild(tr);
});
```

---

## 7. Axios: qué es y cómo se usa

**Axios** es una librería JavaScript que simplifica las peticiones HTTP asíncronas. Es más cómoda de usar que `fetch` nativo porque:

- Convierte automáticamente la respuesta a objeto JavaScript (no hace falta llamar a `.json()`).
- Maneja bien los parámetros de URL con la opción `params`.
- Tiene mejor manejo de errores por defecto.

### Inclusión en el proyecto

```html
{{-- Desde el archivo local del proyecto --}}
<script src="{{ asset('vendor/axios/axios.min.js') }}"></script>
```

### 7.1 GET básico

```javascript
axios.get('/enrollments/resumen')
    .then(function (response) {
        // response.data contiene el JSON del servidor
        console.log('Datos recibidos:', response.data);
    })
    .catch(function (error) {
        console.error('Algo falló:', error);
    });
```

### 7.2 GET con parámetros de URL

```javascript
// Esto genera: GET /enrollments/materias?q=Progra&min_enrollments=0
axios.get('/enrollments/materias', {
    params: {
        q: 'Progra',
        min_enrollments: 0
    }
})
.then(function (response) {
    console.log('Materias filtradas:', response.data.data);
});
```

### 7.3 GET con múltiples parámetros opcionales

```javascript
// Los parámetros undefined se omiten automáticamente de la URL
axios.get('/enrollments/data', {
    params: {
        student:   'Ana',        // filtra por nombre
        min_grade: 6,            // nota mínima
        max_grade: undefined,    // este se omite (sin valor)
        per_page:  8
    }
})
.then(function (response) {
    const filas = response.data.data;
    console.log('Inscripciones:', filas);
});
```

---

## 8. Promesas y manejo de errores

Axios usa **Promesas** (_Promises_). Una promesa representa un valor que todavía no existe pero que existirá en el futuro (cuando el servidor responda).

### Estructura de una promesa con Axios

```javascript
axios.get('/enrollments/resumen')
    //  ↓ Se ejecuta cuando el servidor responde OK (status 2xx)
    .then(function (response) {
        console.log('Éxito:', response.data);
    })
    //  ↓ Se ejecuta si algo falla (red caída, error 404, 500, etc.)
    .catch(function (error) {
        console.error('Error:', error);
        alert('No se pudo cargar la información.');
    });
```

### Estados de una promesa

| Estado | Qué significa |
|---|---|
| **pending** (pendiente) | La petición fue enviada, esperando respuesta |
| **fulfilled** (resuelta) | El servidor respondió con éxito → se ejecuta `.then()` |
| **rejected** (rechazada) | Algo falló → se ejecuta `.catch()` |

### `Promise.all`: ejecutar varias peticiones en paralelo

```javascript
// Las tres peticiones se envían al mismo tiempo
Promise.all([
    axios.get('/enrollments/resumen'),
    axios.get('/enrollments/data', { params: { per_page: 10 } }),
    axios.get('/enrollments/materias')
])
.then(function (responses) {
    const resumen  = responses[0].data;
    const datos    = responses[1].data;
    const materias = responses[2].data;

    console.log('Resumen:',  resumen);
    console.log('Datos:',    datos);
    console.log('Materias:', materias);
})
.catch(function (error) {
    console.error('Al menos una petición falló:', error);
});
```

---

## 9. Laboratorio incremental — 9 pasos

El laboratorio está implementado en [resources/views/subjects/edit.blade.php](../resources/views/subjects/edit.blade.php), debajo del formulario principal de edición de materias.

---

### Paso 1: Botón con alerta

**Objetivo:** entender el evento `click` y la llamada a una función.

```html
<button type="button" id="btn-alerta" class="btn btn-sm btn-dark">
    Mostrar alerta
</button>
```

```javascript
document.getElementById('btn-alerta').addEventListener('click', function () {
    alert('¡Hola! Esta es una alerta simple de JavaScript.');
});
```

**Conceptos:** `addEventListener`, callback, `alert()`.

---

### Paso 2: Consola para debug

**Objetivo:** usar `console.log()` con un objeto real del servidor (la materia actual).

```html
<div id="subject-meta"
     data-subject-id="{{ $subject->id }}"
     data-subject-name="{{ e($subject->name) }}">
</div>
<button type="button" id="btn-consola">Enviar a consola</button>
```

```javascript
const subjectMeta = document.getElementById('subject-meta');

document.getElementById('btn-consola').addEventListener('click', function () {
    const payload = {
        subjectId:   Number(subjectMeta.dataset.subjectId),
        subjectName: subjectMeta.dataset.subjectName,
        timestamp:   new Date().toISOString()
    };
    console.log('Debug payload:', payload);
});
```

**Conceptos:** `data-*` attributes, `dataset`, `console.log`, objetos JS.

---

### Paso 3: Checkbox que habilita/deshabilita botones

**Objetivo:** ver cómo un control puede cambiar el estado de otros (interactividad encadenada).

```html
<input type="checkbox" id="chk-activar-axios">
<label for="chk-activar-axios">Activar consultas Axios</label>
<button type="button" id="btn-check-accion" disabled>Acción habilitada</button>
```

```javascript
document.getElementById('chk-activar-axios').addEventListener('change', function () {
    const habilitado = document.getElementById('chk-activar-axios').checked;
    document.getElementById('btn-check-accion').disabled = !habilitado;
});
```

**Conceptos:** propiedad `checked`, propiedad `disabled`, encadenamiento de controles.

---

### Paso 4: Select con evento `change`

**Objetivo:** reaccionar al cambio de selección y mostrar el valor elegido en pantalla.

```html
<select id="sel-tema">
    <option value="">-- Elegir --</option>
    <option value="eventos">Eventos básicos</option>
    <option value="dom">Manipulación DOM</option>
    <option value="axios">Consultas con Axios</option>
</select>
<div id="out-tema">Aún sin selección.</div>
```

```javascript
document.getElementById('sel-tema').addEventListener('change', function () {
    const valor = document.getElementById('sel-tema').value;

    document.getElementById('out-tema').textContent = valor
        ? 'Selección actual: ' + valor
        : 'Aún sin selección.';
});
```

**Conceptos:** evento `change`, `.value` en un select, `textContent`.

---

### Paso 5: Entry text en vivo (evento `input`)

**Objetivo:** actualizar el DOM en tiempo real mientras el usuario escribe.

```html
<input id="txt-alumno" type="text" placeholder="Escribe un nombre...">
<div id="out-texto">Esperando texto...</div>
```

```javascript
document.getElementById('txt-alumno').addEventListener('input', function () {
    const texto = document.getElementById('txt-alumno').value.trim();

    document.getElementById('out-texto').textContent = texto
        ? 'Texto en vivo: ' + texto
        : 'Esperando texto...';
});
```

**Conceptos:** evento `input` vs `change`, `.trim()`, actualización en tiempo real.

---

### Paso 6: Lista local incremental

**Objetivo:** construir una lista en el DOM sin ir al servidor (lógica 100% del lado del cliente).

```html
<input id="txt-item" type="text" placeholder="Ej: Revisar promesas">
<button type="button" id="btn-agregar-item">Agregar a lista</button>
<ul id="lista-local">
    <li class="text-muted">Sin ítems.</li>
</ul>
```

```javascript
document.getElementById('btn-agregar-item').addEventListener('click', function () {
    const valor = document.getElementById('txt-item').value.trim();

    if (!valor) {
        alert('Escribe un ítem antes de agregar.');
        return;
    }

    const lista = document.getElementById('lista-local');

    // Si existe el placeholder "Sin ítems", lo removemos
    if (lista.querySelector('.text-muted')) {
        lista.innerHTML = '';
    }

    const li       = document.createElement('li');
    li.className   = 'list-group-item';
    li.textContent = valor;
    lista.appendChild(li);

    document.getElementById('txt-item').value = '';
    document.getElementById('txt-item').focus();
});
```

**Conceptos:** `createElement`, `appendChild`, validación simple, `focus()`.

---

### Paso 7: Axios GET — resumen estadístico

**Objetivo:** primera petición real al backend. El checkbox del Paso 3 debe estar activo.

```javascript
document.getElementById('btn-cargar-resumen').addEventListener('click', function () {
    axios.get('/enrollments/resumen')
        .then(function (res) {
            const t = res.data.totals || {};

            document.getElementById('out-resumen').textContent =
                'Total=' + (t.enrollments ?? '-') +
                ' | Prom=' + (t.avg_grade   ?? '-') +
                ' | Min='  + (t.min_grade   ?? '-') +
                ' | Max='  + (t.max_grade   ?? '-');
        })
        .catch(function (err) {
            console.error(err);
            document.getElementById('out-resumen').textContent = 'Error al consultar resumen.';
        });
});
```

**Backend respondiendo (`GET /enrollments/resumen`):**
```json
{
    "totals": {
        "enrollments": 42,
        "avg_grade": 7.35,
        "min_grade": 2.5,
        "max_grade": 10.0
    }
}
```

**Conceptos:** primera petición AJAX real, `.then()`, `.catch()`, acceso a `response.data`.

---

### Paso 8: Axios GET con parámetro de filtro

**Objetivo:** enviar un parámetro `q` y ver cómo el servidor filtra los datos antes de responder.

```javascript
document.getElementById('btn-cargar-materias').addEventListener('click', function () {
    axios.get('/enrollments/materias', {
        params: {
            q: document.getElementById('txt-filtro-materia').value.trim() || undefined,
            min_enrollments: 0
        }
    })
    .then(function (res) {
        const rows = res.data.data || [];
        const lista = document.getElementById('lista-materias-axios');
        lista.innerHTML = '';

        if (!rows.length) {
            lista.innerHTML = '<li class="list-group-item text-muted">Sin resultados.</li>';
            return;
        }

        rows.forEach(function (m) {
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            li.innerHTML =
                '<span>' + m.nombre + ' (' + m.codigo + ')</span>' +
                '<span class="badge bg-secondary">Inscriptos: ' + m.inscriptos + '</span>';
            lista.appendChild(li);
        });
    });
});
```

**Backend (fragmento de `EnrollmentController::materias`):**

```php
$materias = Subject::query()
    ->select(['id', 'name', 'code'])
    ->withCount('enrollments')               // conteo en una sola consulta SQL
    ->withAvg('enrollments as avg_grade', 'grade')   // promedio en la misma consulta
    ->when($request->filled('q'), function ($query) use ($request) {
        $text = trim((string) $request->input('q'));
        $query->where(function ($inner) use ($text) {
            $inner->where('name', 'like', "%{$text}%")
                  ->orWhere('code', 'like', "%{$text}%");
        });
    })
    ->orderBy('name')
    ->get();
```

**Conceptos:** `params` en Axios, `undefined` para omitir parámetros, `when()` en Eloquent,  
`withCount()`, `withAvg()`.

---

### Paso 9: Axios GET con múltiples filtros (tabla de resultados)

**Objetivo:** combinar varios parámetros opcionales y renderizar una tabla con los resultados.

```javascript
document.getElementById('btn-filtrar-inscripciones').addEventListener('click', function () {
    axios.get('/enrollments/data', {
        params: {
            student:   document.getElementById('txt-student-filter').value.trim() || undefined,
            min_grade: document.getElementById('txt-min-grade').value || undefined,
            max_grade: document.getElementById('txt-max-grade').value || undefined,
            per_page:  8
        }
    })
    .then(function (res) {
        const rows = res.data.data || [];
        const tbody = document.getElementById('tbody-inscripciones');
        tbody.innerHTML = '';

        if (!rows.length) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-muted text-center">Sin resultados.</td></tr>';
            return;
        }

        rows.forEach(function (row) {
            const tr = document.createElement('tr');
            tr.innerHTML =
                '<td>' + row.id + '</td>' +
                '<td>' + (row.student  ?? '-') + '</td>' +
                '<td>' + (row.classroom ?? '-') + '</td>' +
                '<td>' + (row.subject  ?? '-') + '</td>' +
                '<td>' + (row.grade    ?? '-') + '</td>';
            tbody.appendChild(tr);
        });
    });
});
```

**Backend (fragmento de `EnrollmentController::data`):**

```php
$query = Enrollment::query()
    ->with(['student.classroom', 'subject'])     // evita N+1
    ->when(!empty($validated['student']), function ($q) use ($validated) {
        $q->whereHas('student', function ($sq) use ($validated) {
            $sq->where('name', 'like', '%' . trim($validated['student']) . '%');
        });
    })
    ->when(isset($validated['min_grade']), function ($q) use ($validated) {
        $q->whereNotNull('grade')->where('grade', '>=', $validated['min_grade']);
    })
    ->when(isset($validated['max_grade']), function ($q) use ($validated) {
        $q->whereNotNull('grade')->where('grade', '<=', $validated['max_grade']);
    })
    ->orderBy('id', 'desc')
    ->paginate($perPage);
```

**Conceptos:** filtros combinados, `whereHas` (filtrar por relación), `with` (eager loading),  
paginación en backend, renderizado dinámico de tabla.

---

## 10. Endpoints Eloquent del backend

Los tres endpoints definidos en `routes/web.php` para este módulo:

| Método | URL | Método del controlador | Descripción |
|--------|-----|------------------------|-------------|
| GET | `/enrollments/resumen` | `EnrollmentController@resumen` | Métricas globales (count, avg, min, max) |
| GET | `/enrollments/materias` | `EnrollmentController@materias` | Lista de materias con filtro de texto e inscriptos |
| GET | `/enrollments/data` | `EnrollmentController@data` | Inscripciones con filtros combinados y paginación |

### Parámetros disponibles

**`/enrollments/materias`**

| Parámetro | Tipo | Descripción |
|---|---|---|
| `q` | string | Texto a buscar en nombre o código |
| `min_enrollments` | integer | Cantidad mínima de inscriptos |

**`/enrollments/data`**

| Parámetro | Tipo | Descripción |
|---|---|---|
| `subject_id` | integer | Filtrar por materia |
| `classroom_id` | integer | Filtrar por aula |
| `student` | string | Filtrar por nombre parcial de alumno |
| `min_grade` | decimal | Nota mínima (0–10) |
| `max_grade` | decimal | Nota máxima (0–10) |
| `per_page` | integer | Registros por página (máx. 50) |

---

## 11. Resumen de archivos del módulo

| Archivo | Qué contiene |
|---|---|
| `resources/views/subjects/edit.blade.php` | Laboratorio incremental: HTML + JS de los 9 pasos |
| `resources/views/enrollments/index.blade.php` | Demo de filtros completos con Promise.all |
| `app/Http/Controllers/EnrollmentController.php` | Métodos `materias()`, `data()`, `resumen()` con Eloquent |
| `routes/web.php` | Rutas GET para los tres endpoints JSON |
| `public/vendor/axios/axios.min.js` | Librería Axios (local, sin necesidad de internet) |
| `resources/views/layouts/app.blade.php` | Menú de navegación con accesos a Subjects y Enrollments |

---

## Glosario rápido

| Término | Definición |
|---|---|
| **AJAX** | Técnica para comunicarse con el servidor sin recargar la página |
| **JSON** | Formato de texto para intercambiar datos (llave/valor) |
| **Axios** | Librería JS para hacer peticiones HTTP de forma simple |
| **Promesa** | Objeto que representa un valor futuro (pendiente, resuelta, rechazada) |
| **DOM** | Estructura en memoria que representa la página HTML |
| **Evento** | Acción del usuario (clic, tecla, cambio de selección) |
| **Callback** | Función que se pasa como argumento para ejecutarse después |
| **Endpoint** | URL del servidor que responde a una petición HTTP |
| **Eager loading** | Cargar relaciones en bloque para evitar el problema N+1 |
| **whereHas** | Filtrar en Eloquent por condiciones en una relación |

---

_Manual generado con GitHub Copilot — Programación III 2026_
