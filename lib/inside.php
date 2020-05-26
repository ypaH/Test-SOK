<?php
if (!isset($_SESSION['loginOk']) || time()-$_SESSION['loginOk']>1800){
   session_destroy();
   header('Location: /');
}

$thisFolder=end($uri);
if ($thisFolder==''){
    $thisFolder=0;
}

//Add new folder
if (isset($_POST['new'])){
    if ($_POST['title']=='' || $_POST['description']==''){
        $_SESSION['msg']="All fields must be filled!";
        $_SESSION['new']['title']=$_POST['title'];
        $_SESSION['new']['description']=$_POST['description'];
        header ('Location: /');
    }
    $query="INSERT INTO folders (parent, title, description) VALUES (".$_POST['perFolder'].",'".$_POST['title']."','".$_POST['description']."') ";
    Insert($query);
    $_SESSION['msg']="New folder added successfully";
    header('Location: /');
}

//Edit folder
if (isset($_POST['update'])){
    $query="UPDATE folders SET title='".$_POST["title"]."', description='".$_POST['description']."' WHERE id=".$_POST['folder'];
    Update($query);
    $_SESSION['msg']="The folder edit successfully";
}

//Delete folder and subfolders
function DeleteFolder($parent){
    global $foldersToDelete;
    $result=Selects("SELECT parent,id FROM folders ORDER BY parent");
    while ($row=$result->fetch_assoc()) {
        if ($row['parent'] == $parent) {
            $foldersToDelete[] = $row['id'];
            DeleteFolder($row['id']);
        }
    }
}
if (isset($_POST['delete'])){
    $foldersToDelete=array(0=>$_POST['folder']);
    DeleteFolder($foldersToDelete[0]);
    $query="";
    foreach ($foldersToDelete as $item) {
        $query.="DELETE FROM folders WHERE id=$item;";
    }
    $mysql->multi_query($query);
    $_SESSION['msg']='Your selected folder and all subfolders was deleted';
    header('Location: /');
}

?>
<div class="container">
    <div class="row">
        <div class="left mt-3">
            <?php
            //Show folder tree
            function FolderList($parent,$exUri){
                $result=Selects("SELECT id, parent, title FROM folders");
                while ($row=$result->fetch_assoc()){
                    echo '<ul>';
                    if ($row['parent']==$parent){
                        echo '<li class="ml-1"><a href="'.$exUri.$row['id'].'">'.$row['title'].'</a></li>';
                        FolderList($row['id'],$exUri.$row['id'].'/');
                    }
                    echo '</ul>';
                }
            }
            ?>
            <ul>
                <li class="ml-1"><a href="/">Home</a></li>
                <?php FolderList(0,'/'); ?>
            </ul>
        </div>
        <div class="center col mt-3">
            <?php

            //Show information message
            if (isset($_SESSION['msg'])){
                echo '<p class="col mt-1 border text-danger">'.$_SESSION['msg'].'</p>';
                unset($_SESSION['msg']);
            }

            //Show breadcrumbs
            echo '<hr>';
            $newuri='';
            echo '<a href="/">Home</a>';
            foreach ($uri as $key=>$value){
                if ($key==0){
                    continue;
                }elseif ($key==1 && empty($value)){
                    continue;
                }else {
                    echo '->';
                    $newuri.='/'.strval($value);
                    $row=Select("SELECT title FROM folders WHERE id=".$value);
                    echo '<a href="'.$newuri.'">'.$row['title'].'</a>';
                }
            }
            echo '<hr>';

            //Show new folder form
            if (isset($_POST['addNew'])){
                ?>
                <div class="addFolder">
                    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
                        <p>Parent folder:
                            <select name="perFolder">
                                <?php
                                $query="SELECT * FROM folders ORDER BY id";
                                $result=Selects($query);
                                if ($thisFolder==0) {
                                    echo '<option value="'.$thisFolder.'" selected>First level</option>';
                                }
                                while ($row=$result->fetch_assoc()){
                                    echo '<option value="'.$row['id'].'"';
                                    if ($thisFolder==$row['id']){
                                        echo ' selected ';
                                    }
                                    echo '>'.$row['title'].'</option>';                       }
                                ?>
                            </select>
                        </p>
                        <p>Title: </p>
                        <input type="text" name="title" autofocus maxlength="50" <?php
                        if (isset($_SESSION['new'])){
                            echo 'value="'.$_SESSION['new']['title'].'"';
                        }
                        ?>>
                        <p>Description: </p>
                        <textarea name="description" rows="4" cols="50"><?php
                        if (isset($_SESSION['new'])){
                            echo 'value="'.$_SESSION['new']['description'].'"';
                            unset($_SESSION['new']);
                        }
                        ?></textarea>
                        <p><input type="submit" name="new" value="Add"></p>
                    </form>
                </div>
                <?php
            }

            //Show EDIT folder form
            if (isset($_POST['edit'])){
                $rowThisFolder=Select("SELECT * FROM folders WHERE id=".$_POST['folder']);
                ?><div class="addFolder">
                    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
                        <p>Parent folder:
                            <select name="perFolder">
                                <?php
                                $query="SELECT * FROM folders ORDER BY id";
                                $result=Selects($query);
                                if ($_POST['folder']==0) {
                                    echo '<option value="'.$_POST['folder'].'" selected>First level</option>';
                                }
                                while ($row=$result->fetch_assoc()){
                                    echo '<option value="'.$row['id'].'"';
                                    if ($_POST['folder']==$row['id']){
                                        echo ' selected ';
                                    }
                                    echo '>'.$row['title'].'</option>';                       }
                                ?>
                            </select>
                        </p>
                        <p>Title: </p>
                        <input type="text" name="title" autofocus maxlength="50" <?php
                        echo 'value="'.$rowThisFolder['title'].'"';
                        ?>>
                        <p>Description: </p>
                        <textarea name="description" rows="4" cols="50"><?php
                            echo $rowThisFolder['description'];
                            ?></textarea>
                        <input type="hidden" name="folder" value="<?php echo $_POST['folder']; ?>">
                        <p><input type="submit" name="update" value="Update"></p>
                    </form>
                </div>
                <?php
            }

            //Show folder title and description
            if ($thisFolder>0){
                $query="SELECT * FROM folders WHERE id=".$thisFolder;
                $row=Select($query);
                echo '<h4>Folder title:</h4>
                <p>'.$row['title'].'</p><hr>
                ';
                echo '<h4>Folder description:</h4>
                <p>'.$row['description'].'</p><hr>
                ';
            }

            //Show all folder in this level
            ?>

            <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
                <button class="menuBtn" name="addNew">Add new Folder</button>
                <?php
                if ($thisFolder>0){
                ?>
                    <input type="hidden" name="folder" value="<?php echo $thisFolder; ?>">
                    <button name="edit">Edit</button>
                    <button name="delete">Delete</button>
                <?php } ?>
            </form>
            <hr>
            <div class="row">
                <div class="col">
                    <strong>title</strong>
                </div>
                <div class="col">
                    <strong>description</strong>
                </div>
                <div class="col">
                    <strong>tools</strong>
                </div>
            </div><hr>
            <?php
            $query="SELECT * FROM folders WHERE parent=".$thisFolder;
            $result=Selects($query);
            while ($row=$result->fetch_assoc()){
                $query="SELECT id FROM folders WHERE parent=".$row['id'];
                $nextLevel=Selects($query);
                ?>
                <div class="row">
                    <div class="col">
                        <?php
                        if ($nextLevel->num_rows>0){
                            echo '<a href="'.$newuri.'/'.$row['id'].'">'.$row['title'].'</a>';
                        }else {
                            echo $row['title'];
                        }
                        ?>
                    </div>
                    <div class="col">
                        <?php
                            echo $row['description'];
                        ?>
                    </div>
                    <div class="col">
                        <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
                            <input type="hidden" name="folder" value="<?php echo $row['id']; ?>">
                            <button name="edit">Edit</button>
                            <button name="delete">Delete</button>
                        </form>
                    </div>
                </div>
                <hr>
            <?php
            }
            ?>
        </div>
    </div>
</div>

