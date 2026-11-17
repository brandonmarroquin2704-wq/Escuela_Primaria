<?php

namespace App\Http\Controllers;

use App\Models\Escuela;
use App\Models\Grupo;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GrupoController extends Controller
{
        public function index(Request $request)
    {
        $grupos = Grupo::with(['maestro', 'escuela'])
            // Búsqueda por nombre del maestro responsable
            ->when($request->filled('buscar'), function ($q) use ($request) {
                $texto = '%' . $request->buscar . '%';

                $q->whereHas('maestro', fn ($m) => $m
                    ->where('nombre', 'like', $texto)
                    ->orWhere('apellidoPaterno', 'like', $texto)
                    ->orWhere('apellidoMaterno', 'like', $texto));
            })
            // Filtros por grado, ciclo escolar y estado
            ->when($request->filled('grado'), fn ($q) => $q->where('grado', $request->grado))
            ->when($request->filled('ciclo'), fn ($q) => $q->where('cicloEsc', $request->ciclo))
            ->when($request->filled('estado'), fn ($q) => $q->where('estado', $request->estado))
            ->orderBy('cicloEsc')
            ->orderBy('grado')
            ->orderBy('letraGrupo')
            ->paginate(10)
            ->withQueryString();

        // Ciclos escolares existentes, para el filtro
        $ciclos = Grupo::select('cicloEsc')->distinct()->orderBy('cicloEsc')->pluck('cicloEsc');

        return view('grupos.index', compact('grupos', 'ciclos'));
    }

    public function create()
    {
        $escuelas = Escuela::orderBy('nombre')->get();

        $maestros = Usuario::where('estado', true)
            ->whereHas('rol', function ($query) {
                $query->where('nombre', 'Docente');
            })
            ->orderBy('apellidoPaterno')
            ->orderBy('apellidoMaterno')
            ->orderBy('nombre')
            ->get();

        return view('grupos.create', compact('escuelas', 'maestros'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'grado' => ['required', 'integer', 'between:1,6'],
            'letraGrupo' => ['required', 'string', 'size:1', 'alpha'],
            'cicloEsc' => ['required', 'string', 'max:25'],
            'estado' => ['required', 'boolean'],
            'idMaestro' => ['required', 'exists:usuarios,idUsuario'],
            'escuela_idEscuela' => ['required', 'exists:escuelas,idEscuela'],
        ]);

        $datos['letraGrupo'] = strtoupper($datos['letraGrupo']);

        $duplicado = Grupo::where('grado', $datos['grado'])
            ->where('letraGrupo', $datos['letraGrupo'])
            ->where('cicloEsc', $datos['cicloEsc'])
            ->where('escuela_idEscuela', $datos['escuela_idEscuela'])
            ->exists();

        if ($duplicado) {
            return back()
                ->withErrors([
                    'letraGrupo' => 'Este grupo ya existe para la escuela y ciclo escolar seleccionados.',
                ])
                ->withInput();
        }

        Grupo::create($datos);

        return redirect()
            ->route('grupos.index')
            ->with('success', 'Grupo registrado correctamente.');
    }

    public function show($idGrupo)
    {
        return redirect()->route('grupos.index');
    }

    public function edit($idGrupo)
    {
        $grupo = Grupo::findOrFail($idGrupo);
        $escuelas = Escuela::orderBy('nombre')->get();

        $maestros = Usuario::where('estado', true)
            ->whereHas('rol', function ($query) {
                $query->where('nombre', 'Docente');
            })
            ->orderBy('apellidoPaterno')
            ->orderBy('apellidoMaterno')
            ->orderBy('nombre')
            ->get();

        return view('grupos.edit', compact('grupo', 'escuelas', 'maestros'));
    }

    public function update(Request $request, $idGrupo)
    {
        $grupo = Grupo::findOrFail($idGrupo);

        $datos = $request->validate([
            'grado' => ['required', 'integer', 'between:1,6'],
            'letraGrupo' => ['required', 'string', 'size:1', 'alpha'],
            'cicloEsc' => ['required', 'string', 'max:25'],
            'estado' => ['prohibited'],
            'idMaestro' => ['required', 'exists:usuarios,idUsuario'],
            'escuela_idEscuela' => ['required', 'exists:escuelas,idEscuela'],
        ]);

        $datos['letraGrupo'] = strtoupper($datos['letraGrupo']);

        $duplicado = Grupo::where('grado', $datos['grado'])
            ->where('letraGrupo', $datos['letraGrupo'])
            ->where('cicloEsc', $datos['cicloEsc'])
            ->where('escuela_idEscuela', $datos['escuela_idEscuela'])
            ->where('idGrupo', '!=', $grupo->idGrupo)
            ->exists();

        if ($duplicado) {
            return back()
                ->withErrors([
                    'letraGrupo' => 'Este grupo ya existe para la escuela y ciclo escolar seleccionados.',
                ])
                ->withInput();
        }

        $grupo->update($datos);

        return redirect()
            ->route('grupos.index')
            ->with('success', 'Grupo actualizado correctamente.');
    }

    public function destroy($idGrupo)
    {
        $grupo = Grupo::findOrFail($idGrupo);

        $grupo->delete();

        return redirect()
            ->route('grupos.index')
            ->with('success', 'Grupo eliminado correctamente.');
    }
}