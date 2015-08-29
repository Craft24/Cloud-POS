<?php

require_once('Ticket.php');

class Dinningtable_model extends CI_Model
{

    public function __construct()
    {
        $this->load->database();
    }

    /*
     * filter = all, opened, closed.
     */
    public function get_AllTables($filter = 'all')
    {
        $query = $this->db->get('shop_table');

        $items = array();
        foreach ($query->result_array() as $row) {

            $ticket_id = $this->findTicketIDByTable($row['id']);
            $is_opened = !is_null($ticket_id);

            $item = [
                'id' => $row['id'],
                'name' => $row['name'],
                'capacity' => $row['capacity'],
                'ticket' => $ticket_id,
                'total_price' => $is_opened ? $this->findTicket($ticket_id)['total_price'] : 0.00,
                'create_time' => $is_opened ? $this->findTicket($ticket_id)['create_time'] : null,
            ];

            if ($filter == 'opened' && $is_opened) {
                $items[] = $item;
            } else if ($filter == 'closed' && (!$is_opened)) {
                $items[] = $item;
            } else if ($filter == 'all') {
                $items[] = $item;
            }
        }

        return $items;
    }

    public function get_AllMenuCategory()
    {
        $query = $this->db->get('menu_category');
        $items = array();

        foreach ($query->result_array() as $row) {

            if ($row['visible']){
                $menu_groups = $this->findMenuGroups($row['id']);
                $item_menu = array();

                foreach ($menu_groups as $group){

                    if ($group['visible']){
                        $item_menu[] = [
                            'id' => $group['id'],
                            'name' => $group['name'],
                            'menus' => $this->findMenuItems($group['id']),
                        ];
                    }
                }

                $items[] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'menu_groups' => $item_menu,
                ];
            }

        }

        return $items;
    }

    /**
     * @param $id
     * @return array for display
     */
    public function get_Ticket($id)
    {
        $result = array();
        $ticket = $this->findTicket($id);

        if (!empty($ticket)) {
            $result['id'] = $id;
            // 订单价格
            $result['total_price'] = $ticket['total_price'];

            //桌台名称
            $table_id = $this->findTableIDByTicket($ticket['id']);
            $result['table_name'] = $this->findTable($table_id)['name'];

            //开台时间
            $result['create_time'] = $ticket['create_time'];

            // 顾客人数
            $result['number_of_guests'] = $ticket['number_of_guests'];

            // 菜单列表
            $items = array();
            foreach ($this->findTicketItems($id) as $item) {
                $menu = $this->findMenu($item['menu_id']);
                if (!empty($menu)) {
                    $items[] = [
                        'id' => $menu['id'],
                        'name' => $menu['name'],
                        'count' => $item['menu_count'],
                        'price' => $menu['price'],
                        'total_price' => $item['total_price'],
                        'refunded' => $item['refunded'],
                        'settled' => $item['settled'],
                    ];
                }
            }
            $result['menu_items'] = $items;

        }


        return $result;
    }

    /**
     * @param $ticket_id
     * @param $table_id
     */
    public function change_TicketTable($ticket_id, $table_id)
    {
        $data = [
            'ticket_id' => $ticket_id,
            'table_id' => $table_id,
        ];

        $this->db
            ->where('ticket_id', $ticket_id)
            ->update('ticket_table', $data);
    }

    public function create_Ticket($table_id)
    {
        $ticket = new Ticket($table_id);

        return $ticket;
    }

    /**
     * @param $ticket_id
     * @return TicketItem array
     */
    private function findTicketItems($ticket_id)
    {
        $query = $this->db->get_where('ticket_item', array(
            'ticket_id' => $ticket_id,
        ));
        return $query->result_array();
    }

    /**
     * @param $category_id
     * @return Menu group array
     */
    private function findMenuGroups($category_id)
    {
        $query = $this->db->get_where('menu_group', array(
            'category_id' => $category_id,
        ));
        return $query->result_array();
    }

    /**
     * @param $group_id
     * @return Menu item array
     */
    private function findMenuItems($group_id)
    {
        $query = $this->db->get_where('menu_item', array(
            'group_id' => $group_id,
        ));

        return $query->result_array();
    }

    /**
     * @param $ticket_id
     * @return Ticket object
     */
    private function findTicket($ticket_id)
    {
        $query = $this->db->get_where('ticket', array(
            'id' => $ticket_id
        ));

        return $query->row_array();
    }

    /**
     * @param $menu_id
     * @return Menu Item Object
     */
    private function findMenu($menu_id)
    {
        $query = $this->db->get_where('menu_item', [
            'id' => $menu_id,
        ]);

        return $query->row_array();
    }


    /*
     * Param table_id
     */
    private function findTable($table_id)
    {
        $query = $this->db->get_where('shop_table', array(
            'id' => $table_id
        ));

        return $query->row_array();
    }

    /**
     * @param $table_id
     * @return Ticket id.
     * 订单ID有多个，只返回没有清台的订单ID
     */
    private function findTicketIDByTable($table_id)
    {
        $query = $this->db->get_where('ticket_table', array(
            'table_id' => $table_id
        ));
        if ($query->num_rows() < 1)
            return null; //invalid id

        foreach ($query->result() as $row)
        {
            $t = $this->findTicket ($row->ticket_id);
            if (empty($t['settled'])){
                return $row->ticket_id;
            }
        }

        return null;

    }

    /*
     * param Ticket id
     * return Table id
    */
    private function findTableIDByTicket($ticket_id)
    {
        $query = $this->db->get_where('ticket_table', array(
            'ticket_id' => $ticket_id
        ));
        if ($query->num_rows() < 1)
            return null; //invalid id

        $row = $query->first_row();

        return $row->table_id;
    }

}
