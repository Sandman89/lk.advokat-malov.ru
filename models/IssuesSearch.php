<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Issues;

/**
 * IssuesSearch represents the model behind the search form of `app\models\Issues`.
 */
class IssuesSearch extends Issues
{
    public $client;
    public $assigns;
    public $workflow;
    public $court_date_filter;
    /* /**
      * {@inheritdoc}
      */
    public function rules()
    {
        return [
            [['id', 'created_at', 'parent', 'id_category', 'id_assign', 'id_client'], 'integer'],
            [['title', 'description','contract_number','court_date','client','assigns','workflow'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param string $type тип поиска, если archive - то ищем записи с status = 'completed'
     *
     * @return ActiveDataProvider
     */
    public function search($params,$type = null)
    {
        $query = Issues::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->setSort([
            'attributes' => [
                'title',
                'client' => [
                    'asc' => ['client.username' => SORT_ASC],
                    'desc' => ['client.username' => SORT_DESC],
                    'label' => 'client'
                ],
                'assigns' => [
                    'asc' => ['assigns.username' => SORT_ASC],
                    'desc' => ['assigns.username' => SORT_DESC],
                    'label' => 'assigns'
                ],
                'court_date',
                'contract_number',
                'created_at',
            ]
        ]);
        $dataProvider->sort->defaultOrder = ['created_at' => SORT_DESC];
        //проверяем параметр архив. Если там есть archive, то ищем записи с status = 'completed'
        if (!empty($type)){
            if ($type == 'archive'){
                $query->where(['=','status','completed']);
            }
        }
        else{
            $query->where(['!=','status','completed']);
        }

        if (!($this->load($params) && $this->validate())) {
            /**
             * Жадная загрузка данных модели для работы сортировки.
             */
            $query->joinWith(['client client']);
            $query->joinWith(['assigns assigns']);
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'title', $this->title])
              ->andFilterWhere(['like', 'contract_number', $this->contract_number]);


        // Фильтр по клиенту
        if (!empty($this->client)) {
            $query->joinWith(['client client'])->where('client.username LIKE "%' . $this->client . '%"');
        }
        // Фильтр по исполнителю
        if (!empty($this->assigns)) {
            $query->joinWith(['assigns assigns'])->where('assigns.username LIKE "%' . $this->assigns . '%"');
        }
        // Фильтр по дате суда
        if (!empty($this->court_date)) {
            $range_date_arr = explode(' — ',$this->court_date);
            if (count($range_date_arr) > 1){
                $today_start = date('Y-m-d H:i:s', strtotime($range_date_arr[0]));
                $today_end = date('Y-m-d 23:59:59', strtotime($range_date_arr[1]));
                $query->andWhere(['between', 'court_date', $today_start, $today_end]);
            }
        }
        // Фильтр по ходу рабочего процесса
        if (!empty($this->workflow)){
            /*$query->joinWith(['workflows' => function ($q) {
                $q->where('comment.title LIKE "%' . $this->workflow . '%"');
            }]);*/
            $query->joinWith(['workflows'])->where('comment.title LIKE "%'.$this->workflow.'%"');
        }

        //$query->andFilterWhere(['like', 'id', $this->id]);

        return $dataProvider;
    }
}
