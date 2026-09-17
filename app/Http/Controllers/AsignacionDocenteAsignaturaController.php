<?php

namespace App\Http\Controllers;

use App\Models\AsignacionDocenteAsignatura;
use App\Models\Asignatura;
use App\Models\Grupo;
use App\Models\Usuario;
use Illuminate\Http\Request;

class AsignacionDocenteAsignaturaController extends Controller
{
    public function index()
    {
        $asignaciones = AsignacionDocenteAsignatura::with([
            'grupo',
            'asignatura',
            'docente',
        ])
            ->orderBy('grupo_idGrupo')
            ->orderBy('asignatura_idAsignatura')
            ->get();

        return view('asignacion_docente_asignaturas.index', compact('asignaciones'));
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