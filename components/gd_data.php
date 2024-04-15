<?php
$document_root = $_SERVER['DOCUMENT_ROOT'];
include $document_root . '/gd-payments/Assets/backendfiles/config.php';

$query = []; // This variable seems unnecessary, consider removing it
$lf = "";

$queryq = "SELECT gdpays.*, gd_pay.*
FROM gdpays
JOIN gd_pay ON gdpays.Gid = gd_pay.id"; // Fixed variable name typo

// Loads en route
if (isset($_REQUEST['Paid'])) {
    $queryq .= " WHERE gdpays.status = 'Paid' ORDER BY gdpays.id DESC"; // Corrected WHERE clause
    $query = $mysqli->query($queryq) or die($mysqli->error);
    $result = $query->fetch_all(MYSQLI_ASSOC);
    print_r($result); // Output the result
} else if (isset($_REQUEST['Payable'])) {
    // Delivered Data
    $queryq .= " WHERE gdpays.status = 'Payable' ORDER BY gdpays.id DESC"; // Corrected WHERE clause
    $query = $mysqli->query($queryq) or die($mysqli->error);
    $result = $query->fetch_all(MYSQLI_ASSOC);
    print_r($result); // Output the result
} else if (isset($_REQUEST['partially_paid'])) {
    // Loads Issue
    $queryq .= " WHERE gdpays.status = 'partially_paid' ORDER BY gdpays.id DESC"; // Corrected WHERE clause
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
    $pamount = $row['total_paid'];
    $status  = $row['status'];
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
            <a style="font-size: 15px; margin: 7px 10px 7px 0; cursor: pointer; color: var(--font); " class="load_action" data-action_type="add-payment" data-load_id="<?php echo $row['id'] ?>">
                <i class="fa-solid fa-route"></i> Add Payment
            </a>
        </li>

        

        <li>
            <a style="font-size: 15px; margin: 7px 10px 7px 0; cursor: pointer; color: var(--font); " class="load_action" data-action_type="delete" data-load_id="<?php echo $row['id'] ?>">
                <i class="fa-solid fa-route"></i> Delete
            </a>
        </li>

    </ul>
</div>
</td>

    </tr>

<?php } ?>