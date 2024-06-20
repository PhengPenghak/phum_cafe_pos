<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user}}`.
 */
class m240620_080226_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'user_type_id' => $this->integer(),
            'email' => $this->string(),
            'username' => $this->string(),
            'status' => $this->tinyInteger(),
            'first_name' => $this->string(50),
            'last_name' => $this->string(50),
            'phone_number' => $this->string(50),
            'password_hash' => $this->string(),
            'password_reset_token' => $this->string(),
            'auth_key' => $this->string(),
            'verification_token' => $this->string(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'avatar_url' => $this->string(),
            'last_login' => $this->dateTime(),
            'default_lang' => $this->string(10)->defaultValue('en-US')
        ]);

        $this->batchInsert(
            '{{%user}}',
            [
                'user_type_id',
                'email',
                'username',
                'status',
                'first_name',
                'last_name',
                'phone_number',
                'password_hash',
                'auth_key',
                'created_at',
                'updated_at',
                'last_login',
            ],
            [
                [
                    1,
                    'admin@prointix.com',
                    'admin',
                    1,
                    'PRO',
                    'Admin',
                    '010 200 300',
                    '$2y$13$7edJqUXLuXHllTCpNRIV.OK5.Q6b7RVKsMsLq30Kd//OR2TxSdWbu',
                    'UpuWQa0CISobLvc8AI_ZQcqF69ff08nu',
                    date("Y-m-d H:i:s"),
                    date("Y-m-d H:i:s"),
                    date("Y-m-d H:i:s"),
                ],
                [
                    1,
                    'savath@gdmc.com',
                    'savath',
                    1,
                    'Savath',
                    'Mao',
                    '010 300 300',
                    '$2y$13$7edJqUXLuXHllTCpNRIV.OK5.Q6b7RVKsMsLq30Kd//OR2TxSdWbu',
                    'UpuWQa0CISobLvc8AI_ZQcqF69ff08nu',
                    date("Y-m-d H:i:s"),
                    date("Y-m-d H:i:s"),
                    date("Y-m-d H:i:s"),
                ]
            ]

        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}
