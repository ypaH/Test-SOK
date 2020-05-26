<?php
//Check login information
if (isset($_POST['logB'])){
    if ($_POST['name']==''){
        $_SESSION['errors']['name']='Name cannot be blank';
    }
    if ($_POST['pass']==''){
        $_SESSION['errors']['pass']='Password cannot be blank';
    }
    //Check login and password
    if (!isset($_SESSION['errors'])){
        if ($_POST['name']=='test'){
            if ($_POST['pass']=='pass'){
                $_SESSION['loginOk']=time();
                header ('Location: /');
            }
        }else {
            $_SESSION['errors']['general']='Incorrect username ar password';
        }
    }


}
?>
<div class="container">
    <div class="row">
        <div class="mt-5 mx-auto">
            <h4>Login form:</h4>
            <form action="/" method="post">
                <p>Name:</p>
                <?php
                if (isset($_SESSION['errors']['name'])){
                    echo '<p class="error">'.$_SESSION['errors']['name'].'</p>';
                }
                ?>
                <p><input type="text" name="name" maxlength="10" autofocus></p>
                <p>Password:</p>
                <?php
                if (isset($_SESSION['errors']['pass'])){
                    echo '<p class="error">'.$_SESSION['errors']['pass'].'</p>';
                }
                ?>
                <p><input type="password" name="pass" maxlength="20"></p>
                <?php
                if (isset($_SESSION['errors']['general'])){
                    echo '<p class="error">'.$_SESSION['errors']['general'].'</p>';
                }
                ?>
                <p><input type="submit" name="logB" value="Enter"></p>
            </form>
        </div>
    </div>
</div>
