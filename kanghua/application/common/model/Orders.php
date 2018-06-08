<?php
namespace app\common\model;
use think\Db;
use think\Model;

/**
 *
 * User: lirong
 * Date: 2017/7/9
 * Time: 22:49
 */
class Orders extends Model{

    protected $name ='orders';
    private $order_goods ='orders_goods';

    public function getOrderlist($input){

        $filed ="*";
        if (isset($input['field']) && !empty($input['field'])){
            $filed =$input['field'];
        }

        $map['os_isDelete']=0;
        if (isset($input['condition']) && is_array($input['condition'])){
            $map =array_merge($map,$input['condition']);
        }
        $cacheid =md5(serialize($input));
        $orders = Db::name($this->name)->field($filed)->where($map)->order('os_create_time','desc')->select();
    //    echo Db::name($this->name)->field($filed)->where($map)->order('os_create_time','desc')->cache()->buildSql();
        if (empty($orders)){
            return false;
        }

        $orderStatus =get_dict(4);

        foreach ($orders as &$item){
            $item['statusText']=isset($orderStatus[$item['os_status']])?$orderStatus[$item['os_status']]:'--';
            $goods =Db::name($this->order_goods)->where(['og_order_id'=>$item['os_id']])->cache()->select();
            if (!empty($goods)){
                $item['goods']=$goods;
            }else{
                $item['goods']=[];
            }
        }
        return $orders;
    }

    /**
     * 返回所有类型订单数量
     * '订单状态 -1 : 关闭  0：待付款 1：待发货 2：待收货  3：交易成功  4：退款中  5：已退款,
     * @param $upid
     */
    public function getOrderCount($upid){

        if (empty($upid)){
            return false;
        }

        $data =[];
        $map['os_isDelete']=0;
        $map['os_buyer_id']=$upid;
        $all =Db::name($this->name)->where($map)->count();
        $data['all']=$all!==false?$all:0;

        $waitpay =Db::name($this->name)->where($map)->where(['os_status'=>0])->count();
        $data['waitpay']=$waitpay!==false?$waitpay:0;

        $waitsend =Db::name($this->name)->where($map)->where(['os_status'=>1])->count();
        $data['waitsend']=$waitsend!==false?$waitsend:0;

        $waitreceive =Db::name($this->name)->where($map)->where(['os_status'=>2])->count();
        $data['waitreceive']=$waitreceive!==false?$waitreceive:0;

        return $data;

    }


    /**
     * 返回所有类型订单数量
     * '订单状态 -1 : 关闭  0：待付款 1：待发货 2：待收货  3：交易成功  4：退款中  5：已退款,
     * @param $upid
     */
    public function getSellerOrderCount($upid){

        if (empty($upid)){
            return false;
        }

        $data =[];
        $map['os_isSellerDelete']=0;
        $map['os_seller_id']=$upid;
        $all =Db::name($this->name)->where($map)->count();
        $data['all']=$all!==false?$all:0;

        $waitpay =Db::name($this->name)->where($map)->where(['os_status'=>0])->count();
        $data['waitpay']=$waitpay!==false?$waitpay:0;

        $waitsend =Db::name($this->name)->where($map)->where(['os_status'=>1])->count();
        $data['waitsend']=$waitsend!==false?$waitsend:0;

        $waitreceive =Db::name($this->name)->where($map)->where(['os_status'=>2])->count();
        $data['waitreceive']=$waitreceive!==false?$waitreceive:0;

        return $data;

    }

    public function deleteOrder($id){
        if(!$id || !is_numeric($id)){
            return false;
        }

        return db($this->name)->where(['os_id' => $id])->update(['os_isDelete' => 1]);
    }

    /*
     * 更新订单状态
     * @params $id              订单ID
     * @params $conditionStatus 订单当前状态
     * @params $updateStatus    订单更改状态
     * */
    public function updateOrderStatus($id, $conditionStatus, $updateStatus){

        $where = array('os_id' => $id, 'os_isDelete' => 0, 'os_status' => $conditionStatus);
        $data = [
            'os_status'     => $updateStatus,
            'os_update_time' => time(),
            'os_operate_id'  => session('user.userId')
        ];

        return db($this->name)->where($where)->update($data);
    }

    /*
     * 获取一条订单
     * */
    public function getOrderByOrderId($orderId, $field = '*'){
        $where = array('os_id' => $orderId, 'os_isDelete' => 0);
        $join  = [
            ['db_orders_goods og','os.os_id=og.og_order_id', 'LEFT']
        ];

        return Db::name('orders os')->where($where)->field($field)->join($join)->find();
    }

    /**
     * 查找单条订单完整信息
     * @param $orderId
     * @param string $field
     * @return bool
     */
    public function getOrderAndGoodsByOrderId($orderId,$field="*"){

        if (empty($orderId)){
            return false;
        }

        $orders =Db::name($this->name)->where(['os_id'=>$orderId])->field($field)->find();
        $orderStatus =get_dict(4);
        if (!empty($orders)){
            $orders['statusText']=isset($orderStatus[$orders['os_status']])?$orderStatus[$orders['os_status']]:'--';
            $goods =Db::name($this->order_goods)->where(['og_order_id'=>$orderId])->select();
            if (!empty($goods)){
                $orders['goods']=$goods;
            }else{
                $orders['goods']=[];
            }
        }
        return $orders;
    }



}