<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\User;
use App\Models\Usuario;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $usuarios = Usuario::with('rol')
            // Búsqueda por nombre, apellidos o correo
            ->when($request->filled('buscar'), function ($q) use ($request) {
                $texto = '%' . $request->buscar . '%';

                $q->where(fn ($q) => $q
                    ->where('nombre', 'like', $texto)
                    ->orWhere('apellidoPaterno', 'like', $texto)
                    ->orWhere('apellidoMaterno', 'like', $texto)
                    ->orWhere('correo', 'like', $texto));
            })
            // Filtros por rol y estado
            ->when($request->filled('rol'), fn ($q) => $q->where('rol_idRol', $request->rol))
            ->when($request->filled('estado'), fn ($q) => $q->where('estado', $request->estado))
            // Orden alfabético por apellidos
            ->orderBy('apellidoPaterno')
            ->orderBy('apellidoMaterno')
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        $roles = Rol::orderBy('nombre')->get();

        return view('usuarios.index', compact('usuarios', 'roles'));
    }

    public function create()
    {
        $roles = Rol::orderBy('nombre')->get();

        return view('usuarios.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate($this->reglasBase() + [
            'correo' => [
                'required',
                'string',
                'email',
                'max:150',
                'unique:usuarios,correo',
                'unique:users,email',
            ],
            'contrasena' => [
                'required',
                'string',
                'confirmed',
                Password::defaults(),
            ],
        ], $this->mensajesContrasena());

        DB::transaction(function () use ($datos) {
            $contrasenaCifrada = Hash::make($datos['contrasena']);

            $usuario = Usuario::create([
                'nombre' => $datos['nombre'],
                'apellidoPaterno' => $datos['apellidoPaterno'],
                'apellidoMaterno' => $datos['apellidoMaterno'],
                'correo' => $datos['correo'],
                'contrasena' => $contrasenaCifrada,
                'rol_idRol' => $datos['rol_idRol'],
                'estado' => $datos['estado'],
                'fRegistro' => now()->toDateString(),
            ]);

            User::create([
                'name' => $this->nombreCompleto($datos),
                'email' => $datos['correo'],
                'password' => $contrasenaCifrada,
                'usuario_idUsuario' => $usuario->idUsuario,
            ]);
        });

        return redirect()
            ->route('usuarios.index')
            ->with(
                'success',
                'Usuario y cuenta de acceso registrados correctamente.'
            );
    }

    public function edit($idUsuario)
    {
        $usuario = Usuario::findOrFail($idUsuario);
        $roles = Rol::orderBy('nombre')->get();

        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    public function update(Request $request, $idUsuario)
    {
        $usuario = Usuario::findOrFail($idUsuario);

        $reglas = $this->reglasBase() + [
            'correo' => [
                'required',
                'string',
                'email',
                'max:150',
                Rule::unique('usuarios', 'correo')
                    ->ignore($usuario->idUsuario, 'idUsuario'),
                Rule::unique('users', 'email')
                    ->ignore($usuario->cuentaAcceso?->id),
            ],
        ];

        if ($request->filled('contrasena')) {
            $reglas['contrasena'] = [
                'string',
                'confirmed',
                Password::defaults(),
            ];
        }

        $datos = $request->validate($reglas, $this->mensajesContrasena());

        $esMiCuenta = (int) $request->user()->usuario_idUsuario
            === (int) $usuario->idUsuario;

        if (
            $esMiCuenta
            && (
                ! (bool) $datos['estado']
                || (int) $datos['rol_idRol'] !== (int) $usuario->rol_idRol
            )
        ) {
            return back()
                ->withInput($request->except([
                    'contrasena',
                    'contrasena_confirmation',
                ]))
                ->with(
                    'error',
                    'No puedes desactivar tu propia cuenta ni cambiar tu propio rol.'
                );
        }

        DB::transaction(function () use ($usuario, $datos) {
            $contrasenaCifrada = null;

            if (isset($datos['contrasena'])) {
                $contrasenaCifrada = Hash::make($datos['contrasena']);
                $datos['contrasena'] = $contrasenaCifrada;
            }

            $usuario->update($datos);

            $cuenta = $usuario->cuentaAcceso;

            $cambios = [
                'name' => $this->nombreCompleto($datos),
                'email' => $datos['correo'],
            ];

            if ($cuenta) {
                if ($contrasenaCifrada !== null) {
                    $cambios['password'] = $contrasenaCifrada;
                }

                $cuenta->update($cambios);
            } else {
                // Si el usuario no tenía cuenta de acceso, se crea ahora
                User::create($cambios + [
                    'password' => $contrasenaCifrada ?? $usuario->contrasena,
                    'usuario_idUsuario' => $usuario->idUsuario,
                ]);
            }
        });

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy($idUsuario)
    {
        $usuario = Usuario::findOrFail($idUsuario);

        if (
            (int) auth()->user()->usuario_idUsuario
            === (int) $usuario->idUsuario
        ) {
            return back()->with(
                'error',
                'No puedes eliminar tu propia cuenta.'
            );
        }

        // Protege al único administrador activo
        $esAdministrador = $usuario->rol?->nombre === 'Administrador';

        if (
            $esAdministrador
            && Usuario::where('estado', true)
                ->whereHas('rol', fn ($q) => $q->where('nombre', 'Administrador'))
                ->count() <= 1
        ) {
            return back()->with(
                'error',
                'No puedes eliminar al único administrador activo.'
            );
        }

        $motivos = $this->motivosDeVinculacion($usuario->idUsuario);

        if (! empty($motivos)) {
            return back()->with(
                'error',
                "No se puede eliminar a {$usuario->nombre} {$usuario->apellidoPaterno} porque "
                . implode('; ', $motivos)
                . '. Si ya no debe entrar al sistema, desactívalo desde Editar.'
            );
        }

        try {
            DB::transaction(function () use ($usuario) {
                $usuario->cuentaAcceso?->delete();
                $usuario->delete();
            });
        } catch (QueryException $e) {
            if ((int) ($e->errorInfo[1] ?? 0) === 1451) {
                return back()->with(
                    'error',
                    'No se puede eliminar este usuario porque tiene información vinculada en el sistema. Desactívalo desde Editar.'
                );
            }

            throw $e;
        }

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }

    private function reglasBase(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:45'],
            'apellidoPaterno' => ['required', 'string', 'max:45'],
            'apellidoMaterno' => ['required', 'string', 'max:45'],
            'rol_idRol' => ['required', 'exists:roles,idRol'],
            'estado' => ['required', 'boolean'],
        ];
    }

    private function mensajesContrasena(): array
    {
        return [
            'contrasena.confirmed' => 'Las contraseñas no coinciden.',
            'contrasena.min' => 'La contraseña debe tener al menos 10 caracteres.',
            'contrasena.letters' => 'La contraseña debe incluir al menos una letra.',
            'contrasena.mixed' => 'La contraseña debe incluir mayúsculas y minúsculas.',
            'contrasena.numbers' => 'La contraseña debe incluir al menos un número.',
            'contrasena.uncompromised' => 'Esa contraseña apareció en filtraciones de datos. Elige otra.',
        ];
    }

    private function nombreCompleto(array $datos): string
    {
        return trim(
            "{$datos['nombre']} {$datos['apellidoPaterno']} {$datos['apellidoMaterno']}"
        );
    }

    private function motivosDeVinculacion(int $idUsuario): array
    {
        $motivos = [];

        $alumnos = DB::table('tutor_alumnos')
            ->join(
                'alumnos',
                'alumnos.idAlumno',
                '=',
                'tutor_alumnos.alumno_idAlumno'
            )
            ->where('tutor_alumnos.usuario_idUsuario', $idUsuario)
            ->get([
                'alumnos.nombre',
                'alumnos.apellidoPaterno',
            ]);

        if ($alumnos->isNotEmpty()) {
            $nombres = $alumnos
                ->map(fn ($alumno) => trim(
                    "{$alumno->nombre} {$alumno->apellidoPaterno}"
                ))
                ->unique()
                ->implode(', ');

            $motivos[] = "tiene alumnos vinculados en el sistema ({$nombres})";
        }

        $grupos = DB::table('grupos')
            ->where('idMaestro', $idUsuario)
            ->get(['grado', 'letraGrupo', 'cicloEsc']);

        if ($grupos->isNotEmpty()) {
            $nombresGrupos = $grupos
                ->map(fn ($grupo) =>
                    "{$grupo->grado}° {$grupo->letraGrupo} ({$grupo->cicloEsc})"
                )
                ->implode(', ');

            $motivos[] = "es maestro titular de grupos ({$nombresGrupos})";
        }

        $vinculos = [
            'asignacion_docente_asignaturas' => [
                'docente_idUsuario',
                'está asignado como docente a grupos o materias',
            ],
            'solicitudes_acceso_salon' => [
                'tutor_idUsuario',
                'tiene solicitudes de acceso registradas',
            ],
            'calificaciones' => [
                'usuario_idUsuario',
                'tiene calificaciones capturadas',
            ],
        ];

        foreach ($vinculos as $tabla => [$columna, $mensaje]) {
            if (
                DB::table($tabla)
                    ->where($columna, $idUsuario)
                    ->exists()
            ) {
                $motivos[] = $mensaje;
            }
        }

        return $motivos;
    }
}