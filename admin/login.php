<?php


?>


<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>Iniciar sesión</title>
  <link rel="stylesheet" href="../style/loginstyle.css">

</head>
<body>

<div class="wrapper">

  <div class="login-box">
    <form action="">
      <h2>Iniciar sesión</h2>

      <div class="input-box">
        <span class="icon">
          <ion-icon name="mail"></ion-icon>
        </span>
        <input type="text" required>
        <label>Documento</label>
      </div>

      <div class="input-box">
        <span class="icon">
          <ion-icon name="lock-closed"></ion-icon>
        </span>
        <input type="password" required>
        <label>PIN </label>
      </div>

       <div class="input-box">
        <span class="icon">
          <ion-icon name="lock-closed"></ion-icon>
        </span>
        <input type="password" required>
        <label>Contraseña</label>
      </div>

      <button type="submit" class="btn-login">Login</button>
      
      <form action="pagina-destino.html">
        <button type="submit">Ir a la página</button>
      </form> 
      

    </form>
  </div>

</div>

</body>
</html>