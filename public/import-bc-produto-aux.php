<?php

error_reporting(E_ALL);
set_time_limit(0);

$db_host = 'guardiao.cfgsnjemvkdk.sa-east-1.rds.amazonaws.com';
$db_user = 'postgres';
$db_name = 'guardiao_testes';
$db_pass = 'Gsv2019!';

//$path = "C:\\wamp64\\www\\br.com.guardiaotributario\\storage\\app\\public\\bc_produto_aux\\tbl_eans_780.json";
$path = "C:\\wamp64\\www\\br.com.guardiaotributario\\storage\\app\\public\\bc_produto_aux\\tbl_eans_780.json";

$dsn = "pgsql:host=$db_host;port=5432;dbname=$db_name;user=$db_user;password=$db_pass";

try{

    $myPDO = new PDO($dsn);

}catch (PDOException $e){

    echo $e->getMessage();
    die;
}

if(file_exists($path)){

    $file    = json_decode(file_get_contents($path));
    $records = $file->{"RECORDS"};

    foreach ($records as $index => $record) {

        $gtin         = $record->codbar;
        $nome         = $record->produto_acento;
        $peso_liquido = $record->peso;
        $ncm          = $record->ncm;
        $cest         = $record->cest_codigo;
        $preco_medio  = $record->preco_medio;


        echo "Inserindo GTIN - $gtin \n";

        try{

            $sql = "INSERT INTO
                        public.bc_produto_aux(
                            status,
                            nome,
                            descricao,
                            preco_medio,
                            preco_maximo,
                            thumbnail,
                            altura,
                            largura,
                            comprimento,
                            peso_liquido,
                            cest_fk_id,
                            gpc_fk_id,
                            ncm_fk_id,
                            gtin_fk_id
                            )
	            VALUES ('', '$nome','', '$preco_medio', '0', '', '0','0', '0','$peso_liquido', '$cest', '', '$ncm', '$gtin');";

            $stmtCliente = $myPDO->query($sql);

            echo "Inserido \n";

        }catch (PDOException $e){
            echo $e->getMessage().'\n';
        }

    }

    echo "Processo finalizado";
}else{
    echo "File or path not found";
}




?>
