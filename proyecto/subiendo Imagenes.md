# Documentación: Subiendo Imágenes - Sistema de Carga de Fotos de Estudiantes

**Fecha de Implementación:** 2026-06-25  
**Objetivo:** Implementar carga de fotos de perfil para estudiantes con preview en tiempo real

---

## 📋 Resumen Ejecutivo

Se implementó un sistema completo de carga de imágenes para estudiantes utilizando **Axios + FileReader API (JavaScript)** para preview, integrado con la API RESTful existente de Laravel. La solución permite crear y editar estudiantes con soporte para fotos hasta 10 MB, con vista previa instantánea sin envío previo al servidor.

---

## 📁 Archivos Modificados

### 1. **resources/views/students/create.blade.php** ✏️
**Tipo:** Vista Blade (Frontend)  
**Cambios Realizados:**
- Reemplazó formulario tradicional POST por formulario AJAX con IDs específicos
- Cambio de layout: Agregó columna lateral (Bootstrap col-md-4) para preview de imagen
- Agregó input file con id `student_photo` y aceptación de imágenes (`accept="image/*"`)
- Reemplazó botón `<submit>` por botón `<button type="button">` con id `btnSaveStudent`
- Agregó div `#imagePreview` para mostrar la imagen en tiempo real
- Agregó div contenedor con clase `row` para layout responsivo

**Antes:**
```blade
<form method="POST" action="{{ route('students.store') }}">
    @csrf
    <!-- Formulario tradicional sin preview -->
</form>
```

**Después:**
```blade
<form id="studentForm">
    <!-- Inputs con IDs específicos para JS -->
    <div id="imagePreview">Sube una imagen para verla aquí</div>
    <!-- Botón type="button" para Axios -->
</form>
```

---

### 2. **resources/views/students/edit.blade.php** ✏️
**Tipo:** Vista Blade (Frontend)  
**Cambios Realizados:**
- Reemplazó formulario POST/PUT tradicional por formulario AJAX
- Agregó input hidden `#student_id` para capturar ID del estudiante
- Pre-carga valores del estudiante en los inputs (`value="{{ $student->name }}"`, etc.)
- Agregó input file `#student_photo` con soporte para cambiar imagen
- Implementó preview que muestra foto actual del estudiante al cargar la página
- Cambio a layout de 2 columnas con preview lado derecho
- Agregó mensaje "Deja vacío si no quieres cambiar la foto"

**Antes:**
```blade
<form method="POST" action="{{ route('students.update', $student) }}">
    @csrf
    @method('PUT')
    <!-- Sin foto ni preview -->
</form>
```

**Después:**
```blade
<form id="studentForm">
    <input type="hidden" id="student_id" value="{{ $student->id }}">
    <!-- Con foto actual mostrada en preview -->
</form>
```

---

## 🔧 Cambios en Controladores

### **No se modificaron controladores** ✅

Se utilizaron los controladores existentes:

#### **app/Http/Controllers/Api/StudentApiController.php**
**Métodos Utilizados:**
- `store()` - POST /api/students
  - Ya tenía soporte para fotos con validación `'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:10240']`
  - Almacena archivo: `$path = $request->file('photo')->store('student_photos', 'public');`
  - Retorna URL pública: `$validated['photo'] = Storage::url($path);`

- `update()` - PUT /api/students/{id}
  - Ya tenía soporte idéntico al store() para fotos
  - Maneja tanto actualizaciones de datos como de foto

**Importaciones Activas:**
```php
use Illuminate\Support\Facades\Storage;
```

**Sin cambios requeridos** - El controlador ya estaba completamente preparado para manejar fotos.

---

## 🗄️ Cambios en Modelos

### **No se modificaron modelos** ✅

Se utilizó el modelo existente:

#### **app/Models/Student.php**
**Atributo Utilizado:**
- `protected $fillable = ['classroom_id', 'name', 'email', 'photo'];`
  - Ya tenía 'photo' en fillable para asignación masiva
  - Permite que Eloquent acepte el campo photo en create() y update()

**Sin cambios requeridos** - El modelo ya estaba preparado.

---

## 🛣️ Rutas Utilizadas

### **No se crearon nuevas rutas** ✅

Se utilizaron rutas existentes de la API:

| Ruta | Método | Controlador | Propósito |
|------|--------|-------------|----------|
| `/api/students` | POST | StudentApiController@store() | Crear estudiante con foto |
| `/api/students/{id}` | POST* | StudentApiController@update() | Editar estudiante con foto |
| `/students/create` | GET | StudentController@create() | Mostrar formulario crear |
| `/students/{id}/edit` | GET | StudentController@edit() | Mostrar formulario editar |
| `/students` | GET | StudentController@index() | Listar estudiantes |

*Nota: Se usa POST con `_method=PUT` en FormData porque Axios con multipart/form-data no soporta directamente PUT method header con archivos.

---

## 📜 JavaScript Implementado

### **Tecnologías Utilizadas:**

#### 1. **FileReader API** (JavaScript Nativo) 📸
- **Propósito:** Leer archivo seleccionado localmente sin enviarlo al servidor
- **Ubicación:** Ambas vistas (create.blade.php y edit.blade.php)
- **Código:**
```javascript
const reader = new FileReader();
reader.onload = function(event) {
    const previewDiv = document.getElementById('imagePreview');
    previewDiv.innerHTML = `<img src="${event.target.result}" ...>`;
};
reader.readAsDataURL(file);
```
- **Ventaja:** Preview instantáneo, sin latencia de red

#### 2. **Axios HTTP Client** 📡
- **Propósito:** Enviar datos y archivo al servidor de forma asíncrona
- **Ubicación:** Ambas vistas (create.blade.php y edit.blade.php)
- **Métodos Axios Utilizados:**

**En create.blade.php:**
```javascript
axios.post('/api/students', formData)
    .then(response => {
        // Éxito: redirecciona a index
        window.location.href = '{{ route("students.index") }}';
    })
    .catch(error => {
        // Muestra errores de validación por campo
    })
    .finally(() => {
        // Restaura estado del botón
    });
```

**En edit.blade.php:**
```javascript
axios.post(`/api/students/${studentId}`, formData)
    // Mismo flujo: then/catch/finally
```

#### 3. **FormData API** 📦
- **Propósito:** Construir datos multipart/form-data con archivo binario
- **Ubicación:** Ambas vistas
- **Código:**
```javascript
const formData = new FormData();
formData.append('name', name);
formData.append('email', email);
formData.append('classroom_id', classroomId);
if (photoFile) {
    formData.append('photo', photoFile);
}
```
- **Ventaja:** Maneja automáticamente boundary y encoding multipart

#### 4. **Event Listeners** 🎧
- **Propósito:** Capturar eventos de usuario
- **Tipos Utilizados:**
  - `change` - Cuando selecciona imagen (trigger FileReader para preview)
  - `click` - Cuando hace click en guardar (trigger Axios POST)

---

## 🔄 Flujo de Datos

### **Crear Estudiante (create.blade.php)**
```
Usuario selecciona imagen
    ↓
FileReader lee archivo localmente
    ↓
Imagen aparece en preview (sin envío)
    ↓
Usuario completa formulario + clickea Guardar
    ↓
JavaScript captura todos los valores
    ↓
FormData construye objeto multipart con archivo
    ↓
Axios POST a /api/students
    ↓
StudentApiController@store() valida (max:10240, image, mimes:jpeg,png,jpg)
    ↓
Archivo se guarda en storage/app/public/student_photos/
    ↓
URL pública se almacena en BD (Storage::url())
    ↓
Retorna 201 Created con datos del alumno
    ↓
JavaScript redirecciona a /students (grilla)
```

### **Editar Estudiante (edit.blade.php)**
```
Página carga con datos actuales (foto mostrada)
    ↓
Usuario selecciona imagen nueva (opcional)
    ↓
FileReader actualiza preview (sin envío)
    ↓
Usuario clickea Guardar Cambios
    ↓
JavaScript captura ID del estudiante (input hidden)
    ↓
FormData construye objeto multipart + _method=PUT
    ↓
Axios POST a /api/students/{id} (simula PUT)
    ↓
StudentApiController@update() valida y actualiza
    ↓
Archivo se reemplaza en storage/app/public/student_photos/
    ↓
URL se actualiza en BD
    ↓
Retorna 200 OK con datos actualizados
    ↓
JavaScript redirecciona a /students
```

---

## 🖼️ Vista Previa (Preview) - Cómo Funciona

### **Sin Envío al Servidor:**
```javascript
// El usuario selecciona un archivo
document.getElementById('student_photo').addEventListener('change', function(e) {
    const file = e.target.files[0];  // Archivo local
    if (file) {
        const reader = new FileReader();  // Lector de archivos local
        reader.onload = function(event) {
            // event.target.result es una URL data: en base64
            previewDiv.innerHTML = `<img src="${event.target.result}">`;
        };
        reader.readAsDataURL(file);  // Lee como Data URL
    }
});
```

**Ventajas:**
- ✅ Instantáneo (sin latencia de red)
- ✅ No sobrecarga servidor
- ✅ Funciona incluso sin conexión
- ✅ Solo lee en memoria local

---

## 📝 Validación en Servidor

**Ubicación:** `app/Http/Controllers/Api/StudentApiController.php`

```php
$validated = $request->validate([
    'name' => 'required|string|max:255',
    'email' => 'required|email|unique:students,email,' . ($request->id ?? 'NULL'),
    'classroom_id' => 'required|exists:classrooms,id',
    'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:10240'],
]);
```

**Reglas Aplicadas:**
| Regla | Significa |
|-------|-----------|
| `nullable` | Foto es opcional |
| `image` | Debe ser MIME type imagen |
| `mimes:jpeg,png,jpg` | Solo estas extensiones |
| `max:10240` | Máximo 10 MB (10240 KB) |

**Respuesta en Error (422):**
```json
{
  "errors": {
    "photo": [
      "The photo must be an image.",
      "The photo may not be greater than 10240 kilobytes."
    ]
  }
}
```

---

## 💾 Almacenamiento de Archivos

### **Ruta en Servidor:**
```
storage/app/public/student_photos/
    ├── UUID-filename-1.jpg
    ├── UUID-filename-2.png
    └── ...
```

### **URL Pública Almacenada en BD:**
```
/storage/student_photos/UUID-filename-1.jpg
```

### **Symlink Requerido:**
```bash
php artisan storage:link
# Crea: public/storage → storage/app/public
```

---

## 🚀 Tecnologías Stack

| Tecnología | Uso | Ubicación |
|-----------|-----|-----------|
| **Bootstrap 5** | Layout responsivo (row, col-md-4, etc.) | Blade Templates |
| **FileReader API** | Preview de imagen local | JavaScript (create + edit) |
| **FormData API** | Construcción de multipart/form-data | JavaScript (create + edit) |
| **Axios** | HTTP POST asíncrono | JavaScript (create + edit) |
| **Laravel Blade** | Templates y directivas (@csrf, @foreach, etc.) | .blade.php files |
| **Storage Facade** | Almacenamiento de archivos | StudentApiController |
| **Eloquent ORM** | Persistencia de datos | Student Model |

---

## ✅ Checklist de Implementación

- ✅ Vista create.blade.php con preview
- ✅ Vista edit.blade.php con foto actual + preview
- ✅ JavaScript FileReader para preview instantáneo
- ✅ Axios POST para envío asíncrono
- ✅ FormData multipart/form-data construction
- ✅ Validación en servidor (max:10240, image, mimes)
- ✅ Almacenamiento en storage/app/public/student_photos/
- ✅ URL pública generada con Storage::url()
- ✅ Manejo de errores de validación 422
- ✅ Button disabled state durante envío
- ✅ Redirección automática a grilla tras guardar

---

## 🐛 Posibles Mejoras Futuras

1. **Crop de Imagen** - Permitir recortar imagen antes de guardar
2. **Múltiples Formatos** - Soportar más formatos (GIF, WebP)
3. **Compresión** - Comprimir imagen antes de enviar
4. **Drag & Drop** - Permitir arrastrar imagen a zona de drop
5. **Confirmación Modal** - Modal de confirmación antes de guardar
6. **Galería de Fotos** - Mostrar historial de fotos subidas
7. **Eliminación de Foto** - Botón para remover foto actual
8. **Progreso de Carga** - Barra de progreso en Axios

---

## 📚 Referencias Técnicas

### **APIs JavaScript Utilizadas:**
- [FileReader API - MDN](https://developer.mozilla.org/es/docs/Web/API/FileReader)
- [FormData API - MDN](https://developer.mozilla.org/es/docs/Web/API/FormData)
- [Axios Documentation](https://axios-http.com/)

### **Framework Laravel:**
- [Storage Facade - Laravel Docs](https://laravel.com/docs/8.x/filesystem)
- [Request Validation - Laravel Docs](https://laravel.com/docs/8.x/validation)

### **Bootstrap:**
- [Grid System - Bootstrap Docs](https://getbootstrap.com/docs/5.0/layout/grid/)

---

## 🎯 Conclusión

Se implementó un sistema completo y funcional de carga de imágenes utilizando tecnologías modernas (FileReader, FormData, Axios) sin modificar controladores ni modelos existentes. El sistema es **seguro** (validación en servidor), **responsivo** (layout Bootstrap), y **user-friendly** (preview instantáneo).

**Estado:** ✅ **COMPLETADO Y FUNCIONAL**

