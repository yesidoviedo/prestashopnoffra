<?php
//* Database connection start */
/*$servername = "206.72.200.115:3306";
$username = "empresas_develop";
$password = "G6x90xfRIed4voZO";
$dbname = "empresas_ps_venezuela";
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (mysqli_connect_errno()) {
    printf("Connect failed: %sn", mysqli_connect_error());
    exit();
}
$servername = "206.72.200.115:3306";
$username = "qpselectric";
$password = "x+cWlo8+AlQ9Hics";
$dbname = "qpselect_store";
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (mysqli_connect_errno()) {
    printf("Connect failed: %sn", mysqli_connect_error());
    exit();
}
return($conn);*/
        function conectar($bd){
            $servername = "206.72.200.115";
            $db_port = '3306';
            
            if ($bd=='QPS') {
                $servername = "localhost";
                $username = "qpselect_master";
                $password = "masterqps2019";
                $dbname = "qpselect_store";
            }
            else if ($bd=='QPSPANAMA'){
                $servername = "localhost";
                $username = "root";
                $password = "Y_Rar&t$P9MF";
                $dbname = "qpselect_stpanama";}
            else if ($bd=='Noffra'){
                $username = "empresas_develop";
                $password = "G6x90xfRIed4voZO";
                $dbname = "empresas_ps_venezuela";}
            else if ($bd=='NoffraP'){
                $username = "empresas_develop";
                $password = "G6x90xfRIed4voZO";
                $dbname = "empresas_test_venezuela";}
            else if ($bd=='QPSP'){
                $username = "qpsbusin_master";
                $password = "zaz3RFzuwH}U%3En";
                $dbname = "qpsbusin_store";}
            else{
                echo'<script type="text/javascript">
                        alert("No selecciono Ninguna Empresa");
                        window.location.href="index.php";
                    </script>';
                die();

            }
            $conn = mysqli_connect($servername, $username, $password, $dbname, $db_port);
            if (mysqli_connect_errno()) {
                printf("Connect failed: %sn", mysqli_connect_error());
                exit();
            }
            return($conn);
        }
