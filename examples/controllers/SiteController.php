<?php

namespace app\controllers;

use yii\web\Controller;

class SiteController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * @return string
     */
    public function actionPublisher()
    {
        return $this->render('publisher');
    }

    /**
     * @return string
     */
    public function actionReceiver()
    {
        return $this->render('receiver');
    }

    /**
     * @return string
     */
    public function actionReceiverWithPolicy()
    {
        return $this->render('receiver-with-policy');
    }

    /**
     * @return string
     */
    public function actionRoom()
    {
        return $this->render('room');
    }

    /**
     * @return string
     */
    public function actionRoomWithEvent()
    {
        return $this->render('room-with-event');
    }

    /**
     * @return string
     */
    public function actionRoomTwoWay()
    {
        return $this->render('room-two-way');
    }
}
