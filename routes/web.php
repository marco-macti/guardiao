<?php

Route::get('/'                                        ,'HomeController@index');
Route::get('/relatorio-lote/{lote}'                   ,'ClienteLoteController@relatorioLote');
Route::get('/relatorio-lote-passo-4/{lote}'           ,'ClienteLoteController@relatorioLotePasso4');

Route::get('/export-json'                             ,'ClienteLoteController@exportJsonNcm');

Route::get('/relatorio-lote-pos-iob/{lote}'           ,'ClienteLoteController@relatorioLotePosIOB');
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
Route::post('/cliente/import-sheet-cest'               ,'ClienteLoteController@importSheetCest');

//IA
Route::get('/ia'                                       ,'IaController@index');
Route::post('/ia/import-e-auditor'                     ,'IaController@importEAuditor')->name('importEAuditor');
Route::get('/ia/preditar'                              ,'IaController@preditar');
Route::get('/ia/trainamento-base'                      ,'IaController@trainamentoBase')->name('trainamentoBase');
Route::get('/ia/registra-ia'                           ,'IaController@registraIa')->name('registraIa');

// Retorno parao Guardião

Route::any('/ia/retorna-dados'                         ,'IaController@retornaDadosIa');
Route::any('/ia/retorna-dados-planilha/{ncm}'          ,'IaController@retornaDadosPlanilhaIa');


// v2.0

Auth::routes();
Route::get('logout', 'Auth\LoginController@logout');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/home'          , 'HomeController@index')->name('home');
    Route::group(['namespace' => 'Frontend'], function(){
        Route::resource('/lotes', 'LotesController');
    });
    
    Route::prefix('admin')->group(function () {
        Route::group(['namespace' => 'Admin'], function(){
            Route::resource('/lotes', 'LotesController');
        });
    });
});







