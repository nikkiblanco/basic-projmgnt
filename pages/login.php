<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Login</title>

     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
     <link rel="stylesheet" href="/pages/assets/css/styles.css">
     <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/solid.js" integrity="sha384-tzzSw1/Vo+0N5UhStP3bvwWPq+uvzCMfrN1fEFe+xBmv1C/AtVX5K0uZtmcHitFZ" crossorigin="anonymous"></script>
     <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/fontawesome.js" integrity="sha384-6OIrr52G08NpOFSZdxxz1xdNSndlD4vdcf/q2myIUVO0VsqaGHJsB0RaBE01VTOY" crossorigin="anonymous"></script>
     <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</head>

<body style="background-color: #fff">
     <div class="container mt-5">
          <div class="row d-flex justify-content-center">
               <div class="col-md-6 ">
                    <div class="card px-5 py-5">
                         <div class="row">
                              <div class="col-12">
                                   <div class="form-group">
                                        <label for="">Username</label>
                                        <input type="text" id="txt_uname" class="form form-control" />
                                   </div>
                              </div>
                         </div>
                         <div class="row">
                              <div class="col-12">
                                   <div class="form-group">
                                        <label for="">Password</label>
                                        <input type="password" id="txt_pass" class="form form-control" onkeydown="triggerClick()" />
                                   </div>
                              </div>
                         </div>
                         <div class="row mt-2">
                              <div class="col">
                                   <button class="btn btn-info w-100" onclick="return logMeIn();">Login</button>
                              </div>
                         </div>
                    </div>
               </div>
          </div>
     </div>

     <script>
          function logMeIn() {
               let log_id = $("#txt_uname").val();
               let password = $("#txt_pass").val();

               if (log_id.trim() != "" || password.trim() != "") {
                  
                    $.ajax({
                         method: "post",
                         url: "../actions/login.php",
                         cache: false,
                         data: {
                              logId: log_id,
                              password: password
                         },
                         success: (res) => {
                              switch (res) {
                                   case "0":
                                        alert("Username does not exist");
                                        break;
                                   case "1":
                                        window.location = "/";
                                        break;
                                   case "2":
                                        alert("Password is incorrect. Please try again");
                                        break;
                                   default:
                                        alert("Username or password is incorrect. Please try again.");
                                        break;
                              }
                         },
                         error: (err) => {
                              console.log("[Error] logMeIn()", err.responseText)
                         }
                    })
               } else {
                    alert("Please enter you username and password");
               }
          }

          function triggerClick() {
               if (event.keyCode == 13) {
                    logMeIn()
               }
          }
     </script>
</body>

</html>