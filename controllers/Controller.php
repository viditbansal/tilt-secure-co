<?php class Controller
{
	function loadModel($model_class, $model_obj)
	{
		if(strstr($_SERVER['REQUEST_URI'], '/models/'))
		{
			if(file_exists('models/'.$model_class.'.php'))
			{
				require_once('models/'.$model_class.'.php');
			}
			else
				require_once('../models/'.$model_class.'.php');
		}
		else
		{
			if(file_exists('models/'.$model_class.'.php'))
				require_once('models/'.$model_class.'.php');
			else
				require_once('../models/'.$model_class.'.php');
		}
		if (class_exists($model_class))
		{
			$this->$model_obj =  new $model_class;
			return true;
		}
		else
			return false;
	}
}?>