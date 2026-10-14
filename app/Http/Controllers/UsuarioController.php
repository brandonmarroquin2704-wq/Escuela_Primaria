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

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::with('rol')
            ->orderBy('idUsuario')
            ->get();

        return view('usuarios.index', compact('usuarios'));
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
                'min:8',
                'confirmed',
            ],
        ]);

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
                'min:8',
                'confirmed',
            ];
        }

        $datos = $request->validate($reglas);

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

            if ($cuenta) {
                $cambios = [
                    'name' => $this->nombreCompleto($datos),
                    'email' => $datos['correo'],
                ];

                if ($contrasenaCifrada !== null) {
                    $cambios['password'] = $contrasenaCifrada;
                }

                $cuenta->update($cambios);
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

        //protege administrador 
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