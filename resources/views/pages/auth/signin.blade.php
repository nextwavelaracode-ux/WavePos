@extends('layouts.fullscreen-layout')

@section('content')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

        .login-root {
            font-family: 'Inter', sans-serif;
            display: flex;
            height: 100vh;
            width: 100%;
            overflow: hidden;
        }

        /* ===================== LEFT PANEL ===================== */
        .login-left {
            position: relative;
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 55%;
            overflow: hidden;
            padding: 48px;
        }

        @media (min-width: 1024px) {
            .login-left {
                display: flex;
            }
        }

        .login-bg {
            position: absolute;
            inset: 0;
            background-image: url('/images/login/fondo-2.svg');
            /* Imagen de fondo */
            background-size: cover;
            background-position: center top;
            background-repeat: no-repeat;
        }

        .login-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(160deg,
                    rgba(10, 17, 40, 0.92) 0%,
                    rgba(30, 58, 95, 0.88) 35%,
                    rgba(80, 158, 189, 0.82) 70%,
                    rgba(0, 191, 255, 0.75) 100%);
        }

        .login-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        .glow-circle-1 {
            position: absolute;
            top: -80px;
            left: -80px;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(167, 139, 250, 0.15) 0%, transparent 70%);
            filter: blur(40px);
            pointer-events: none;
        }

        .glow-circle-2 {
            position: absolute;
            bottom: 40px;
            right: -60px;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(20, 184, 166, 0.15) 0%, transparent 70%);
            filter: blur(50px);
            pointer-events: none;
        }

        .login-left-content {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            max-width: 100%;
            width: 100%;
            padding: 0 20px;
        }

        .welcome-title {
            font-size: 2.2rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 24px;
            text-shadow: 0 2px 20px rgba(0, 0, 0, 0.3);
            letter-spacing: -0.5px;
        }

        .welcome-title span {
            background: linear-gradient(135deg, #a78bfa 0%, #2dd4bf 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .mascot-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
        }

        .mascot-container img {
            width: 280px;
            height: auto;
            filter: drop-shadow(0 20px 40px rgba(0, 0, 0, 0.4));
        }

        .pos-description {
            font-size: 15px;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.7;
            font-weight: 400;
            max-width: 500px;
            margin-bottom: 28px;
        }

        .pos-features {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-bottom: 28px;
            flex-wrap: wrap;
        }

        .pos-feature {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .pos-feature-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .pos-feature h4 {
            font-size: 14px;
            font-weight: 700;
            color: #ffffff;
            margin: 0 0 2px;
            text-align: left;
        }

        .pos-feature p {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
            margin: 0;
            text-align: left;
        }

        .pos-stats {
            display: flex;
            justify-content: center;
            gap: 48px;
        }

        .pos-stat {
            text-align: center;
        }

        .pos-stat-value {
            font-size: 26px;
            font-weight: 800;
            background: linear-gradient(135deg, #f0edf9ff 0%, #ffffffff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .pos-stat-label {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.6);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 4px;
        }

        /* ===================== RIGHT PANEL ===================== */
        .login-right {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: #f1f5f9;
            padding: 48px 40px;
            overflow-y: auto;
            position: relative;
        }

        .dark .login-right {
            background: #0f172a;
        }

        /* Subtle pattern on right */
        .login-right::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle at 1px 1px, rgba(148, 163, 184, 0.1) 1px, transparent 0);
            background-size: 24px 24px;
            pointer-events: none;
        }

        .form-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 480px;
        }


        /* Form card - transparent, no border */
        .form-card {
            background: transparent;
            border-radius: 0;
            padding: 0;
            box-shadow: none;
            border: none;
        }

        .dark .form-card {
            background: transparent;
            border: none;
            box-shadow: none;
        }

        /* Separator between form sections */
        .form-section-sep {
            height: 1.5px;
            background: rgba(226, 232, 240, 0.8);
            margin: 22px 0;
        }

        .dark .form-section-sep {
            background: rgba(51, 65, 85, 0.7);
        }

        .form-header {
            margin-bottom: 28px;
        }

        .form-logo-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
        }

        .form-logo-row img {
            height: 38px;
        }

        .form-divider-v {
            width: 1px;
            height: 28px;
            background: rgba(226, 232, 240, 0.8);
        }

        .dark .form-divider-v {
            background: rgba(51, 65, 85, 0.8);
        }

        .form-product-tag {
            font-size: 10px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            line-height: 1.3;
        }

        .dark .form-product-tag {
            color: #94a3b8;
        }

        .form-title {
            font-size: 1.6rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
            margin: 0 0 6px;
        }

        .dark .form-title {
            color: #f1f5f9;
        }

        .form-subtitle {
            font-size: 0.875rem;
            color: #64748b;
            font-weight: 400;
        }

        .dark .form-subtitle {
            color: #94a3b8;
        }

        /* Form fields */
        .field-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 7px;
        }

        .dark .field-label {
            color: #cbd5e1;
        }

        .field-wrap {
            position: relative;
        }

        .field-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            transition: color 0.2s;
            pointer-events: none;
        }

        .field-input {
            width: 100%;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            background: white;
            padding: 12px 14px 12px 42px;
            font-size: 14px;
            color: #0f172a;
            outline: none;
            transition: all 0.2s;
            font-family: 'Inter', sans-serif;
        }

        .field-input:focus {
            border-color: #0ea5e9;
            background: white;
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
            color: #0f172a;
        }

        .dark .field-input {
            background: #1e293b;
            border-color: #334155;
            color: #f1f5f9;
        }

        .dark .field-input:focus {
            border-color: #38bdf8;
            background: #1e293b;
            box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.1);
        }

        .field-input::placeholder {
            color: #94a3b8;
        }

        .field-input:focus~.field-icon,
        .field-wrap:focus-within .field-icon {
            color: #0ea5e9;
        }

        .pw-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #94a3b8;
            padding: 4px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            transition: color 0.2s;
        }

        .pw-toggle:hover {
            color: #475569;
        }

        .dark .pw-toggle:hover {
            color: #cbd5e1;
        }

        .field-error {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            color: #ef4444;
            margin-top: 6px;
            font-weight: 500;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            font-weight: 500;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            color: #475569;
        }

        .dark .remember-label {
            color: #94a3b8;
        }

        .remember-label input[type=checkbox] {
            width: 16px;
            height: 16px;
            accent-color: #0ea5e9;
            border-radius: 4px;
        }

        .forgot-link {
            color: #0ea5e9;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
        }

        .forgot-link:hover {
            color: #0284c7;
        }

        /* Alert boxes */
        .alert-success,
        .alert-error {
            display: flex;
            align-items: center;
            gap: 10px;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #15803d;
        }

        .alert-error {
            background: #fff1f2;
            border: 1px solid #fecdd3;
            color: #be123c;
        }

        .dark .alert-success {
            background: rgba(34, 197, 94, 0.1);
            border-color: rgba(34, 197, 94, 0.2);
            color: #4ade80;
        }

        .dark .alert-error {
            background: rgba(244, 63, 94, 0.1);
            border-color: rgba(244, 63, 94, 0.2);
            color: #fb7185;
        }

        /* Submit button */
        .btn-submit {
            width: 100%;
            padding: 13px;
            border-radius: 12px;
            background: linear-gradient(135deg, #7c3aed 0%, #0d9488 100%);
            /* Purple to Teal */
            color: white;
            font-size: 15px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0 4px 16px rgba(124, 58, 237, 0.35);
            letter-spacing: 0.3px;
            font-family: 'Inter', sans-serif;
            position: relative;
            overflow: hidden;
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.15) 0%, transparent 50%);
            opacity: 0;
            transition: opacity 0.25s;
        }

        .btn-submit:hover {
            opacity: 0.9;
        }

        .btn-submit:active {
            opacity: 0.85;
        }

        /* Footer */
        .form-footer {
            margin-top: 24px;
            padding-top: 18px;
            border-top: 1.5px solid rgba(226, 232, 240, 0.8);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .dark .form-footer {
            border-top-color: rgba(51, 65, 85, 0.7);
        }

        .footer-copy {
            font-size: 11px;
            color: #94a3b8;
            font-weight: 500;
        }

        .footer-secure {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            color: #10b981;
            font-weight: 600;
        }

        /* Theme toggle */
        .theme-toggle-btn {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 50;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            border: 1px solid #e2e8f0;
            background: white;
            color: #475569;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            transition: all 0.25s ease;
        }

        .dark .theme-toggle-btn {
            background: #1e293b;
            border-color: #334155;
            color: #94a3b8;
        }

        .theme-toggle-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            color: #0ea5e9;
        }

        /* Mobile left-panel benefits (simplified) */
        .mobile-benefits {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 24px;
        }

        @media (min-width: 1024px) {
            .mobile-benefits {
                display: none;
            }
        }

        .mobile-benefit-pill {
            display: flex;
            align-items: center;
            gap: 5px;
            background: rgba(14, 165, 233, 0.08);
            border: 1px solid rgba(14, 165, 233, 0.2);
            border-radius: 100px;
            padding: 4px 12px;
            font-size: 11px;
            color: #0ea5e9;
            font-weight: 600;
        }

        /* Space between form fields */
        .form-fields {
            display: flex;
            flex-direction: column;
            gap: 18px;
            margin-bottom: 18px;
        }
    </style>

    <div class="login-root">

        {{-- ==================== LEFT PANEL ==================== --}}
        <div class="login-left">
            {{-- Background image --}}
            <div class="login-bg"></div>
            {{-- Overlay --}}
            <div class="login-overlay"></div>
            {{-- Grid --}}
            <div class="login-grid"></div>
            {{-- Ambient glows --}}
            <div class="glow-circle-1"></div>
            <div class="glow-circle-2"></div>

            <div class="login-left-content">
                <h1 class="welcome-title">Bienvenido a <span>WAVEPOS</span></h1>

                <div class="mascot-container">
                    <img src="/images/login/mascota-2.png" alt="Mascota WavePOS">
                </div>

                <p class="pos-description">
                    Sistema integral de punto de venta para gestionar ventas, inventario,
                    clientes y reportes en una sola plataforma moderna y eficiente.
                </p>

                <div class="pos-features">
                    <div class="pos-feature">
                        <div class="pos-feature-icon" style="background: linear-gradient(135deg, #8b5cf6, #6d28d9);">⚡</div>
                        <div>
                            <h4>Ventas Rápidas</h4>
                            <p>Transacciones ágiles y seguras</p>
                        </div>
                    </div>
                    <div class="pos-feature">
                        <div class="pos-feature-icon" style="background: linear-gradient(135deg, #22c55e, #16a34a);">📦
                        </div>
                        <div>
                            <h4>Inventario</h4>
                            <p>Control de stock en tiempo real</p>
                        </div>
                    </div>
                    <div class="pos-feature">
                        <div class="pos-feature-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">📊
                        </div>
                        <div>
                            <h4>Reportes</h4>
                            <p>Análisis y métricas clave</p>
                        </div>
                    </div>
                </div>

                <div class="pos-stats">
                    <div class="pos-stat">
                        <div class="pos-stat-value">+500</div>
                        <div class="pos-stat-label">Empresas</div>
                    </div>
                    <div class="pos-stat">
                        <div class="pos-stat-value">99.9%</div>
                        <div class="pos-stat-label">Disponibilidad</div>
                    </div>
                    <div class="pos-stat">
                        <div class="pos-stat-value">24/7</div>
                        <div class="pos-stat-label">Soporte</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==================== RIGHT PANEL ==================== --}}
        <div class="login-right">
            <div class="form-container">



                {{-- Form Card --}}
                <div class="form-card">

                    {{-- Alerts --}}
                    @if (session('success'))
                        <div class="alert-success" role="alert">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert-error" role="alert">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Header --}}
                    <div class="form-header">
                        <div class="form-logo-row">
                            {{-- Light mode: wavepos-logo-dark.svg tiene POS en azul oscuro = visible en fondo claro --}}
                            <img src="/images/logo/wavepos-logo-dark.svg" alt="WavePOS" class="block dark:hidden">
                            {{-- Dark mode: wavepos-logo.svg tiene POS en blanco = visible en fondo oscuro --}}
                            <img src="/images/logo/wavepos-logo.svg" alt="WavePOS" class="hidden dark:block">
                            <div class="form-divider-v"></div>
                            <span class="form-product-tag">POS Empresarial<br>Panamá</span>
                        </div>
                        <h1 class="form-title">Iniciar Sesión</h1>
                        <p class="form-subtitle">Accede a tu panel de control</p>
                    </div>

                    {{-- Form --}}
                    <form action="{{ route('login') }}" method="POST">
                        @csrf

                        <div class="form-fields">

                            {{-- Email --}}
                            <div>
                                <label for="email" class="field-label">Correo Electrónico</label>
                                <div class="field-wrap">
                                    <span class="field-icon">
                                        <svg width="18" height="18" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </span>
                                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                                        placeholder="usuario@empresa.com" class="field-input" required autocomplete="email">
                                </div>
                                @error('email')
                                    <div class="field-error">
                                        <svg width="14" height="14" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Password --}}
                            <div>
                                <label for="password" class="field-label">Contraseña</label>
                                <div class="field-wrap" x-data="{ show: false }">
                                    <span class="field-icon">
                                        <svg width="18" height="18" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </span>
                                    <input :type="show ? 'text' : 'password'" id="password" name="password"
                                        placeholder="••••••••" class="field-input" style="padding-right: 46px;" required
                                        autocomplete="current-password">
                                    <button type="button" class="pw-toggle" @click="show = !show"
                                        title="Mostrar/Ocultar contraseña">
                                        <svg x-show="!show" width="18" height="18" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg x-show="show" style="display:none;" width="18" height="18"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Options row --}}
                            <div class="form-options">
                                <label class="remember-label">
                                    <input type="checkbox" name="remember">
                                    Recordarme
                                </label>
                                <a href="#" class="forgot-link">¿Olvidaste tu contraseña?</a>
                            </div>

                        </div>

                        {{-- Submit --}}
                        <button type="submit" class="btn-submit">
                            Iniciar Sesión &rarr;
                        </button>
                    </form>

                    {{-- Footer --}}
                    <div class="form-footer">
                        <span class="footer-copy">&copy; {{ date('Y') }} WavePOS</span>
                        <span class="footer-secure">
                            <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z" />
                            </svg>
                            Conexión segura SSL
                        </span>
                    </div>

                </div>{{-- /form-card --}}
            </div>{{-- /form-container --}}
        </div>{{-- /login-right --}}

        {{-- Theme Toggle --}}
        <button class="theme-toggle-btn" title="Alternar Tema" @click.prevent="$store.theme.toggle()">
            {{-- Sun icon --}}
            <svg class="hidden dark:block" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M9.99998 1.5415C10.4142 1.5415 10.75 1.87729 10.75 2.2915V3.5415C10.75 3.95572 10.4142 4.2915 9.99998 4.2915C9.58577 4.2915 9.24998 3.95572 9.24998 3.5415V2.2915C9.24998 1.87729 9.58577 1.5415 9.99998 1.5415ZM10.0009 6.79327C8.22978 6.79327 6.79402 8.22904 6.79402 10.0001C6.79402 11.7712 8.22978 13.207 10.0009 13.207C11.772 13.207 13.2078 11.7712 13.2078 10.0001C13.2078 8.22904 11.772 6.79327 10.0009 6.79327ZM5.29402 10.0001C5.29402 7.40061 7.40135 5.29327 10.0009 5.29327C12.6004 5.29327 14.7078 7.40061 14.7078 10.0001C14.7078 12.5997 12.6004 14.707 10.0009 14.707C7.40135 14.707 5.29402 12.5997 5.29402 10.0001ZM15.9813 5.08035C16.2742 4.78746 16.2742 4.31258 15.9813 4.01969C15.6884 3.7268 15.2135 3.7268 14.9207 4.01969L14.0368 4.90357C13.7439 5.19647 13.7439 5.67134 14.0368 5.96423C14.3297 6.25713 14.8045 6.25713 15.0974 5.96423L15.9813 5.08035ZM18.4577 10.0001C18.4577 10.4143 18.1219 10.7501 17.7077 10.7501H16.4577C16.0435 10.7501 15.7077 10.4143 15.7077 10.0001C15.7077 9.58592 16.0435 9.25013 16.4577 9.25013H17.7077C18.1219 9.25013 18.4577 9.58592 18.4577 10.0001ZM14.9207 15.9806C15.2135 16.2735 15.6884 16.2735 15.9813 15.9806C16.2742 15.6877 16.2742 15.2128 15.9813 14.9199L15.0974 14.036C14.8045 13.7431 14.3297 13.7431 14.0368 14.036C13.7439 14.3289 13.7439 14.8038 14.0368 15.0967L14.9207 15.9806ZM9.99998 15.7088C10.4142 15.7088 10.75 16.0445 10.75 16.4588V17.7088C10.75 18.123 10.4142 18.4588 9.99998 18.4588C9.58577 18.4588 9.24998 18.123 9.24998 17.7088V16.4588C9.24998 16.0445 9.58577 15.7088 9.99998 15.7088ZM5.96356 15.0972C6.25646 14.8043 6.25646 14.3295 5.96356 14.0366C5.67067 13.7437 5.1958 13.7437 4.9029 14.0366L4.01902 14.9204C3.72613 15.2133 3.72613 15.6882 4.01902 15.9811C4.31191 16.274 4.78679 16.274 5.07968 15.9811L5.96356 15.0972ZM4.29224 10.0001C4.29224 10.4143 3.95645 10.7501 3.54224 10.7501H2.29224C1.87802 10.7501 1.54224 10.4143 1.54224 10.0001C1.54224 9.58592 1.87802 9.25013 2.29224 9.25013H3.54224C3.95645 9.25013 4.29224 9.58592 4.29224 10.0001ZM4.9029 5.9637C5.1958 6.25659 5.67067 6.25659 5.96356 5.9637C6.25646 5.6708 6.25646 5.19593 5.96356 4.90303L5.07968 4.01915C4.78679 3.72626 4.31191 3.72626 4.01902 4.01915C3.72613 4.31204 3.72613 4.78692 4.01902 5.07981L4.9029 5.9637Z" />
            </svg>
            {{-- Moon icon --}}
            <svg class="dark:hidden" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                <path
                    d="M17.4547 11.97L18.1799 12.1611C18.265 11.8383 18.1265 11.4982 17.8401 11.3266C17.5538 11.1551 17.1885 11.1934 16.944 11.4207L17.4547 11.97ZM8.0306 2.5459L8.57989 3.05657C8.80718 2.81209 8.84554 2.44682 8.67398 2.16046C8.50243 1.8741 8.16227 1.73559 7.83948 1.82066L8.0306 2.5459ZM12.9154 13.0035C9.64678 13.0035 6.99707 10.3538 6.99707 7.08524H5.49707C5.49707 11.1823 8.81835 14.5035 12.9154 14.5035V13.0035ZM16.944 11.4207C15.8869 12.4035 14.4721 13.0035 12.9154 13.0035V14.5035C14.8657 14.5035 16.6418 13.7499 17.9654 12.5193L16.944 11.4207ZM16.7295 11.7789C15.9437 14.7607 13.2277 16.9586 10.0003 16.9586V18.4586C13.9257 18.4586 17.2249 15.7853 18.1799 12.1611L16.7295 11.7789ZM10.0003 16.9586C6.15734 16.9586 3.04199 13.8433 3.04199 10.0003H1.54199C1.54199 14.6717 5.32892 18.4586 10.0003 18.4586V16.9586ZM3.04199 10.0003C3.04199 6.77289 5.23988 4.05695 8.22173 3.27114L7.83948 1.82066C4.21532 2.77574 1.54199 6.07486 1.54199 10.0003H3.04199ZM6.99707 7.08524C6.99707 5.52854 7.5971 4.11366 8.57989 3.05657L7.48132 2.03522C6.25073 3.35885 5.49707 5.13487 5.49707 7.08524H6.99707Z" />
            </svg>
        </button>

    </div>
@endsection
