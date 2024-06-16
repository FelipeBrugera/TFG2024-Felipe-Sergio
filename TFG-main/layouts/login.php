<form method="post">

<div class="formulario">
        <h1>Inicio de sesion</h1>
        <form method="post">
            <div class="username">
                <input type="text"placeholder="Alias" name="usuario" required>
                <label>Alias</label>
            </div>
            <div class="contrasena">
                <input type="password" placeholder="Contraseña" name="contraseña" required>
                <label>Contraseña</label>
            </div>
            <div class="recordar"><a href="#" onclick="submitForgotPassword()">¿Olvido su contraseña?</a></div>
            <input type="submit" name="Login" value="Iniciar sesión" <?php if ($_SESSION['numError']>=3): ?>disabled <?php endif; ?>>
            <div class="registrarse">
                Quiero hacer el <a href="#" onclick="redirectRegistro()">Registro</a>
            </div>
        </form>
    </div>
    <form id="registroForm" method="post" style="display: none;">
     <input type="hidden" name="Login" value="Registrarse">
    </form>
    <form id="forgotPasswordForm" method="post" style="display: none;">
      <input type="hidden" name="Login" value="forgotpsw" id="actionInput">
    </form>

    <script>
        function redirectRegistro(){
        // Mostrar el formulario
        document.getElementById("registroForm").style.display = "block";
        // Enviar el formulario automáticamente
        document.getElementById("registroForm").submit();        }

        function submitForgotPassword() {
        // Establecer el valor del campo oculto, si no está ya predefinido
        document.getElementById('actionInput').value = 'forgotpsw';
        // Enviar el formulario
        document.getElementById('forgotPasswordForm').submit();
      }
    </script>