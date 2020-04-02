<?php
include_once("db_connect.php");
$conn = conectar($_POST['empresa']);
if(isset($_POST['eliminar_precio_neto'])){
// validate to check uploaded file is a valid csv file
    //$file_mimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
    //if(!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'],$file_mimes)){
      //  echo($_FILES['file']['tmp_name']);
        //die();
        if(is_uploaded_file($_FILES['file']['tmp_name'])){
            
            $csv_file = fopen($_FILES['file']['tmp_name'], 'r');
            $mysql_insert = "truncate table dev_product_on_sale";
            mysqli_query($conn, $mysql_insert) or die("database error:". mysqli_error($conn));
            while(($emp_record = fgetcsv($csv_file)) !== FALSE){
                    $mysql_insert = "INSERT INTO dev_product_on_sale (reference)VALUES('".$emp_record[0]."')";
                    mysqli_query($conn, $mysql_insert) or die("database error:". mysqli_error($conn));
            }
            fclose($csv_file);
            $mysql_sp = "call eliminarPrecioNeto";
            mysqli_query($conn, $mysql_sp) or die("database error:". mysqli_error($conn));
            $import_status = '?import_status=success';
            echo mysqli_affected_rows(mysqli_query);
        } else {
            $import_status = '?import_status=error';
        }
    //} else
    //    $import_status = '?import_status=invalid_file';
} elseif (isset($_POST['cargar_precio_neto'])){
    if(is_uploaded_file($_FILES['file']['tmp_name'])){
        $csv_file = fopen($_FILES['file']['tmp_name'], 'r');
        $mysql_insert = "truncate table dev_product_on_sale";
        mysqli_query($conn, $mysql_insert) or die("database error:". mysqli_error($conn));
        while(($emp_record = fgetcsv($csv_file)) !== FALSE){
            $mysql_insert = "INSERT INTO dev_product_on_sale (reference)VALUES('".$emp_record[0]."')";
            mysqli_query($conn, $mysql_insert) or die("database error:". mysqli_error($conn));
        }
        fclose($csv_file);
        $mysql_sp = "call cargarPrecioNeto";
        mysqli_query($conn, $mysql_sp) or die("database error:". mysqli_error($conn));
        $import_status = '?import_status=success';
        echo mysqli_affected_rows(mysqli_query);
    } else
        $import_status = '?import_status=error';
/*else
        $import_status = '?import_status=invalid_file';*/

}elseif (isset($_POST['eliminar_promocion_paises'])){
    if(is_uploaded_file($_FILES['file']['tmp_name'])){
        $csv_file = fopen($_FILES['file']['tmp_name'], 'r');
        $mysql_insert = "truncate table dev_product_on_sale";
        mysqli_query($conn, $mysql_insert) or die("database error:". mysqli_error($conn));
        while(($emp_record = fgetcsv($csv_file)) !== FALSE){
            $mysql_insert = "INSERT INTO dev_product_on_sale (reference,id_category,id_category2,id_category3,id_category4,id_category5,id_category6,id_category7,
                                                              id_category8,id_category9,id_category10,id_category11,id_category12)/*,id_category13,id_category14,id_category15),
                                                              id_category16,id_category17,id_category18,id_category19,id_category20*/
                                                      VALUES('$emp_record[0]','$emp_record[1]','$emp_record[2]','$emp_record[3]','$emp_record[4]',
                                                             '$emp_record[5]','$emp_record[6]','$emp_record[7]','$emp_record[8]','$emp_record[9]',
                                                             '$emp_record[10]','$emp_record[11]','$emp_record[12]')";/*,'".$emp_record[13]."','".$emp_record[14]."',
                                                             '".$emp_record[15]."')";",
                                                             '".$emp_record[16]."','".$emp_record[17]."','".$emp_record[18]."','".$emp_record[19]."','".$emp_record[20]."');*/
            mysqli_query($conn, $mysql_insert) or die("database error:". mysqli_error($conn));
        }
        $mysql_sp = "call actualizarIdProduct";
        mysqli_query($conn, $mysql_sp) or die("database error:". mysqli_error($conn));

        $mysql_sp = "call eliminarPromocion";
        mysqli_query($conn, $mysql_sp) or die("database error:". mysqli_error($conn));
        $import_status = '?import_status=success';

        /*  fclose($csv_file);
          $mysql_sp = "call cargarPrecioNeto";
          mysqli_query($conn, $mysql_sp) or die("database error:". mysqli_error($conn));
          $import_status = '?import_status=success';
          echo mysqli_affected_rows(mysqli_query);*/
    } else
        $import_status = '?import_status=error';

}
elseif (isset($_POST['eliminar_promocion'])){
    if(is_uploaded_file($_FILES['file']['tmp_name'])){
        $csv_file = fopen($_FILES['file']['tmp_name'], 'r');
        $mysql_insert = "truncate table dev_product_on_sale";
        mysqli_query($conn, $mysql_insert) or die("database error:". mysqli_error($conn));
        while(($emp_record = fgetcsv($csv_file)) !== FALSE){
            $mysql_insert = "INSERT INTO dev_product_on_sale (reference,id_category)/*,id_category2,id_category3,id_category4,id_category5,id_category6,id_category7,
                                                              id_category8,id_category9,id_category10,id_category11,id_category12),id_category13,id_category14,id_category15),
                                                              id_category16,id_category17,id_category18,id_category19,id_category20*/
                                                      VALUES('$emp_record[0]','$emp_record[1]')";/*",'$emp_record[2]','$emp_record[3]','$emp_record[4]',
                                                             '$emp_record[5]','$emp_record[6]','$emp_record[7]','$emp_record[8]','$emp_record[9]',
                                                             '$emp_record[10]','$emp_record[11]','$emp_record[12]')";/*,'".$emp_record[13]."','".$emp_record[14]."',
                                                             '".$emp_record[15]."')";",
                                                             '".$emp_record[16]."','".$emp_record[17]."','".$emp_record[18]."','".$emp_record[19]."','".$emp_record[20]."');*/
            mysqli_query($conn, $mysql_insert) or die("database error:". mysqli_error($conn));
        }
        $mysql_sp = "call actualizarIdProduct";
        mysqli_query($conn, $mysql_sp) or die("database error:". mysqli_error($conn));

        $mysql_sp = "call eliminarPromocion";
        mysqli_query($conn, $mysql_sp) or die("database error:". mysqli_error($conn));
        $import_status = '?import_status=success';
        /*  fclose($csv_file);
          $mysql_sp = "call cargarPrecioNeto";
          mysqli_query($conn, $mysql_sp) or die("database error:". mysqli_error($conn));
          $import_status = '?import_status=success';
          echo mysqli_affected_rows(mysqli_query);*/
    } else
        $import_status = '?import_status=error';}
elseif (isset($_POST['eliminar_promocion_global'])){
    if(is_uploaded_file($_FILES['file']['tmp_name'])){
        $csv_file = fopen($_FILES['file']['tmp_name'], 'r');
        $mysql_insert = "truncate table dev_product_on_sale";
        mysqli_query($conn, $mysql_insert) or die("database error:". mysqli_error($conn));
        while(($emp_record = fgetcsv($csv_file)) !== FALSE){
            $mysql_insert = "INSERT INTO dev_product_on_sale (id_category)
                                                      VALUES('$emp_record[0]')";
            mysqli_query($conn, $mysql_insert) or die("database error:". mysqli_error($conn));
        }
        $mysql_sp = "call eliminarPromocion";
        mysqli_query($conn, $mysql_sp) or die("database error:". mysqli_error($conn));
        $import_status = '?import_status=success';
    } else
        $import_status = '?import_status=error';}
elseif (isset($_POST['eliminar_descrip_cortas'])){
        $mysql_sp = "call eliminarDescripCorta";
        mysqli_query($conn, $mysql_sp) or die("database error:". mysqli_error($conn));
        $import_status = '?import_status=success';}
elseif (isset($_POST['cargar_promocion_paises'])){
    if(is_uploaded_file($_FILES['file']['tmp_name'])){
        $csv_file = fopen($_FILES['file']['tmp_name'], 'r');
        $mysql_insert = "truncate table dev_product_on_sale";
        mysqli_query($conn, $mysql_insert) or die("database error:". mysqli_error($conn));
        while(($emp_record = fgetcsv($csv_file)) !== FALSE){
            $mysql_insert = "INSERT INTO dev_product_on_sale (reference,id_category,id_category2,id_category3,id_category4,id_category5,id_category6,id_category7,
                                                              id_category8,id_category9,id_category10,id_category11,id_category12)/*,id_category13,id_category14,id_category15),
                                                              id_category16,id_category17,id_category18,id_category19,id_category20*/
                                                      VALUES('$emp_record[0]','$emp_record[1]','$emp_record[2]','$emp_record[3]','$emp_record[4]',
                                                             '$emp_record[5]','$emp_record[6]','$emp_record[7]','$emp_record[8]','$emp_record[9]',
                                                             '$emp_record[10]','$emp_record[11]','$emp_record[12]')";/*,'".$emp_record[13]."','".$emp_record[14]."',
                                                             '".$emp_record[15]."')";",
                                                             '".$emp_record[16]."','".$emp_record[17]."','".$emp_record[18]."','".$emp_record[19]."','".$emp_record[20]."');*/
            mysqli_query($conn, $mysql_insert) or die("database error:". mysqli_error($conn));
        }
        $mysql_sp = "call actualizarIdProduct";
        mysqli_query($conn, $mysql_sp) or die("database error:". mysqli_error($conn));

        $mysql_sp = "call cargarPromocion";
        mysqli_query($conn, $mysql_sp) or die("database error:". mysqli_error($conn));
        $import_status = '?import_status=success';

        /*  fclose($csv_file);
          $mysql_sp = "call cargarPrecioNeto";
          mysqli_query($conn, $mysql_sp) or die("database error:". mysqli_error($conn));
          $import_status = '?import_status=success';
          echo mysqli_affected_rows(mysqli_query);*/
    } else
        $import_status = '?import_status=error';

}
elseif (isset($_POST['cargar_promocion'])){
    if(is_uploaded_file($_FILES['file']['tmp_name'])){
        $csv_file = fopen($_FILES['file']['tmp_name'], 'r');
        $mysql_insert = "truncate table dev_product_on_sale";
        mysqli_query($conn, $mysql_insert) or die("database error:". mysqli_error($conn));
        while(($emp_record = fgetcsv($csv_file)) !== FALSE){
            $mysql_insert = "INSERT INTO dev_product_on_sale (reference,id_category)
                                                      VALUES('$emp_record[0]','$emp_record[1]')";
            mysqli_query($conn, $mysql_insert) or die("database error:". mysqli_error($conn));
        }
        $mysql_sp = "call actualizarIdProduct";
        mysqli_query($conn, $mysql_sp) or die("database error:". mysqli_error($conn));

        $mysql_sp = "call cargarPromocion";
        mysqli_query($conn, $mysql_sp) or die("database error:". mysqli_error($conn));
        $import_status = '?import_status=success';

        /*  fclose($csv_file);
          $mysql_sp = "call cargarPrecioNeto";
          mysqli_query($conn, $mysql_sp) or die("database error:". mysqli_error($conn));
          $import_status = '?import_status=success';
          echo mysqli_affected_rows(mysqli_query);*/
    } else
        $import_status = '?import_status=error';

}
elseif (isset($_POST['prueba'])){
    $conn = conectar($_POST['empresa']);
//    die('Proceso Finalizado con exito para '.$_POST['empresa']);
    die();
}
else{
    die('NO DEFINIDO');}
    echo'<script type="text/javascript">
                        alert("Proceso Finalizado con Exito");
                        window.location.href="index.php";
                    </script>';
?>