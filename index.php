<?php
session_start();

include 'init.php';

?>

<div class="container mt-5">
    <div class="row row-cols-1 col-items row-cols-md-4 rwo-cols-sm-2">
        
        <?php  
        $result = $db->table("items")->get();
            foreach($result as $item){
                if($item['Status'] == 1){
                ?>
                    <div class="col">
                        <div class="card m-1 p-2">
                            <div class="card-header px-0 d-flex justify-content-between bg-transparent">
                                <span class="fs-6"><i class="fa fa-user "></i> Khalifa alqiadi</span>
                                <span><i class="fa fa-calendar"></i> <?php echo $item['Date']?></span>
                            </div>
                            <div class="card-body px-0 d-flex flex-column align-items-center">
                            <?php echo "<img src='dashboard/upload/images/" . $item['image'] ."' alt='' class='img-fluid border border-5 border-leght'>";?>
                                <h3 class="my-3 "><a class="text-decoration-none text-dark" href='details.php?itemid=<?php echo $item['ItemID']?>'><i class="fa fa-book text-danger"></i> <?php echo $item['Name']?></a></h3>
                                <span class='text-center text-danger fw-bold'><i class="fa fa-money text-dark"></i> RY:<?php echo  $item['Price']?></span>
                            </div>
                            <div class="card-footer d-flex justify-content-between align-items-center bg-transparent">
                            <i class="fa fa-share fs-5" data-bs-toggle="modal" data-bs-target="#staticBackdrop<?php echo $item['ItemID'] ?>"></i>
                            <i class="fa fa-cart-arrow-down fs-6 p-3 bg-danger rounded-circle text-white"></i>
                            <i class="fa fa-star fs-5"></i>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="staticBackdrop<?php echo $item['ItemID'] ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                <?php 
                                    $message = $item['Name'] . "  =>  " . $item['Description'];
                                    $url = "http://localhost/blogin/details.php?itemid=". $item['ItemID'];
                                    $db->showSharer($url, $message);
                                ?>
                                </div>
                            </div>
                        </div>
                    </div>

            <?php }}?>
    </div>


</div> 

<?php
include $tepl . 'footer.php';
?>