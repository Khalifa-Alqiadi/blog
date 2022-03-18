<?php
session_start();
if(isset($_SESSION['user'])){
    include 'init.php';
    $do = isset($_GET['do']) ? $do = $_GET['do'] : 'Manage';

    if($do == 'Manage'){
    
        $rows = $db->table("categories")->get();
    
    ?>

        <h1 class="text-center">Manage Category</h1>
        <div class="container">
            <div class="table-responsive">
                <table class="main-table manage-members text-center table table-bordered">
                    <tr>
                        <td>#ID</td>
                        <td>Name</td>
                        <td>Control</td>

                    </tr>
                    <?php

                        foreach($rows as $row){

                            echo "<tr>" .
                                    "<td>" . $row['ID'] .  
                                    "</td><td>" . $row['NameCategory'] . 
                                    "</td><td>
                                    <a href='categories.php?do=Edit&id=" . $row['ID'] . "' 
                                        class='btn btn-success'>
                                        <i class='fa fa-edit'></i> Edit</a>
                                    <a href='categories.php?do=Delete&id=" . $row['ID'] . "' 
                                        class='btn btn-danger confirm'>
                                        <i class='fa fa-close'></i> Delete</a>";
                                        if($row['Approve'] == 0){
                                            echo "<a href='categories.php?do=Activate&id=" . $row['ID'] . "' 
                                                    class='btn btn-info activate'>
                                                    <i class='fa fa-check'></i> Approve</a>"; 
                                        } 
                            echo "</td></tr>";
                        }
                    ?>
                </table>
            </div>
            <a href="categories.php?do=Add" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Add New</a>
        </div>

<?php }elseif($do == 'Add'){ ?>

        <h1 class="text-center">Add New Category</h1>
        <div class="container">
            <div class="form-container">
            <form class="form-horizontal" action="?do=Insert" method="POST" enctype="multipart/form-data">
                <div class="mb-2 row">
                    <label class="col-sm-2 col-form-label">Category Name</label>
                    <div class="col-sm-10 col-md-9">
                        <input type="text" name="name" class="form-control" autocomplete="off" required="required" placeholder="Enter Name">
                    </div>
                </div>
                <div class="mb-2 row">
                    <div class="offset-sm-2 col-sm-10">
                        <input type="submit" value="Add Member" name="save" class=" btn btn-primary ">
                    </div>
                </div>
            </form>
            </div>
        </div> 

<?php }elseif($do == 'Insert'){  // Insert Page

        if($_SERVER['REQUEST_METHOD'] == 'POST'){

            echo "<h1 class='text-center'>Insert Category</h1>";
            echo "<div class='container'>";
            $name = $_POST['name'];
            
            $resultData = $db->insert("categories", [
                'NameCategory'  => $name,
                'Approve'       => 0
            ]);
            if($resultData){
                $TheMsg = "<div class='alert alert-success'> Record Inserted</div>";
                $db->redirectHome($TheMsg, 'back');
            }else{
                echo "Field to saved";
            }

        }else{

            header("location: category.php");
        }
    
    }elseif($do == 'Edit'){
        
        $id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;
        $rows = $db->table("categories")->where("ID", $id)->get();
        foreach($rows as $row){?>

            <h1 class="text-center">Edit Category</h1>
            <div class="container">
                <div class="form-container">
                <form class="form-horizontal" action="?do=Update" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo $id; ?>" />
                    <!-- Start Name -->
                        <div class="mb-2 row">
                            <label class="col-sm-2 col-form-label">User Name</label>
                            <div class="col-sm-10 col-md-9">
                                <input type="text" name="name" value="<?php echo $row['NameCategory'] ?>" class="form-control" autocomplete="off" required="required">
                            </div>
                        </div>
                    <!-- End Name -->
                    <!-- Start Submit -->
                    <div class="mb-2 row">
                            <div class="offset-sm-2 col-sm-10">
                                <input type="submit" value="Update" class=" btn btn-primary ">
                            </div>
                        </div>
                    <!-- End Submit -->
                </form>
                </div>
            </div>    
<?php
        }
    } 

    elseif($do == 'Update'){ 

    echo "<h1 class='text-center'>Update Category</h1>";
    

    if($_SERVER['REQUEST_METHOD'] == 'POST'){

        $id     = $_POST['id'];
        $name   = $_POST['name'];
        $rows = $db->update("categories", [
            'NameCategory' => $name
        ])->where("ID", $id)->exec();
        if($rows){
            $TheMsg = "<div class='alert alert-success'> Record Updated</div>";
            $db->redirectHome($TheMsg, 'back');
        }else{
            $TheMsg = "<div class='alert alert-danger'> Record Field</div>";
            $db->redirectHome($TheMsg, 'back');
        }
    }else{
        echo "error";
    }

    }elseif($do == 'Delete'){

        echo "<h1 class='text-center'>Delete Category</h1>";            
        $id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0; 
        $check = $db->delete("categories")->where("ID", $id)->exec();
        if($check){
            $TheMsg = "<div class='alert alert-success'>  Record Deleted</div>";
            $db->redirectHome($TheMsg, 'back', 5);
        }
        
    }elseif($do == 'Activate'){

        echo "<h1 class='text-center'>Activate Category</h1>";

        $id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;

        $rows = $db->update("categories", ["Approve" => 1])->where("ID", $id)->exec();
        if($rows){
            $TheMsg = "<div class='alert alert-success'> Record Update</div>";
            redirectHome($TheMsg, 'back');
        }else{
            $TheMsg = "<div class='alert alert-danger'> Error</div>";
            redirectHome($TheMsg, 'back');
        }
    
    }else{

        $TheMsg = '<div class="alert alert-danger">This ID Is Not Exist</div>';
        redirectHome($TheMsg);
    }

    include $tepl . 'footer.php';
}else{
    header("location: login.php");
}
ob_end_flush();

?>