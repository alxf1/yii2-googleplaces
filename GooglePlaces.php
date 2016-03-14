<?php
namespace yii\profanity;

use Yii;
use yii\helpers\Html;
use yii\validators\Validator;
use yii\helpers\Json;
use yii\validators\ValidationAsset;

/**
 * @author Bryan Jayson Tan <admin@bryantan.info>
 * @link http://bryantan.info
 * @date 3/1/13
 * @time 3:06 PM
 *
 * make sure that you have CURL installed
 */
class GoogleProfanityValidator extends Validator
{
    /**
     * trim left and right
     * @var bool
     */
    public $trim=false;

    /**
     * trim left character
     * @var bool
     */
    public $trimLeft=false;

    /**
     * trim right character
     * @var bool
     */
    public $trimRight=false;

    /**
     * char list for trimming
     * @var string
     */
    public $trimCharList='0123456789';

    /**
     * replace special characters to letters
     * e.g. n33d to need
     * @var bool
     */
    public $replaceNumbers=false;

    /**
     * the url for google profanity
     * @var string
     */
    public $url='http://www.wdyl.com/profanity';


    /**
     * @inheritdoc
     */
    public function init()
    {
    	parent::init();
    	if ($this->message === null) {
    		$this->message = Yii::t('yii', '{attribute} is invalid.');
    	}
    }
    
    /**
     * Validates the attribute of the object.
     * If there is any error, the error message is added to the object.
     * @param CModel $object the object being validated
     * @param string $attribute the attribute being validated
     */
    public function validateAttribute($object,$attribute)
    {
//        $value=$object->$attribute;
    	$value=urlencode($object->$attribute);
    		
        if ($this->trim===true || $this->trimLeft===true){
            $value=ltrim($value,$this->trimCharList);
        }
        if ($this->trim===true || $this->trimRight===true){
            $value=rtrim($value,$this->trimCharList);
        }
        if ($this->replaceNumbers===true){
            $value=$this->replaceValue($value);
        }

        $response=$this->curl(sprintf('%s?q=%s',$this->url,$value));

        $response=Json::decode($response);

        if ($response['response']=="true"){
            $message=$this->message!==null?$this->message:\Yii::t('yii','{attribute} "{value}" has already been taken.');
            $this->addError($object,$attribute,$message,array('{value}'=>Html::encode($value)));
        }
    }

    protected function replaceValue($value){
        $numbers=array('-','_','1','3','4','5','6','7','8','0','Z');
        $letters=array('','','i','e','a','s','g','t','b','o','s');

        return str_replace($numbers,$letters,$value);
    }

    protected function curl($url){
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }


}
