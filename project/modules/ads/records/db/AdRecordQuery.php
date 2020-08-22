<?php

namespace project\modules\ads\records\db;

use craft\elements\User;
use project\modules\ads\AdsModule;
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

    public function email($email = ''): AdRecordQuery
    {
        if (!$email) {
            return $this;
        }

        return $this->andWhere(['email' => $email]);
    }

    public function user(User $user = null): AdRecordQuery
    {
        if (!$user) {
            return $this;
        }
        return $this->email($user->email);
    }

    public function status($status = ''): AdRecordQuery
    {
        if (!$status) {
            return $this;
        }

        if ($status == 'active') {
            $query = $this->status('open');
            $query = $query->andWhere(['>', 'dateCreated', date('Y-m-d', strtotime(AdsModule::ACTIVEPERIOD))]);
            return $query;
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
