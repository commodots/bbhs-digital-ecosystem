<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class HistoricalArchive extends Model {
 protected $fillable=['title','slug','category','subtitle','summary','body','record_year','source_title','source_page','cover_image','attachment_path','metadata','is_featured','is_published','published_at','created_by'];
 protected $casts=['metadata'=>'array','is_featured'=>'boolean','is_published'=>'boolean','published_at'=>'datetime'];
 public const CATEGORIES=['Leadership','Graduation Set Representatives','Branch Leadership','Honours & Awards','Distinguished Old Boys','School Reports','Projects & Impact','Events & Anniversaries','Alumni Stories','Heritage','Publications','Gallery Archive'];
}
