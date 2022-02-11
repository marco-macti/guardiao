<?php

// Rotas Antigas

Route::get('/teste-email' , 'TesteEmailController@index');

Route::get('/relatorio-lote/{lote}'                    ,'ClienteLoteController@relatorioLote');
Route::get('/relatorio-lote-passo-4/{lote}'            ,'ClienteLoteController@relatorioLotePasso4');
Route::get('/export-json'                              ,'ClienteLoteController@exportJsonNcm');
Route::get('/relatorio-lote-pos-iob/{lote}'            ,'ClienteLoteController@relatorioLotePosIOB');
Route::get('/sincronizar-lote/{lote}'                  ,'ClienteLoteController@sincronizarLote');
Route::post('/monitoramento-lote'                      ,'ClienteLoteController@monitoramentoLote');
Route::get('/consulta-cosmos/{gtin}'                   ,'ClienteLoteController@consultaCosmos');
Route::get('/importar-bc-produto-aux'                  ,'HomeController@importarBCProdutoAux');
Route::get('/relatorio-produtos-ncm-incorretos'        ,'ClienteLoteController@produtosNcmIncorretos');
Route::get('/relatorio-linear/{cliente}'               ,'ReportsController@relatorioLinear');
Route::get('/get-clientes'                             ,'HomeController@getClientes');
Route::get('/get-cliente-lotes/{cliente}'              ,'HomeController@getClienteLotes');
Route::get('/basecomparativa/produto/find/{produto}'   ,'BCProdutoController@find');
Route::get('/basecomparativa/produto/update/'          ,'BCProdutoController@update');
Route::get('/basecomparativa/produto/toJson'           ,'BCProdutoController@toJson');
Route::get('/areacliente/meus-produtos/{cliente}'      , 'AreaClienteController@meusProdutos');
Route::get('/update-produtos-lote-cliente'             ,'HomeController@updateProdutosLoteCliente');
Route::get('/robo/{pg}/{parametro}/{indice_produtos}'  ,'RoboController@index');
Route::get('/robo/pagina-interna'                      ,'RoboController@paginaInterna');
Route::post('/robo/importar-produtos-cosmos'           ,'RoboController@importarProdutosCosmos');
Route::post('/lote/upload'                             , 'ClienteLoteController@upload');
Route::get('/iob/'                                     ,'IobController@index');
Route::post('/iob/import-sheet'                        ,'IobController@importSheet');
Route::post('/cliente/import-sheet-cest'               ,'ClienteLoteController@importSheetCest');
Route::get('/ia'                                       ,'IaController@index');
Route::post('/ia/import-e-auditor'                     ,'IaController@importEAuditor')->name('importEAuditor');
Route::get('/ia/preditar'                              ,'IaController@preditar');
Route::get('/ia/trainamento-base'                      ,'IaController@trainamentoBase')->name('trainamentoBase');
Route::get('/ia/registra-ia'                           ,'IaController@registraIa')->name('registraIa');
Route::any('/ia/retorna-dados'                         ,'IaController@retornaDadosIa');
Route::any('/ia/retorna-dados-planilha/{ncm}'          ,'IaController@retornaDadosPlanilhaIa')->name('ia.retorna-dados-planilha');
Route::get('/ia/consulta-ncm'                          ,'IA\IaController@comparaNcm')->name('ia.consulta.ncm');
Route::get('/ia/consulta-ncm-unico'                    ,'IA\IaController@consultaNcm')->name('ia.consulta.ncm-unico');

// v2.0

Auth::routes();
Route::get('logout'                  , 'Auth\LoginController@logout');

Route::group(['middleware' => 'auth'], function () {

    Route::group(['middleware' => 'checkFirstAccess'], function () {

        Route::get('/'                 , 'HomeController@index')->name('home.index');
        Route::get('/home'             , 'HomeController@index')->name('home');
        Route::any('/atualizar-senha'  , 'HomeController@atualizarSenha')->name('atualizar.senha');

        Route::group(['namespace' => 'Frontend'], function(){
            Route::get('/lotes/assumir-ncm'  , 'LotesController@assumirNcm')->name('assumir.ncm');
            Route::get('/lotes/{lote}/export','LotesController@export')->name('exportar.lote');
            Route::get('/lotes/busca-relacionados-by-descricao'  , 'LotesController@buscaRelacionadosCosmosByDescricao')->name('busca.relacionados.by.descricao');

            Route::resource('/lotes', 'LotesController');

            Route::get('/lotes/auditar/{lote_id}'  , 'LotesController@auditarLote')->name('lote.auditar');

        });

        Route::group(['prefix' => 'admin','namespace' => 'Admin'], function () {

            Route::resource('/lotes', 'LotesController', ['names' => ['edit' => 'admin.lotes.edit']]);

            Route::group(['prefix' => 'clientes'],function () {

                Route::get('/'                    , 'ClientesController@index')->name('admin.clientes.index');
                Route::get('/create'              , 'ClientesController@create')->name('admin.clientes.create');
                Route::post('/store'              , 'ClientesController@store')->name('admin.clientes.store');
                Route::get('/edit/{cliente}'      , 'ClientesController@edit')->name('admin.clientes.edit');
                Route::put('/update/{cliente}'    , 'ClientesController@update')->name('admin.clientes.update');
                Route::get('/show/{id}'           , 'ClientesController@show')->name('admin.clientes.show');

                Route::post('/add-user'       , 'ClientesController@adduser')->name('admin.clientes.adduser');
                Route::get('/remove-user/{id}', 'ClientesController@removeUser')->name('admin.clientes.removeUser');
                Route::get('/info-user'       , 'ClientesController@infoUser')->name('admin.clientes.infoUser');
                Route::post('/edit-user'      , 'ClientesController@edituser')->name('admin.clientes.edituser');
                Route::get('/check-cnpj'      , 'ClientesController@checkCnpj')->name('admin.clientes.checkCnpj');

            });
        });
    });
});







