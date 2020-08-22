<?php

namespace project\modules\ads\models\queries;

use craft\elements\User;
use project\modules\ads\models\SettingsModel;
use yii\db\ActiveQuery;

class AdModelQuery extends ActiveQuery
{

    public function id($id = 0): AdModelQuery
    {
        if (!$id) {
            return $this;
        }
        return $this->andWhere(['id' => $id]);
    }

    public function type($type = ''): AdModelQuery
    {
        if (!$type) {
            return $this;
        }

        return $this->andWhere(['type' => $type]);
    }

    public function email($email = ''): AdModelQuery
    {
        if (!$email) {
            return $this;
        }

        return $this->andWhere(['email' => $email]);
    }

    public function user(User $user = null): AdModelQuery
    {
        if (!$user) {
            return $this;
        }
        return $this->email($user->email);
    }

    public function status($status = ''): AdModelQuery
    {
        if (!$status) {
            return $this;
        }

        if ($status == 'active') {
            $query = $this->status('open');
            $query = $query->andWhere(['>', 'dateCreated', date('Y-m-d', strtotime(SettingsModel::ACTIVEPERIOD))]);
            return $query;
        }

        return $this->andWhere(['status' => $status]);
    }

    public function search($search = []): AdModelQuery
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
