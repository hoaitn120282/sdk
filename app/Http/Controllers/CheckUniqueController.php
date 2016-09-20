<?php

namespace App\Http\Controllers;

use App\QsHelper;
use QSoftvn\Helper\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Response;
use Schema;
use Illuminate\Support\Facades\Input;
use DB;
use App\Api\V1\Modules\User\Entity\User as User;
use App\Api\V1\Modules\Person\Entity\Person;

class CheckUniqueController extends Controller {

    /**
     * @param Request $request
     * @return string
     */
    public function checkUnique(Request $request) {

//        echo "<pre>"; var_dump($request->all());die;
        $field = $request->has('fieldName') ? snake_case($request->input('fieldName')) : 'notfield';
        $table = $request->has('tableName') ? snake_case($request->input('tableName')) : 'nottable';
        $id = $request->has('id') ? snake_case($request->input('id')) : 'notid';
        //echo $field. ' / ' .$table. ' / ' .$id; die;
        if (($field !== 'notfield') && ($table !== 'nottable')) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, $field)) {
                $datas = $request->all();
                $rs = [];
                foreach ($datas as $key => $value) {
                    $rs [snake_case($key)] = $value;
                }

                unset($rs['field_name']);
                unset($rs['table_name']);

                //update
                if ($id !== 'notid') {
                    unset($rs['id']);
                    //echo "<pre>"; var_dump($rs); die;
                    $flag = QsHelper::checkUniqueData($rs, $field, $table, $id);
                } else {
                    //echo "<pre>"; var_dump($rs); die;
                    $flag = QsHelper::checkUniqueData($rs, $field, $table, $id);
                }

//                if ($request->has('id') == false) {
//                    if (count($flag) > 0 ) {
//                        return Response::json($flag->getData());
//                    } else {
//                        //die("ok");
//                        return QsHelper::returnValueJson(true, "successfully", null, 200);
//                    }
//                }
                //var_dump($flag); die;
                if ($flag == 'it_ok') {
                    return QsHelper::returnValueJson(true, "successfully", null, 200);
                }
//                if (count($flag) == 0) {
//                    return QsHelper::returnValueJson(false, "not exist id in table", null, 405);
//                }
                if (count($flag) > 0) {
                    return Response::json($flag->getData());
                } else {
                    //die("ok");
                    return QsHelper::returnValueJson(true, "successfully", null, 200);
                }
            } else {
                return QsHelper::returnValueJson(false, "not exist field or table", null, 403);
            }
        } else {
            return QsHelper::returnValueJson(false, "not exist field and table", null, 403);
        }
    }

    public function uploadImage() {

        if (count(Input::file('avatar')) > 0) {
            $urlAvatar = uploadImage(Input::file('avatar'));
            return QsHelper::returnValueJson(true, "upload file successfully", $urlAvatar, 403);
        } else {
            return QsHelper::returnValueJson(false, "not upload file", null, 403);
        }
    }

    public function getIdUser() {
        $headers = apache_request_headers();
        if (array_key_exists("Authorization", $headers)) {
            $tokenString = explode(" ", $headers['Authorization']);
            $userLogin = DB::select(DB::raw("
            select s.owner_id from oauth_sessions as s LEFT JOIN oauth_access_tokens as st on s.id = st.session_id WHERE st.id = '" . $tokenString[1] . "' "));
            $id = $userLogin[0]->owner_id;

            return QsHelper::returnValueJson(true, "successfully", $id, 200);
        }
    }

    public function getInforUser() {

        $myAccount = app('Dingo\Api\Auth\Auth')->user()->toArray();
        $arrInfoUser['userName'] = $myAccount['email'];
        $person = Person::find($myAccount['person_id']);
        $arrInfoUser['type'] = $person ? $person->user_type : '';
        $arrInfoUser['personId'] = $myAccount['person_id'];
        $arrInfoUser['userId'] = $myAccount['id'];

        return QsHelper::returnValueJson(true, "successfully", $arrInfoUser, 200);

//        $headers = apache_request_headers();
//        if (array_key_exists("Authorization", $headers)) {
//            $tokenString = explode(" ", $headers['Authorization']);
//            $userLogin = DB::select(DB::raw("
//            select s.owner_id from oauth_sessions as s LEFT JOIN oauth_access_tokens as st on s.id = st.session_id WHERE st.id = '" . $tokenString[1] . "' "));
//            $id = $userLogin[0]->owner_id;
//            $user = User::find($id);
//            $arrInfoUser = ['userName' => $user->email, 'type' => $user->person->user_type];
//            return QsHelper::returnValueJson(true, "successfully", $arrInfoUser, 200);
//        }
    }

    public function getMenu() {
//        echo \QSoftvn\Helper\Helper::simpleEncrypt('refund@4media.com.sg');die;
        //$sr = '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';
        //var_dump(json_decode(Helper::simpleDecrypt($sr)));die;
        //For student

        $myAccount = app('Dingo\Api\Auth\Auth')->user()->toArray();
        $roles = QsHelper::getRolesInfo($myAccount['id']);

        $userMenu = Helper::getUserMenu();

        ///$userMenu = include str_replace('\app', '', app_path()) . '/config/menu.php';
        //$userMenu = $userMenu['menu'];

        if ($myAccount['routes']) {

            //if ($roles['id'] != 2)
            $rs = json_decode(Helper::simpleDecrypt($myAccount['routes']));

            $routes = [];
            foreach ($rs as $el) {
                if (trim($el) != '')
                    $routes[] = $el;
            }

            $children = QsHelper::getUserMenu(
                            $userMenu, QsHelper::perWidgets($routes)
            );
        } else {
            $children = $userMenu;
        }

        $widgets = QsHelper::perWidgets($roles['id'] != 2 ? json_decode(Helper::simpleDecrypt($myAccount['routes'])) : $routes, false);
        $roles['permissions'] = QsHelper::convertWidgetToRoute($widgets, $userMenu);

        // for student;
        if (intval($roles['id']) === 2) {
            $children = [];
            foreach ($userMenu AS $el) {
                if (in_array($el['text'], ['Student', 'Schedule'])) {
                    $children[] = $el;
                }
            }
//            $roles['permissions'] = [
//                'Application'   => ["view","export","list","detail","delete"],
//                'Enrollments'   => ["view","export","list","detail","delete"],
//                'Withdrawal'    => ["view","export","list","detail","delete"],
//                'Deferment'     => ["view","export","list","detail","delete"],
//                'schedule'      => ["view","export","list","detail","delete"],
//                'schedule'      => ["view","export","list","detail","delete"],
//            ]            
        }
        if (intval($roles['id']) !== 2 && Helper::simpleDecrypt($myAccount['secret_key']) === $myAccount['email']) {
            $children = [];
            foreach ($userMenu AS $el) {
                if ($el['text'] == 'Student') {
                    $el['routeId'] = 'students.lists';
                    $el['widget'] = 'Application';
                    unset($el['children']);
                }
                $children[] = $el;
            }
        }
        return [
            'success' => true,
            'messages' => "Done",
            'status_code' => 200,
            'roles' => $roles,
            'children' => $children
        ];
    }

}
