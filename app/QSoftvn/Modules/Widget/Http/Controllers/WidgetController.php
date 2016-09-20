<?php
namespace QSoftvn\Modules\Widget\Http\Controllers;

use QSoftvn\Controllers\BaseController;
use QSoftvn\Helper\Helper;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * This class output fields, routes for a widget or all widgets
 * @package QSoftvn\Modules\Widget\Http\Controllers
 */
class WidgetController extends BaseController
{

    /**
     * Output all fields of a widget
     * @param $id
     * @return mixed
     */
    public function fields($id)
    {
        $widgetList = Helper::getWidgetList();
        if(isset($widgetList[$id])){
            $data = [];
            foreach($widgetList[$id]['viewableFields'] as $field){
                $data[]=array('id'=>$field, 'name'=>explode('.',$field)[1]);
            }
            return $this->response->array(array('data'=>$data));
        }
        else{
            throw new NotFoundHttpException('Not a valid widget');
        }
    }

    /**
     * Output all routes of a widget
     * @param $id
     * @return mixed
     */
    public function route($id){
        $widgetList = Helper::getWidgetList();
        if(isset($widgetList[$id])){
            $config = $widgetList[$id];
            $data = [];
            if(isset($config['routes'])){
                foreach($config['routes'] as $route=>$cfg){
                    $data[] = array(
                        'route'=>$route,
                        'method'=>$cfg['method'],
                        'name'=>$cfg['name'],
                        'widget'=>$id
                    );
                }
            }
            return $this->response->array(array('data'=>$data));
        }
        else{
            throw new NotFoundHttpException('Not a valid widget');
        }
    }

    /**
     * List all routes in the system
     * @return mixed
     */
    public function allRoutes(){
        $data = [];
        $widgetList = Helper::getWidgetList();
        foreach($widgetList as $widget=>$config){
            if(isset($config['routes'])){
                foreach($config['routes'] as $route=>$cfg){
                    $data[] = array(
                        'route'=>str_replace('\\','__',$route),
                        'method'=>$cfg['method'],
                        'name'=>$cfg['name'],
                        'widget'=>$widget
                    );
                }
            }
        }
        return $this->response->array(array('data'=>$data));
    }
}