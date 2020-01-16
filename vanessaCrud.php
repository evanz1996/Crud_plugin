<?php
/*
Plugin Name: Vanessa Crud
Description: Hello guys, this is my first time making a CRUD in wordpress.
Version: 1.0.0
Author: Vanessa Bautista Valenzuela
License: GPL2
*/
register_activation_hook( __FILE__, 'crudOperations');
function crudOperations() {
  global $wpdb;
  $charset_collate = $wpdb->get_charset_collate();
  $table_name = $wpdb->prefix . 'products_table';
  $sql = "CREATE TABLE `$table_name` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_name` varchar(220) DEFAULT NULL,
  `quantity` varchar(220) DEFAULT NULL,
  `price` varchar(220) DEFAULT NULL,
  PRIMARY KEY(product_id)
  ) ENGINE=MyISAM DEFAULT CHARSET=latin1;
  ";
  if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }
}

//------------------------------------------Adding page ---------------------------------------------------------------------//
add_action('admin_menu', 'addAdminPageContent');
function addAdminPageContent() {
  add_menu_page('myCRUD', 'myCRUD', 'manage_options' ,__FILE__, 'mycrudAdminPage', 'dashicons-smiley');
}
//-------------------------------------------Inserting Data into DB-----------------------------------------------------------//
function mycrudAdminPage() {
  global $wpdb;
  $table_name = $wpdb->prefix . 'products_table';
  if (isset($_POST['newsubmit'])) {
    $product_name = $_POST['product_name'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $wpdb->query("INSERT INTO $table_name(product_name,quantity,price) VALUES('$product_name','$quantity','$price')");
    echo "<script>location.replace('admin.php?page=vanessaCrud.php');</script>";
  }

//---------------------------------------------Updating data of the submitted changes-----------------------------------------//
  if (isset($_POST['uptsubmit'])) {
    $product_id = $_POST['uptid'];
    $product_name = $_POST['uptproductname'];
    $quantity = $_POST['uptquantity'];
    $price = $_POST['uptprice'];
    $wpdb->query("UPDATE $table_name SET product_name='$product_name',quantity='$quantity',price='$price' WHERE product_id='$product_id'");
    echo "<script>location.replace('admin.php?page=vanessaCrud.php');</script>";
  }

//----------------------------- Deleting the selected Data --------------------------------------------------------------------//
  if (isset($_GET['del'])) {
    $del_id = $_GET['del'];
    $wpdb->query("DELETE FROM $table_name WHERE product_id='$del_id'");
    echo "<script>location.replace('admin.php?page=vanessaCrud.php');</script>";
  }
  ?>
 
 <!------------------------------------------- front end view table ---------------------------->
  <div class="wrap">
    <h2>CRUD Operations</h2>
    <table class="wp-list-table widefat striped">
        <thead>
            <tr>
            
            <th width="25%">Product Name</th>
            <th width="25%">Quantity</th>
            <th width="25%">Price</th>
            <th width="25%">Actions</th>
            </tr>
        </thead>
      <tbody>
            <form action="" method="post">
            <tr>
                <td><input type="text" id="product_name" name="product_name"></td>
                <td><input type="text" id="quantity" name="quantity"></td>
                <td><input type="text" id="price" name="price"></td>
                <td><button id="newsubmit" name="newsubmit" type="submit">INSERT</button></td>
            </tr>
            </form>
        <?php
          $result = $wpdb->get_results("SELECT * FROM $table_name");
          foreach ($result as $print) {
            echo "
              <tr>
               
                <td width='25%'>$print->product_name</td>
                <td width='25%'>$print->quantity</td>
                <td width='25%'>$print->price</td>
                <td width='25%'><a href='admin.php?page=vanessaCrud.php&upt=$print->product_id'><button type='button'>UPDATE</button></a> <a href='admin.php?page=vanessaCrud.php&del=$print->product_id'><button type='button'>DELETE</button></a></td>
              </tr>
            ";
          }
        ?>
      </tbody>  
    </table>
    <br>
    <br>
 <!-- ------------------------------------------will get  the selected product inside the input field- ---------------------------------------------------->
    <?php
      if (isset($_GET['upt'])) {
        $upt_id = $_GET['upt'];
        $result = $wpdb->get_results("SELECT * FROM $table_name WHERE product_id='$upt_id'");
        foreach($result as $print) {
          $product_name = $print->product_name;
          $quantity = $print->quantity;
          $price = $print->price;
        }
        echo "
        <table class='wp-list-table widefat striped'>
          <thead>
            <tr>
              <th width='25%'>Product ID</th>
              <th width='25%'>Product Name</th>
              <th width='25%'>Quantity</th>
              <th width='25%'>Price</th>
            </tr>
          </thead>
          <tbody>
            <form action='' method='post'>
              <tr>
                <td width='25%'>$print->product_id <input type='hidden' id='uptid' name='uptid' value='$print->product_id'></td>
                <td width='25%'><input type='text' id='uptproductname' name='uptproductname' value='$print->product_name'></td>
                <td width='25%'><input type='text' id='uptquantity' name='uptquantity' value='$print->quantity'></td>
                <td width='25%'><input type='text' id='uptprice' name='uptprice' value='$print->price'></td>
                <td width='25%'><button id='uptsubmit' name='uptsubmit' type='submit'>UPDATE</button> 
                                <a href='admin.php?page=vanessaCrud.php'><button type='button'>CANCEL</button></a></td>
              </tr>
            </form>
          </tbody>
        </table>";
      }
    ?>
  </div>
  <?php
}