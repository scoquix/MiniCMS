<?php
if (isset($_POST['login']) && isset($_POST['password'])) {
    $conn  = mysqli_connect('localhost', 'root', '', 'sszafranski');
    $login = htmlentities($_POST['login']); #funkcja htmlentities zapobiega wstrzykiwaniu SQL !!!!!!
    $haslo = htmlentities($_POST['password']);
    $rs    = mysqli_query($conn, "SELECT Count(id) FROM users WHERE login='$login'
   AND password=sha1('$haslo')");
    $rec   = mysqli_fetch_array($rs);
    if ($rec[0] > 0) {
        session_start();
        $_SESSION['login'] = $_POST['login'];
        header("Location: glowna.php?" . SID);
        exit();
    } else
        $error = "<B>Wrong login or password!</B><BR>";
} else
    $error = false;
?>
<HTML>
<HEAD>
    <TITLE>Logowanie</TITLE>
    <link href='http://fonts.googleapis.com/css?family=Raleway:400,200' rel='stylesheet' type='text/css'>
    <link href='logowanie.css' rel='stylesheet' type='text/css'>
    <meta charset="utf-8"/>
</HEAD>
<BODY>
<?php
echo $error ? $error : "";
?>
        <div class="container">
            <div class="row login_box">
                <div class="col-md-12 col-xs-12" align="center">
                    <div class="outter"><img src="http://lorempixel.com/output/people-q-c-100-100-1.jpg" class="image-circle"/></div>   
                    <h1>Hi Guest</h1>
                    <B>Welcome to your mini&nbsp;CMS</B>
                </div>
                <div class="col-md-12 col-xs-12 login_control">
                    <form method="POST">
                        <div class="control" align="center">
                            <div class="label">Login</div>
                            <input type="text" class="form-control" name="login" value="user"/>
                        </div>
                        <div class="control" align="center">
                             <div class="label">Password</div>
                            <input type="password" class="form-control" name="password" value="haslo"/>
                        </div>
                        <div align="center">
                             <input type="submit" class="btn btn-orange" value="Log in"/>  
                        </div>
                   </form>
                </div>
            </div>
        </div>
    </BODY>
</HTML> 