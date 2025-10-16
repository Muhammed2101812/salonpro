<?php
declare(strict_types=1);
namespace App\Models;
use Illuminate\Database\Eloquent\{Concerns\HasUuids,Factories\HasFactory,Model,Relations\HasMany,SoftDeletes};
class ServiceCategory extends Model{use HasFactory,HasUuids,SoftDeletes;protected $fillable=['name','description','is_active'];protected function casts():array{return['name'=>'array','is_active'=>'boolean','created_at'=>'datetime','updated_at'=>'datetime','deleted_at'=>'datetime'];}public function services():HasMany{return $this->hasMany(Service::class);}}
