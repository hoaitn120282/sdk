<?php
namespace QSoftvn\Listeners;

use Dingo\Api\Event\ResponseWasMorphed;

/**
 * This class provide the way to add meta data to the response of the API
 * @package QSoftvn\Listeners
 */
class AddMetaDataToResponse
{
    /**
     * Format the response
     * @param ResponseWasMorphed $event
     */
    public function handle(ResponseWasMorphed $event)
    {
        $successStatuses = array(200, 201);
        if(in_array($event->response->status(), $successStatuses)){
            if(isset($event->content['meta'])){
                if(isset($event->content['meta']['routes'])){
                    $event->content['permissions'] = [];
                    foreach($event->content['meta']['routes'] as $key=>$meta){
                        $event->content['permissions'][$key]=$meta;
                    }
                }
                if(isset($event->content['meta']['pagination'])){
                    foreach($event->content['meta']['pagination'] as $key=>$meta){
                        $event->content[$key]=$meta;
                    }
                }
                foreach($event->content['meta'] as $key=>$meta){
                    if(!is_array($meta)){
                        $event->content[$key]=$meta;
                    }
                }
                unset($event->content['meta']);
            }
            $event->content['success'] = true;
            if(!isset($event->content['message'])){
                $event->content['message'] = 'Done';
            }
        }
        else{
            $event->content['success'] = false;
            $event->content['message'] = (isset($event->content['message'])?$event->content['message']:'Done');
        }

    }
}