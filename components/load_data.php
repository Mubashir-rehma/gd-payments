<?php
$document_root = $_SERVER['DOCUMENT_ROOT'];
include $document_root . '/gd-payments/Assets/backendfiles/config.php';

$query = []; // This variable seems unnecessary, consider removing it
$lf = "";

$queryq = "SELECT * FROM gd_pay"; // Fixed variable name typo

// Loads en route
if (isset($_REQUEST['opening'])) {
    $queryq .= " WHERE Status = 'opening' ORDER BY id DESC"; // Corrected WHERE clause
    $query = $mysqli->query($queryq) or die($mysqli->error);
    $result = $query->fetch_all(MYSQLI_ASSOC);
    print_r($result); // Output the result
} else if (isset($_REQUEST['posted'])) {
    // Delivered Data
    $queryq .= " WHERE Status = 'posted' ORDER BY id DESC"; // Corrected WHERE clause
    $query = $mysqli->query($queryq) or die($mysqli->error);
    $result = $query->fetch_all(MYSQLI_ASSOC);
    print_r($result); // Output the result
} else if (isset($_REQUEST['bs_matched'])) {
    // Loads Issue
    $queryq .= " WHERE Status = 'bs_matched' ORDER BY id DESC"; // Corrected WHERE clause
    $query = $mysqli->query($queryq) or die($mysqli->error);
    $result = $query->fetch_all(MYSQLI_ASSOC);
    print_r($result); // Output the result
}

$i = 0;

echo $i;
foreach ($query as $row) {
    print_r($row);

    $gno = $row['GD_number'];
    $bankDate = $row['Gd_bankDate'];
    $tamount = $row['TotalAmount'];
    $pamount = $row['PaidAmount'];
    $status  = $row['Status'];
    $i++;

?>

    <tr>
        <td><?php echo $i ?></td>
        <td> <?php echo $gno ?></td>
        <td> <?php echo $bankDate ?></td>
        <td><?php echo $tamount ?></td>
        <td> <?php echo $pamount ?></td>
        <td> <?php echo $status ?></td>
        <td style="display: flex; justify-content: center;">

<!-- <li>
    <div style="margin: 10px 15px 0px" onclick="newcheckcallsbtn()">

        <a style="color: var(--font); font-size: 15px; margin: 7px 10px 10px 0; cursor: pointer; " href="index.php?action_type=newcall&id=<?php echo $row['id'] ?>">
            <img class="checkcall" src="" width="15px" height="15px" />
        </a>
    </div>

</li> -->

<div class="btn-group btn-group-rounded">
    <button type="button" class="btn btn-default btn-xs" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border-radius:3px; background: none; border: none; outline: none; text-align:center;">
        <i class="uil uil-ellipsis-h"></i>
    </button>
    <ul class="dropdown-menu">
        <li>
            <a style="color: green; font-size: 15px; margin: 7px 10px 10px 0; cursor: pointer;" class="driver_info_form" data-action_type="edit_gd" data-load_id="<?php echo $row['id'] ?>">
                <i class="uil uil-pen"></i>Edit
            </a>
        </li>


        <li>
            <a style="font-size: 15px; margin: 7px 10px 7px 0; cursor: pointer; color: var(--font); " class="load_action" data-action_type="posted" data-load_id="<?php echo $row['id'] ?>">
                <i class="fa-solid fa-route"></i> Posted
            </a>
        </li>

        

        <li>
            <a style="font-size: 15px; margin: 7px 10px 7px 0; cursor: pointer; color: var(--font); " class="load_action" data-action_type="opening" data-load_id="<?php echo $row['id'] ?>">
                <i class="fa-solid fa-route"></i> Opening
            </a>
        </li>

        <li>
            <a style="font-size: 15px; margin: 7px 10px 7px 0; cursor: pointer; color: var(--font);" class="load_action" data-action_type="bs_matched" data-load_id="<?php echo $row['id'] ?>">
                <i class="fa-solid fa-truck-ramp-box"></i> BS Matched
            </a>
        </li>

  




    </ul>
</div>
</td>

    </tr>

<?php } ?>