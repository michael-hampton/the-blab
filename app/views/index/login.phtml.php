
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>Responsive facebook like homepage, Registration and login with twitter bootstrap 3.0 - wsnippets.com</title>
        <link href="/facebook/public/css/bootstrap.css" rel="stylesheet">
        <link href="/facebook/public/css/sticky-footer.css" rel="stylesheet">
        <link href="/facebook/public/css/custom.css" rel="stylesheet">
    </head>

    <body>
        <div id="wrap">
            <div class="navbar navbar-default navbar-fixed-top">
                <div class="container">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="http://wsnippets.com">wsnippets.com</a>
                    </div>
                    <div class="collapse navbar-collapse">
                        <form class="navbar-form navbar-right" id="header-form" role="form">
                            <div class="lt-left">
                                <div class="form-group">
                                    <label for="exampleInputEmail2">Username</label>
                                    <input type="text" class="form-control input-sm" id="username" placeholder="Email or Phone">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputPassword2">Password</label>
                                    <input type="password" class="form-control input-sm" id="password" placeholder="Password">
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox"> Remember me
                                    </label>
                                </div>
                            </div>
                            <div class="lt-right">
                                <button type="button" class="login-btn">Login</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
            <div class="container" id="home">
                <div class="row">

                    <div class="col-md-7">
                        <h3 class="slogan">
                            Facebook helps you connect and share with the people in your life.
                        </h3>
                        <img src="img/background.png" class="img-responsive" />
                        <hr>

                    </div>
                    <div class="col-md-5">
                        <form id="newUserForm" action="#" method="post" class="form" role="form">
                            <legend><a>Create your account</a></legend>
                            <h4>It's free and always will be.</h4>
                            <div class="row">
                                <div class="col-xs-6 col-md-6">
                                    <input class="form-control input-lg" name="firstname" placeholder="First Name" type="text" autofocus />
                                </div>
                                <div class="col-xs-6 col-md-6">
                                    <input class="form-control input-lg" name="lastname" placeholder="Last Name" type="text" />
                                </div>
                            </div>
                            <input class="form-control input-lg" name="youremail" placeholder="Your Email" type="email" />
                            <input class="form-control input-lg" name="reenteremail" placeholder="Re-enter Email" type="email" />
                            <input class="form-control input-lg" name="password" placeholder="New Password" type="password" />
                            <label for="">
                                Birth Date</label>
                            <div class="row">
                                <div class="col-xs-4 col-md-4">
                                    <select class="form-control input-lg">
                                        <option value="Month">Month</option>
                                    </select>
                                </div>
                                <div class="col-xs-4 col-md-4">
                                    <select class="form-control input-lg">
                                        <option value="Day">Day</option>
                                    </select>
                                </div>
                                <div class="col-xs-4 col-md-4">
                                    <select class="form-control input-lg">
                                        <option value="Year">Year</option>
                                    </select>
                                </div>
                            </div>
                            <label class="radio-inline">
                                <input type="radio" name="sex" id="inlineCheckbox1" value="male" />
                                Male
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="sex" id="inlineCheckbox2" value="female" />
                                Female
                            </label>
                            <br />
                            <span class="help-block">By clicking Create my account, you agree to our Terms and that you have read our Data Use Policy, including our Cookie Use.</span>
                            <button class="btn btn-lg btn-primary btn-block signup-btn" type="button">
                                Create my account</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>



        <script src="/facebook/public/js/jquery-2.1.1.js"></script>

        <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

        <script src="/facebook/public/js/menu.js"></script>
        <script src="/facebook/public/js/inspinia.js"></script>

        <script src="/facebook/public/js/bootstrap.js"></script>
        <script src="/facebook/public/js/slimscroll.js"></script>
        <script src="/facebook/public/js/pagination.js"></script>


        <script>

            $ (".login-btn").on ("click", function ()
            {
                var password = $ ("#password").val ();
                var username = $ ("#username").val ();

                $.ajax ({
                    url: '/facebook/index/doLogin',
                    type: 'POST',
                    data: {username: username, password: password},
                    success: function (response)
                    {
                        if(response === 'ERROR') {
                            alert("Unable to login");
                        } else {
                            location.href = "index.php";
                        }
                        alert (response);
                    }
                });
            });

            $ (".signup-btn").on ("click", function ()
            {
                var FormData = $ ("#newUserForm").serialize ();

                $.ajax ({
                    url: 'saveNewUser.php',
                    type: 'POST',
                    data: FormData,
                    success: function (response)
                    {
                        alert (response);
                    }
                });

                return false;
            });
        </script>
    </body>
</html>