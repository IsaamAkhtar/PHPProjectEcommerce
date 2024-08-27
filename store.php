<?php
session_start();
include 'includes/header.php';
include 'includes/nav.php';
?>
<?php
require_once('config.php');

// Create a new mysqli connection
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

// Check the connection
if (!$link) {
  die("Connection failed: " . mysqli_connect_error());
}

// Initialize products array
$products = array();

if ( isset($_GET['search']) ) {
  $keyword = trim($_GET['search']);
  $stmt = mysqli_prepare($link, "SELECT `tbl_product`.*, `tbl_category`.`cat_name`
          FROM `tbl_product`
          INNER JOIN `tbl_category`
          ON `tbl_product`.`cat_id` = `tbl_category`.`cat_id`
          WHERE `pd_name` LIKE ?
          ORDER BY `pd_id` DESC");
  $like_keyword = "%$keyword%";
  mysqli_stmt_bind_param($stmt, 's', $like_keyword);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  
  while ($row = mysqli_fetch_object($result)) {
    $products[] = $row;
  }
  mysqli_stmt_close($stmt);
}
elseif ( isset($_GET['category']) ) {
  $category = trim($_GET['category']);
  $stmt = mysqli_prepare($link, "SELECT `tbl_product`.*, `tbl_category`.`cat_name`
          FROM `tbl_product`
          INNER JOIN `tbl_category`
          ON `tbl_product`.`cat_id` = `tbl_category`.`cat_id`
          WHERE `tbl_product`.`cat_id` = ?
          ORDER BY `pd_id` DESC");
  mysqli_stmt_bind_param($stmt, 'i', $category);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  
  while ($row = mysqli_fetch_object($result)) {
    $products[] = $row;
  }
  mysqli_stmt_close($stmt);
}
else {
  $stmt = mysqli_prepare($link, "SELECT `tbl_product`.*, `tbl_category`.`cat_name`
          FROM `tbl_product`
          INNER JOIN `tbl_category`
          ON `tbl_product`.`cat_id` = `tbl_category`.`cat_id`
          ORDER BY `pd_id` DESC");
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  
  while ($row = mysqli_fetch_object($result)) {
    $products[] = $row;
  }
  mysqli_stmt_close($stmt);
}

mysqli_close($link);
?>
<div id="main">
    <header class="container">
      <h3 class="page-header">Store</h3>
    </header>
    <div class="container">
      <div class="row">
        <?php if (count($products) > 0) { ?>
          <?php foreach ($products as $product) { ?>
          <div class="col-sm-6 col-md-3">
            <div class="thumbnail">
              <img src="img/uploads/<?php echo htmlspecialchars($product->pd_image) ?>" alt="<?php echo htmlspecialchars($product->pd_name) ?>">
              <div class="caption">
                <h4 class="text-center"><?php echo htmlspecialchars($product->pd_name) ?></h4>
                <p><a href="product.php?id=<?php echo htmlspecialchars($product->pd_id); ?>" class="btn btn-default">View</a> <a href="cart.php?add=<?php echo htmlspecialchars($product->pd_id); ?>" class="btn btn-primary">Add to cart</a></p>
              </div>
            </div>
          </div>
          <?php } ?>
        <?php } else { ?>
        <div class="alert alert-info"><strong>Oh no!</strong> No products found!</div>
        <?php } ?>
      </div>
    </div>
</div>
<?php
include 'includes/footer.php';
?>
