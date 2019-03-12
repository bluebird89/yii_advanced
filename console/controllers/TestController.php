<?php
/**
 * Created by PhpStorm.
 * User: henry
 * Date: 2018/7/25
 * Time: 10:58 PM
 */

namespace console\controllers;

use yii\console\Controller;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class TestController extends Controller
{
    // public $message;

    // public function options($actionID)
    // {
    //     return [’message’];
    // }
    // public function optionAliases()
    // {
    //     return [’m’ => ’message’];
    // }
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     *
     * ./yii test/index 'Hello henry34'
     */
    public function actionIndex($message = 'hello world')
    {
        echo $message . "\n";
    }

    public function actionCreate()
    {
        echo 'advanced';
    }

    // public function actionTest2()
    // {
    //     echo $this->message . "\n";
    // }
// ./yii test/index 3 4 5
    public function actionTest3($arg1, $arg2, $arg3)
    {
        echo $arg1 . "\n";
        echo $arg2 . "\n";
        echo $arg3 . "\n";
    }

//./yii test/test4 hello,world
    public function actionTest4(array $name)
    {
        $this->stdout("Hello?\n");
        echo $name[0] . "\n";
        echo $name[1] . "\n";
    }

    public function actionTest5()
    {
        echo Table::widget([
        ’headers’ => [’Project’, ’Status’, ’Participant’],
        ’rows’ => [
                [’Yii’, ’OK’, ’@samdark’],
                [’Yii’, ’OK’, ’@cebe’],
            ],
        ]);
    }
}