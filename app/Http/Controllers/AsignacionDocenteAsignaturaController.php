<?php

namespace App\Http\Controllers;

use App\Models\AsignacionDocenteAsignatura;
use App\Models\Asignatura;
use App\Models\Grupo;
use App\Models\Usuario;
use Illuminate\Http\Request;

class AsignacionDocenteAsignaturaController extends Controller
{
    public function index(Request $request)
{
    $filtros = $request->validate([
        'buscar' => ['nullable', 'string', 'max:150'],
        'grupo' => ['nullable', 'integer', 'exists:grupos,idGrupo'],
        'ciclo' => ['nullable', 'string', 'max:50'],
        'estado' => ['nullable', 'in:0,1'],
    ]);

    $buscar = trim($filtros['buscar'] ?? '');
    $grupoSeleccionado = $filtros['grupo'] ?? '';
    $cicloSeleccionado = $filtros['ciclo'] ?? '';
    $estadoSeleccionado = $filtros['estado'] ?? '';

    $consulta = AsignacionDocenteAsignatura::query()
        ->with(['grupo', 'asignatura', 'docente']);

    if ($buscar !== '') {
        $palabras = preg_split('/\s+/u', $buscar, -1, PREG_SPLIT_NO_EMPTY);

        $consulta->where(function ($query) use ($buscar, $palabras) {
            $query->whereHas('asignatura', function ($asignatura) use ($buscar) {
                $asignatura->where('nombre', 'like', '%' . $buscar . '%');
            })
            ->orWhereHas('docente', function ($docente) use ($palabras) {
                foreach ($palabras as $palabra) {
                    $docente->where(function ($nombre) use ($palabra) {
                        $nombre->where('nombre', 'like', '%' . $palabra . '%')
                            ->orWhere('apellidoPaterno', 'like', '%' . $palabra . '%')
                            ->orWhere('apellidoMaterno', 'like', '%' . $palabra . '%');
                    });
                }
            });
        });
    }

    if ($grupoSeleccionado !== '') {
        $consulta->where('grupo_idGrupo', $grupoSeleccionado);
    }

    if ($cicloSeleccionado !== '') {
        $consulta->whereHas('grupo', function ($grupo) use ($cicloSeleccionado) {
            $grupo->where('cicloEsc', $cicloSeleccionado);
        });
    }

    if ($estadoSeleccionado !== '') {
        $consulta->where('estado', (int) $estadoSeleccionado);
    }

    $asignaciones = $consulta
        ->orderBy('grupo_idGrupo')
        ->orderBy('asignatura_idAsignatura')
        ->orderBy('id')
        ->paginate(15)
        ->withQueryString();

    $grupos = Grupo::query()
        ->orderByDesc('cicloEsc')
        ->orderBy('grado')
        ->orderBy('letraGrupo')
        ->get();

    $ciclos = Grupo::query()
        ->whereNotNull('cicloEsc')
        ->where('cicloEsc', '!=', '')
        ->distinct()
        ->orderByDesc('cicloEsc')
        ->pluck('cicloEsc');

    return view('asignacion_docente_asignaturas.index', compact(
        'asignaciones',
        'grupos',
        'ciclos',
        'buscar',
        'grupoSeleccionado',
        'cicloSeleccionado',
        'estadoSeleccionado'
    ));
}

    public function create()
    {
        $grupos = Grupo::where('estado', true)
            ->orderBy('cicloEsc')
            ->orderBy('grado')
            ->orderBy('letraGrupo')
            ->get();

        $asignaturas = Asignatura::where('estado', true)
            ->orderBy('nombre')
            ->get();

        $docentes = Usuario::where('estado', true)
            ->whereHas('rol', function ($query) {
                $query->where('nombre', 'Docente');
            })
            ->orderBy('apellidoPaterno')
            ->orderBy('apellidoMaterno')
            ->orderBy('nombre')
            ->get();

        return view(
            'asignacion_docente_asignaturas.create',
            compact('grupos', 'asignaturas', 'docentes')
        );
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'grupo_idGrupo' => ['required', 'exists:grupos,idGrupo'],
            'asignatura_idAsignatura' => ['required', 'exists:asignaturas,idAsignatura'],
            'docente_idUsuario' => ['required', 'exists:usuarios,idUsuario'],
            'estado' => ['required', 'boolean'],
        ]);

        $duplicada = AsignacionDocenteAsignatura::where(
            'grupo_idGrupo',
            $datos['grupo_idGrupo']
        )
            ->where('asignatura_idAsignatura', $datos['asignatura_idAsignatura'])
            ->exists();

        if ($duplicada) {
            return back()
                ->withErrors([
                    'asignatura_idAsignatura' =>
                        'Esta asignatura ya está asignada a ese grupo.',
                ])
                ->withInput();
        }

        AsignacionDocenteAsignatura::create($datos);

        return redirect()
            ->route('asignaciones-docentes.index')
            ->with('success', 'Materia asignada al docente correctamente.');
    }

    public function show($id)
    {
        return redirect()->route('asignaciones-docentes.index');
    }

    public function edit($id)
    {
        $asignacion = AsignacionDocenteAsignatura::findOrFail($id);

        $grupos = Grupo::where('estado', true)
            ->orderBy('cicloEsc')
            ->orderBy('grado')
            ->orderBy('letraGrupo')
            ->get();

        $asignaturas = Asignatura::where('estado', true)
            ->orderBy('nombre')
            ->get();

        $docentes = Usuario::where('estado', true)
            ->whereHas('rol', function ($query) {
                $query->where('nombre', 'Docente');
            })
            ->orderBy('apellidoPaterno')
            ->orderBy('apellidoMaterno')
            ->orderBy('nombre')
            ->get();

        return view(
            'asignacion_docente_asignaturas.edit',
            compact('asignacion', 'grupos', 'asignaturas', 'docentes')
        );
    }

    public function update(Request $request, $id)
    {
        $asignacion = AsignacionDocenteAsignatura::findOrFail($id);

        $datos = $request->validate([
            'grupo_idGrupo' => ['required', 'exists:grupos,idGrupo'],
            'asignatura_idAsignatura' => ['required', 'exists:asignaturas,idAsignatura'],
            'docente_idUsuario' => ['required', 'exists:usuarios,idUsuario'],
            'estado' => ['required', 'boolean'],
        ]);

        $duplicada = AsignacionDocenteAsignatura::where(
            'grupo_idGrupo',
            $datos['grupo_idGrupo']
        )
            ->where('asignatura_idAsignatura', $datos['asignatura_idAsignatura'])
            ->where('id', '!=', $asignacion->id)
            ->exists();

        if ($duplicada) {
            return back()
                ->withErrors([
                    'asignatura_idAsignatura' =>
                        'Esta asignatura ya está asignada a ese grupo.',
                ])
                ->withInput();
        }

        $asignacion->update($datos);

        return redirect()
            ->route('asignaciones-docentes.index')
            ->with('success', 'Asignación actualizada correctamente.');
    }

    public function destroy($id)
    {
        $asignacion = AsignacionDocenteAsignatura::findOrFail($id);

        $asignacion->delete();

        return redirect()
            ->route('asignaciones-docentes.index')
            ->with('success', 'Asignación eliminada correctamente.');
    }
}