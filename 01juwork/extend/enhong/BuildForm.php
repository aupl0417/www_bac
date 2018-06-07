<?php
/**
+----------------------------------------------------------------------
| RestFull API 
+----------------------------------------------------------------------
| 表单生成器
+----------------------------------------------------------------------
| Author: lazycat <673090083@qq.com>
+----------------------------------------------------------------------
*/

namespace enhong;
class BuildForm {
	protected $option;	//字段选项
	protected $item;	//单个表单项
	protected $methods = array('text','textarea','hidden','radio','checkbox','editor','date','select','password','file','html','images','verify','images_more','radio_list','checkbox_list','vcode','select_images','color','datetime','widget','button');
	protected $outhtml = array();	//表单生成后输出
	private $value = null;	//表单值
    /**
     * 架构函数
     * @access public
     * @param string $this->str  数据
     */
    public function __construct($option=[]) {
    }
	
	/**
	* 设置属性
	*/
	public function __set($name,$v){
		return $this->$name=$v;
	}

	/**
	* 获取属性
	*/
	public function __get($name){
		return isset($this->$name)?$this->$name:null;
	}
	
	/**
	* 销毁属性
	*/
    public function __unset($name) {
        unset($this->$name);
    }

    /**
    * 连贯操作的实现
    * @param string $method  方法
    * @param array 参数
    */
    public function __call($method,$args){
    	$method=strtolower($method);
    	if(in_array($method,$this->methods,true)) {
    		$action='_'.$method;
			$this->item = $this->$action($args[0]);
            $this->outhtml[] =   $this->_row($this->item);
            return $this;
        }else{
        	echo '调用类'.get_class($this).'中的方法'.$method.'()不存在';
        }
    }

    /**
    * 连贯操作后的结果组合
	* @param array $option 字段选项    
    */
    public function create(){
    	$html=@implode('',$this->outhtml);
    	$this->outhtml	= []; 	//销毁之前的内容以便create后重新生成
        $this->option	= [];
		$this->item 	= [];
    	return $html;
    }

	/**
	 * 输出单个表单项
	 */
	public function item(){
		$html = $this->_item_row($this->item);
		return $html;
	}
	/**
	* input
	* @param array $option 字段选项	
	*/
	public function _text($option=null){
		$this->option 	= $option;
		$value			= $this->_value($option);
		$attr 	= implode(' ',$this->_attr($option));
		$html 	='<input type="text" '.$attr.' value="'.htmlspecialchars($value).'">';

		//$html 	= $this->_row($html,$option);
		return $html;
	}

	/**
	 * password
	 * @param array $option 字段选项
	 */
	public function _password($option=null){
		$this->option 	= $option;
		$value	= $this->_value($option);
		$attr 	= implode(' ',$this->_attr($option));
		$html 	='<input type="password" '.$attr.' value="'.htmlspecialchars($value).'">';
		$html	.='<input type="hidden" name="_password_'.$option['name'].'" value="'.$value.'">';

		//$html 	= $this->_row($html,$option);
		return $html;
	}
	//创建文本框
	public function _textarea($option=null){
		$this->option 	= $option;
		$value	= $this->_value($option);
		$attr = implode(' ',$this->_attr($option));
		$html 	='<textarea '.$attr.'>'.$value.'</textarea>';

		//$html 	= $this->_row($html,$option);
		return $html;
	}

	//创建文本框
	public function _hidden($option=null){
		$this->option 	= $option;
		$value	= $this->_value($option);
		$attr 	= implode(' ',$this->_attr($option));
		$html 	='<input type="hidden" '.$attr.' value="'.$value.'">';
		return $html;
	}

	//创建按钮
	public function _button($option=null){
		$this->option = $option;
		$html	= [];
		foreach($option['btns'] as $val){
			$type = isset($val[2]) ? $val[2] : 'button';
			$attr = isset($val[3]) ? $val['3'] : '';
			$html[] = '<button type="'.$type.'" class="'.$val[1].'" '.$attr.'>'.$val[0].'</button>';
		}

		$html = implode(' ',$html);
		//$html 	= $this->_row($html,$option);
		return $html;
	}

	/**
	 * file
	 * @param array $option 字段选项
	 */
	public function _file($option=null){
		$this->option 	= $option;
		$value			= $this->_value($option);
		$attr 	= implode(' ',$this->_attr($option));
		$html 	='<input type="file" '.$attr.' value="">';

		//$html 	= $this->_row($html,$option);
		return $html;
	}

	/**
	 * date
	 * @param array $option 字段选项
	 */
	public function _date($option=null){
		$this->option 	= $option;
		$value			= $this->_value($option);
		$attr 	= implode(' ',$this->_attr($option));
		$attr   = str_replace('form-control','form-control date-picker',$attr);
		$html 	='<input type="text" '.$attr.' value="'.htmlspecialchars($value).'">';

		//$html 	= $this->_row($html,$option);
		return $html;
	}

	/**
	 * datetime
	 * @param array $option 字段选项
	 */
	public function _datetime($option=null){
		$this->option 	= $option;
		$value			= $this->_value($option);
		$attr 	= implode(' ',$this->_attr($option));
		$attr   = str_replace('form-control','form-control form_meridian_datetime',$attr);
		$html 	='<input type="text" '.$attr.' value="'.htmlspecialchars($value).'">';

		//$html 	= $this->_row($html,$option);
		return $html;
	}

	//创建图片上传
	public function _images($option=null){
		$this->option 	= $option;
		$value	= $this->_value($option);
		$attr 	= implode(' ',$this->_attr($option));
		$html 	= '<input type="hidden" '.$attr.' value="'.$value.'">';
		$img	= $value ? '<div class="sub-action"><i class="fa fa-times" onclick="deleteImage($(this))"></i></div>'.imgwh($value,100) : '<img src="'.thumb('/images/work/icon-images-add.png',100).'" alt="上传图片" onclick="uploadImages($(this))">';
		$html	.= '<ul class="form-images-list"><li>'.$img.'</li></ul>';

		return $html;
	}

	//创建图片上传
	public function _images_more($option=null){
		$this->option 	= $option;
		$value	= $this->_value($option);
		$attr 	= implode(' ',$this->_attr($option));
		$html 	= '<input type="hidden" '.$attr.' value="'.$value.'">';
		//$img	= $value ? '<div class="sub-action"><i class="fa fa-times" onclick="deleteImageMore($(this))"></i></div>'.imgwh($value,100) : '<img src="'.thumb('/images/work/icon-images-add.png',100).'" alt="上传图片" onclick="uploadImages($(this))">';
		$click 	= '<li class="click-upload"><img src="'.thumb('/images/work/icon-images-add.png',100).'" alt="上传图片" onclick="uploadImagesMore($(this))"></li>';
		$images_list = '';
		if($value) {
			if (!is_array($value)) $value = explode(',', $value);
			foreach ($value as $val) {
				$images_list .= '<li class="img-item" data-url="' . $val . '"><input type="hidden" name="' . $option['name'] . '[]" value="' . $val . '"><div class="sub-action"><i class="fa fa-times" onclick="deleteImageMore($(this))"></i></div>' . imgwh($val, 100) . '</li>';
			}
		}

		$html	.= '<ul class="form-images-list-more">'.$click.$images_list.'</ul>';

		return $html;
	}

	//select
	public function _select($option=null){
		$this->option = $option;
		$value	= (string)$this->_value($option);
		$attr = implode(' ',$this->_attr($option));
		$html 	='<select '.$attr.'">';
		$is_first 	= isset($option['is_first']) && $option['is_first'] === 0 ? 0 : 1;
		if($is_first == 1) $html	.='<option value="">请选择'.(isset($option['label']) ? $option['label'] : '').'</option>';

		if(isset($option['data']) && is_array($option['data'])){
			if(isset($option['is_category']) && $option['is_category'] == 1){
				$html .= create_option($option['data'],$option['field'],$value);
			}else {
				foreach ($option['data'] as $val) {
					$selected = (string)$val[$option['field'][0]] === $value ? 'selected' : '';
					$html .= '<option value="' . $val[$option['field'][0]] . '" ' . $selected . '>' . $val[$option['field'][1]] . '</option>';
				}
			}
		}
		$html	.='</select>';

		//$html 	= $this->_row($html,$option);
		return $html;
	}

	//radio
	public function _radio($option=null){
		$this->option 	= $option;
		$value	= (string)$this->_value($option);
		$attr = implode(' ',$this->_attr($option));
		$html = '<div class="mt-radio-inline">';
		if(isset($option['data']) && is_array($option['data'])){
			foreach($option['data'] as $val){
				$checked = (string)$val[$option['field'][0]] === $value ? 'checked="checked"' : '';
				$html .= '<label class="mt-radio mt-radio-outline md0">
                             <input type="radio" '.$attr.' value="'.$val[$option['field'][0]].'" '.$checked.'>'.$val[$option['field'][1]].'
                             <span></span>
                           </label>';
			}
		}
		$html .= '</div>';

		return $html;
	}

	//checkbox
	public function _checkbox($option=null){
		$this->option = $option;
		$value	= (string)$this->_value($option);
		$attr = implode(' ',$this->_attr($option));
		$html = '<div class="mt-checkbox-inline">';
		if(isset($option['data']) && is_array($option['data'])){
			foreach($option['data'] as $val){
				$checked = (string)$val[$option['field'][0]] === $value ? 'checked="checked"' : '';
				$html .= '<label class="mt-checkbox mt-checkbox-outline md0">
                             <input type="checkbox" '.$attr.' value="'.$val[$option['field'][0]].'" '.$checked.'>'.$val[$option['field'][1]].'
                             <span></span>
                           </label>';
			}
		}
		$html .= '</div>';

		return $html;
	}

	/**
	 * editor 百度编辑器 $('[data-type="ueditor"]') 去启用编辑器
	 * @param array $option 字段选项
	 */
	public function _editor($option=null){
		$option['style'] = $option['style'] ? $option['style'] : 'min-height:400px;';
		$this->option = $option;
		$value	= $this->_value($option);
		$attr 	= implode(' ',$this->_attr($option));
		$html	='<script data-type="ueditor" '.$attr.' type="text/plain">'.html_entity_decode($value).'</script>';
		return $html;
	}

	//表单属性
	public function _attr($option=null){
		$attr 	= [];
		$attr[] = (isset($option['name']) && $option['name']) ? 'name="'.$option['name'].'" id="'.$option['name'].'"' : '';
		$attr[] = (isset($option['style']) && $option['style']) ? 'style="'.$option['style'].'"' : '';
		$attr[] = (isset($option['attr']) && $option['attr']) ? $option['attr'] : '';

		$label = isset($option['label']) ? $option['label'] : '';
		if(!in_array($option['formtype'],['editor','hidden'])) {
			$attr[] = 'class="'.((isset($option['class']) && $option['class']) ? 'form-control ' . $option['class'] : 'form-control').'"';
			$attr[] = 'placeholder="' . ((isset($option['placeholder']) && $option['placeholder']) ? $option['placeholder'] : '请填写' . $label) . '"';
		}
		return $attr;
	}

	//表单值
	public function _value($option=null){
		if(isset($this->value[$option['name']]) && $this->value[$option['name']] !== '') return $this->value[$option['name']];
		elseif(isset($option['value']) && $option['value'] !== '') return $option['value'];
		elseif(isset($option['default']) && $option['default'] !== '') return $option['default'];

		return '';
	}

	//建立一行表单
	public function _row($html){
		$option = $this->option;
		if($option['formtype'] == 'hidden') return $html;

		$label 	= isset($option['label']) && $option['label'] ? $option['label'] : '&nbsp;';
		if(isset($option['is_need']) && $option['is_need'] == 1)$label .= '<span class="required"> * </span>';

		//提示
		$tips = (isset($option['tips']) && $option['tips']) ? '<div class="tips">'.$option['tips'].'</div>' : '';

		$prev_addon 	= isset($option['prev_addon']) && $option['prev_addon'] ? '<span class="input-group-addon">'.$option['prev_addon'].'</span>' : '';
		$next_addon 	= isset($option['next_addon']) && $option['next_addon'] ? '<span class="input-group-addon">'.$option['next_addon'].'</span>' : '';

		$prev_btn 		= isset($option['prev_btn']) && $option['prev_btn'] ? '<span class="input-group-btn">'.$option['prev_btn'].'</span>' : '';
		$next_btn 		= isset($option['next_btn']) && $option['next_btn'] ? '<span class="input-group-btn">'.$option['next_btn'].'</span>' : '';

		$prev = $prev_btn ? $prev_btn : $prev_addon;
		$next = $next_btn ? $next_btn : $next_addon;

		if($prev || $next) $html = '<div class="input-group">'.$prev.$html.$next.'</div>';

		$html = '<div class="form-group">
					<label class="control-label col-xs-3">'.$label.'</label>
					<div class="col-xs-6">'.$html. $tips .'</div>
				</div>';


		return $html;
	}

	//建立单个表单项
	public function _item_row($html){
		$option = $this->option;
		if($option['formtype'] == 'hidden') return $html;

		$prev_addon 	= isset($option['prev_addon']) && $option['prev_addon'] ? '<span class="input-group-addon">'.$option['prev_addon'].'</span>' : '';
		$next_addon 	= isset($option['next_addon']) && $option['next_addon'] ? '<span class="input-group-addon">'.$option['next_addon'].'</span>' : '';

		$prev_btn 		= isset($option['prev_btn']) && $option['prev_btn'] ? '<span class="input-group-btn">'.$option['prev_btn'].'</span>' : '';
		$next_btn 		= isset($option['next_btn']) && $option['next_btn'] ? '<span class="input-group-btn">'.$option['next_btn'].'</span>' : '';

		$prev = $prev_btn ? $prev_btn : $prev_addon;
		$next = $next_btn ? $next_btn : $next_addon;

		$input_group_attr = isset($option['input_group_attr']) ? ' '.$option['input_group_attr'] : '';
		if($prev || $next) $html = '<div class="input-group" '.$input_group_attr.'>'.$prev.$html.$next.'</div>';

		return  $html;
	}



	/**
     * 析构方法，清除
     */
	public function __destruct(){
	    unset($this->option);
        unset($this->outhtml);
		unset($this->item);
    }
}
