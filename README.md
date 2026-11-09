# PetCare AI

Asistente inteligente para el cuidado de mascotas (perros y gatos).
Proyecto integrador · Unidad 3 (Laravel) · IIP323W — Tecnologías y Aplicaciones Web y Móviles · UDD 2026.

**Integrantes:** Leandro Añasco · Sebastián Ruíz (FullStack)

## Stack

- **Backend:** Laravel 13 + PHP 8.4
- **Interfaz:** Blade, HTML, CSS y Bootstrap
- **Base de datos:** PostgreSQL (Eloquent ORM)
- **IA:** API de Groq vía Laravel HTTP Client (clave en `.env`)

## Funcionalidades del MVP

1. Registro e inicio de sesión de usuarios
2. Gestión de mascotas (CRUD): nombre, especie, raza, edad y peso
3. Consultas a la IA contextualizadas con los datos de la mascota
4. Orientación ante posibles riesgos (alimentos/sustancias tóxicas, cuándo ir al veterinario)
5. Historial de consultas

## Estructura de carpetas

```
app/
  Http/Controllers/Autenticacion/      # Registro e inicio de sesión
  Http/Controllers/                    # Controladores de mascotas y consultas
  Http/Requests/                       # Validación de formularios
  Models/                              # Modelos: usuario, mascota, consulta
  Services/IA/                        # Conexión con la API de Groq
resources/views/
  plantillas/                          # Plantilla base de todas las páginas (Bootstrap)
  components/                          # Piezas de interfaz reutilizables
  autenticacion/                       # Pantallas de login y registro
  mascotas/                            # Pantallas para crear, ver, editar y borrar mascotas
  consultas/                           # Preguntas a la IA e historial
public/imagenes/                       # Imágenes y logos
database/migrations/                   # Tablas de la base de datos
documentacion/                         # Propuesta del proyecto
```

> Las carpetas en inglés (`app`, `Http`, `Controllers`, `Requests`, `Models`, `components`, etc.)
> son estándar de Laravel y no se pueden renombrar sin romper el framework.

## Instalación local

```bash
composer install
cp .env.example .env
php artisan key:generate
# Configurar credenciales de PostgreSQL y GROQ_API_KEY en .env
php artisan migrate
php artisan serve
```

## Cronograma

| Semana | Hito |
|---|---|
| 1 | Propuesta + Mockup inicial |
| 2 | Rutas, vistas (Blade) y base de datos (Eloquent) |
| 3 | Integración de la IA (API de Groq) y funcionalidad principal |
| 4 | Presentación final + Control |

> PetCare AI entrega orientación general; no reemplaza el diagnóstico ni el tratamiento de un médico veterinario.
