<?php 

namespace frontend\controllers;

use yii\web\Controller;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use frontend\models\User;
use frontend\models\Country;

class HelperController extends Controller {

	public function actionHtml(){
		echo Html::encode('Test > test');	
	}

	public function actionArray(){
		// todo: add self-defined helper
		// echo ArrayHelper::merge(['name' => 'Henry'], ['last' => ' World']);

		$array = ['foo' => ['bar' => new User()]];

		// get value
		echo ArrayHelper::getValue($array, 'foo.bar.name') . "<br>";
		echo ArrayHelper::getValue(new User(), function($user, $defaultValue){
			return $user->firstName . ' ' . $user->lastName;
		});

		// set value
		var_dump($array);
		ArrayHelper::setValue($array, 'foo.bar', ['key0' => 'val0']);
		var_dump($array);
		// have exist
		ArrayHelper::setValue($array, ['foo', 'bar'], ['key1' => 'val1']);
		var_dump($array);
		ArrayHelper::setValue($array, 'foo.two', ['key2' => 'val2']);
		var_dump($array);
		// pop Only variables can be passed by reference
		$a = ['name' => 'henry', 'age' => 18];
		$age = ArrayHelper::remove($a, 'age');
		echo $age . '<br>';
		// is exist
		var_dump(ArrayHelper::keyExists('Name', $a, true));
		var_dump(ArrayHelper::keyExists('name', $a, true));
		// get column
		$data = [
			['id' => '123', 'data' => '567'],
			['id' => '456', 'data' => '789'],
		];
		var_dump(array_column($data, 'data'));
		var_dump(ArrayHelper::getColumn($data, 'data'));
		var_dump(ArrayHelper::getColumn($data, function($elements){
			return $elements['data'];
		}));

		// reindex
		$data = [
			['id' => '123', 'data' => '567', 'device' => 'laptop'],
			['id' => '456', 'data' => '789', 'device' => 'tablet'],
			['id' => '456', 'data' => '987', 'device' => 'smartphone'],
		];
		// same id will be replace
		var_dump(ArrayHelper::index($data, 'id'));
		var_dump(ArrayHelper::index($data,  function($elements){
			return $elements['id'];
		}));
		// same id will be merge
		var_dump(ArrayHelper::index($data, null, 'id'));
		// 分级组合
		var_dump(ArrayHelper::index($data, 'data', function($elements){
			return $elements['id'];
		}));
		var_dump(ArrayHelper::index($data, 'data', [function($elements){
			return $elements['id'];
		}, 'device']));

		// build map ($key, $value, $parentKey)
		$data = [
			['id' => '123', 'name' => 'aaa', 'class' => 'x'],
			['id' => '124', 'name' => 'bbb', 'class' => 'x'],
			['id' => '456', 'name' => 'ccc', 'class' => 'y'],
		];
		var_dump(ArrayHelper::map($data, 'id', 'name'));
		var_dump(ArrayHelper::map($data, 'id', 'name', 'class'));

		// multidemensional Sorting
		$data = [
			['age' => 30, 'name' => 'Monreal'],
			['age' => 30, 'name' => 'Ramsey'],
			['age' => 19, 'name' => 'Walccot'],
		];
		ArrayHelper::multisort($data, ['age', 'name'], [SORT_ASC, SORT_DESC]);
		var_dump($data);

		// detect type
		$indexed = ['Li', 'Henry'];
		var_dump(ArrayHelper::isIndexed($indexed));
		$associative = ['Framework' => 'Yii', 'version' => '2.0'];
		var_dump(ArrayHelper::isAssociative($associative));

		// decode encode
		$data = ["hello &nbsp; &amp;world", '&gt;/ safsd'];
		var_dump(ArrayHelper::htmlEncode($data));
		var_dump(ArrayHelper::htmldecode($data));

		// merge
		$array1 =[
			'name' =>'Yii',
			'version' => '1.1',
			'ids' => [1],
			'validDomains' => [
				'example.com',
				'www.example.com'
			],
			'emails' => [
				'admin' => 'admin@example.com',
				'dev' => 'dev@example.com'
			],
		];

		$array2 =[
			'name' =>'Yii',
			'version' => '2.0',
			'ids' => [2],
			'validDomains' => [
				'yiiframework.com',
				'www.yiiframework.com'
			],
			'emails' => [
				'admin' => 'admin@yiiframework.com',
			],
		];
		var_dump(ArrayHelper::merge($array1, $array2));

		// convert object to array
		$countrys = Country::find()->limit(10)->all();
		var_dump(ArrayHelper::toArray($countrys, [
			'frontend\models\Country' => [
				'code', 'name', 'length' =>function($country) {
					return strlen($country->population);
				}
			]
		]));

		// check in:failed
		// echo \yii\base\ArrayHelper::isIn('a', ['a']);
		// echo \yii\base\ArrayHelper::isIn('a', new(ArrayObject['a']));
		// echo \yii\base\ArrayHelper::isSubset(new(ArrayObject['a', 'c']), new(ArrayObject['a', 'b', 'c']));
		die;
	}

}