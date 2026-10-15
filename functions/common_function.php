<?php
// Including connection file
// include('./includes/connection.php');

// Ensure $con is defined and initialized
if (!isset($con)) {
    die("Database connection not established.");
}

// Getting all products
function getproducts() {
    global $con;

    // Check if 'category' is not set
    if (!isset($_GET['category'])) {
        // Fetch products in random order
        $select_query = "SELECT * FROM `products` ORDER BY RAND()";
        $result_query = mysqli_query($con, $select_query);

        // Check if the query was successful
        if (!$result_query) {
            die("Error in query: " . mysqli_error($con));
        }

        // Fetch and display products
            while ($row = mysqli_fetch_assoc($result_query)) {
            $product_id = $row['product_id'];
            $product_name = $row['product_name'];
            $description = $row['description'];
            $product_image = $row['product_image'];
            $product_unit = $row['Product_unit'] ?? 'N/A'; 
            $product_price = $row['Product_price'];
            $stock_qty = isset($row['stock_quantity']) ? intval($row['stock_quantity']) : 0;

            // If stock is not available show out of stock label
            $action_html = '';
            if ($stock_qty > 0) {
                $action_html = "<a href='index.php?add_to_cart=$product_id' class='btn btn-primary'>Add to Cart</a>";
            } else {
                $action_html = "<span class='badge bg-danger'>Out of stock</span>";
            }

            echo "
            <div class='col-6 col-md-4 col-lg-3 mb-3'>
                <div class='card h-100 shadow-sm border-0' style='border-radius:12px; overflow:hidden;'>
                    <div style='height:180px; overflow:hidden; background:#f8f9fa; display:flex; align-items:center; justify-content:center;'>
                        <img src='./Admin/product_images/$product_image' alt='$product_name' style='width:100%; height:100%; object-fit:contain; padding:8px;'>
                    </div>
                    <div class='card-body d-flex flex-column'>
                        <h6 class='card-title fw-bold mb-1' style='font-size:0.9rem; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;'>$product_name</h6>
                        <p class='card-text text-muted small mb-2' style='display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; font-size:0.78rem;'>$description</p>
                        <p class='card-text small mb-1'><span class='text-muted'>Unit:</span> <strong>$product_unit</strong></p>
                        <p class='card-text mb-3'><span class='text-muted small'>Price:</span> <strong class='text-success'>&#8377;$product_price</strong></p>
                        <div class='mt-auto'>$action_html</div>
                    </div>
                </div>
            </div>";
            }
    }
}



// Getting products by category
function get_unique_categories() {
    global $con;

    // Check if 'category' is set
    if (isset($_GET['category'])) {
        $category_id = intval($_GET['category']); // Ensure category_id is an integer

        // Fetch products for the specific category
        $select_query = "SELECT * FROM `products` WHERE category_id = $category_id";
        $result_query = mysqli_query($con, $select_query);

        // Check if the query was successful
        if (!$result_query) {
            die("Error in query: " . mysqli_error($con));
        }

        $num_rows = mysqli_num_rows($result_query);
        if ($num_rows == 0) {
            echo "<h2 class='text-center text-danger'>No products available in this category</h2>";
        }

        // Fetch and display products
        while ($row = mysqli_fetch_assoc($result_query)) {
            $product_id = $row['product_id'];
            $product_name = $row['product_name'];
            $description = $row['description'];
            $product_image = $row['product_image'];
            $product_unit = $row['Product_unit'] ?? 'N/A'; // Default value if column is missing
            $product_price = $row['Product_price'];
            $stock_qty = isset($row['stock_quantity']) ? intval($row['stock_quantity']) : 0;

            $action_html = $stock_qty > 0 ? "<a href='index.php?add_to_cart=$product_id' class='btn btn-primary'>Add to Cart</a>" : "<span class='badge bg-danger'>Out of stock</span>";

            echo "
            <div class='col-6 col-md-4 col-lg-3 mb-3'>
                <div class='card h-100 shadow-sm border-0' style='border-radius:12px; overflow:hidden;'>
                    <div style='height:180px; overflow:hidden; background:#f8f9fa; display:flex; align-items:center; justify-content:center;'>
                        <img src='./Admin/product_images/$product_image' alt='$product_name' style='width:100%; height:100%; object-fit:contain; padding:8px;'>
                    </div>
                    <div class='card-body d-flex flex-column'>
                        <h6 class='card-title fw-bold mb-1' style='font-size:0.9rem; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;'>$product_name</h6>
                        <p class='card-text text-muted small mb-2' style='display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; font-size:0.78rem;'>$description</p>
                        <p class='card-text small mb-1'><span class='text-muted'>Unit:</span> <strong>$product_unit</strong></p>
                        <p class='card-text mb-3'><span class='text-muted small'>Price:</span> <strong class='text-success'>&#8377;$product_price</strong></p>
                        <div class='mt-auto'>$action_html</div>
                    </div>
                </div>
            </div>";
        }
    }
}

// Displaying categories in the sidebar
function getcategories() {
    global $con;

    // Fetch categories
    $select_categories = "SELECT * FROM `categories`";
    $result_categories = mysqli_query($con, $select_categories);

    // Check if the query was successful
    if (!$result_categories) {
        die("Error in query: " . mysqli_error($con));
    }

    // Check if categories exist
    $num_rows = mysqli_num_rows($result_categories);
    if ($num_rows == 0) {
        echo "<li class='nav-item'><a href='#' class='nav-link text-light'>No Categories Available</a></li>";
        return;
    }

    // Fetch and display categories
    while ($row_data = mysqli_fetch_assoc($result_categories)) {
        $category_name = $row_data['category_name'];
        $category_id = $row_data['category_id'];

        //check if this category is selected
              $active_class = (isset($_GET['category']) && $_GET['category'] == $category_id) ? 'active' : '';


        echo "
        <li class='category-sidebar-item'>
            <a href='display_all.php?category=$category_id' class='$active_class'>$category_name</a>
        </li>";
    }
}

//get searching products
function search_product() {
    global $con;

    // Check if the search form is submitted
    if (isset($_GET['search_data_product'])) {
        $search_value = $_GET['search_data']; // Get the search input value

        // Fetch products matching the search query
        $search_query = "SELECT * FROM `products` WHERE product_keywords LIKE '%$search_value%'";
        $result_query = mysqli_query($con, $search_query);

        // Check if the query was successful
        if (!$result_query) {
            die("Error in query: " . mysqli_error($con));
        }

        // Check if any products are found
        $num_rows = mysqli_num_rows($result_query);
        if ($num_rows == 0) {
            echo "<h2 class='text-center text-danger'>No products found for '$search_value'</h2>";
            return;
        }

        // Fetch and display products
        while ($row = mysqli_fetch_assoc($result_query)) {
            $product_id = $row['product_id'];
            $product_name = $row['product_name'];
            $description = $row['description'];
            $product_image = $row['product_image'];
            $product_price = $row['Product_price'];
            $stock_qty = isset($row['stock_quantity']) ? intval($row['stock_quantity']) : 0;

            $action_html = $stock_qty > 0 ? "<a href='index.php?add_to_cart=$product_id' class='btn btn-primary'>Add to Cart</a>" : "<span class='badge bg-danger'>Out of stock</span>";

            echo "
            <div class='col-6 col-md-4 col-lg-3 mb-3'>
                <div class='card h-100 shadow-sm border-0' style='border-radius:12px; overflow:hidden;'>
                    <div style='height:180px; overflow:hidden; background:#f8f9fa; display:flex; align-items:center; justify-content:center;'>
                        <img src='./Admin/product_images/$product_image' alt='$product_name' style='width:100%; height:100%; object-fit:contain; padding:8px;'>
                    </div>
                    <div class='card-body d-flex flex-column'>
                        <h6 class='card-title fw-bold mb-1' style='font-size:0.9rem; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;'>$product_name</h6>
                        <p class='card-text text-muted small mb-3' style='display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; font-size:0.78rem;'>$description</p>
                        <p class='card-text mb-3'><span class='text-muted small'>Price:</span> <strong class='text-success'>&#8377;$product_price</strong></p>
                        <div class='mt-auto'>$action_html</div>
                    </div>
                </div>
            </div>";
        }
    }
}



//get ip address function
function getIPAddress() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}
function cart() {
    if (isset($_GET['add_to_cart'])) {
        global $con;
        $get_ip_add = getIPAddress();  
        $product_id = intval($_GET['add_to_cart']); 

        // Check if the product is already in the cart
        $select_query = "SELECT * FROM `cart` WHERE product_id = $product_id AND ip_address = '$get_ip_add'";
        $result_query = mysqli_query($con, $select_query);

        if (!$result_query) {
            die("Error in query: " . mysqli_error($con));
        }

        $num_rows = mysqli_num_rows($result_query);
        if ($num_rows > 0) {
            echo "<script>alert('This item is already present in the cart');</script>";
            echo "<script>window.open('index.php', '_self');</script>";
        } else {
            // Check product stock before adding
            $pq = mysqli_query($con, "SELECT stock_quantity FROM products WHERE product_id = $product_id");
            $p = $pq ? mysqli_fetch_assoc($pq) : null;
            $stock_qty = $p ? intval($p['stock_quantity']) : 0;
            if ($stock_qty <= 0) {
                echo "<script>alert('This product is out of stock and cannot be added to the cart.');</script>";
                echo "<script>window.open('index.php', '_self');</script>";
                return;
            }

            // Insert the product into the cart with default quantity 1
            $insert_query = "INSERT INTO `cart` (product_id, ip_address, quantity) VALUES ($product_id, '$get_ip_add', 1)";
            $insert_result = mysqli_query($con, $insert_query);

            if (!$insert_result) {
                die("Error in query: " . mysqli_error($con));
            }

            echo "<script>alert('Item added to cart successfully');</script>";
            echo "<script>window.open('index.php', '_self');</script>";
        }
    }
}

//funcation to get cart item number
function cart_item(){
    global $con;
    $get_ip_add = getIPAddress();  

    // Fetch all cart items for the current IP address
    $select_query = "SELECT * FROM `cart` WHERE ip_address = '$get_ip_add'";
    $result_query = mysqli_query($con, $select_query);
    $count_cart_items = mysqli_num_rows($result_query);


    
    echo $count_cart_items;
    }

    //total price function
    function total_cart_price() {
        global $con;
        $get_ip_add = getIPAddress(); 
        $total_price = 0; 
    
        $cart_query = "SELECT * FROM `cart` WHERE ip_address = '$get_ip_add'";
        $result = mysqli_query($con, $cart_query);
    
        if (!$result) {
            die("Error in query: " . mysqli_error($con));
        }
    
        while ($row = mysqli_fetch_array($result)) {
            $product_id = $row['product_id'];
                $select_product = "SELECT Product_price FROM `products` WHERE product_id = '$product_id'";
                $result_products = mysqli_query($con, $select_product);

                while ($row_product_price = mysqli_fetch_array($result_products)) {
                    // parse numeric price from stored Product_price (e.g. '40$')
                    $price_raw = $row_product_price['Product_price'];
                    $num = preg_replace('/[^0-9.]/', '', $price_raw);
                    $product_price = $num === '' ? 0.0 : floatval($num);
                    $qty = intval($row['quantity']);
                    $total_price += ($product_price * $qty);
                }
        }
    
        echo $total_price;
    }

?>
