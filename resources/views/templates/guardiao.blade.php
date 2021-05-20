<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Twitter -->
    <meta name="twitter:site" content="@themepixels">
    <meta name="twitter:creator" content="@themepixels">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Slim">
    <meta name="twitter:description" content="Premium Quality and Responsive UI for Dashboard.">
    <meta name="twitter:image" content="http://themepixels.me/slim/img/slim-social.png">

    <!-- Facebook -->
    <meta property="og:url" content="http://themepixels.me/slim">
    <meta property="og:title" content="Slim">
    <meta property="og:description" content="Premium Quality and Responsive UI for Dashboard.">

    <meta property="og:image" content="http://themepixels.me/slim/img/slim-social.png">
    <meta property="og:image:secure_url" content="http://themepixels.me/slim/img/slim-social.png">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="600">

    <!-- Meta -->
    <meta name="description" content="Premium Quality and Responsive UI for Dashboard.">
    <meta name="author" content="ThemePixels">

    <title>Guardia Tributário</title>

    <!-- vendor css -->
    <link href="{{ URL('lib/font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ URL('lib/Ionicons/css/ionicons.css') }}" rel="stylesheet">
    <link href="{{ URL('lib/chartist/css/chartist.css') }}" rel="stylesheet">
    <link href="{{ URL('lib/rickshaw/css/rickshaw.min.css')}}" rel="stylesheet">

    <!-- Slim CSS -->
    <link rel="stylesheet" href="{{ URL('css/slim.css') }}">

    <link rel="stylesheet" href="{{ URL('js/dropzone/dist/dropzone.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/10.16.6/sweetalert2.min.css">

  </head>
  <body>

    <div class="slim-header">
      <div class="container">
        <div class="slim-header-left">
          <h2 class="slim-logo">
            <a href="{{ URL('/') }}">
              <img src="https://guardiaotributario.com.br/wp-content/uploads/2019/06/guardia%CC%83o_tributario_logotipo.png" style="width:100px" class="img-responsive">
            </a>
          </h2>

          <div style="display: none" class="search-box">
            <input type="text" class="form-control" placeholder="Search">
            <button class="btn btn-primary"><i class="fa fa-search"></i></button>
          </div><!-- search-box -->
        </div><!-- slim-header-left -->
        <div class="slim-header-right">
          <div style="display:none" class="dropdown dropdown-a">
            <a href="" class="header-notification" data-toggle="dropdown">
              <i class="icon ion-ios-bolt-outline"></i>
            </a>
            <div class="dropdown-menu">
              <div class="dropdown-menu-header">
                <h6 class="dropdown-menu-title">Activity Logs</h6>
                <div>
                  <a href="">Filter List</a>
                  <a href="">Settings</a>
                </div>
              </div><!-- dropdown-menu-header -->
              <div class="dropdown-activity-list">
                <div class="activity-label">Today, December 13, 2017</div>
                <div class="activity-item">
                  <div class="row no-gutters">
                    <div class="col-2 tx-right">10:15am</div>
                    <div class="col-2 tx-center"><span class="square-10 bg-success"></span></div>
                    <div class="col-8">Purchased christmas sale cloud storage</div>
                  </div><!-- row -->
                </div><!-- activity-item -->
                <div class="activity-item">
                  <div class="row no-gutters">
                    <div class="col-2 tx-right">9:48am</div>
                    <div class="col-2 tx-center"><span class="square-10 bg-danger"></span></div>
                    <div class="col-8">Login failure</div>
                  </div><!-- row -->
                </div><!-- activity-item -->
                <div class="activity-item">
                  <div class="row no-gutters">
                    <div class="col-2 tx-right">7:29am</div>
                    <div class="col-2 tx-center"><span class="square-10 bg-warning"></span></div>
                    <div class="col-8">(D:) Storage almost full</div>
                  </div><!-- row -->
                </div><!-- activity-item -->
                <div class="activity-item">
                  <div class="row no-gutters">
                    <div class="col-2 tx-right">3:21am</div>
                    <div class="col-2 tx-center"><span class="square-10 bg-success"></span></div>
                    <div class="col-8">1 item sold <strong>Christmas bundle</strong></div>
                  </div><!-- row -->
                </div><!-- activity-item -->
                <div class="activity-label">Yesterday, December 12, 2017</div>
                <div class="activity-item">
                  <div class="row no-gutters">
                    <div class="col-2 tx-right">6:57am</div>
                    <div class="col-2 tx-center"><span class="square-10 bg-success"></span></div>
                    <div class="col-8">Earn new badge <strong>Elite Author</strong></div>
                  </div><!-- row -->
                </div><!-- activity-item -->
              </div><!-- dropdown-activity-list -->
              <div class="dropdown-list-footer">
                <a href="page-activity.html"><i class="fa fa-angle-down"></i> Show All Activities</a>
              </div>
            </div><!-- dropdown-menu-right -->
          </div><!-- dropdown -->
          <div style="display:none" class="dropdown dropdown-b">
            <a href="" class="header-notification" data-toggle="dropdown">
              <i class="icon ion-ios-bell-outline"></i>
              <span class="indicator"></span>
            </a>
            <div class="dropdown-menu">
              <div class="dropdown-menu-header">
                <h6 class="dropdown-menu-title">Notifications</h6>
                <div>
                  <a href="">Mark All as Read</a>
                  <a href="">Settings</a>
                </div>
              </div><!-- dropdown-menu-header -->
              <div class="dropdown-list">
                <!-- loop starts here -->
                <a href="" class="dropdown-link">
                  <div class="media">
                    <img src="http://via.placeholder.com/500x500" alt="">
                    <div class="media-body">
                      <p><strong>Suzzeth Bungaos</strong> tagged you and 18 others in a post.</p>
                      <span>October 03, 2017 8:45am</span>
                    </div>
                  </div><!-- media -->
                </a>
                <!-- loop ends here -->
                <a href="" class="dropdown-link">
                  <div class="media">
                    <img src="http://via.placeholder.com/500x500" alt="">
                    <div class="media-body">
                      <p><strong>Mellisa Brown</strong> appreciated your work <strong>The Social Network</strong></p>
                      <span>October 02, 2017 12:44am</span>
                    </div>
                  </div><!-- media -->
                </a>
                <a href="" class="dropdown-link read">
                  <div class="media">
                    <img src="http://via.placeholder.com/500x500" alt="">
                    <div class="media-body">
                      <p>20+ new items added are for sale in your <strong>Sale Group</strong></p>
                      <span>October 01, 2017 10:20pm</span>
                    </div>
                  </div><!-- media -->
                </a>
                <a href="" class="dropdown-link read">
                  <div class="media">
                    <img src="http://via.placeholder.com/500x500" alt="">
                    <div class="media-body">
                      <p><strong>Julius Erving</strong> wants to connect with you on your conversation with <strong>Ronnie Mara</strong></p>
                      <span>October 01, 2017 6:08pm</span>
                    </div>
                  </div><!-- media -->
                </a>
                <div class="dropdown-list-footer">
                  <a href="page-notifications.html"><i class="fa fa-angle-down"></i> Show All Notifications</a>
                </div>
              </div><!-- dropdown-list -->
            </div><!-- dropdown-menu-right -->
          </div><!-- dropdown -->
          <div class="dropdown dropdown-c">
            <a href="#" class="logged-user" data-toggle="dropdown">
              <img class="img-responsive" src="{{ URL('img/default-user.png')}}" alt="">
              <span>{{ auth()->user()->name }}</span>
              <i class="fa fa-angle-down"></i>
            </a>

            <div class="dropdown-menu dropdown-menu-right">
              <nav class="nav">
                <a style="display: none" href="page-profile.html" class="nav-link"><i class="fa fa-file"></i> &nbsp;Meus Dados</a>
                <a style="display: none" href="page-signin.html" class="nav-link"><i class="fa fa-users"></i>&nbsp; Usuários</a>
                <a style="display: none" href="page-signin.html" class="nav-link"><i class="fa fa-cog"></i>&nbsp; Ajustes</a>
                <a href="/logout" class="nav-link"><i class="fa fa-exit"></i> &nbsp; Sair</a>
              </nav>
            </div><!-- dropdown-menu -->
          </div><!-- dropdown -->
        </div><!-- header-right -->
      </div><!-- container -->
    </div><!-- slim-header -->

    <div class="container mt-4">
      <div class="row">
        <div class="col-md-12">
          @include('templates.componentes.alerts')
        </div>
      </div>
    </div>

    @yield('conteudo')

    <div class="slim-footer">
      <div class="container">
        <p>Copyright {{ date('Y')}} &copy; Todos os direitos reservados</p>
        <p>Desenvolvido por : <a href="https://greensignal.com.br">Green Signal Softwares</a></p>
      </div><!-- container -->
    </div><!-- slim-footer -->

    <script src="{{ URL('lib/jquery/js/jquery.js') }} "></script>

    <script src="{{ URL('lib/popper.js/js/popper.js') }}"></script>
    <script src="{{ URL('lib/bootstrap/js/bootstrap.js') }}"></script>
    <script src="{{ URL('lib/jquery.cookie/js/jquery.cookie.js') }}"></script>

    <script src="{{ URL('lib/d3/js/d3.js') }}"></script>
    <script src="{{ URL('lib/rickshaw/js/rickshaw.min.js') }}"></script>
    <script src="{{ URL('lib/jquery.sparkline.bower/js/jquery.sparkline.min.js') }}"></script>

    <script src="{{ URL('js/ResizeSensor.js') }}"></script>
    <script src="{{ URL('js/slim.js') }}"></script>
    <script src="{{ URL('js/dropzone/dist/dropzone.js') }}"></script>
    <script src="{{ URL('js/jquery.mask.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/10.16.6/sweetalert2.min.js"></script>

    @stack('post-scripts')

  </body>
</html>
