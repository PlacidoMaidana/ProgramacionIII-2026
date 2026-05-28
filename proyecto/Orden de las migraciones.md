## Slide 1: Crear Migraciones de Ejemplo
```bash
# Agregar columnas nuevas a distintas tablas
php artisan make:migration add_phone_and_birth_date_to_students_table --table=students
php artisan make:migration add_is_active_to_subjects_table --table=subjects
php artisan make:migration add_enrolled_at_to_enrollments_table --table=enrollments
```
👉 Laravel genera archivos en `database/migrations` con nombre + timestamp.  
👉 El orden de ejecución depende del timestamp inicial.

---

## Slide 2: Editar Migración (Ejemplo Students)
```php
public function up()
{
    Schema::table('students', function (Blueprint $table) {
        $table->string('phone')->nullable();      // nuevo campo teléfono
        $table->date('birth_date')->nullable();   // nuevo campo fecha de nacimiento
    });
}

public function down()
{
    Schema::table('students', function (Blueprint $table) {
        $table->dropColumn(['phone', 'birth_date']); // revertir cambios
    });
}
```
👉 Regla de oro: **no editar migraciones ya ejecutadas**, siempre crear nuevas.  

---

## Slide 3: Ver Estado y Ejecutar Migraciones
```bash
# Ver qué migraciones están pendientes
php artisan migrate:status

# Ejecutar todas las migraciones pendientes
php artisan migrate

# Verificar nuevamente el estado
php artisan migrate:status
```
👉 Laravel guarda en la tabla `migrations` qué archivos ya corrieron y en qué **batch**.  
👉 En tu caso: iniciales en Batch 1, nuevas en Batch 2.  

---

## Slide 4: Revertir Migraciones
```bash
# Revertir la última migración (último batch)
php artisan migrate:rollback

# Revertir todas las migraciones
php artisan migrate:reset

# Volver a ejecutar desde cero (rollback + migrate)
php artisan migrate:refresh

# Borrar todas las tablas y reconstruir limpio
php artisan migrate:fresh --seed
```
👉 Útil para entornos de práctica: reinicia todo y aplica seeders.  

---

## Slide 5: Flujo Didáctico en Clase
```bash
#### 1. Mostrar estado actual
php artisan migrate:status

#### 2. Crear migración nueva
php artisan make:migration add_phone_to_students_table --table=students

#### 3. Editar archivo con up()/down()

#### 4. Ejecutar migraciones
php artisan migrate

#### 5. Verificar estado
php artisan migrate:status

# 6. Revertir cambios
php artisan migrate:rollback

#### 7. Aplicar otra vez
php artisan migrate
```
👉 Este ciclo muestra **crear → aplicar → verificar → revertir → reaplicar**.  



## Slide 6: Resumen para Estudiantes
```text
- Las migraciones son archivos PHP que definen cambios en la base de datos.
- Se ejecutan en orden por timestamp y se registran en la tabla migrations.
- Siempre usar up() para aplicar y down() para revertir.
- Artisan provee comandos para crear, aplicar, revertir y reiniciar migraciones.
- En producción: nunca editar migraciones ya corridas, siempre crear nuevas.
```