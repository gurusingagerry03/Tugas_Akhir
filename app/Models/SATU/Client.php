<?php
namespace app\Models\SATU;

use Illuminate\Database\Eloquent\Model;
use DB;

class Client extends Model {

    protected $table = 'oauth_clients';
    protected $connection = 'framework';
}
