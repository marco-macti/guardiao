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

    <title>Guardião Tributário</title>

    <!-- Vendor css -->
    <link href="{{ URL('lib/font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ URL('lib/Ionicons/css/ionicons.css') }}" rel="stylesheet">

    <!-- Slim CSS -->
    <link rel="stylesheet" href="{{ URL('css/slim.css') }}">

    <style>
        .small-alert{
            font-size: 0.8rem !important;
        }
    </style>

  </head>
  <body>

    <div class="signin-wrapper">
        

      <div class="signin-box">
        <h2 class="slim-logo">
            <p align="center">
                <img src="https://guardiaotributario.com.br/wp-content/uploads/2019/06/guardia%CC%83o_tributario_logotipo.png" style="width:200px" class="img-responsive">
            </p>
        <h2>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <input placeholder="Digite seu e-mail" id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
        
                    @error('email')
                        <small class="invalid-feedback small-alert" role="alert">
                            <strong>{{ $message }}</strong>
                        </small>
                    @enderror
        
                </div><!-- form-group -->
        
                <div class="form-group mg-b-50">
                    <input placeholder="Digite sua senha" id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
        
                    @error('password')
                        <span class="invalid-feedback small-alert" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div><!-- form-group -->
        
                <button type="submit" class="btn btn-primary btn-block btn-signin">Entrar</button>
                
            </form>        
        
       
      </div><!-- signin-box -->

    </div><!-- signin-wrapper -->

    <script src="{{ URL('lib/jquery/js/jquery.js') }}"></script>
    <script src="{{ URL('lib/popper.js/js/popper.js') }}"></script>
    <script src="{{ URL('lib/bootstrap/js/bootstrap.js') }}"></script>

    <script src="{{ URL('js/slim.js') }}"></script>

  </body>
</html>
