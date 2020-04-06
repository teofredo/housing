<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Base extends Model
{
	use SoftDeletes;
	
	public $timestamps = true;
	
	protected $guarded = [];
	
	public function getTableColumns()
    {
        return $this->getConnection()
        	->getSchemaBuilder()
        	->getColumnListing($this->getTable());
    }
}