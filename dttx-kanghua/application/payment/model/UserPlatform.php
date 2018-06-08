<?php
namespace app\payment\model;

use think\Db;
use think\Model;


/**
 *
 * User: lirong
 * Date: 2017/7/12
 * Time: 20:00
 */
class UserPlatform extends Model{

    protected $name ='user_platform';

    public function findTowLevelByUpid($upid){

        $userinfo = Db::name($this->name)
            ->alias('up')
            ->field('up_id,up_fcode,up_user_level_id,up_user_agent_level,up_provinceId,up_cityId,up_regionId,u_nick,up_plateform_id')
            ->join('user u','up.up_uid=u.u_id')
            ->where(['up_id'=>$upid])->find();

        $info=[];

        $info['member'] =[
                'levelone'=>[
                    'uid'=>"",
                    'nick'=>"",
                    'levelid'=>'',
                    'ratio'=>'8'
                ],
                'leveltwo'=>[
                    'uid'=>"",
                    'nick'=>"",
                    'levelid'=>'',
                    'ratio'=>'4'
                ]
            ];

        $info['agent'] =[
            'province'=>[
                'uid'=>"",
                'nick'=>"",
                'ratio'=>'3'
            ],
            'city'=>[
                'uid'=>"",
                'nick'=>"",
                'ratio'=>'15'
            ]
        ];
        $info['org']=[
          'dttx'=>[
              'uid'=>"",
              'nick'=>"",
              'ratio'=>'8'
          ],
          'lyj'=>[
              'uid'=>"",
              'nick'=>"",
              'ratio'=>'4'
          ],
          'platfrom'=>[
              'uid'=>'',
              'nick'=>'',
          ]
        ];

        if (empty($userinfo)){
            return $info;
        }
        $up_platformId = $userinfo['up_plateform_id'];
        $info['buyer']=[
            'uid'=>$userinfo['up_id'],
            'nick'=>$userinfo['u_nick'],
            'platformid'=>$up_platformId
        ];

        if ($userinfo['up_fcode']!=0){
            $oneinfo =$this->finduserinfoByfcode($userinfo['up_fcode'],$up_platformId);
            if (!empty($oneinfo)){
                $oneCount = Db::name('orders')->where(['os_buyer_id'=>$oneinfo['up_id'],'os_bus_id'=>0,'os_status'=>3])->count();
                if ($oneCount>0){
                    $info['member']['levelone']['uid'] =$oneinfo['up_id'];
                    $info['member']['levelone']['nick'] =$oneinfo['u_nick'];
                    $info['member']['levelone']['levelid'] =$oneinfo['up_user_level_id'];
                    if ($oneinfo['up_user_level_id']>0){
                        $info['member']['levelone']['ratio'] =20;
                    }
                }
                $info['member']['levelone']['uid'] =$oneinfo['up_id'];
                $info['member']['levelone']['nick'] =$oneinfo['u_nick'];
                $info['member']['levelone']['levelid'] =$oneinfo['up_user_level_id'];
                if ($oneinfo['up_user_level_id']>0){
                    $info['member']['levelone']['ratio'] =20;
                }
                if (!empty($oneinfo['up_fcode'])){
                    $twoinfo =$this->finduserinfoByfcode($oneinfo['up_fcode'],$oneinfo['up_plateform_id']);
                    if (!empty($twoinfo)){
                        $twoCount =Db::name('orders')->where(['os_buyer_id'=>$twoinfo['up_id'],'os_bus_id'=>0,'os_status'=>3])->count();
                        if ($twoCount>0){
                            $info['member']['leveltwo']['uid'] =$twoinfo['up_id'];
                            $info['member']['leveltwo']['nick'] =$twoinfo['u_nick'];
                            $info['member']['leveltwo']['levelid'] =$twoinfo['up_user_level_id'];
                            if ($twoinfo['up_user_level_id']>0){
                                $info['member']['leveltwo']['ratio'] =7;
                            }
                        }
                    }
                    unset($twoinfo);
                }
                unset($oneinfo);
            }

        }

            if (!empty($userinfo['up_provinceId'])){
                $provincedata = Db::name('agent')->field('a_userId,a_dttxNick')->where(['a_provinceId'=>$userinfo['up_provinceId'],'a_cityId'=>0,'a_state'=>'normal','a_projectId'=>$up_platformId])->find();
                if (!empty($provincedata)){
                    $info['agent']['province']['uid']=$provincedata['a_userId'];
                    $info['agent']['province']['nick']=$provincedata['a_dttxNick'];
                }
                unset($provincedata);
            }

            if (!empty($userinfo['up_cityId'])){
                $citydata = Db::name('agent')->field('a_userId,a_dttxNick')->where(['a_provinceId'=>$userinfo['up_provinceId'],'a_cityId'=>$userinfo['up_cityId'],'a_state'=>'normal','a_projectId'=>$up_platformId])->find();
                if (!empty($citydata)){
                    $info['agent']['city']['uid']=$citydata['a_userId'];
                    $info['agent']['city']['nick']=$citydata['a_dttxNick'];
                    unset($citydata);
                }

            }

            if (!empty($up_platformId)){
                $platformdata = Db::name('platform')->where(['pl_id'=>$up_platformId])->find();

                if (!empty($platformdata)){

                    if ($platformdata['pl_dttx_uid']!=''){
                        $res =$this->finduserinfoByDttxUidAndPlatformId($platformdata['pl_dttx_uid'],$up_platformId,'up_id,up_unick');
                        if (false!==$res){
                            $info['org']['platfrom']['uid'] =$res['up_id'];
                            $info['org']['platfrom']['nick'] =$res['up_unick'];
                        }
                        unset($res);
                    }

                    if ($platformdata['pl_tech_uid']!=''){
                        $res =$this->finduserinfoByDttxUidAndPlatformId($platformdata['pl_tech_uid'],$up_platformId,'up_id,up_unick');
                        if (false!==$res){
                            $info['org']['lyj']['uid'] =$res['up_id'];
                            $info['org']['lyj']['nick'] =$res['up_unick'];
                        }
                        unset($res);
                    }

                    if ($platformdata['pl_service_uid']!=''){
                        $res =$this->finduserinfoByDttxUidAndPlatformId($platformdata['pl_service_uid'],$up_platformId,'up_id,up_unick');
                        if (false!==$res){
                            $info['org']['dttx']['uid'] =$res['up_id'];
                            $info['org']['dttx']['nick'] =$res['up_unick'];
                        }
                        unset($res);
                    }
                    unset($platformdata);
                }
            }

        return $info;

    }


    /**
     * 查找用户信息基于大唐uid和平台id
     * @param $dttxuid
     * @param $platformid
     * @param string $field
     * @return array|bool|false|\PDOStatement|string|Model
     */
    private function finduserinfoByDttxUidAndPlatformId($dttxuid,$platformid,$field='*'){
          if (empty($dttxuid) || empty($platformid)){
              return false;
          }

          $res = Db::name($this->name)->field($field)->where(['up_dttx_uid'=>$dttxuid,'up_plateform_id'=>$platformid])->find();
          return $res;
    }



    private function finduserinfoByfcode($code,$platform_id){
        if (empty($code) || empty($platform_id)){
            return false;
        }

        $res =Db::name($this->name)->alias('up')->field('up_id,up_fcode,up_plateform_id,up_user_level_id,up_user_agent_level,up_provinceId,up_cityId,up_regionId,u_nick')->join('user u','up.up_uid=u.u_id','left')->where(['u_code'=>$code,'up_plateform_id'=>$platform_id])->find();
        $sql =Db::name($this->name)->alias('up')->field('up_id,up_fcode,up_plateform_id,up_user_level_id,up_user_agent_level,up_provinceId,up_cityId,up_regionId,u_nick')->join('user u','up.up_uid=u.u_id','left')->where(['u_code'=>$code,'up_plateform_id'=>$platform_id])->buildSql();
        return $res;
    }
}