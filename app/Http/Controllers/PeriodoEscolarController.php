<?php

namespace App\Http\Controllers;

use App\Models\PeriodoEscolar;
use Illuminate\Http\Request;

class PeriodoEscolarController extends Controller
{
    /**
     * Muestra todos los periodos escolares.
     */
    public function index()
    {
        $periodos = PeriodoEscolar::orderByDesc('fechaInicio')->get();

        return view('periodos_escolares.index', compact('periodos'));
    }

    /**
     * Muestra el formulario para registrar un periodo escolar.
     */
    public function create()
    {
        return view('periodos_escolares.create');
    }

    /**
     * Guarda un periodo escolar.
     */
    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:45'],
            'cicloEscolar' => ['required', 'string', 'max:40'],
            'fechaInicio' => ['required', 'date'],
            'fechaFin' => ['required', 'date', 'after:fechaInicio'],
            'estado' => ['required', 'boolean'],
        ]);

        PeriodoEscolar::create($datos);

        return redirect()
            ->route('periodos-escolares.index')
            ->with('success', 'Periodo escolar registrado correctamente.');
    }

    /**
     * Muestra un periodo escolar específico.
     */
    public function show(PeriodoEscolar $periodoEscolar)
    {
        return view('periodos_escolares.show', compact('periodoEscolar'));
    }

    /**
     * Muestra el formulario para editar un periodo escolar.
     */
    public function edit(PeriodoEscolar $periodoEscolar)
    {
        return view('periodos_escolares.edit', compact('periodoEscolar'));
    }

    /**
     * Actualiza un periodo escolar.
     */
    public function update(Request $request, PeriodoEscolar $periodoEscolar)
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:45'],
            'cicloEscolar' => ['required', 'string', 'max:40'],
            'fechaInicio' => ['required', 'date'],
            'fechaFin' => ['required', 'date', 'after:fechaInicio'],
            'estado' => ['required', 'boolean'],
        ]);

        $periodoEscolar->update($datos);

        return redirect()
            ->route('periodos-escolares.index')
            ->with('success', 'Periodo escolar actualizado correctamente.');
    }

    /**
     * Elimina un periodo escolar.
     */
    public function destroy(PeriodoEscolar $periodoEscolar)
    {
        $periodoEscolar->delete();

        return redirect()
            ->route('periodos-escolares.index')
            ->with('success', 'Periodo escolar eliminado correctamente.');
    }
}