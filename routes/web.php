<?php

Route::get('/'                                        ,'HomeController@index');
Route::get('/relatorio-lote/{lote}'                   ,'ClienteLoteController@relatorioLote');
Route::get('/sincronizar-lote/{lote}'                 ,'ClienteLoteController@sincronizarLote');
Route::post('/monitoramento-lote'                     ,'ClienteLoteController@monitoramentoLote');
Route::get('/consulta-cosmos/{gtin}'                  ,'ClienteLoteController@consultaCosmos');
Route::get('/importar-bc-produto-aux'                 ,'HomeController@importarBCProdutoAux');
Route::get('/relatorio-produtos-ncm-incorretos'       ,'ClienteLoteController@produtosNcmIncorretos');
Route::get('/relatorio-linear/{cliente}'              ,'ReportsController@relatorioLinear');

Route::get('/get-clientes'                            ,'HomeController@getClientes');
Route::get('/get-cliente-lotes/{cliente}'             ,'HomeController@getClienteLotes');

// Rotas base Comparativa

Route::get('/basecomparativa/produto/find/{produto}'  ,'BCProdutoController@find');
Route::get('/basecomparativa/produto/update/'         ,'BCProdutoController@update');
Route::get('/basecomparativa/produto/toJson'          ,'BCProdutoController@toJson');

// Rotas area do cliente

Route::get('/areacliente/meus-produtos/{cliente}'      , 'AreaClienteController@meusProdutos');
Route::get('/update-produtos-lote-cliente'             ,'HomeController@updateProdutosLoteCliente');

// Robo

Route::get('/robo/{pg}/{parametro}/{indice_produtos}'  ,'RoboController@index');
Route::get('/robo/pagina-interna'                      ,'RoboController@paginaInterna');
Route::post('/robo/importar-produtos-cosmos'           ,'RoboController@importarProdutosCosmos');

// Lote

Route::post('/lote/upload'                             , 'ClienteLoteController@upload');

// Operações do IOB

Route::get('/iob/'                                     ,'IobController@index');
Route::post('/iob/import-sheet'                        ,'IobController@importSheet');
