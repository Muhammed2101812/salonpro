<?php
declare(strict_types=1);
namespace App\Models;
use Illuminate\Database\Eloquent\{Concerns\HasUuids,Factories\HasFactory,Model,Relations\BelongsTo,SoftDeletes};
class Service extends Model{use HasFactory,HasUuids,SoftDeletes;protected $fillable=['service_category_id','name','description','price','duration_minutes','is_active'];protected function casts():array{return['name'=>'array','price'=>'decimal:2','is_active'=>'boolean','created_at'=>'datetime','updated_at'=>'datetime','deleted_at'=>'datetime'];}public function serviceCategory():BelongsTo{return $this->belongsTo(ServiceCategory::class);}}
