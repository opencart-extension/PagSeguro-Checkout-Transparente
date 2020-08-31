<?php

class ModelExtensionPaymentPagSeguro extends Model
{
    /**
     * Cria as tabelas necessárias para o funcionamento
     */
    public function createTables()
    {
        $this->dropTables();

        $this->db->query('
        CREATE TABLE `' . DB_PREFIX . 'pagseguro_orders` (
          `code` VARCHAR(100) PRIMARY KEY,
          `last_event_date` VARCHAR(50),
          `payment_method` VARCHAR(20),
          `payment_link` VARCHAR(255),
          `gross_amount` FLOAT,
          `discount_amount` FLOAT,
          `fee_amount` FLOAT,
          `net_amount` FLOAT,
          `extra_amount` FLOAT,
          `raw` LONGTEXT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ');
    }

    /**
     * Remove as tabelas
     */
    public function dropTables()
    {
        $this->db->query('DROP TABLE IF EXISTS `' . DB_PREFIX . 'pagseguro_orders`;');
    }
}
