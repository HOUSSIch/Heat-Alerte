<?php
namespace App\Http\Controllers;
use App\Models\Conseil; use App\Models\Equipement; use Illuminate\Http\Request;
class ConseilController extends Controller {
 public function index(){ return view('conseils.index',['conseils'=>Conseil::withCount('equipements')->latest()->get()]); }
 public function create(){ return view('conseils.create',['equipements'=>Equipement::orderBy('nom')->get()]); }
 public function store(Request $r){ $c=Conseil::create($this->data($r)); $c->equipements()->sync($r->input('equipements',[])); return redirect()->route('conseils.index')->with('success','Conseil ajouté.'); }
 public function show(Conseil $conseil){ return view('conseils.show',['conseil'=>$conseil->load('equipements')]); }
 public function edit(Conseil $conseil){ return view('conseils.edit',['conseil'=>$conseil->load('equipements'),'equipements'=>Equipement::orderBy('nom')->get()]); }
 public function update(Request $r, Conseil $conseil){ $conseil->update($this->data($r)); $conseil->equipements()->sync($r->input('equipements',[])); return redirect()->route('conseils.show',$conseil)->with('success','Conseil modifié.'); }
 public function destroy(Conseil $conseil){ $conseil->delete(); return redirect()->route('conseils.index')->with('success','Conseil supprimé.'); }
 public function mes(Request $r){ $conseils=Conseil::query()->where('actif',true)->whereHas('equipements',fn($q)=>$q->where('user_id',$r->user()->id))->with(['equipements'=>fn($q)=>$q->where('user_id',$r->user()->id)])->distinct()->get(); return view('conseils.mes',compact('conseils')); }
 private function data(Request $r): array { $d=$r->validate(['titre'=>['required','string','max:255'],'description'=>['required','string'],'categorie'=>['required','string','max:255'],'niveau'=>['required','string','max:255'],'actif'=>['nullable','boolean'],'equipements'=>['nullable','array'],'equipements.*'=>['integer','exists:equipements,id']]); $d['actif']=$r->boolean('actif'); unset($d['equipements']); return $d; }
}
