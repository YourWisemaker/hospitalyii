<?php

use yii\db\Migration;

class m250313_015105_create_clinic_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Create auth assignment tables for RBAC
        $this->createTable('{{%auth_item}}', [
            'name' => $this->string(64)->notNull(),
            'type' => $this->integer()->notNull(),
            'description' => $this->text(),
            'rule_name' => $this->string(64),
            'data' => $this->text(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'PRIMARY KEY ([[name]])',
        ]);

        $this->createTable('{{%auth_item_child}}', [
            'parent' => $this->string(64)->notNull(),
            'child' => $this->string(64)->notNull(),
            'PRIMARY KEY ([[parent]], [[child]])',
        ]);

        $this->addForeignKey('fk-auth_item_child-parent', '{{%auth_item_child}}', 'parent', '{{%auth_item}}', 'name', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-auth_item_child-child', '{{%auth_item_child}}', 'child', '{{%auth_item}}', 'name', 'CASCADE', 'CASCADE');

        $this->createTable('{{%auth_assignment}}', [
            'item_name' => $this->string(64)->notNull(),
            'user_id' => $this->string(64)->notNull(),
            'created_at' => $this->integer(),
            'PRIMARY KEY ([[item_name]], [[user_id]])',
        ]);

        $this->addForeignKey('fk-auth_assignment-item_name', '{{%auth_assignment}}', 'item_name', '{{%auth_item}}', 'name', 'CASCADE', 'CASCADE');

        $this->createTable('{{%auth_rule}}', [
            'name' => $this->string(64)->notNull(),
            'data' => $this->text(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'PRIMARY KEY ([[name]])',
        ]);

        // Create user table
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(50)->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string(255)->notNull(),
            'password_reset_token' => $this->string(255)->unique(),
            'email' => $this->string(255)->notNull()->unique(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        // Create regions table
        $this->createTable('{{%region}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'description' => $this->text(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);

        // Create employees table
        $this->createTable('{{%employee}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'position' => $this->string(50),
            'contact' => $this->string(15),
            'address' => $this->text(),
            'region_id' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);

        $this->addForeignKey(
            'fk-employee-region_id',
            '{{%employee}}',
            'region_id',
            '{{%region}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        // Create medicines table
        $this->createTable('{{%medicine}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'price' => $this->decimal(10, 2)->notNull(),
            'stock' => $this->integer()->notNull()->defaultValue(0),
            'description' => $this->text(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);

        // Create treatments table
        $this->createTable('{{%treatment}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'price' => $this->decimal(10, 2)->notNull(),
            'description' => $this->text(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);

        // Create patients table
        $this->createTable('{{%patient}}', [
            'id' => $this->primaryKey(),
            'registration_number' => $this->string(20)->unique(),
            'name' => $this->string(100)->notNull(),
            'birth_date' => $this->date(),
            'gender' => "ENUM('M', 'F')",
            'address' => $this->text(),
            'contact' => $this->string(15),
            'registration_date' => $this->dateTime()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);

        // Create medical records table
        $this->createTable('{{%medical_record}}', [
            'id' => $this->primaryKey(),
            'patient_id' => $this->integer()->notNull(),
            'treatment_date' => $this->dateTime()->notNull(),
            'diagnosis' => $this->text(),
            'status' => "ENUM('ongoing', 'completed') NOT NULL DEFAULT 'ongoing'",
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);

        $this->addForeignKey(
            'fk-medical_record-patient_id',
            '{{%medical_record}}',
            'patient_id',
            '{{%patient}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Create treatment details table
        $this->createTable('{{%treatment_detail}}', [
            'id' => $this->primaryKey(),
            'medical_record_id' => $this->integer()->notNull(),
            'treatment_id' => $this->integer()->notNull(),
            'quantity' => $this->integer()->notNull()->defaultValue(1),
            'price' => $this->decimal(10, 2)->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);

        $this->addForeignKey(
            'fk-treatment_detail-medical_record_id',
            '{{%treatment_detail}}',
            'medical_record_id',
            '{{%medical_record}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-treatment_detail-treatment_id',
            '{{%treatment_detail}}',
            'treatment_id',
            '{{%treatment}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Create medicine details table
        $this->createTable('{{%medicine_detail}}', [
            'id' => $this->primaryKey(),
            'medical_record_id' => $this->integer()->notNull(),
            'medicine_id' => $this->integer()->notNull(),
            'quantity' => $this->integer()->notNull(),
            'price' => $this->decimal(10, 2)->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);

        $this->addForeignKey(
            'fk-medicine_detail-medical_record_id',
            '{{%medicine_detail}}',
            'medical_record_id',
            '{{%medical_record}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-medicine_detail-medicine_id',
            '{{%medicine_detail}}',
            'medicine_id',
            '{{%medicine}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Create payments table
        $this->createTable('{{%payment}}', [
            'id' => $this->primaryKey(),
            'medical_record_id' => $this->integer()->notNull(),
            'amount' => $this->decimal(10, 2)->notNull(),
            'payment_date' => $this->dateTime()->notNull(),
            'payment_method' => $this->string(50)->notNull(),
            'status' => "ENUM('pending', 'paid') NOT NULL DEFAULT 'pending'",
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);

        $this->addForeignKey(
            'fk-payment-medical_record_id',
            '{{%payment}}',
            'medical_record_id',
            '{{%medical_record}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Insert default admin user
        $this->insert('{{%user}}', [
            'username' => 'admin',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => Yii::$app->security->generatePasswordHash('admin123'),
            'email' => 'admin@example.com',
            'status' => 10,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        // Insert default roles
        $this->batchInsert('{{%auth_item}}', ['name', 'type', 'description', 'created_at', 'updated_at'], [
            ['admin', 1, 'Administrator', time(), time()],
            ['doctor', 1, 'Doctor', time(), time()],
            ['receptionist', 1, 'Receptionist', time(), time()],
            ['cashier', 1, 'Cashier', time(), time()],
        ]);

        // Assign admin role to admin user
        $this->insert('{{%auth_assignment}}', [
            'item_name' => 'admin',
            'user_id' => '1',
            'created_at' => time(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop foreign keys first
        $this->dropForeignKey('fk-payment-medical_record_id', '{{%payment}}');
        $this->dropForeignKey('fk-medicine_detail-medicine_id', '{{%medicine_detail}}');
        $this->dropForeignKey('fk-medicine_detail-medical_record_id', '{{%medicine_detail}}');
        $this->dropForeignKey('fk-treatment_detail-treatment_id', '{{%treatment_detail}}');
        $this->dropForeignKey('fk-treatment_detail-medical_record_id', '{{%treatment_detail}}');
        $this->dropForeignKey('fk-medical_record-patient_id', '{{%medical_record}}');
        $this->dropForeignKey('fk-employee-region_id', '{{%employee}}');
        $this->dropForeignKey('fk-auth_assignment-item_name', '{{%auth_assignment}}');
        $this->dropForeignKey('fk-auth_item_child-child', '{{%auth_item_child}}');
        $this->dropForeignKey('fk-auth_item_child-parent', '{{%auth_item_child}}');

        // Drop tables
        $this->dropTable('{{%payment}}');
        $this->dropTable('{{%medicine_detail}}');
        $this->dropTable('{{%treatment_detail}}');
        $this->dropTable('{{%medical_record}}');
        $this->dropTable('{{%patient}}');
        $this->dropTable('{{%treatment}}');
        $this->dropTable('{{%medicine}}');
        $this->dropTable('{{%employee}}');
        $this->dropTable('{{%region}}');
        $this->dropTable('{{%user}}');
        $this->dropTable('{{%auth_assignment}}');
        $this->dropTable('{{%auth_item_child}}');
        $this->dropTable('{{%auth_item}}');
        $this->dropTable('{{%auth_rule}}');

        return true;
    }
}
