<?php

namespace kartik\mailmanager\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * QueueSearch represents the model behind the search form of `kartik\mailmanager\models\Queue`.
 */
class QueueSearch extends Queue
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'attempts', 'created_at', 'processed_at', 'scheduled_at', 'sent_at', 'status'], 'integer'],
            [['category', 'subject'], 'safe'],
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
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Queue::find()->select([
            'id',
            'category',
            'subject',
            'attempts',
            'created_at',
            'processed_at',
            'scheduled_at',
            'sent_at',
            'status',
        ]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'attempts' => $this->attempts,
            'created_at' => $this->created_at,
            'processed_at' => $this->processed_at,
            'scheduled_at' => $this->scheduled_at,
            'sent_at' => $this->sent_at,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'category', $this->category])
            ->andFilterWhere(['like', 'subject', $this->subject]);

        return $dataProvider;
    }
}
