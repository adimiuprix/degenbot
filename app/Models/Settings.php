<?php

namespace App\Models;

use CodeIgniter\Model;

class Settings extends Model
{
    protected $table            = 'settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['project_name', 'token', 'symbol', 'reward', 'description'];
}
