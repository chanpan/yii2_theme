<?php

namespace backend\modules\ezforms2\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\ezforms2\models\Ezform;

/**
 * EzformSearch represents the model behind the search form about `backend\modules\ezforms2\models\Ezform`.
 */
class EzformSearch extends Ezform {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['ezf_id', 'created_by', 'updated_by', 'status', 'shared', 'public_listview', 'public_edit', 'public_delete', 'category_id', 'query_tools', 'unique_record', 'consult_tools'], 'integer'],
            [['ezf_version', 'ezf_name', 'ezf_detail', 'xsourcex', 'ezf_table', 'created_at', 'updated_at', 'co_dev', 'assign', 'field_detail', 'ezf_sql', 'ezf_js', 'ezf_error', 'consult_users', 'consult_telegram', 'ezf_options', 'fullname'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
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
    public function search($params) {
        $query = Ezform::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ezf_id' => $this->ezf_id,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
            'status' => $this->status,
            'shared' => $this->shared,
            'public_listview' => $this->public_listview,
            'public_edit' => $this->public_edit,
            'public_delete' => $this->public_delete,
            'category_id' => $this->category_id,
            'query_tools' => $this->query_tools,
            'unique_record' => $this->unique_record,
            'consult_tools' => $this->consult_tools,
        ]);

        $query->andFilterWhere(['like', 'ezf_version', $this->ezf_version])
                ->andFilterWhere(['like', 'ezf_name', $this->ezf_name])
                ->andFilterWhere(['like', 'ezf_detail', $this->ezf_detail])
                ->andFilterWhere(['like', 'xsourcex', $this->xsourcex])
                ->andFilterWhere(['like', 'ezf_table', $this->ezf_table])
                ->andFilterWhere(['like', 'co_dev', $this->co_dev])
                ->andFilterWhere(['like', 'assign', $this->assign])
                ->andFilterWhere(['like', 'field_detail', $this->field_detail])
                ->andFilterWhere(['like', 'ezf_sql', $this->ezf_sql])
                ->andFilterWhere(['like', 'ezf_js', $this->ezf_js])
                ->andFilterWhere(['like', 'ezf_error', $this->ezf_error])
                ->andFilterWhere(['like', 'consult_users', $this->consult_users])
                ->andFilterWhere(['like', 'consult_telegram', $this->consult_telegram])
                ->andFilterWhere(['like', 'ezf_options', $this->ezf_options]);

        return $dataProvider;
    }

    public function searchMyForm($params, $tab) {

        if ($tab == '1') {
            $query = Ezform::find()
                    ->select(["ezform.*", "CONCAT(`profile`.`firstname`,' ',`profile`.`lastname`) AS fullname"])
                    ->leftJoin('profile', '`ezform`.`created_by` = `profile`.`user_id`')
                    ->andWhere(['created_by' => Yii::$app->user->id])
                    ->andWhere('ezform.status = :status', [':status' => '1'])
                    ->orderBy('created_at DESC');
        } elseif ($tab == '2') {
            $query = Ezform::find()
                    ->select(["ezform.*", "CONCAT(`profile`.`firstname`,' ',`profile`.`lastname`) AS fullname"])
                    ->leftJoin('profile', '`ezform`.`created_by` = `profile`.`user_id`')
                    ->where(['created_by' => Yii::$app->user->id])
                    ->orWhere('ezf_id in (SELECT ezf_id FROM ezform_co_dev WHERE user_co = :user_id)', [':user_id' => Yii::$app->user->id])
                    ->andWhere('ezform.status = :status', [':status' => 1])
                    ->orderBy('created_at DESC');
        } elseif ($tab == '3') {
            $query = Ezform::find()
                    ->select(["ezform.*", "CONCAT(`profile`.`firstname`,' ',`profile`.`lastname`) AS fullname"])
                    ->leftJoin('profile', '`ezform`.`created_by` = `profile`.`user_id`')
                    //->andWhere(['created_by' => Yii::$app->user->id])
                    ->andWhere('ezform.status = :status AND ezform.shared = :shared', [':status' => '1', ':shared' => '1'])
                    ->orderBy('created_at DESC');
        } elseif ($tab == '4') {
            $query = Ezform::find()
                    ->select(["ezform.*", "CONCAT(`profile`.`firstname`,' ',`profile`.`lastname`) AS fullname"])
                    ->leftJoin('profile', '`ezform`.`created_by` = `profile`.`user_id`')
                    ->where('ezf_id in (SELECT ezf_id FROM ezform_assign WHERE user_id = :user_id)', [':user_id' => Yii::$app->user->id])
                    ->andWhere('ezform.status = :status AND ezform.shared = :shared', [':status' => 1, ':shared' => '2'])
                    ->orderBy('created_at DESC');
        } elseif ($tab == '5') {
            $query = Ezform::find()
                    ->select(["ezform.*", "CONCAT(`profile`.`firstname`,' ',`profile`.`lastname`) AS fullname"])
                    ->leftJoin('profile', '`ezform`.`created_by` = `profile`.`user_id`')
                    ->where('ezform.status = :status', [':status' => '1'])
                    ->andWhere('ezf_id in (SELECT ezf_id FROM ezform_co_dev WHERE user_co = :user_id)', [':user_id' => Yii::$app->user->id])
                    ->orWhere('ezf_id in (SELECT ezf_id FROM ezform_assign WHERE user_id = :user_id) AND ezform.shared = :shared', [':user_id' => Yii::$app->user->id, ':shared' => '2'])
                    ->orWhere('ezform.shared = :shared', [':shared' => '1'])
                    ->orderBy('created_at DESC');
        } elseif ($tab == '6') {
            $query = Ezform::find()
                    ->select(["ezform.*", "CONCAT(`profile`.`firstname`,' ',`profile`.`lastname`) AS fullname"])
                    ->leftJoin('profile', '`ezform`.`created_by` = `profile`.`user_id`')
                    ->andWhere(['created_by' => Yii::$app->user->id])
                    ->andWhere('ezform.status = :status', [':status' => '0'])
                    ->orderBy('created_at DESC');
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['attributes' => ['fullname', 'ezf_name', 'ezf_detail', 'created_at']],
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ezf_id' => $this->ezf_id,
            'created_by' => $this->created_by,
            /*'created_at' => $this->created_at,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
            'status' => $this->status,
            'shared' => $this->shared,
            'public_listview' => $this->public_listview,
            'public_edit' => $this->public_edit,
            'public_delete' => $this->public_delete,*/
        ]);

        $query->andFilterWhere(['like', 'ezf_name', $this->ezf_name]);

        return $dataProvider;
    }

}
