<?php

namespace App\Http\Controllers;

use App\Models\AsignacionDocenteAsignatura;
use App\Models\Comunicado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ForoDocenteController extends Controller
{
    private function docenteId(Request $request): int
    {
        return (int) $request->user()->usuario->idUsuario;
    }

    private function asignaciones(Request $request)
    {
        return AsignacionDocenteAsignatura::with(['grupo', 'asignatura'])
            ->where('docente_idUsuario', $this->docenteId($request))
            ->where('estado', true)
            ->get();
    }

    private function reglas(Request $request): array
    {
        return [
            'asignacion_id' => [
                'required', 'integer',
                Rule::exists('asignacion_docente_asignaturas', 'id')
                    ->where('docente_idUsuario', $this->docenteId($request))
                    ->where('estado', true),
            ],
            'tipo' => ['required', Rule::in(array_keys(Comunicado::TIPOS))],
            'titulo' => ['required', 'string', 'max:120'],
            'contenido' => ['required', 'string', 'max:2000'],
            'fecha_entrega' => ['nullable', 'required_if:tipo,tarea', 'date', 'after_or_equal:today'],
        ];
    }

    private function mensajes(): array
{
    return [
        'asignacion_id.required' => 'Selecciona un grupo y una materia.',
        'asignacion_id.exists' => 'Selecciona un grupo y una materia válidos.',
        'tipo.required' => 'Selecciona el tipo de comunicado.',
        'titulo.required' => 'Ingresa un título.',
        'titulo.max' => 'El título supera el límite permitido.',
        'contenido.required' => 'Escribe el mensaje del comunicado.',
        'contenido.max' => 'El mensaje supera el límite permitido.',
        'fecha_entrega.required_if' => 'Indica la fecha de entrega de la tarea.',
        'fecha_entrega.after_or_equal' => 'La fecha de entrega no puede ser anterior a hoy.',
    ];
}

    public function index(Request $request)
    {
        $comunicados = Comunicado::with(['asignacion.grupo', 'asignacion.asignatura'])
            ->where('docente_idUsuario', $this->docenteId($request))
            ->latest()
            ->paginate(10);

        return view('docente.foro.index', compact('comunicados'));
    }

    public function create(Request $request)
    {
        return view('docente.foro.form', [
            'comunicado' => new Comunicado(),
            'asignaciones' => $this->asignaciones($request),
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate($this->reglas($request));

        if ($datos['tipo'] !== 'tarea') {
            $datos['fecha_entrega'] = null;
        }

        $datos['docente_idUsuario'] = $this->docenteId($request);
        Comunicado::create($datos);

        return redirect()->route('docente.foro.index')
            ->with('success', 'Comunicado publicado correctamente.');
    }

    public function edit(Request $request, Comunicado $comunicado)
    {
        Gate::authorize('update', $comunicado);

        return view('docente.foro.form', [
            'comunicado' => $comunicado,
            'asignaciones' => $this->asignaciones($request),
        ]);
    }

    public function update(Request $request, Comunicado $comunicado)
    {
        Gate::authorize('update', $comunicado);

        $datos = $request->validate($this->reglas($request));

        if ($datos['tipo'] !== 'tarea') {
            $datos['fecha_entrega'] = null;
        }

        $comunicado->update($datos);

        return redirect()->route('docente.foro.index')
            ->with('success', 'Comunicado actualizado correctamente.');
    }

    public function destroy(Comunicado $comunicado)
    {
        Gate::authorize('delete', $comunicado);

        $comunicado->delete();

        return redirect()->route('docente.foro.index')
            ->with('success', 'Comunicado eliminado.');
    }
}