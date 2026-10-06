<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;use App\Models\Equipement;
class EquipementController extends Controller {public function index(){return view('admin.equipements.index',['equipements'=>Equipement::with('user')->withCount('conseils')->latest()->get()]);}public function show(Equipement $equipement){return view('admin.equipements.show',['equipement'=>$equipement->load('user','conseils')]);}}
