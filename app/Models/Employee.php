<?php
  
namespace App\Models;
  
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
  
class Employee extends Model
{
    use HasFactory;
  
    protected $fillable = [
        'name', 'email', 'details' 
    ]; 
  
    /**
     * Get and set details data of employee.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function setDetailsAttribute($value)
    {
        $this->attributes['details'] = json_encode($value);
    }

    protected function getDetailsAttribute($value)
    {
        return json_decode($value);
    } 
}