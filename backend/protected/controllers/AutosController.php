<?php

class AutosController extends Controller
{
    public function actiongetWindowInfo(){
        header('Access-Control-Allow-Origin: *');
        $post = json_decode($_POST['data']);
        $data = Autos::model()->findByPk((int)$post->windowId);
        $status = (!$data)? 401: 200;
        $phone = '';
        if($data){
            $LastPhone = TasksForInstallers::getLastPhone($data->num_auto);
            if($LastPhone) {
                $phone = $LastPhone['tel'];
            }else{
                $gl = TasksForInstallers::getGlonassPhone((int)$post->windowId);
                $phone = (substr($gl['tel'],0,2) == '+7')?substr($gl['tel'],2):$gl['tel'];
            }
        }
        $start = MYDate::_date_diff(strtotime($data->window_from), strtotime($data->windows));
        $end = MYDate::_date_diff(strtotime($data->windows), strtotime($data->window_to));
        if($phone == '') $phone = substr($data->phone,2);
        $id = ($task = TasksForInstallers::model()->find('`status`!=2 and date_create > now() - interval 2 day and plate=:plate',array(':plate'=>$data->num_auto))) ? $task->id: 0;
        echo CJSON::encode(array( 'status'=>$status, 'phone'=>$phone,'task_id'=>$id,'start'=>(int)$start['h'],'end'=>(int)$end['h'],'plate'=>$data->num_auto, 'window'=>$data->windows, 'window_to'=>$data->window_to, 'window_from'=>$data->window_from ));
    }

    public function actionchangeWindow(){
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST');
        $post = json_decode($_POST['data']);

        try {
            $data = Autos::model()->findByPk((int)$post->windowId);
            $start = MYDate::_date_diff(strtotime($data->window_from), strtotime($data->windows));
            $end = MYDate::_date_diff(strtotime($data->windows), strtotime($data->window_to));
            $save = false;
            switch ((int)$post->param) {
                case 1: {
                    if ($start['h'] > 1) {
                        $data->window_from = date('Y-m-d H:i:s', strtotime("{$data->window_from} +1 hour"));
                        $save = true;
                    }
                    break;
                }
                case 2: {
                    if ($start['h'] < 3) {
                        $data->window_from = date('Y-m-d H:i:s', strtotime("{$data->window_from} -1 hour"));
                        $save = true;
                    }
                    break;
                }
                case 3: {
                    if ($end['h'] > 1) {
                        $data->window_to = date('Y-m-d H:i:s', strtotime("{$data->window_to} -1 hour"));
                        $save = true;
                    }
                    break;
                }
                case 4: {
                    if ($end['h'] < 3) {
                        $data->window_to = date('Y-m-d H:i:s', strtotime("{$data->window_to} +1 hour"));
                        $save = true;
                    }
                    break;
                }
            }
            if ($save) {
                if ($data->save(false)) {
                    $archive = AutosArchive::model()->findByPk((int)$data->id);
                    $archive->window_to = $data->window_to;
                    $archive->window_from = $data->window_from;
                    if($archive->save(false)){
                        $array = array('status'=>200,'message'=> 'Успешно');
                    }else $array = array('status'=>401,'message'=> 'Ошибка сохранения архива!');

                    if ($archive) {
                        $a = array(1=>'уменьшить дату начала',2=>'увеличить дату начала',3=>'уменьшить дату конца',4=>'увеличить дату конца');
                        $w = @fopen('window.txt', 'a-');
                        @fwrite($w, $archive->num_auto . " id: " . $archive->id . " param: " . $a[$post->param] . " user: " . $post->user . " date_create -  " . date("Y-m-d H:i:s") . "\r\n");
                        @fclose($w);
                    }
                }else $array = array('status'=>401,'message'=> 'Ошибка сохранения!');
            }else $array = array('status'=>401,'message'=> 'Нельзя изменить таймслот');

        }catch (Exception $e){
            $array = array('status'=>401,'message'=> $e->getMessage() );
        }
        echo CJSON::encode($array);
        // 1 - уменьшить дату начала, 2 - увеличить дату начала, 3 - уменьшить дату конца, 4 - увеличить дату конца.
    }

    public function actiongetWindows()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET');
        $result = Autos::getTimeslots();
        $in_work = Autos::platesExistInInstallers();
        $payments = array();
        $timestamp = time() - 60*60*3;
        foreach ($result as $item) {

            $is_install = !in_array($item['dev'],array(1, 0, 11111111, 'testtest')) ? 1 : 2;

            if($is_install == 1) {
              $time = BNComplex::getBn($item['dev']);
              if($time!=='null' && ( strtotime($time) > $timestamp )) continue;
            }

            array_push($payments, array(
                'glonass_id' => $item['gl'],
                'window_id' => $item['ia'],
                'window_from' => $item['fr'],
                'window_to' => $item['to'],
                'window' => $item['windows'], //а224вк93
                'plate' => $item['num_auto'],
                'in_work' => (int)in_array((int)$item['gl'],$in_work),
                'type' => $is_install
            ));
        }
        echo CJSON::encode($payments);
    }



}
