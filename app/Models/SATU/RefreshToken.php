<?php
namespace app\Models\SATU;

use Illuminate\Database\Eloquent\Model;
use DB;

class RefreshToken extends Model {

    protected $table = 'oauth_refresh_tokens';
    protected $connection = 'framework';
}
