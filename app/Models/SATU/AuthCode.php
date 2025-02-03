<?php
namespace app\Models\SATU;

use Illuminate\Database\Eloquent\Model;
use DB;

class AuthCode extends Model {

    protected $table = 'oauth_auth_codes';
    protected $connection = 'framework';
}
