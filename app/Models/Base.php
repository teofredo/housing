<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Base extends Model
{
	public $timestamps = true;
	
	protected $guarded = [];

	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at'
	];
	
	public function getTableColumns()
    {
        return $this->getConnection()
        	->getSchemaBuilder()
        	->getColumnListing($this->getTable());
    }
}