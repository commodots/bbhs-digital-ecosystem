<?php
namespace App\Http\Controllers;
use App\Models\HistoricalArchive; use Illuminate\Http\Request; use Inertia\Inertia;
class HistoricalArchiveController extends Controller {
 public function index(Request $request) {
  $archives=HistoricalArchive::where('is_published',true)
   ->when($request->filled('category'),fn($q)=>$q->where('category',$request->input('category')))
   ->when($request->filled('year'),fn($q)=>$q->where('record_year',$request->integer('year')))
   ->when($request->filled('search'),function($q)use($request){$term=trim($request->input('search'));$q->where(fn($s)=>$s->where('title','like',"%{$term}%")->orWhere('summary','like',"%{$term}%")->orWhere('body','like',"%{$term}%"));})
   ->orderByDesc('is_featured')->orderByDesc('record_year')->orderByDesc('published_at')->paginate(12)->withQueryString();
  return Inertia::render('Archives/Index',['archives'=>$archives,'categories'=>HistoricalArchive::CATEGORIES,'filters'=>$request->only('category','year','search')]);
 }
 public function show(HistoricalArchive $archive) {
  abort_unless($archive->is_published,404);
  return Inertia::render('Archives/Show',['archive'=>$archive,'related'=>HistoricalArchive::where('is_published',true)->where('category',$archive->category)->where('id','!=',$archive->id)->latest('record_year')->limit(4)->get(['title','slug','category','record_year','summary'])]);
 }
}
