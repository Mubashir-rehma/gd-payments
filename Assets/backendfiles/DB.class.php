<?php
/* 
 * DB Class 
 * This class is used for database related (connect, insert, update, and delete) operations 
 * @author    CodexWorld.com 
 * @url        http://www.codexworld.com 
 * @license    http://www.codexworld.com/license 
 */

class DB
{
    
    private $dbHost     = "localhost";
    private $dbUsername = "root";
    private $dbPassword = "";
    private $dbName     = "logisticscrm";
    private $newload = "newload";
    private $load_tracking = "load_tracking";
    private $conFiles     = "rate_con_files";
    private $pod_files     = "pod_files";
    private $bol_files     = "bol_files";
    private $pickup_files     = "pickup_files";

    public function __construct()
    {
        if (!isset($this->db)) {
            // Connect to the database 
            $conn = new mysqli($this->dbHost, $this->dbUsername, $this->dbPassword, $this->dbName);
            if ($conn->connect_error) {
                die("Failed to connect with MySQL: " . $conn->connect_error);
            } else {
                $this->db = $conn;
            }
        }
    }

    /* 
     * Returns rows from the database based on the conditions 
     * @param string name of the table 
     * @param array select, where, order_by, limit and return_type conditions 
     */
    public function getRows($conditions = array())
    {
        $sql = 'SELECT ';
        $sql .= '*, (SELECT fileName FROM ' . $this->conFiles . ' WHERE newload_id = ' . $this->newload . '.id ORDER BY id DESC LIMIT 1) as default_image';

        $sql .= ' FROM ' . $this->newload ;
        if (array_key_exists("where", $conditions)) {
            $sql .= ' WHERE ';
            $i = 0;
            foreach ($conditions['where'] as $key => $value) {
                $pre = ($i > 0) ? ' AND ' : '';
                $sql .= $pre . $key . " = '" . $value . "'";
                $i++;
            }
        }

        if (array_key_exists("order_by", $conditions)) {
            $sql .= ' ORDER BY ' . $conditions['order_by'];
        } else {
            $sql .= ' ORDER BY id DESC ';
        }

        if (array_key_exists("start", $conditions) && array_key_exists("limit", $conditions)) {
            $sql .= ' LIMIT ' . $conditions['start'] . ',' . $conditions['limit'];
        } elseif (!array_key_exists("start", $conditions) && array_key_exists("limit", $conditions)) {
            $sql .= ' LIMIT ' . $conditions['limit'];
        }

        $result = $this->db->query($sql);

        if (array_key_exists("return_type", $conditions) && $conditions['return_type'] != 'all') {
            switch ($conditions['return_type']) {
                case 'count':
                    $data = $result->num_rows;
                    break;
                case 'conFiles':
                    $data = $result->fetch_assoc();

                    if (!empty($data)) {
                        $sql = 'SELECT * FROM ' . $this->conFiles . ' WHERE newload_id = ' . $data['id'];

                        $result = $this->db->query($sql);
                        $imgData = array();
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $imgData[] = $row;
                            }
                        }

                        $files = ['rate_con_files', 'bol_files', 'pod_files', 'pickup_files'];
                        $data['rate_con_files'] = $imgData;
                        // $data['bol_files'] = $imgData;
                        // $data['pod_files'] = $imgData;

                    }
                    break;
                case 'podFiles':
                    $data = $result->fetch_assoc();

                    if (!empty($data)) {

                        $sql = 'SELECT * FROM ' . $this->pod_files . ' WHERE pod_newload_id = ' . $data['id'];

                        $result = $this->db->query($sql);
                        $imgData = array();
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $imgData[] = $row;
                            }
                        }

                        $files = ['rate_con_files', 'bol_files', 'pod_files', 'pickup_files'];
                        $data['pod_files'] = $imgData;
                    }
                    break;
                case 'bolFiles':
                    $data = $result->fetch_assoc();

                    if (!empty($data)) {

                        $sql = 'SELECT * FROM ' . $this->bol_files . ' WHERE bol_newload_id = ' . $data['id'];

                        $result = $this->db->query($sql);
                        $imgData = array();
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $imgData[] = $row;
                            }
                        }

                        $files = ['rate_con_files', 'bol_files', 'pod_files', 'pickup_files'];
                        $data['bol_files'] = $imgData;
                    }
                    break;
                case 'pickup_files':
                    $data = $result->fetch_assoc();

                    if (!empty($data)) {

                        $sql = 'SELECT * FROM ' . $this->pickup_files . ' WHERE pickup_newload_id = ' . $data['id'];

                        $result = $this->db->query($sql);
                        $imgData = array();
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $imgData[] = $row;
                            }
                        }

                        $files = ['rate_con_files', 'bol_files', 'pod_files', 'pickup_files'];
                        $data['pickup_files'] = $imgData;
                    }
                break;
                default:
                    $data = '';
            }
        } else {
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
            }
        }
        return !empty($data) ? $data : false;
    }

    public function getConFileRow($id)
    {
        $sql = 'SELECT * FROM ' . $this->conFiles . ' WHERE id = ' . $id;
        $result = $this->db->query($sql);
        return ($result->num_rows > 0) ? $result->fetch_assoc() : false;
    }

    public function getPodFile($id)
    {
        $sql = 'SELECT * FROM ' . $this->pod_files . ' WHERE pod_id = ' . $id;
        $result = $this->db->query($sql);
        // print_r($result);
        return ($result->num_rows > 0) ? $result->fetch_assoc() : false;
    }

    public function getIgetBolFilemgRow($id)
    {
        $sql = 'SELECT * FROM ' . $this->bol_files . ' WHERE bol_id = ' . $id;
        $result = $this->db->query($sql);
        // print_r($result);
        return ($result->num_rows > 0) ? $result->fetch_assoc() : false;
    }

    public function getpickupfilerow($id)
    {
        $sql = 'SELECT * FROM ' . $this->pickup_files . ' WHERE pickup_file_id = ' . $id;
        $result = $this->db->query($sql) or die($this->db->error);
        return ($result->num_rows > 0) ? $result->fetch_assoc() : false;
    }

    /* 
     * Insert data into the database 
     * @param string name of the table 
     * @param array the data for inserting into the table 
     */
    public function insert($data)
    {
        if (!empty($data) && is_array($data)) {
            $columns = '';
            $values  = '';
            $i = 0;
            if (!array_key_exists('created', $data)) {
                $data['created'] = date("Y-m-d H:i:s");
            }
            if (!array_key_exists('modified', $data)) {
                $data['modified'] = date("Y-m-d H:i:s");
            }
            if (!array_key_exists('created_by', $data)) {
                $data['created_by'] = $_SESSION['myusername'];
            }
            if (!array_key_exists('dispatcher', $data)) {
                $data['dispatcher'] = $_SESSION['myusername'];
            }
            foreach ($data as $key => $val) {
                $pre = ($i > 0) ? ', ' : '';
                $columns .= $pre . $key;
                $values  .= $pre . "'" . $this->db->real_escape_string($val) . "'";
                $i++;
            }
            $query = "INSERT INTO " . $this->newload . " (" . $columns . ") VALUES (" . $values . ")";
            $insert = $this->db->query($query) or die($this->db->error);

            $load_id = $this->db->insert_id;
            $created_by = $_SESSION['myusername'];
            $PU_location = unserialize($data['Pick_up_Location']);
            $destination = unserialize($data['Destination']);
            $distance = unserialize($data['distance']);
            $duration = unserialize($data['time']);
            $PU_count = count((is_countable($PU_location)) ? $PU_location : []);
            
            $PU_count < 1 ? $PU_count = 1 : $PU_count = $PU_count;
            for ($i = 0; $i < $PU_count; $i++) {
                $PU_count > 0 ? $PU_loc = $this->db->real_escape_string($PU_location[$i]) : $PU_loc = $this->db->real_escape_string($PU_location);
                $PU_count > 0 ? $des = $this->db->real_escape_string($destination[$i]) : $des = $this->db->real_escape_string($destination);
                $PU_count > 0 ? $dis = $this->db->real_escape_string($distance[$i]) : $dis = $this->db->real_escape_string($distance);
                $PU_count > 0 ? $dur = $this->db->real_escape_string($duration[$i]) : $dur = $this->db->real_escape_string($duration);

                $query = "INSERT INTO " . $this->load_tracking . " (load_id, Created_By, Load_pickup_location, load_Destination, status, total_distance, duration) VALUES ('$load_id', '$created_by', '$PU_loc', '$des' , 'Load Added', '$dis', '$dur')";
                $tracking = $this->db->query($query) or die($this->db->error);
            }

            return $load_id;
        } else {
            return false;
        }
    }

    public function insetrateconFile($data)
    {
        if (!empty($data) && is_array($data)) {
            $columns = '';
            $values  = '';
            $i = 0;
            if (!array_key_exists('uploaded_on', $data)) {
                $data['uploaded_on'] = date("Y-m-d H:i:s");
            }
            foreach ($data as $key => $val) {
                $pre = ($i > 0) ? ', ' : '';
                $columns .= $pre . $key;
                $values  .= $pre . "'" . $this->db->real_escape_string($val) . "'";
                $i++;
            }
            $query = "INSERT INTO " . $this->conFiles . " (" . $columns . ") VALUES (" . $values . ")";
            $insert = $this->db->query($query);
            return $insert ? $this->db->insert_id : false;
        } else {
            return false;
        }
    }

    public function insetbolFile($data)
    {
        if (!empty($data) && is_array($data)) {
            $columns = '';
            $values  = '';
            $i = 0;
            if (!array_key_exists('uploaded_on', $data)) {
                $data['uploaded_on'] = date("Y-m-d H:i:s");
            }
            foreach ($data as $key => $val) {
                $pre = ($i > 0) ? ', ' : '';
                $columns .= $pre . $key;
                $values  .= $pre . "'" . $this->db->real_escape_string($val) . "'";
                $i++;
            }
            $query = "INSERT INTO " . $this->bol_files . " (" . $columns . ") VALUES (" . $values . ")";
            $insert = $this->db->query($query);
            return $insert ? $this->db->insert_id : false;
        } else {
            return false;
        }
    }

    public function insetpodFile($data)
    {
        if (!empty($data) && is_array($data)) {
            $columns = '';
            $values  = '';
            $i = 0;
            if (!array_key_exists('uploaded_on', $data)) {
                $data['uploaded_on'] = date("Y-m-d H:i:s");
            }
            foreach ($data as $key => $val) {
                $pre = ($i > 0) ? ', ' : '';
                $columns .= $pre . $key;
                $values  .= $pre . "'" . $this->db->real_escape_string($val) . "'";
                $i++;
            }
            $query = "INSERT INTO " . $this->pod_files . " (" . $columns . ") VALUES (" . $values . ")";
            $insert = $this->db->query($query);
            return $insert ? $this->db->insert_id : false;
        } else {
            return false;
        }
    }

    public function insetpickupfile($data)
    {
        if (!empty($data) && is_array($data)) {
            $columns = '';
            $values  = '';
            $i = 0;
            if (!array_key_exists('uploaded_on', $data)) {
                $data['uploaded_on'] = date("Y-m-d H:i:s");
            }
            foreach ($data as $key => $val) {
                $pre = ($i > 0) ? ', ' : '';
                $columns .= $pre . $key;
                $values  .= $pre . "'" . $this->db->real_escape_string($val) . "'";
                $i++;
            }
            $query = "INSERT INTO " . $this->pickup_files . " (" . $columns . ") VALUES (" . $values . ")";
            $insert = $this->db->query($query);
            return $insert ? $this->db->insert_id : false;
        } else {
            return false;
        }
    }

    /* 
     * Update data into the database 
     * @param string name of the table 
     * @param array the data for updating into the table 
     * @param array where condition on updating data 
     */
    public function update($data, $conditions)
    {
        if (!empty($data) && is_array($data)) {
            $colvalSet = '';
            $whereSql = '';
            $i = 0;
            if (!array_key_exists('modified', $data)) {
                $data['modified'] = date("Y-m-d H:i:s");
            }
            if (!array_key_exists('last_modified_by', $data)) {
                $data['last_modified_by'] = $_SESSION['myusername'];
            }
            foreach ($data as $key => $val) {
                $pre = ($i > 0) ? ', ' : '';
                $colvalSet .= $pre . $key . "='" . $this->db->real_escape_string($val) . "'";
                $i++;
            }
            if (!empty($conditions) && is_array($conditions)) {
                $whereSql .= ' WHERE ';
                $i = 0;
                foreach ($conditions as $key => $value) {
                    $pre = ($i > 0) ? ' AND ' : '';
                    $whereSql .= $pre . $key . " = '" . $value . "'";
                    $i++;
                }
            }
            $query = "UPDATE " . $this->newload . " SET " . $colvalSet . $whereSql;
            $update = $this->db->query($query);

            $load_id = $conditions['id'];
            $created_by = $_SESSION['myusername'];
            $PU_location = unserialize($data['Pick_up_Location']);
            $destination = unserialize($data['Destination']);
            $distance = unserialize($data['distance']);
            $duration = unserialize($data['time']);
            $PU_count = count((is_countable($PU_location)) ? $PU_location : []);

            $PU_count < 1 ? $PU_count = 1 : $PU_count = $PU_count;
            for ($i = 0; $i < $PU_count; $i++) {
                $PU_count > 0 ? $PU_loc = $this->db->real_escape_string($PU_location[$i]) : $PU_loc = $this->db->real_escape_string($PU_location);
                $PU_count > 0 ? $des = $this->db->real_escape_string($destination[$i]) : $des = $this->db->real_escape_string($destination);
                $PU_count > 0 ? $dis = $this->db->real_escape_string($distance[$i]) : $dis = $this->db->real_escape_string($distance);
                $PU_count > 0 ? $dur = $this->db->real_escape_string($duration[$i]) : $dur = $this->db->real_escape_string($duration);

                // Query to check if the record for the pick up and destination locations already exists
                $check = "select * from " . $this->load_tracking . " where load_id='$load_id' and Load_pickup_location='$PU_loc' and load_Destination='$des'";
                $checkquery = $this->db->query($check) or die($this->db->error);

                if($checkquery->num_rows < 1){
                    // Query for adding load new tracking details on load update
                    $query = "INSERT INTO " . $this->load_tracking . " (load_id, Created_By, Load_pickup_location, load_Destination, status, total_distance, duration) VALUES ('$load_id', '$created_by', '$PU_loc', '$des' , 'Load Added', '$dis', '$dur')";

                    $insert = $this->db->query($query) or die($this->db->error);
                }
            }

            return $update ? $this->db->affected_rows : false;
        } else {
            return false;
        }
    }

    /* 
     * Delete data from the database 
     * @param string name of the table 
     * @param array where condition on deleting data 
     */
    public function delete($conditions)
    {
        $whereSql = '';
        if (!empty($conditions) && is_array($conditions)) {
            $whereSql .= ' WHERE ';
            $i = 0;
            foreach ($conditions as $key => $value) {
                $pre = ($i > 0) ? ' AND ' : '';
                $whereSql .= $pre . $key . " = '" . $value . "'";
                $i++;
            }
        }
        $query = "DELETE FROM " . $this->newload . $whereSql;
        $delete = $this->db->query($query);
        return $delete ? true : false;
    }

    public function deleteConFile($conditions)
    {
        $whereSql = '';
        if (!empty($conditions) && is_array($conditions)) {
            $whereSql .= ' WHERE ';
            $i = 0;
            foreach ($conditions as $key => $value) {
                $pre = ($i > 0) ? ' AND ' : '';
                $whereSql .= $pre . $key . " = '" . $value . "'";
                $i++;
            }
        }
        $query = "DELETE FROM " . $this->conFiles . $whereSql;
        $delete = $this->db->query($query);
        return $delete ? true : false;
    }

    public function deletebolFile($conditions)
    {
        $whereSql = '';
        if (!empty($conditions) && is_array($conditions)) {
            $whereSql .= ' WHERE ';
            $i = 0;
            foreach ($conditions as $key => $value) {
                $pre = ($i > 0) ? ' AND ' : '';
                $whereSql .= $pre . $key . " = '" . $value . "'";
                $i++;
            }
        }
        $query = "DELETE FROM " . $this->bol_files . $whereSql;
        $delete = $this->db->query($query);
        return $delete ? true : false;
    }

    public function deletepodFile($conditions)
    {
        $whereSql = '';
        if (!empty($conditions) && is_array($conditions)) {
            $whereSql .= ' WHERE ';
            $i = 0;
            foreach ($conditions as $key => $value) {
                $pre = ($i > 0) ? ' AND ' : '';
                $whereSql .= $pre . $key . " = '" . $value . "'";
                $i++;
            }
        }
        $query = "DELETE FROM " . $this->pod_files . $whereSql;
        $delete = $this->db->query($query);
        return $delete ? true : false;
    }

    public function deletepcikupfile($conditions)
    {
        $whereSql = '';
        if (!empty($conditions) && is_array($conditions)) {
            $whereSql .= ' WHERE ';
            $i = 0;
            foreach ($conditions as $key => $value) {
                $pre = ($i > 0) ? ' AND ' : '';
                $whereSql .= $pre . $key . " = '" . $value . "'";
                $i++;
            }
        }
        $query = "DELETE FROM " . $this->pickup_files . $whereSql;
        $delete = $this->db->query($query);
        return $delete ? true : false;
    }
}
