<?php
   session_start();
   if (!isset($_SESSION["login"])){
     header("Location: logowanie.php");
    exit();
    
    
   }
   ?>
<html>
   <HEAD>
      <TITLE>Tajne/poufne</TITLE>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
   </HEAD>
   <body>
      <!-- Navbar -->
      <nav class="navbar navbar-default">
         <div class="container">
            <div class="navbar-header">
               <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
               <span class="icon-bar"></span>
               <span class="icon-bar"></span>
               <span class="icon-bar"></span>                        
               </button>
               <a class="navbar-brand" href="#"><?php  echo "Witaj " . $_SESSION["login"]; ?></a>
            </div>
            <div class="collapse navbar-collapse" id="myNavbar">
               <ul class="nav navbar-nav navbar-right">
                  <li data-toggle='modal' data-target='#myModal'><a href="#" >DODAJ NOWY</a></li>
                  <li><?php 
                     echo "<a href='wyloguj.php?" . SID . "'>";
                     echo "WYLOGUJ</a>";
                     ?>
                  </li>
               </ul>
            </div>
         </div>
      </nav>
      <?php
         $conn = new mysqli('localhost','root','','sszafranski')
             or die('Nie można połączyć się z serwerem');
         $przycisk="Dodaj";
         $author='';
         $name='';
         $image='';
         $prize='';
         $id='';
         
         if(isset($_POST['akcja']))
             switch($_POST['akcja'])
             {
                 case "Dodaj":
                     if ( isset($_POST['name'],$_POST['prize'],$_POST['author']) )
                     {
                         # code...
                         $name = $_POST['name'];
                         $prize = $_POST['prize'];
                         #$image = $_POST['image'];
                         $author = $_POST['author'];
                           /* sprawdzenie czy został wybrany plik */
                             if ($_FILES['image']['name'] != '')
                                 if ($_FILES['image']['type'] == 'image/jpeg')
                                 {
                                     if(is_uploaded_file($_FILES['image']['tmp_name']))
                                     {
                                         $image=base64_encode(file_get_contents($_FILES['image']['tmp_name']));
                                         //połączenie z MySQL
                                         //dodanie pliku do pola typu BLOB
                                        echo "<div class='alert alert-info' style='text-align:center'> <strong>Zapisano</strong></div>";  
                                          # echo $sql;
                                         $user = $_SESSION["login"];
                                         $sql="INSERT INTO articles(name,author,prize,image,user) VALUES ('$name','$author','$prize','$image','$user');";
                                         $conn->query($sql) or die("Nie dodano produktow do bazy");
                                     }
                                     else
                                     {
                                         echo("<div class='alert alert-warning' style='text-align:center'> <strong>Problem: Możliwy atak podczas przesyłania pliku.Plik nie został zapisany.</div><br>");
                                     }
                                 }
                                 else
                                 {
                                     echo("<div class='alert alert-warning' style='text-align:center'> <strong>Nie ma pliku</strong></div><br>");
                                     echo($_FILES['image']['error']);
                                 }
                             else
                             {
                                 /* jeśli plik nie został wybrany */
                                 echo("<div class='alert alert-warning' style='text-align:center'> <strong>Wybierz plik.</strong></div><br>");
                             }
                     }                
                     break;
                 case "Usun":
                 $proper = $conn->query('SELECT user FROM articles WHERE id='.$_POST['id'])
                      or die('Nie mozna pobrac rekordu uzytkownika');
                    $row = $proper->fetch_assoc();
                     if($_SESSION['login'] == $row['user'])
                         {
                             if(isset($_POST['id']))
                             {
                                 $id = $_POST['id'];
                                 $sql="DELETE FROM articles WHERE id=$id";
                                 #echo $sql;
                                 $conn->query($sql) or die("Nie usunieto produktu z bazy");
                             }  
                         }
                         else{
                             echo "<div class='alert alert-info' style='text-align:center'> <strong>Nie mozesz usuwac cudzych artykulow</strong></div>";
                         }                     
                     break;
                 case "Edytuj":
                      $proper = $conn->query('SELECT user FROM `articles` WHERE id='.$_POST['id'])
                      or die('Nie mozna pobrac rekordu uzytkownika');
                      $row = $proper->fetch_assoc();
                     if($_SESSION['login'] == $row['user'])
                         {
                             if(isset($_POST['id']))
                             {
                                     $wynik = $conn->query('SELECT * FROM articles WHERE id='.$_POST['id'])
                                             or die('Nie mozna pobrac rekordow');
                                 if($wynik->num_rows>0)
                                 {
                                     $rekord=$wynik->fetch_array();                                
                                 }
                                 $name=$rekord['name'];
                                 $prize=$rekord['prize'];
                                 $image=$rekord['image'];
                                 $author=$rekord['author'];
                                 $id = $_POST['id'];
                                 $przycisk = 'Zapisz'; 
                                 
         
                                 echo('<div class="panel panel-primary">
                                     <div class="panel-heading">EDYCJA</div>
         <div class="panel-body"><form class="form-inline" enctype="multipart/form-data" method="post" >
                                 <input type="hidden" name="id" value="'.$id.'"/> 
                                 Nazwa produktu: <input type="text" class="form-control" name="name" value="'.$name.'"/>
                                 Autor: <input type="text" class="form-control" name="author" value="'.$author.'"/>
                                 Cena: <input type="text" class="form-control" name="prize" value="'.$prize.'"/>
                                 Zdjecie: <input type="file" class="form-control" name="image" value="'.$image.'"/>
                                 <input type="submit" name="akcja" class="btn btn-success" value="Zapisz"/><br>
                                 </form></div>
         </div></div>');
                             }                            
                         }
                         else{
                             echo("<div class='alert alert-info' style='text-align:center'> <strong>Nie mozesz edytowac cudzych zdjec</strong></div>");
                         }
                         
         
                     break;
                 case "Zapisz":
                 if ( isset($_POST['name'],$_POST['prize'],$_POST['author']) )
                     {
                         # code...
                         $name = $_POST['name'];
                         $prize = $_POST['prize'];
                          /* sprawdzenie czy został wybrany plik */
                             if ($_FILES['image']['name'] != '')
                                 if ($_FILES['image']['type'] == 'image/jpeg')
                                 {
                                     if(is_uploaded_file($_FILES['image']['tmp_name']))
                                     {
                                         $image=base64_encode(file_get_contents($_FILES['image']['tmp_name']));
                                         //połączenie z MySQL
                                         //dodanie pliku do pola typu BLOB
                                         $author = $_POST['author'];
                                         $id= $_POST['id'];
                                         $user = $_SESSION["login"];
                                         $sql="UPDATE articles SET name='$name',prize='$prize',image='$image',author='$author' WHERE id='$id'";
                                         $conn->query($sql) or die("Nie zaktualizowano produktu z bazy"); 
                                     }
                                     else
                                     {
                                         echo '<div class="alert alert-warning"><strong>problem: Możliwy atak podczas przesyłania pliku.Plik nie został zapisany.</strong></div><br>';
                                     }
                                 }
                                 else
                                 {
                                     echo('<div class="alert alert-warning"><strong>Nie ma pliku</strong></div><br>');
                                     echo($_FILES['image']['error']);
                                 }
                             else
                             {
                                 /* jeśli plik nie został wybrany */
                                 echo '<div class="alert alert-warning"><strong>Wybierz plik!</strong></div><br>';
                             }
                                               
                     } 
                 break;
             }
         
         $wynik = $conn->query('SELECT * FROM articles;')
             or die('Nie mozna pobrac rekordow');
         if($wynik->num_rows>0)
         {
         ?>
      <?php
         echo('<table border=1 class="table table-hover table-responsive">');
         echo('<tr class="danger"><th >Nazwa Produktu</th><th>Autor</th><th>Zdjecie</th><th>Cena</th><th>Edycja</th><th>Usuwanie</th></tr>');
         while($rekord=$wynik->fetch_array())
         {
             #$edytuj = "<span class='btn btn-warning' data-toggle='modal' data-target='#myModal'><input type='submit' name='akcja'  value='Edytuj'/></span>";
             $edytuj = "<button name='akcja' value='Edytuj' class='btn btn-warning' class='btn btn-info'>Edytuj</button>";
             $usun = "<input type='submit' name='akcja' class='btn btn-danger' value='Usun'/>";
             echo("<form enctype='multipart/form-data' method='post'><tr><td><input type='hidden' name='id' value='".$rekord['id']."'/>".$rekord['name']."</td><td>".$rekord['author']."</td><td><img alt='Embedded Image' src='data:image/jpeg;base64,".$rekord['image']."' /></td><td>".$rekord['prize']."</td><td>$edytuj</td><td>$usun</td></tr></form>");
         }
         echo('</table>');
         }
         $conn->close();
         ?>
      <div class="modal fade" id="myModal" role="dialog">
         <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
               <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">DODAWANIE</h4>
               </div>
               <div class="modal-body">
                  <div class="panel panel-primary">
                     <div class="panel-heading">NIE ZAPOMNIJ WYPELNIC WSZYSTKICH POL :) </div>
                     <div class="panel-body">
                        <form class="form-inline" enctype="multipart/form-data" method="post" >
                        <?php
                           echo('<form enctype="multipart/form-data" method="post" >
                                  <input type="hidden" name="id" value="'.$id.'"/> 
                                  Nazwa produktu: <input type="text" class="form-control" name="name" value="'.$name.'"/><br>
                                  Autor: <input type="text" class="form-control" name="author" value="'.$author.'"/><br/>
                                  Cena: <input type="text" class="form-control" name="prize" value="'.$prize.'"/><br/>
                                  Zdjecie: <input type="file" class="form-control" name="image" value="'.$image.'"/><br/>
                                  <input type="submit" class="btn btn-success" name="akcja" value="'.$przycisk.'"/><br>
                              </form>');
                           ?>
                     </div>
                  </div>
               </div>
               <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
               </div>
            </div>
        </div>
      </div>
      <BR><B>Copyright Sebastian Szafrański</B>
   </body>
</html>