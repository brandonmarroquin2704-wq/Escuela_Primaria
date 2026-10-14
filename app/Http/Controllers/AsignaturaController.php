<?php

namespace App\Http\Controllers;

use App\Models\Asignatura;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AsignaturaController extends Controller
{
    public function index()
    {
        $asignaturas = Asignatura::orderBy('nombre')->get();

        return view('asignaturas.index', compact('asignaturas'));
    }

    public function create()
    {
        return view('asignaturas.create');
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:85', 'unique:asignaturas,nombre'],
            'descripcion' => ['nullable', 'string', 'max:200'],
            'estado' => ['required', 'boolean'],
        ]);

        $datos['fechaRegistro'] = now();
        $datos['fechaActualizacion'] = now();

        Asignatura::create($datos);

        return redirect()
            ->route('asignaturas.index')
            ->with('success', 'Asignatura registrada correctamente.');
    }

    public function edit(Asignatura $asignatura)
    {
        return view('asignaturas.edit', compact('asignatura'));
    }

    public function update(Request $request, Asignatura $asignatura)
    {
        $datos = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:85',
                Rule::unique('asignaturas', 'nombre')
                    ->ignore($asignatura->idAsignatura, 'idAsignatura'),
            ],
            'descripcion' => ['nullable', 'string', 'max:200'],
            'estado' => ['required', 'boolean'],
        ]);

        $datos['fechaActualizacion'] = now();

        $asignatura->update($datos);

        return redirect()
            ->route('asignaturas.index')
            ->with('success', 'Asignatura actualizada correctamente.');
    }

    public function destroy(Asignatura $asignatura)
    {
        $motivos = $this->motivosDeVinculacion($asignatura->idAsignatura);

        if (! empty($motivos)) {
            return back()->with(
                'error',
                "No se puede eliminar la asignatura {$asignatura->nombre} porque "
                . implode('; ', $motivos)
            );
        }

        try {
            $asignatura->delete();
        } catch (QueryException $e) {
            if ((int) ($e->errorInfo[1] ?? 0) === 1451) {
                return back()->with(
                    'error',
                    'No se puede eliminar esta asignatura porque tiene información vinculada en el sistema. Puedes ponerla como inactiva desde Editar.'
                );
            }

            throw $e;
        }

        return redirect()
            ->route('asignaturas.index')
            ->with('success', 'Asignatura eliminada correctamente.');
    }

    private function motivosDeVinculacion(int $idAsignatura): array
    {
        $motivos = [];

        $asignaciones = DB::table('asignacion_docente_asignaturas as ada')
            ->join('usuarios as u', 'u.idUsuario', '=', 'ada.docente_idUsuario')
            ->join('grupos as g', 'g.idGrupo', '=', 'ada.grupo_idGrupo')
            ->where('ada.asignatura_idAsignatura', $idAsignatura)
            ->get(['u.nombre', 'u.apellidoPaterno', 'g.grado', 'g.letraGrupo']);

        if ($asignaciones->isNotEmpty()) {
            $detalle = $asignaciones
                ->map(fn ($a) => trim("{$a->nombre} {$a->apellidoPaterno}") . " en {$a->grado}° {$a->letraGrupo}")
                ->implode(', ');

            $motivos[] = "está asignada a docentes ({$detalle}). Quita primero esa asignación";
        }

        if (DB::table('calificaciones')->where('asignatura_idAsignatura', $idAsignatura)->exists()) {
            $motivos[] = 'ya tiene calificaciones capturadas. Para conservar el historial, ponla como inactiva desde Editar';
        }

        return $motivos;
    }
}