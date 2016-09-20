<?php

namespace App\Api\V1\Modules\User\Http\Controllers;

use App\Api\V1\Modules\User\Repository\UserRepository;
use QSoftvn\Controllers\WidgetBaseController;
use QSoftvn\Helper\Helper;
use QSoftvn\Modules\UserRole\Entity\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends WidgetBaseController
{
    public $repository;
    protected $transformer = \App\Api\V1\Modules\User\Transformer\UserTransformer::class;


    function __construct(UserRepository $repo)
    {
        $this->repository = $repo;
    }
    public function index()
    {
        return parent::index();
    }
    public function all(){
        return parent::all();
    }
    public function lists(){
        return parent::lists();
    }
    public function show($id)
    {
        return parent::show($id);
    }
    public function store(Request $request)
    {
        return parent::store($request);
    }
    public function update($id, Request $request)
    {
        return parent::update($id, $request);
    }
    public function destroy($id,Request $request)
    {
        return parent::destroy($id,$request);
    }
    public function export(){
        return parent::export();
    }
    public function menu(){
        return $this->response->array(array('children'=>Helper::getUserMenu()));
    }
    /**
     * Manipulate data before inserting
     * @param $data
     * @return mixed
     */
    public function beforeAddItem($data){
        return $data;
    }

    /**
     * Manipulate result before responding
     * @param $result
     * @return mixed
     */
    public function afterAddItem($item, $data){
        return $this->updateRole($item, $data);
    }

    /**
     * Manipuldate data before updating
     * @param $data
     * @return mixed
     */
    public function beforeUpdateItem($data){
        return $data;
    }

    /**
     * Manipulate result before responding
     * @param $item
     * @return mixed
     */
    public function afterUpdateItem($item, $data){
        return $this->updateRole($item, $data);
    }

    /**
     * Update roles for user
     * @param $item
     * @param $data
     * @return mixed
     */
    protected function updateRole($item, $data){
        $myAccount = app('Dingo\Api\Auth\Auth')->user();
        $myAccountId = $myAccount->id;
        $userId = $item->id;
        $existingRoles = UserRole::where('user_id','=',$userId)->get(array('role_id'))->toArray();
        $existingRoleIds = [];
        $roles = array();
        foreach($existingRoles as $eRole){
            array_push($existingRoleIds, $eRole['role_id']);
        }
        if(isset($data['roles'])){
            $roles = $data['roles'];
        }
        if(count($roles)>0){
            foreach($roles as $role){
                if(!in_array($role,$existingRoleIds)){
                    UserRole::create(array('user_id'=>$userId, 'role_id'=>$role,'from_date'=>new \DateTime(),'created_by'=>$myAccountId,'updated_by'=>$myAccountId));
                }
            }
            DB::table('user_roles')->where('user_id','=',$userId)->whereNotIn('role_id',$roles)->delete();
        }
        else{
            DB::table('user_roles')->where('user_id','=',$userId)->delete();
        }
        $user = $this->repository->find($userId);
        $user->normalizeUserPermission();
        return $user;
    }
}
    