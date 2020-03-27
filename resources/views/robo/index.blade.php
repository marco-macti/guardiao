<html>
    <body>  
    <div style="display:none">
        <?php
            
            $parametro_busca = urlencode($parametro);
            $url_padrao_cosmos = 'https://cosmos.bluesoft.com.br';
            
            if(isset($pg) && !empty($pg)){

                $url = $url_padrao_cosmos.'/pesquisar?page='.$pg.'&q='.$parametro_busca.'&utf8=%E2%9C%93';

                $pagina = $pg;
            }
            else{
                $url = $url_padrao_cosmos.'/pesquisar?utf8=%E2%9C%93&q='.$parametro_busca;
                
                $pagina = 1;
            }

            $cosmos = file_get_contents($url);

            echo $cosmos;

        ?>
    </div>
    
    <input type="hidden" id="pagina_atual" value="<?= $pagina ?>" />
    <input type="hidden" id="indice_produtos" value="<?= $indice_produtos ?>" />
    <div id="conteudo-pagina-interna" style="display:none"></div>
    <table id="tabela-resultados" border="1">
        <tr>
            <td colspan="7">
                Progresso de páginas: <span id="pagina-atual"></span> de <span id="ultima_pagina"></span>
            </td>
        </tr>
        <tr>
            <td colspan="7">
                Progresso de produtos da página: <span id="produto_atual"></span> de <span id="total_produtos"></span>
            </td>
        </tr>
        <tr>
            <td>Imagem</td>
            <td>Produto</td>
            <td>NCM</td>
            <td>GTIM</td>
            <td>NCM Interno</td>
            <td>Status</td>
            <td>CEST</td>
        </tr>
    </table>
    <script
        src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
        crossorigin="anonymous"></script>
    <script type="text/javascript">
        $(document).ready(function(){

            //verifica paginação 
            var ultima_pagina   = $(".pagination ul li:last").prev().find('a').html()
            var total_itens     =  $(".item").length
            var cont_itens      = 0
            var pagina_atual    = $("#pagina_atual").val()
            var url_atual       = window.location.href;
            var indice_produtos = $("#indice_produtos").val();
            
            $("#pagina-atual").html(pagina_atual)
            $("#ultima_pagina").html(ultima_pagina)
            $("#total_produtos").html(total_itens)

            $(".item").each(function(e){

                
                //recupera variaveis para enviar para o banco
                var img          = $(this).find('.picture a img').attr('src');
                img              = '<?= $url_padrao_cosmos ?>/'+img;
                img              = img.replace("http://localhost/", "")
                var nome_produto = $(this).find('.item-title a').html()
                var ncm          = $(this).find('.list-unstyled li a').first().html()
                var gtim         = $(this).find('.list-unstyled li a').last().html()
                var link_item    = $(this).find('.item-title a').attr('href')
                

                url_atual   = String(url_atual);
                url_atual_ex         = url_atual.split("/", url_atual);

                console.log(url_atual_ex[3]);
                
                if(cont_itens < total_itens){
                    //acessa link interno do item para pegar o ncm e cest
                    setTimeout(function(){

                        //monta url interna
                        link_item = '<?= $url_padrao_cosmos ?>'+link_item;

                        //limpa div
                        $("#conteudo-pagina-interna").html('')

                        //dispara ajax para retornar o conteudo html
                        $.ajax({
                            method:'GET',
                            url: "http://api.guardiaotributario.com.br/robo/pagina-interna",
                            data: {
                                'url':link_item
                            }, 
                            success: function(html_pg_interna){
                                $("#conteudo-pagina-interna").html(html_pg_interna);

                                var ncm_interno    = $("#conteudo-pagina-interna .list-unstyled .ncm-name a").html()
                                var status_interno = $("#conteudo-pagina-interna .list-unstyled li").last().find('strong').html()
                                var cest_interno   = $("#conteudo-pagina-interna .list-unstyled li:last").prev().find('a').html()

                                cont_itens++;

                                $("#produto_atual").html(cont_itens)

                                $("#tabela-resultados").append('<tr><td><img src="'+img+'" width="50" height="50" /></td><td>'+nome_produto+'</td><td>'+ncm+'</td><td>'+gtim+'</td><td>'+ncm_interno+'</td><td>'+status_interno+'</td><td>'+cest_interno+'</td></tr>')

                                if(cont_itens == total_itens){
                                    //redireciona para nova página enquanto tiver páginas para o termo
                                    if(Number(pagina_atual) < Number(ultima_pagina)){

                                                                            
                                        
                                        pagina_atual++
                                        url_atual            = 'http://api.guardiaotributario.com.br/robo/'+pagina_atual+'/{{ $parametro }}/'+indice_produtos

                                        //inserir ajax aqui para inserir no banco de dados e no success dele redirecionar para a próxima página
                                        //variaveis do data do ajax
                                            $.ajax({
                                                method:'POST',
                                                url: "http://api.guardiaotributario.com.br/robo/importar-produtos-cosmos",
                                                data: {
                                                    'ncm_interno':ncm_interno,
                                                    'status_interno':status_interno,
                                                    'cest_interno':cest_interno,
                                                    'img':img,
                                                    'nome_produto':nome_produto,
                                                    'ncm':ncm,
                                                    'gtin':gtim
                                                }, 
                                                success: function(data){
                                                    
                                                    //redireciona para a próxima página e insere o último registro
                                                    window.location.href = url_atual
                                                
                                                }
                                            });

                                    }
                                    else{
                                        //troca de produto
                                        indice_produtos++
                                        pagina_atual         = 1
                                        parametro            = '{{$produtos[$indice_produtos++]}}'
                                        url_atual            = 'http://18.231.15.251/robo/'+pagina_atual+'/'+parametro+'/'+indice_produtos
                                        window.location.href = url_atual
                                    }
                                }
                                else{
                                    $.ajax({
                                                method:'POST',
                                                url: "http://18.231.15.251/robo/importar-produtos-cosmos",
                                                data: {
                                                    'ncm_interno':ncm_interno,
                                                    'status_interno':status_interno,
                                                    'cest_interno':cest_interno,
                                                    'img':img,
                                                    'nome_produto':nome_produto,
                                                    'ncm':ncm,
                                                    'gtin':gtim
                                                }, 
                                                success: function(data){
                                                   //faz nada só insere
                                                    
                                                }
                                            });  
                                }
                                
                            }
                        });

                        
                    }, 500)
                }


            })

        })
    </script>
    </body>
</html>

