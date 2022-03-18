
<?php
include "init.php";
$itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;
$result = $db->table("items")->select("items.*", "categories.ID")
->join("categories", "categories.ID", "items.cat_id")->where("ItemID",$itemid)->get();

?>

<div class="container mt-5">
    
    <div class="row">
        <?php foreach($result as $item){ ?>
            <h1 class="text-center mb-5"><?php echo $item['Name']?></h1>
            <div class="col-sm-4">
                <?php echo "<img src='dashboard/upload/images/" . $item['image'] ."' alt='' class='img-fluid border border-5 border-leght'>";?>
            </div>
            <div class="col-sm-7 p-4 border bg-light">
                <h2><?php echo $item['Name']?></h2>
                <p><?php echo $item['Description']?></p>
                <ul class="nav flex-column p-3">
                    <li class="p-2 border bg-body mb-2">Price: <?php echo $item['Price'] ?></li>
                    <li class="p-2 mb-2">Quentity: <?php echo $item['quentity'] ?></li>
                    <li class="p-2 border bg-body mb-2">Date: <?php echo $item['Date'] ?></li>
                    <li class="p-2 mb-2">ID: <?php echo $item['ItemID'] ?></li>
                    <li class="p-2 border bg-body mb-2">Category: <?php echo $item['NameCategory'] ?></li>
                </ul>
            </div>
        <?php } ?>
    </div>
</div>

