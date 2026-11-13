<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller; use App\Models\HistoricalArchive; use Illuminate\Http\Request; use Illuminate\Support\Facades\Storage; use Illuminate\Support\Str; use Inertia\Inertia;
class AdminHistoricalArchiveController extends Controller {
 public function index(Request $request){
  $archives=HistoricalArchive::query()
   ->when($request->filled('search'),function($q)use($request){$term=trim($request->input('search'));$q->where(fn($s)=>$s->where('title','like',"%{$term}%")->orWhere('summary','like',"%{$term}%")->orWhere('body','like',"%{$term}%"));})
   ->when($request->filled('category'),fn($q)=>$q->where('category',$request->input('category')))
   ->when($request->input('status')==='published',fn($q)=>$q->where('is_published',true))
   ->when($request->input('status')==='draft',fn($q)=>$q->where('is_published',false))
   ->latest()->paginate(20)->withQueryString();
  return Inertia::render('Admin/Archives/Index',['archives'=>$archives,'categories'=>HistoricalArchive::CATEGORIES,'filters'=>$request->only('search','category','status')]);
 }
 public function create(){return Inertia::render('Admin/Archives/Form',['archive'=>null,'categories'=>HistoricalArchive::CATEGORIES]);}
 public function store(Request $request){$data=$this->validated($request);$data['slug']=$this->uniqueSlug($data['title']);$data['created_by']=$request->user()->id;$data['is_published']=$request->boolean('is_published');$data['is_featured']=$request->boolean('is_featured');$data['published_at']=$data['is_published']?now():null;HistoricalArchive::create($data);return redirect()->route('admin.archives.index')->with('success','Historical archive record created.');}
 public function edit(HistoricalArchive $archive){return Inertia::render('Admin/Archives/Form',['archive'=>$archive,'categories'=>HistoricalArchive::CATEGORIES]);}
 public function update(Request $request,HistoricalArchive $archive){$data=$this->validated($request);$data['slug']=$this->uniqueSlug($data['title'],$archive->id);$data['is_published']=$request->boolean('is_published');$data['is_featured']=$request->boolean('is_featured');$data['published_at']=$data['is_published']?($archive->published_at??now()):null;$archive->update($data);return redirect()->route('admin.archives.index')->with('success','Historical archive record updated.');}
 public function destroy(HistoricalArchive $archive){foreach(['cover_image','attachment_path'] as $field){if($archive->{$field})Storage::disk('public')->delete($archive->{$field});}$archive->delete();return back()->with('success','Historical archive record deleted.');}
 private function validated(Request $request):array{$data=$request->validate(['title'=>['required','string','max:255'],'category'=>['required','in:'.implode(',',HistoricalArchive::CATEGORIES)],'subtitle'=>['nullable','string','max:255'],'summary'=>['nullable','string','max:1500'],'body'=>['nullable','string'],'record_year'=>['nullable','integer','min:1900','max:2100'],'source_title'=>['nullable','string','max:255'],'source_page'=>['nullable','string','max:100'],'cover_image'=>['nullable','image','max:4096'],'attachment'=>['nullable','file','mimes:pdf,doc,docx,jpg,jpeg,png','max:15360'],'is_published'=>['nullable','boolean'],'is_featured'=>['nullable','boolean']]);
  if($request->hasFile('cover_image'))$data['cover_image']=$request->file('cover_image')->store('bbhs-archives/covers','public');if($request->hasFile('attachment'))$data['attachment_path']=$request->file('attachment')->store('bbhs-archives/files','public');unset($data['attachment']);return $data;}
 private function uniqueSlug(string $title,?int $ignoreId=null):string{$base=Str::slug($title)?:'bbhs-archive';$slug=$base;$i=1;while(HistoricalArchive::where('slug',$slug)->when($ignoreId,fn($q)=>$q->where('id','!=',$ignoreId))->exists())$slug=$base.'-'.$i++;return $slug;}
}
