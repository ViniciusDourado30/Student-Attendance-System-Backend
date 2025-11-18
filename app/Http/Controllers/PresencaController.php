<?php

namespace App\Http\Controllers;

use App\Models\Aluno;
use App\Models\Chamada;
use App\Models\Presenca;
use App\Models\Turma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PresencaController extends Controller
{
    /**
     * Store a newly created resource in storage.
     * Guarda os registos de presença para uma chamada específica.
     */
    public function store(Request $request, Chamada $chamada)
    {
        // 1. Verificação de Segurança: A chamada pertence ao professor logado?
        if ($chamada->user_id !== Auth::id()) {
            return response()->json(['message' => 'Acesso não autorizado a esta chamada.'], 403);
        }

        // 2. Valida os dados recebidos. Esperamos um array de presenças.
        $validatedData = $request->validate([
            'presencas' => 'required|array',
            'presencas.*.aluno_id' => 'required|integer|exists:alunos,id',
            'presencas.*.status' => ['required', Rule::in(['presente', 'ausente', 'justificado'])],
        ]);

        // 3. Itera sobre cada registo de presença enviado
        foreach ($validatedData['presencas'] as $presencaData) {
            // Usamos updateOrCreate para evitar duplicados.
            // Ele procura um registo com a mesma chamada_id e aluno_id.
            // Se encontrar, atualiza o status. Se não, cria um novo.
            Presenca::updateOrCreate(
                [
                    'chamada_id' => $chamada->id,
                    'aluno_id' => $presencaData['aluno_id'],
                ],
                [
                    'status' => $presencaData['status'],
                ]
            );
        }

        // 4. Retorna uma resposta de sucesso
        return response()->json(['message' => 'Presenças registadas com sucesso.'], 201);
    }

    /**
     * POST /api/faltas
     * Cria (em lote) faltas (status = 'ausente') para os alunos informados,
     * vinculando à chamada do dia para a turma. Se a chamada não existir, cria.
     */
    public function storeFaltas(Request $request)
    {
        $data = $request->validate([
            'turma_id'    => 'nullable|integer|exists:turmas,id',
            'data'        => 'required|date',
            'aluno_ids'   => 'required|array|min:1',
            'aluno_ids.*' => 'integer|exists:alunos,id',
        ]);

        $alunoIds = array_values(array_unique($data['aluno_ids']));

        // Carrega turma_id(s) dos alunos informados
        $alunos = Aluno::query()
            ->whereIn('id', $alunoIds)
            ->get(['id', 'turma_id']);

        if ($alunos->count() !== count($alunoIds)) {
            return response()->json(['message' => 'Alguns alunos não foram encontrados.'], 422);
        }

        // Determina a turma: usa a informada, ou deduz pela turma do primeiro aluno
        $turmaId = $data['turma_id'] ?? $alunos->first()->turma_id;
        if (!$turmaId) {
            return response()->json(['message' => 'Não foi possível identificar a turma. Informe turma_id ou alunos com turma definida.'], 422);
        }

        // Verifica se todos os alunos pertencem à mesma turma
        $todosMesmaTurma = $alunos->every(fn ($a) => (int) $a->turma_id === (int) $turmaId);
        if (!$todosMesmaTurma) {
            return response()->json(['message' => 'Todos os alunos devem pertencer à mesma turma.'], 422);
        }

        // Verifica se o professor tem acesso à turma
        $turma = Turma::query()->findOrFail($turmaId);
        if ($turma->user_id !== Auth::id()) {
            return response()->json(['message' => 'Acesso não autorizado a esta turma.'], 403);
        }

        // Busca ou cria a chamada do dia para esta turma e professor
        $chamada = Chamada::query()->firstOrCreate(
            [
                'user_id'  => Auth::id(),
                'turma_id' => $turma->id,
                'data'     => $data['data'],
            ],
            []
        );

        $created = 0;
        DB::beginTransaction();
        try {
            foreach ($alunoIds as $alunoId) {
                Presenca::updateOrCreate(
                    [
                        'chamada_id' => $chamada->id,
                        'aluno_id'   => $alunoId,
                    ],
                    [
                        'status'     => 'ausente',
                    ]
                );
                $created++;
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erro ao registar faltas.',
                'error'   => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message'        => 'Faltas registadas com sucesso.',
            'chamada_id'     => $chamada->id,
            'turma_id'       => $turma->id,
            'data'           => $data['data'],
            'total_registos' => $created,
            'aluno_ids'      => $alunoIds,
        ], 201);
    }

    /**
     * GET /api/faltas?turma_id=...&data=YYYY-MM-DD
     * Retorna as faltas (status = 'ausente') da turma no dia.
     */
    public function indexFaltas(Request $request)
    {
        // Permite usar tanto 'turma_id' quanto 'sala_id' como alias
        $turmaId = $request->input('turma_id');
        if (!$turmaId) {
            $turmaId = $request->input('sala_id');
        }

        if (!$turmaId) {
            return response()->json([
                'message' => 'Informe turma_id (ou sala_id) e data no formato YYYY-MM-DD.'
            ], 422);
        }

        // Mescla para validar normalmente com a regra de exists
        $request->merge(['turma_id' => $turmaId]);

        $query = $request->validate([
            'turma_id' => 'required|integer|exists:turmas,id',
            'data'     => 'required|date',
        ]);

        // Autorização: turma do professor logado
        $turma = Turma::query()->findOrFail($query['turma_id']);
        if ($turma->user_id !== Auth::id()) {
            return response()->json(['message' => 'Acesso não autorizado a esta turma.'], 403);
        }

        // Encontra a chamada do dia
        $chamada = Chamada::query()->where([
            'user_id'  => Auth::id(),
            'turma_id' => $turma->id,
            'data'     => $query['data'],
        ])->first();

        if (!$chamada) {
            return response()->json([
                'faltas' => [],
                'total'  => 0,
            ]);
        }

        // Lista as presenças com status 'ausente'
        $faltas = Presenca::query()
            ->with('aluno')
            ->where('chamada_id', $chamada->id)
            ->where('status', 'ausente')
            ->get()
            ->map(function ($p) {
                return [
                    'id'         => $p->id,
                    'aluno_id'   => $p->aluno_id,
                    'aluno_nome' => optional($p->aluno)->name,
                    'status'     => $p->status,
                ];
            });

        return response()->json([
            'chamada_id' => $chamada->id,
            'turma_id'   => $turma->id,
            'data'       => $query['data'],
            'total'      => $faltas->count(),
            'faltas'     => $faltas,
        ]);
    }
}
