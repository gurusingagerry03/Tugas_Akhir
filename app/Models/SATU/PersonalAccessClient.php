<?php
namespace app\Models\SATU;

use Illuminate\Database\Eloquent\Model;
use DB;

class PersonalAccesClient extends Model {

    protected $table = 'oauth_personal_access_clients';
    protected $connection = 'framework';
}
