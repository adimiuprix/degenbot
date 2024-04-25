<?php

namespace App\Models;

use CodeIgniter\Model;

class Members extends Model
{
    protected $table            = 'members';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['username', 'chat_id', 'balance', 'email'];

}
