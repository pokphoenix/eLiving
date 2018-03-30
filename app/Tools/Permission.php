<?php
namespace App\Tools;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Permission extends Model
{
    protected $permission ;

    public function __construct()
    {
        $sql = "SELECT p.* FROM permissions  p 
                INNER JOIN permission_role pr ON pr.permission_id = p.id 
                INNER JOIN role_user ru ON ru.role_id = pr.role_id
                WHERE ru.id_card = '".Auth::user()->id_card."'
                AND ru.domain_id = ".Auth::user()->recent_domain  ;
        $query = DB::select(DB::raw($sql));
        $this->permission = $query ;
    }

    public function hasPermission($name)
    {
        foreach ($this->permission as $key => $p) {
            if ($p->name == $name) {
                return true;
            }
        }
        return false ;
    }
}
