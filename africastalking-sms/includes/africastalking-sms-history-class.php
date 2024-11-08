<?php

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

// Custom Table Class
class Supporthost_List_Table extends WP_List_Table {
    private $table_data;
    private function get_table_data( $search = '' ) {
        global $wpdb;

        $table = $wpdb->prefix . 'sms_history';

        if ( !empty($search) ) {
            // phpcs:ignore 
            $table = esc_sql($table); // Escaping the table name safely
            //phpcs:disable WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            $sql = $wpdb->prepare(
                "SELECT * FROM $table WHERE recipient LIKE %s OR message LIKE %s OR sms_history LIKE %s",
                '%' . $wpdb->esc_like($search) . '%',
                '%' . $wpdb->esc_like($search) . '%',
                '%' . $wpdb->esc_like($search) . '%'
            );
            // phpcs:ignore
            return $wpdb->get_results($sql, ARRAY_A);
        } else {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery
            return $wpdb->get_results(
                "SELECT * from {$table}",
                ARRAY_A
            );
        }
    }
    public function __construct() {
        parent::__construct(array(
            'singular' => 'item',
            'plural'   => 'items',
            'ajax'     => false
        ));
    }

    public function column_default($item, $column_name) {
        switch ($column_name) {
            case 'recipient':
            case 'user_id':
            case 'sms_type':
            case 'message':
            case 'sender_id':
            case 'sent_at':
            case 'actions':
                return $item[$column_name];
            default:
                return print_r($item, true);
        }
        
    }

    public function get_columns() {
        $columns = array(
                'cb'    => '<input type="checkbox" />',
                'user_id'          => __('User Id', 'supporthost-admin-table'),
                'sms_type'          => __('SMS Type', 'supporthost-admin-table'),
                'recipient'          => __('Recipient', 'supporthost-admin-table'),
                'message'         => __('Message', 'supporthost-admin-table'),
                'sender_id'   => __('Sender Id', 'supporthost-admin-table'),
                'sent_at'        => __('Date & Time', 'supporthost-admin-table'),
        );
        return $columns;
    }

    public function get_bulk_actions() {
        $actions = array(
            'delete' => 'Delete',
        );
        return $actions;
    }

    public function process_bulk_action() {
        if ('delete' === $this->current_action()) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'sms_history'; 
            // phpcs:ignore 
            $ids = isset($_REQUEST['element']) && is_array($_REQUEST['element']) ? $_REQUEST['element'] : array();
            $ids = array_map('sanitize_text_field', $ids);
           
            foreach ($ids as $id) {
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery
                $wpdb->query("DELETE FROM $table_name WHERE id IN($id)");
            }
        }
    }

    public function prepare_items() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'sms_history'; 
        $per_page = 10;

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);

        $this->process_bulk_action();

        $current_page = $this->get_pagenum();
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery
        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");

        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items / $per_page),
        ));
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery
        $this->items = $wpdb->get_results(
                "SELECT * from {$table_name}  ORDER BY sent_at DESC",
                ARRAY_A
            );
    }
    public function column_cb($item)
    {
        return sprintf(
                '<input type="checkbox" name="element[]" value="%s" />',
                $item['id']
        );
    }

}

