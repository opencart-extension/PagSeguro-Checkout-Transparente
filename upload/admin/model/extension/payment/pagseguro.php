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
          `order_id` INT(11),
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

    /**
     * Captura as informações de uma transação
     *
     * @param string $order_id
     * @param array $columns `null` para todas, exceto includeRaw
     * @param bool $includeRaw
     *
     * @return array
     */
    public function getTransactionInfo($order_id, $columns = null, bool $includeRaw = false)
    {
        $columns_default = [
            'code',
            'order_id',
            'last_event_date',
            'payment_method',
            'payment_link',
            'gross_amount',
            'discount_amount',
            'fee_amount',
            'net_amount',
            'extra_amount'
        ];

        if ($columns === null && $includeRaw === true) {
            array_push($columns_default, 'raw');
        }

        if ($columns === null) {
            $columns = $columns_default;
        }

        $id = $this->db->escape($order_id);

        $query = $this->db->query('
            SELECT ' . implode(',', $columns) . '
            FROM ' . DB_PREFIX . 'pagseguro_orders
            WHERE `order_id` = "' . $id . '"
        ');

        return $query->row;
    }
}
