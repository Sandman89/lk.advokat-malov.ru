<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Task;

/**
 * TaskSearch represents the model behind the search form of `app\models\Task`.
 */
class TaskSearch extends Task
{
    public $dates;
    public $author;
    public $assigns;
    public $dates_filter = [3 => 'Все',1 => 'Предстоящие', 2 => 'Прошедшие', ];
    public $dates_filter_result =3;
    public $created_date_filter;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at', 'id_issue', 'id_author'], 'integer'],
            [['title', 'description', 'deadline','dates','dates_filter_result','created_date_filter','author','assigns'], 'safe'],
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
        $query = Task::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'title',
                'dates' => [
                    'asc' => ['deadline' => SORT_ASC,'start'=>SORT_ASC],
                    'desc' => ['deadline' => SORT_DESC,'start'=>SORT_DESC],
                    'label' => 'dates'
                ],
                'author' => [
                    'asc' => ['user.username' => SORT_ASC],
                    'desc' => ['user.username' => SORT_DESC],
                    'label' => 'client'
                ],
                'assigns' => [
                    'asc' => ['user.username' => SORT_ASC],
                    'desc' => ['user.username' => SORT_DESC],
                    'label' => 'assigns'
                ],
                'created_at',
            ]
        ]);
        $dataProvider->sort->defaultOrder = ['created_at' => SORT_DESC];

        //проверяем параметр архив. Если там есть archive, то ищем записи с status = 'completed'
        if (!empty($type)){
            if ($type == 'archive'){
                $query->Where(['=','status','completed']);
            }
        }
        else{
            $query->Where(['!=','status','completed']);
        }

        if (!($this->load($params) && $this->validate())) {
            /**
             * Жадная загрузка данных модели для работы сортировки.
             */
            $query->joinWith(['assigns']);
            return $dataProvider;
        }
        $query->andFilterWhere(['like', 'title', $this->title]);

        // фильтр по датам из фильтра с формой
        if (!empty($this->dates_filter_result)){
            $this->dates_filter_result = (int)$this->dates_filter_result;
            if ($this->dates_filter_result==1){//предстоящие
                $current_time = date('Y-m-d H:i:s');
                $query->andWhere([
                    'or',
                    ['>=','deadline', $current_time],
                    ['>=','start',$current_time]
                ]);
            }
            if ($this->dates_filter_result==2){//прошедшие
                $current_time = date('Y-m-d H:i:s');
                $query->andWhere([
                    'or',
                    ['<=','deadline', $current_time],
                    ['<=','end',$current_time]
                ]);

            }
        }
        // Фильтр по клиенту
        if (!empty($this->author)) {
            $query->joinWith(['author'])->where('user.username LIKE "%' . $this->author . '%"');
        }
        // Фильтр по исполнителю
        if (!empty($this->assigns)) {
            $query->joinWith(['assigns'])->where('user.username LIKE "%' . $this->assigns . '%"');
        }
        // Фильтр по дате суда
        if (!empty($this->created_date_filter)) {
            $range_date_arr = explode(' — ',$this->created_date_filter);
            if (count($range_date_arr) > 1){
                $today_start = strtotime($range_date_arr[0]);
                $today_end = strtotime(date('Y-m-d 23:59:59', strtotime($range_date_arr[1])));
                $query->andWhere(['between', 'created_at', $today_start, $today_end]);
            }
        }

        return $dataProvider;
    }
}
