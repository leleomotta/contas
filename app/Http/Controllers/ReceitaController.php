<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Conta;
use App\Models\Receita;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class ReceitaController extends Controller
{
    public function bosta(int $ID_Receita)
    {
        dd($ID_Receita);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $receita = new Receita();

        $receita->Descricao = $request->Descricao;
        $receita->Valor =
            str_replace(",",'.',str_replace(".","",
                str_replace("R$ ","",$request->Valor)));
        $receita->Data = implode("-",array_reverse(explode("/",$request->Data)));
        $receita->ID_Conta = $request->Conta;
        $receita->ID_Categoria = $request->Categoria;

        $request["Efetivada"] = (isset($request["Efetivada"]))?1:0;
        $receita->Efetivada = $request->Efetivada;

        $receita->save();

        $url ='/receitas?date_filter=' . \Carbon\Carbon::now()->isoFormat('Y') . '-' .
        \Carbon\Carbon::now()->isoFormat('MM');
        return redirect::to($url);
        //return redirect()->route('receitas.showAll');

    }

    /**
     * Display the specified resource.
     */
    public function show(Receita $receita)
    {
        //
    }
    public function showAll(Request $request){
        $contas = Conta::where(function ($query) {
            $query->select('*');
            $query->orderBy('Descricao','ASC');
        })->get();

        $categorias = Categoria::where(function ($query) {
            $query->select('*');
            $query->orderBy('Nome','ASC');
        })->get();

        $dateFilter = $request->date_filter;
        $receitas = new Receita();

        /*
         * Não precisa mais disso por que agora sempre vem o date_filter

        //fiz uma alteração para mostrar só as despesas do mês corrente
        if (is_null($dateFilter) ) {
            //Session::forget('filtros');
            $dateFilter = Carbon::now()->isoFormat('Y') . '-' . Carbon::now()->isoFormat('MM');

            return view('receitaListar', [
                'receitas' => $receitas->show($dateFilter),
                'contas' => $contas,
                'categorias' => $categorias
            ]);
        }
        else {
            */
        return view('receitaListar', [
            'receitas' => $receitas->show($dateFilter),
            'contas' => $contas,
            'categorias' => $categorias
        ]);

    }

    public function filter(Request $request){
        $start_date = implode("-",array_reverse(explode("/",substr($request->datas,0,10) )));
        $end_date = implode("-",array_reverse(explode("/",substr($request->datas,13,10) )));

        $request["chkCategoria"] = (isset($request["chkCategoria"]))?1:0;
        $request["chkConta"] = (isset($request["chkConta"]))?1:0;
        $request["chkTexto"] = (isset($request["chkTexto"]))?1:0;
        $request["chkDatas"] = (isset($request["chkDatas"]))?1:0;

        //$filtros = Receita::orderBy('Data','DESC');

        $filtros = DB::table('receita')
            ->select('receita.*', 'categoria.Nome as NomeCategoria', 'conta.Banco' )
            ->join('conta', 'receita.ID_Conta', '=', 'conta.ID_Conta')
            ->join('categoria', 'receita.ID_Categoria', '=', 'categoria.ID_Categoria')
            ->orderBy('Data','DESC');

        if($request["chkCategoria"]){
            $filtros = $filtros->where("receita.ID_Categoria", "=", $request->categoria);
        }

        if($request["chkConta"]){
            $filtros = $filtros->where("receita.ID_Conta", "=", $request->conta);
        }

        if($request["chkTexto"]){
            $filtros = $filtros->where("receita.Descricao", "LIKE", "%" . $request->texto . "%");
        }
        if($request["chkDatas"]){
            $filtros = $filtros->whereBetween('Data',[$start_date,$end_date]);
        }


        $receitas = $filtros->get();

        $contas = Conta::where(function ($query) {
            $query->select('*');
            $query->orderBy('Descricao','ASC');
        })->get();

        $categorias = Categoria::where(function ($query) {
            $query->select('*');
            $query->orderBy('Nome','ASC');
        })->get();

        return view('receitaListar',
            [ 'receitas' => $receitas,
              'categorias' => $categorias,
              'contas' => $contas,
        ]);
    }
    /**
     * Update the specified resource in storage.
     */

    public function edit(int $ID_Receita) {
        dd($ID_Receita);
        //$questao = Questao::find($idQuestao);
        /*
        $questao = Questao::where('idQuestao', $idQuestao)->get()->toArray();

        if ( ((session()->get('UsuarioLogado'))->Administrador == 1) or ( (session()->get('UsuarioLogado'))->idUsuario  == $questao[0]['idUsuario'] ) ) {
            $alternativas = Alternativa::where("idQuestao", '=', $idQuestao)->get();
            $questao[0]["NumOpcoes"] = $alternativas->count();

            array_push($questao, $alternativas->toArray());

            return view('admin/questaoEditar', [
                'questao' => $questao
            ]);
        }
        else{
            return redirect('/')->with('fail', 'Você não pode realizar essa ação!');
        }
        */

    }

    public function update(Request $request, Receita $receita)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    //public function destroy(Receita $receita)
    public function destroy(int $ID_Receita)
    {
        $receita = Receita::find($ID_Receita);
        try {
            DB::beginTransaction();

            $receita->delete();

            DB::commit();
            /*return redirect()->route('receitas.showAll', [
                'page' => Request::capture()->page
            ]);*/
            $url ='/receitas?date_filter=' . \Carbon\Carbon::now()->isoFormat('Y') . '-' .
                \Carbon\Carbon::now()->isoFormat('MM');
            return redirect::to($url);

        } catch (\Exception $e) {
            DB::rollback();

            return back();
        }
    }

    public function new(){
        $contas = Conta::where(function ($query) {
            $query->select('*');
            $query->orderBy('Descricao','ASC');
        })->get();

        $categorias = Categoria::where(function ($query) {
            $query->select('*');
            $query->orderBy('Nome','ASC');
        })->get();

        return view('receitaCriar', [
            'categorias' => $categorias,
            'contas' => $contas,
        ]);

    }
}
