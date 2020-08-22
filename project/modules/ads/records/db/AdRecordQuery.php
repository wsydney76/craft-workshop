<?php

namespace project\modules\ads\records\db;

use yii\db\ActiveQuery;

class AdRecordQuery extends ActiveQuery
{

    public function id($id = 0): AdRecordQuery
    {
        if (!$id) {
            return $this;
        }
        return $this->andWhere(['id' => $id]);
    }

    public function type($type = ''): AdRecordQuery
    {
        if (!$type) {
            return $this;
        }
        return $this->andWhere(['type' => $type]);
    }

    public function status($status = ''): AdRecordQuery
    {
        if (!$status) {
            return $this;
        }
        return $this->andWhere(['status' => $status]);
    }

    public function search($search = []): AdRecordQuery
    {
        if (!$search) {
            return $this;
        }
        return $this->andWhere([
            'or',
            ['like', 'title', $search],
            ['like', 'text', $search],
            ['like', 'email', $search],
        ]);
    }
}
