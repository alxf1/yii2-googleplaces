<?php
namespace indicalabs\google;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\widgets\InputWidget;
/**
 * Class PlacesAutocomplete
 *
 * @author Thiago Oliveira <thiago.oliveira.gt14@gmail.com>
 */
class GooglePlaces extends InputWidget
{
	/**
	 * @var array Autocomplete plugin options
	 */
	public $pluginOptions = [];
	/**
	 * @var array TypeAhead options
	 */
	public $typeaheadOptions = [];
	/**
	 * @var JsExpression|string On select plugin event handler
	 */
	public $onSelect;
	/**
	 * @var array Default typeahead options
	 */
	protected $defaultOptions = ['displayKey' => 'description'];
	/**
	 * @var string Adapter variable name
	 */
	protected $varName = '';
	/**
	 * @var int Widgets counter
	 */
	protected static $widgetCounter = 0;
	/**
	 * @inheritdoc
	 */
	public function run()
	{
		$view = $this->getView();
		PlacesPluginAsset::register($view);
		$this->varName = "addressPicker" . static::$widgetCounter++;
		$script = implode("\n", [$this->buildAdapter(), $this->buildTypeAhead(), $this->buildEvent()]);
		$view->registerJs($script);
		return $this->hasModel()
		? Html::activeTextInput($this->model, $this->attribute, $this->options)
		: Html::textInput($this->name, $this->value, $this->options);
	}
	/**
	 * Build the required adapter JS
	 *
	 * @return string
	 */
	protected function buildAdapter()
	{
		$options = !empty($this->pluginOptions) ? Json::encode($this->pluginOptions) : '{}';
		return "var $this->varName = new AddressPicker($options);";
	}
	/**
	 * Build the required plugin event JS
	 *
	 * @return string
	 */
	protected function buildEvent()
	{
		$script = '';
		if ($this->onSelect) {
			$event = $this->onSelect instanceof JsExpression ? $this->onSelect : new JsExpression($this->onSelect);
			$script .= "$this->varName.bindDefaultTypeaheadEvent($('#{$this->options['id']}'));\n";
			$script .= "$($this->varName).on('addresspicker:selected', $event);";
		}
		return $script;
	}
	/**
	 * Build the typeahead initialization JS code
	 *
	 * @return string
	 */
	protected function buildTypeAhead()
	{
		$options = ArrayHelper::merge($this->defaultOptions, $this->typeaheadOptions);
		$options['source'] = new JsExpression("$this->varName.ttAdapter()");
		return "$('#{$this->options['id']}').typeahead(null, " . Json::encode($options) . ");";
	}
}