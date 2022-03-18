<?php

include "init.php";
echo '<div class="container mt-5">';
    if(isset($_GET['catid']) && is_numeric($_GET['catid'])){
        $category = intval($_GET['catid']);
?>
        <div class="row row-cols-1 col-items row-cols-md-4 rwo-cols-sm-2">
        
        <?php  
        $result = $db->table("items")->select("categories.ID")
        ->join("categories", "categories.ID", "items.cat_id")->where("cat_id", $category)->get();
            foreach($result as $item){
                
                ?>
                <div class="col">
                    <div class="card m-1 p-2">
                        <div class="card-header px-0 d-flex justify-content-between bg-transparent">
                            <span class="fs-6"><i class="fa fa-user "></i> Khalifa alqiadi</span>
                            <span><i class="fa fa-calendar"></i> <?php echo $item['Date']?></span>
                            <span><i class="fa fa-comments"></i> 5</span>
                        </div>
                        <div class="card-body px-0 d-flex flex-column align-items-center">
                        <?php echo "<img src='dashboard/upload/images/" . $item['image'] ."' alt='' class='img-fluid border border-5 border-leght'>";?>
                            <h3 class="my-3 "><a class="text-decoration-none text-dark" href='details.php?itemid=<?php echo $item['ItemID']?>'><i class="fa fa-book text-danger"></i> <?php echo $item['Name']?></a></h3>
                            <span class='text-center text-danger fw-bold'><i class="fa fa-money text-dark"></i> RY:<?php echo  $item['Price']?></span>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center bg-transparent">
                        <i class="fa fa-arrows fs-5"></i>
                        <i class="fa fa-cart-arrow-down fs-6 p-3 bg-danger rounded-circle text-white"></i>
                        <i class="fa fa-star fs-5"></i>
                        </div>
                    </div>
                </div>
            <?php }?>
        </div>
<?php
    }else{        
        echo "<div class='naice-message'>You Must Add Page ID</div>";
    }
echo '</div>';
?>