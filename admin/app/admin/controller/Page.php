<?php
namespace app\admin\controller;

use com\Form;


class Page extends \app\admin\Controller
{
    protected  $dao,$fields;
    protected function initialize()
    {
		parent::initialize();
        $this->moduleid = $this->mod[strtolower(CONTROLLER_NAME)];
        $this->dao = db(CONTROLLER_NAME);
        $fields = cache($this->moduleid.'_Field');
        foreach($fields as $key => $res){
            $res['setup']=string2array($res['setup']);
            $this->fields[$key]=$res;
        }
        unset($fields);
        unset($res);
        $this->result['fields'] = $this->fields;
    }


    public function edit()
	{
		$model = model(CONTROLLER_NAME);
		if (request()->isPost()) {
			try {

				$post = request()->post();
				$validate = validate(CONTROLLER_NAME);
				if (!$validate->check($post)) {
					return $this->error($validate->error);
				}
				$result = $model->update($post);
				if (!$result) {
					return $this->error('编辑失败');
				}
				return $this->success('编辑成功', $result);
			} catch (\Exception $e) {
				return $this->error($e->getMessage());
			}

		} else {
			$id =input($model->getPk());
			$info = $model->where('id', $id)->find();
			$form=new Form($info);

			$this->result['info'] = $info;
			$this->result['form'] = $form;
			$this->result['title'] = '编辑';
			$this->result['info'] = $info;
			return $this->fetch('form');
		}
	}
}

?>