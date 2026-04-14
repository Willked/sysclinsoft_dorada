<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SysClinSoft') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('styles.css') }}">
</head>
<body>
    <div class="screen">
        <div class="left">
            <div class="left-top">
                <div class="brand">
                    <div class="brand-icon">
                        <x-lucide-heart-pulse id="icon-heart-pulse" />
                    </div>
                    <div>
                        <div class="brand-name">SysClinSoft</div>
                        <div class="brand-sub">Sistema de gestion clinica</div>
                    </div>
                </div>

                <div class="hero-title">Historia clinica prehospitalaria integrada</div>
                <div class="hero-sub">
                    Registro de atenciones APH con integracion HL7 FHIR R4 y generacion automatica de RIPS JSON para ADRES.
                </div>
            </div>

            <div class="left-bottom">
                Resolucion 2275 de 2023 · HL7 FHIR R4 · Colombia
            </div>
        </div>

        <div class="right">
            <div class="form-wrap">
                <div class="form-title">Bienvenido</div>
                <div class="form-sub">Ingresa tus credenciales para acceder al sistema de historia clinica.</div>

                @if ($errors->any())
                    <div class="form-alert form-alert-error">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if (session('status'))
                    <div class="form-alert form-alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="field">
                        <label for="email">Correo electronico</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="usuario@ejemplo.com" autocomplete="username" required autofocus>
                    </div>

                    <div class="field">
                        <label for="password">Contrasena</label>
                        <div class="input-wrap">
                            <input id="password" type="password" name="password" autocomplete="current-password" required>
                            <button
                                type="button"
                                class="input-icon"
                                id="toggle-password"
                                aria-label="Mostrar contrasena"
                                aria-controls="password"
                                aria-pressed="false"
                            >
                                <x-lucide-eye-off id="icon-eye-off" />
                                <x-lucide-eye id="icon-eye" style="display: none;" />
                            </button>
                        </div>
                    </div>

                    <div class="row-check">
                        <label class="check-label" for="remember">
                            <input id="remember" name="remember" type="checkbox" class="native-checkbox" {{ old('remember') ? 'checked' : '' }}>
                            Mantener sesion activa
                        </label>
                        @if (Route::has('password.request'))
                            <a class="forgot" href="{{ route('password.request') }}">¿Olvidaste tu contrasena?</a>
                        @endif
                    </div>

                    <button type="submit" class="btn-login">Ingresar al sistema</button>
                </form>

                <div class="form-footer">
                    Sistema restringido a personal autorizado.<br>
                    Toda actividad queda registrada.
                    <div class="version-badge">v1.0.0 · Laravel {{ app()->version() }} · FHIR R4</div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const passwordInput = document.getElementById('password');
        const toggleButton = document.getElementById('toggle-password');
        const eyeOffIcon = document.getElementById('icon-eye-off');
        const eyeIcon = document.getElementById('icon-eye');

        if (passwordInput && toggleButton && eyeOffIcon && eyeIcon) {
            toggleButton.addEventListener('click', () => {
                const isHidden = passwordInput.type === 'password';

                passwordInput.type = isHidden ? 'text' : 'password';
                toggleButton.setAttribute('aria-pressed', String(isHidden));
                toggleButton.setAttribute('aria-label', isHidden ? 'Ocultar contrasena' : 'Mostrar contrasena');
                eyeOffIcon.style.display = isHidden ? 'none' : 'block';
                eyeIcon.style.display = isHidden ? 'block' : 'none';
            });
        }
    </script>
</body>
</html>
